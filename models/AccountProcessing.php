<?php

require_once "models/ErrorCollector.php";
require_once "models/DatabaseOps.php";
require_once "models/reCAPTCHA.php";

class AccountProcessing
{
    private string $title = '';
    private string $head = '';
    private string $body = '';

    private $errCollector;
    private string $errorLogContext;

    public function __construct(string $errorLogContext)
    {
        $this->errorLogContext = $errorLogContext . '->' .'AccountProces';
        $this->errCollector = new ErrorCollector($this->errorLogContext);
    }

    public function ProcessLogin() : void
    {
        if (session_status() != PHP_SESSION_ACTIVE)  // sesiune inactiva/dezactivata
        {
            $this->title = 'Autentificare eșuată';
            $this->head = 'Autentificare eșuată :(';
            $this->body = 'Ne pare rău, autentificarea nu a putut fi efectuată cu succes. Încearcă mai târziu.';
        }
        else if (isset($_SESSION['UtilizatorID']))  // sesiune activa si utilizator autentificat
        {
            header('Location: /', true, 301);
            die();
        }
        else if (!$this->ValidRequestReferer())  // Se verifica daca cererea HTTP a fost trimisa prin mijloace de pe acest site (acelasi HOST)
        {
            $this->title = 'Autentificare eșuată';
            $this->head = 'Autentificare eșuată :(';
            $this->body = 'Ne pare rău, autentificarea nu a putut fi efectuată cu succes. Încearcă mai târziu.';
        }
        else if (!isset($_POST['tokenFormular']) || !isset($_SESSION['tokenFormular']) || $_POST['tokenFormular'] != $_SESSION['tokenFormular'])
        {
            // Se verifica daca cererea a fost trimisa folosind formularul de pe site (acelasi TOKEN)

            $this->title = 'Autentificare eșuată';
            $this->head = 'Autentificare eșuată :(';
            $this->body = 'Ne pare rău, autentificarea nu a putut fi efectuată cu succes. Încearcă mai târziu.';
        }
        else if (!$this->ValidCAPTCHA())
        {

            $_SESSION['lipsaCAPTCHA'] = true;
            $_SESSION['AutEsuata'] = 'Verificați-vă identitatea';
            header('Location: /autentificare', true, 301);
            die();
        }
        else if (!$this->ValidLoginFields())  // Verificari ale campurilor (exceptand cele pe baza de date)
        {
            header('Location: /autentificare', true, 301);
            die();
        }
        else
        {
            $db = new DatabaseOps($this->errorLogContext);

            $id = $db->EscapeString($_POST['Utilizator']);
            $password = $db->EscapeString($_POST['Parola']);

            $exist = $db->query("SELECT cod_utilizator, tip, stare FROM utilizator WHERE id='$id' AND parola='$password'");
            if (empty($exist) || strtoupper($exist[0]['stare']) == 'STERS')
            {
                // Date incorecte
                $_SESSION['AutEsuata'] = 'Date incorecte';
                header('Location: /autentificare', true, 301);
                die();
            }
            else
            {
                $_SESSION['UtilizatorID'] = $_POST['Utilizator'];
                $_SESSION['UtilizatorTip'] = $exist[0]['tip'];
                $_SESSION['UtilizatorCod'] = $exist[0]['cod_utilizator'];

                $actTime = date('Y-m-d H:i:s');
                $actTime = "STR_TO_DATE($actTime, '%Y%m%d %h%i%s')";
                $userID = $exist[0]['cod_utilizator'];
                $db->query("INSERT INTO activitate VALUES (NULL, $userID, 'LOGIN', default)");

                unset($_SESSION['tokenFormular']);  // dupa transmitere cu succes, sesiunea formularului se incheie

                header('Location: /', true, 301);
                die();
            }
        }
    }

    public function ProcessLogout() : void
    {
        if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION['UtilizatorID']))  // sesiune activa si utilizator autentificat
        {
            $db = new DatabaseOps($this->errorLogContext);
            $actTime = date('Y-m-d H:i:s');
            $actTime = "STR_TO_DATE($actTime, '%Y%m%d %h%i%s')";
            $codUtiliz = $_SESSION['UtilizatorCod'];
            $db->query("INSERT INTO activitate VALUES (NULL, $codUtiliz, 'LOGOUT', default)");

            unset($_SESSION['UtilizatorID']);
            unset($_SESSION['UtilizatorTip']);
            unset($_SESSION['UtilizatorCod']);
        }

        header('Location: /', true, 301);
        die();
    }

    public function ProcessRegistration() : void
    {
        if (session_status() != PHP_SESSION_ACTIVE)  // sesiune inactiva/dezactivata
        {
            $this->title = 'Înregistrare eșuată';
            $this->head = 'Înregistrare eșuată :(';
            $this->body = 'Ne pare rău, înregistrarea nu a putut fi efectuată cu succes. Încearcă mai târziu.';
        }
        else if (isset($_SESSION['UtilizatorID']))  // sesiune activa si utilizator autentificat
        {
            header('Location: /', true, 301);
            die();
        }
        else if (!$this->ValidRequestReferer())  // Se verifica daca cererea a fost trimisa folosind formularul de pe site (acelasi HOST)
        {
            $this->title = 'Înregistrare eșuată';
            $this->head = 'Înregistrare eșuată :(';
            $this->body = 'Ne pare rău, înregistrarea nu a putut fi efectuată cu succes. Încearcă mai târziu.';
        }
        else if (!isset($_POST['tokenFormular']) || !isset($_SESSION['tokenFormular']) || $_POST['tokenFormular'] != $_SESSION['tokenFormular'])
        {
            // Se verifica daca cererea a fost trimisa folosind formularul de pe site (acelasi TOKEN)
            
            $this->title = 'Înregistrare eșuată';
            $this->head = 'Înregistrare eșuată :(';
            $this->body = 'Ne pare rău, înregistrarea nu a putut fi efectuată cu succes. Încearcă mai târziu.';
        }
        else if (!$this->ValidCAPTCHA())
        {
            $_SESSION['lipsaCAPTCHA'] = true;
            $_SESSION['InregEsuata'] = 'Verificați-vă identitatea';
            header('Location: /inregistrare', true, 301);
            die();
        }
        else if (!$this->ValidRegistFields())  // Verificari ale campurilor (exceptand cele pe baza de date)
        {
            header('Location: /inregistrare', true, 301);
            die();
        }
        else
        {
            $db = new DatabaseOps($this->errorLogContext);

            $fieldID = $db->EscapeString($_POST['Utilizator']);
            $fieldPassword = $db->EscapeString($_POST['Parola']);
            $fieldLastName = $db->EscapeString($_POST['Nume']);
            $fieldFirstName = $db->EscapeString($_POST['Prenume']);
            $fieldEmail = $db->EscapeString($_POST['Email']);
            $fieldPhone = $db->EscapeString($_POST['Telefon']);

            $exist = $db->query("SELECT 1 FROM utilizator WHERE id='$fieldID'");
            if (!empty($exist))
            {
                unset($db);

                $_SESSION['InregEsuata'] = 'Există deja un cont cu acest nume de utilizator.';
                header('Location: /inregistrare', true, 301);
                die();
            }
            else
            {
                $qResult = $db->query("INSERT INTO utilizator values (NULL, 'CLIENT', 'ACTIV', '$fieldID', '$fieldPassword', default)");
                if ($qResult)
                {
                    $userID = $db->query("SELECT cod_utilizator FROM utilizator WHERE id='$fieldID'");
                    if (!empty($userID))
                    {
                        $id = $userID[0]['cod_utilizator'];
                        $qResult = $db->query("INSERT INTO client values (NULL, $id, '$fieldLastName', '$fieldFirstName', '$fieldPhone', '$fieldEmail')");
                        if ($qResult)
                        {
                            $userID = $userID[0]['cod_utilizator'];
                            $db->query("INSERT INTO activitate values (NULL, $userID, 'CONT_CREAT', default)");

                            $this->title = 'Înregistrare reușită';
                            $this->head = 'Înregistrarea a fost efectuată cu succes! :)';
                            $this->body = 'De acum, poți profita la maxim de serviciile noastre.';

                            unset($_SESSION['tokenFormular']);  // dupa transmitere cu succes, sesiunea formularului se incheie

                            return;
                        }
                    }
                }
                $this->title = 'Înregistrare eșuată';
                $this->head = 'Înregistrare eșuată :(';
                $this->body = 'Ne pare rău, înregistrarea nu a putut fi efectuată cu succes. Încearcă mai târziu.';
            }
        }
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

    private function ValidLoginFields() : bool
    {
        $msg = '';

        // Campuri lipsa
        if (!isset($_POST['Utilizator']) || !isset($_POST['Parola']))
            $msg = 'Date incorecte';
        
        // Cel putin un camp contine prea multe caractere
        if (strlen($_POST['Utilizator']) > 16 || strlen($_POST['Parola']) > 25)
            $msg = 'Date incorecte';

        // Cel putin un camp contine prea putine caractere
        if (strlen($_POST['Utilizator']) < 3 || strlen($_POST['Parola']) < 3)
            $msg = 'Date incorecte';
        
        // Verificari specifice
        if (!ctype_alnum($_POST['Utilizator']))
            $msg = 'Utilizatorul trebuie sa conțină doar litere și cifre';
        else if (preg_match('/^[a-zA-Z0-9&#$~]+$/', $_POST['Parola']) !== 1)
            $msg = 'Parola conține caractere invalide. Se acceptă litere, cifre, caracterele &#$~';

        if (!empty($msg))
        {
            $_SESSION['AutEsuata'] = $msg;
            return false;
        }
        return true;
    }

    private function ValidRegistFields() : bool
    {
        $msg = '';

        // Campuri lipsa
        if (!isset($_POST['Utilizator']) || !isset($_POST['Parola']) || !isset($_POST['Nume']) || !isset($_POST['Prenume']) || 
            !isset($_POST['Email']) || !isset($_POST['Telefon']))
            $msg = 'Formular necompletat';

        // Cel putin un camp contine prea multe caractere
        if (strlen($_POST['Utilizator']) > 16)
            $msg = 'Utilizator prea lung (max. 16)';
        else if (strlen($_POST['Parola']) > 25)
            $msg = 'Parolă prea lungă (max. 25)';
        else if (strlen($_POST['Nume']) > 20)
            $msg = 'Nume prea lung (max. 20)';
        else if (strlen($_POST['Prenume']) > 40)
            $msg = 'Prenume prea lung (max. 40)';
        else if (strlen($_POST['Email']) > 320)
            $msg = 'Email prea lung (max. 320)';
        else if (strlen($_POST['Telefon']) != 10)
            $msg = 'Nr. de telefon trebuie să aibă 10 cifre';

        // Cel putin un camp contine prea putine caractere
        if (strlen($_POST['Utilizator']) < 3)
            $msg = 'Utilizator prea scurt (min. 3)';
        else if (strlen($_POST['Parola']) < 3)
            $msg = 'Parolă prea scurtă (min. 3)';
        else if (strlen($_POST['Nume']) < 2)
            $msg = 'Nume prea scurt (min. 2)';
        else if (strlen($_POST['Prenume']) < 2)
            $msg = 'Prenume prea scurt (min. 2)';

        // Verificari specifice
        if (!ctype_alnum($_POST['Utilizator']))
            $msg = 'Utilizatorul trebuie sa conțină doar litere și cifre';
        else if (preg_match('/^[a-zA-Z0-9&#$~]+$/', $_POST['Parola']) !== 1)
            $msg = 'Parola conține caractere invalide. Se acceptă litere, cifre, caracterele &#$~';
        else if (!ctype_alpha($_POST['Nume']))
            $msg = 'Numele trebuie sa conțină doar litere';
        else if (!ctype_alpha($_POST['Prenume']))
            $msg = 'Prenumele trebuie sa conțină doar litere';
        else if (preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/', $_POST['Email']) !== 1)
            $msg = 'Email invalid';
        else if (!ctype_digit($_POST['Telefon']))
            $msg = 'Nr. de telefon trebuie să conțină doar cifre';

        if (!empty($msg))
        {
            $_SESSION['InregEsuata'] = $msg;
            return false;
        }
        return true;
    }

    public function GetMsgTitle() : string { return $this->title; }

    public function GetMsgHead() : string { return $this->head; }

    public function GetMsgBody() : string { return $this->body; }
}

?>