<?php

require_once "models/ErrorCollector.php";
require_once "models/DatabaseOps.php";
require_once "models/reCAPTCHA.php";

class ReservFormProcessing
{
    private string $title = '';
    private string $head = '';
    private string $body = '';

    private $errCollector;
    private string $errorLogContext;

    public function __construct(string $errorLogContext)
    {
        $this->errorLogContext = $errorLogContext . '->' .'RsvFormProces';
        $this->errCollector = new ErrorCollector($this->errorLogContext);
    }
    
    public function ProcessForm() : bool
    {
        if (!$this->ValidRequestReferer())  // Se verifica daca cererea HTTP a fost trimisa prin mijloace de pe acest site (acelasi HOST)
        {
            $this->title = 'Formular Netrimis!';
            $this->head = 'Trimitere eșuată :(';
            $this->body = 'Ne pare rău, a intervenit o problemă în transmiterea formularului. Incercați mai târziu.';
        }
        else if (!isset($_POST['tokenFormular']) || !isset($_SESSION['tokenFormular']) || $_POST['tokenFormular'] != $_SESSION['tokenFormular'])
        {
            // Se verifica daca cererea a fost trimisa folosind formularul de pe site (acelasi TOKEN)

            $this->title = 'Formular Netrimis!';
            $this->head = 'Trimitere eșuată :(';
            $this->body = 'Ne pare rău, a intervenit o problemă în transmiterea formularului. Incercați mai târziu.';
        }
        else if (!$this->ValidCAPTCHA())
        {
            $_SESSION['lipsaCAPTCHA'] = true;
            $_SESSION['RzvEsuata'] = 'Verificați-vă identitatea';
            header('Location: /rezervare', true, 301);
            die();
        }
        else if (session_status() != PHP_SESSION_ACTIVE || !isset($_SESSION['UtilizatorID']))  // daca nu exista sesiune/utilizator autentificat
        {
            $this->title = 'Formular Netrimis!';
            $this->head = 'Trimitere eșuată :(';
            $this->body = 'Trebuie să vă autentificați înainte de a trimite acest formular.';
        }
        else if (strtoupper($_SESSION['UtilizatorTip']) != 'CLIENT') // daca utilizatorul autentificat nu este client
        {
            $this->title = 'Formular Netrimis!';
            $this->head = 'Trimitere eșuată';
            $this->body = 'Doar clienții pot trimite acest formular.';
        }
        else if (!$this->ValidFormFields())  // Verificari ale campurilor (exceptand cele pe baza de date)
        {
            header('Location: /rezervare', true, 301);
            die();
        }
        else
        {
            $db = new DatabaseOps($this->errorLogContext);
            
            $fieldEventType = $db->EscapeString($_POST['Tip_event']);
            $fieldDate = $db->EscapeString($_POST['Data']);
            $fieldDate = "STR_TO_DATE('$fieldDate', '%Y-%m-%d')";
            $fieldHall = $db->EscapeString($_POST['Salon']);
            $fieldNrGuests = (int)$_POST['Nr_inv'];
            $fieldMenu = $db->EscapeString($_POST['Meniu']);
            $fieldObs = $db->EscapeString($_POST['Obs']);


            // Verificari ale campurilor pe baza de date

            $hallInfo = $db->query("SELECT cod_salon, capacitate FROM salon WHERE denumire = '$fieldHall'");
            if (empty($hallInfo))  // verificare existenta salon
            {
                $_SESSION['RzvEsuata'] = 'Salonul specificat nu există';
                header('Location: /rezervare', true, 301);
                die();
            }

            if (!empty($fieldNrGuests) && $fieldNrGuests > (int)$hallInfo[0]['capacitate'])  // se verifica daca salonul poate accepta nr. max. de inv.
            {
                $_SESSION['RzvEsuata'] = 'Numărul de invitați depășește capacitatea salonului';
                header('Location: /rezervare', true, 301);
                die();
            }

            $menuInfo = $db->query("SELECT cod_meniu FROM meniu WHERE denumire = '$fieldMenu'");
            if (empty($menuInfo))  // verificare existenta meniu
            {
                $_SESSION['RzvEsuata'] = 'Meniul specificat nu există';
                header('Location: /rezervare', true, 301);
                die();
            }

            $hallCode = $hallInfo[0]['cod_salon'];
            $dayTaken = $db->query("SELECT 1 FROM rezervare WHERE stare = 'CONFIRMATA' AND data_eveniment = $fieldDate AND cod_salon = $hallCode");
            if (!empty($dayTaken))  // se verifica daca exista deja un eveniment confirmat pentru data specificata,  salonul specificat
            {
                $_SESSION['RzvEsuata'] = 'Ne pare rău, există deja un eveniment confirmat pentru data și salonul specificat';
                header('Location: /rezervare', true, 301);
                die();
            }


            // Formularul a trecut de toate verificarile. Se incearca inserarea in baza de date.
            
            $userCode = $_SESSION['UtilizatorCod'];
            $queryResult = $db->query("SELECT cod_client FROM client WHERE cod_utilizator = $userCode");
            if (!empty($queryResult))
            {
                $clientCode = $queryResult[0]['cod_client'];
                $menuCode = $menuInfo[0]['cod_meniu'];
                $fieldNrGuests = empty($fieldNrGuests) ? 'null' : $fieldNrGuests;
                $fieldObs = empty($fieldObs) ? 'null' : ("'" . $fieldObs . "'");  // ghilimele adaugate aici deoarece campul poate fi nul 
                                                                                    // (se evita inserarea sirului 'null' in loc de valoarea null)
                $query = "INSERT INTO rezervare values " .
                         "(NULL, $clientCode, 'ASTEPTARE', default, '$fieldEventType', $fieldDate, $hallCode, $fieldNrGuests, $menuCode, $fieldObs)";
                if ($db->query($query))
                {
                    unset($db);
                    unset($_SESSION['tokenFormular']);  // dupa transmitere cu succes, sesiunea formularului se incheie

                    $this->title = 'Formular Trimis!';
                    $this->head = 'Trimitere reușită :)';
                    $this->body = 'Formularul a fost trimis. În următoarea perioadă vă vom contacta prin telefon/email ' . 
                                  'pentru a vă transmite un răspuns cu privire la rezervarea dorită.';
                    return true;
                }
            }
            $this->title = 'Formular Netrimis!';
            $this->head = 'Trimitere eșuată :(';
            $this->body = 'Ne pare rău, a intervenit o problemă în transmiterea formularului. Incercați mai târziu.';

            unset($db);
        }

        return false;
    }

    private function ValidFormFields() : bool
    {
        $msg = '';

        if (empty($_POST['Tip_event']) || empty($_POST['Data']) || empty($_POST['Salon']) || empty($_POST['Meniu']))  // Lipsesc campuri obligatorii din formular
            $msg = 'Formular necompletat';

        $eventTypes = ['Nunta', 'Botez', 'Privat', 'Corporate'];
        if (!in_array($_POST['Tip_event'], $eventTypes))  // Tip invalid de eveniment
            $msg = 'Tip invalid de eveniment';

        $fieldDate = strtotime($_POST['Data']);  // ret. fals in caz ca parametrul nu este o data.
        if ($fieldDate === false || $fieldDate < strtotime('tomorrow') || strtotime('+2 years') < $fieldDate)  // Data even. nu este in intervalul in care se pot organiza even.
            $msg = 'Data evenimentului nu este validă';

        if (!empty($_POST['Nr_inv']) && (int)$_POST['Nr_inv'] < 0)  // Numar negativ de invitati
            $msg  = 'Număr invalid de invitați';


        if (!empty($msg))
        {
            $_SESSION['RzvEsuata'] = $msg;
            return false;
        }
        return true;
    }

    private function ValidCAPTCHA() : bool
    {
        if (empty($_POST['g-recaptcha-response']))
            return false;

        $recaptchaModel = new reCAPTCHA($this->errorLogContext);
        $recaptchaModel->VerifyCAPTCHA($_POST['g-recaptcha-response']);
        return $recaptchaModel->IsValid();
    }

    private function ValidRequestReferer() : bool
    {
        if (!isset($_SERVER['HTTP_REFERER']))
            return false;

        $refererHost = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
        $correctHost = $_SERVER['HTTP_HOST'];
        return $refererHost == $correctHost;
    }

    public function NewFormToken()
    {
        // Se creeaza o 'sesiune' a formularului (se seteaza $_SESSION['tokenFormular'] cu $formToken) 
        // ce îi va corespunde formularul din pagina incarcata (formularul are un camp invizibil in care retine $formToken)

        $formToken = bin2hex(random_bytes(64));
        if (session_status() == PHP_SESSION_ACTIVE)
            $_SESSION['tokenFormular'] = $formToken;
        else
            $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Creare Token esuata: Sesiunea nu este activa!');
        return $formToken;
    }

    public function GetMsgTitle() : string { return $this->title; }

    public function GetMsgHead() : string { return $this->head; }

    public function GetMsgBody() : string { return $this->body; }
}

?>