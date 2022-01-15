<?php

require_once "models/ErrorCollector.php";
require_once "models/DatabaseOps.php";
require_once "models/reCAPTCHA.php";
require_once "models/Mailer.php";

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
            $db = new DatabaseOps($this->errorLogContext . '->Login');

            $id = $db->EscapeString($_POST['Utilizator']);
            $password = $db->EscapeString($_POST['Parola']);

            $userInfo = $db->query("SELECT cod_utilizator, tip, stare, parola, email FROM utilizator WHERE id='$id' AND stare='ACTIV'");
            if (empty($userInfo) || !password_verify($_POST['Parola'], $userInfo[0]['parola']))
            {
                $_SESSION['AutEsuata'] = 'Date incorecte';
                header('Location: /autentificare', true, 301);
                die();
            }
            else
            {
                $_SESSION['UtilizatorID'] = $_POST['Utilizator'];
                $_SESSION['UtilizatorTip'] = $userInfo[0]['tip'];
                $_SESSION['UtilizatorCod'] = $userInfo[0]['cod_utilizator'];
                $_SESSION['UtilizatorEmail'] = $userInfo[0]['email'];

                $actTime = date('Y-m-d H:i:s');
                $actTime = "STR_TO_DATE($actTime, '%Y%m%d %h%i%s')";
                $userID = $userInfo[0]['cod_utilizator'];
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
            $db = new DatabaseOps($this->errorLogContext . '->Logout');
            $actTime = date('Y-m-d H:i:s');
            $actTime = "STR_TO_DATE($actTime, '%Y%m%d %h%i%s')";
            $codUtiliz = $_SESSION['UtilizatorCod'];

            $db->query("INSERT INTO activitate VALUES (NULL, $codUtiliz, 'LOGOUT', default)");

            unset($_SESSION['UtilizatorID']);
            unset($_SESSION['UtilizatorTip']);
            unset($_SESSION['UtilizatorCod']);
            unset($_SESSION['UtilizatorEmail']);
        }

        header('Location: /', true, 301);
        die();
    }

    public function ProcessRegistration() : bool
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
            $db = new DatabaseOps($this->errorLogContext . '->Register');

            $fieldID = $db->EscapeString($_POST['Utilizator']);
            $fieldPassword = password_hash($_POST['Parola'], PASSWORD_DEFAULT);
            $fieldLastName = $db->EscapeString($_POST['Nume']);
            $fieldFirstName = $db->EscapeString($_POST['Prenume']);
            $fieldEmail = $db->EscapeString($_POST['Email']);
            $fieldPhone = $db->EscapeString($_POST['Telefon']);

            // Se verifica existenta unui alt cont (ce nu este sters) cu informatiile date
            $exist = $db->query("SELECT id, email FROM utilizator WHERE stare != 'STERS' AND (id='$fieldID' or email='$fieldEmail')");
            if (!empty($exist))
            {
                if ($exist[0]['id'] == $fieldID)
                    $_SESSION['InregEsuata'] = 'Există deja un cont cu acest nume de utilizator.';
                else
                    $_SESSION['InregEsuata'] = 'Există deja un cont cu acest email.';

                header('Location: /inregistrare', true, 301);
                die();
            }
            else
            {   
                $emailToken = $this->GetEmailToken();
                $qResult = $db->query("INSERT INTO utilizator values (NULL, 'CLIENT', 'INACTIV', '$fieldID', '$fieldPassword', '$fieldEmail', '$emailToken', default)");
                if ($qResult)
                {
                    $userCode = $db->query("SELECT cod_utilizator FROM utilizator WHERE stare='INACTIV' AND id='$fieldID' AND cod_verif_email='$emailToken'");
                    if (!empty($userCode))
                    {
                        $userCode = $userCode[0]['cod_utilizator'];
                        $qResult = $db->query("INSERT INTO client values (NULL, $userCode, '$fieldLastName', '$fieldFirstName', '$fieldPhone')");
                        if ($qResult)
                        {
                            $db->query("INSERT INTO activitate values (NULL, $userCode, 'CONT_CREAT', default)");

                            $this->SendVerifEmail($_POST['Email'], $_POST['Prenume'], $_POST['Nume'], $emailToken);

                            $this->title = 'Verificare Înregistrare';
                            $this->head = 'Înregistrarea este aproape gata.';
                            $this->body = 'Mai trebuie doar sa îți verifici adresa de e-mail printr-un link trimis de noi.';

                            unset($_SESSION['tokenFormular']);  // dupa transmitere cu succes, sesiunea formularului se incheie
                            return true;
                        }
                    }
                }
                $this->title = 'Înregistrare eșuată';
                $this->head = 'Înregistrare eșuată :(';
                $this->body = 'Ne pare rău, înregistrarea nu a putut fi efectuată cu succes. Încearcă mai târziu.';
            }
        }

        return false;
    }

    public function RegistrationConf() : void
    {
        if (empty($_GET['email']) || empty($_GET['token']))
            header('Location: /', true, 301);

        $db = new DatabaseOps($this->errorLogContext . '->ConfirmEmail');
        $fieldEmail = $db->EscapeString($_GET['email']);
        $fieldToken = $db->EscapeString($_GET['token']);
        
        $userInfo = $db->query("SELECT cod_utilizator, stare FROM utilizator WHERE email='$fieldEmail' and cod_verif_email='$fieldToken'");
        if (!empty($userInfo))
        {
            if ($userInfo[0]['stare'] != 'ACTIV')
            {
                $userCode = $userInfo[0]['cod_utilizator'];
                $isActive = $db->query("UPDATE utilizator SET stare='ACTIV' WHERE cod_utilizator = $userCode");
                if ($isActive)
                {
                    $db->query("INSERT INTO activitate values (NULL, $userCode, 'CONT_CONFIRMAT', default)");

                    $this->title = 'Înregistrare reușită';
                    $this->head = 'Înregistrarea a fost efectuată cu succes! :)';
                    $this->body = 'De acum poți profita la maxim de serviciile noastre.';
                    return;
                }
            }
        }

        $this->title = 'Confirmare eșuată';
        $this->head = 'Confirmare eșuată :(';
        $this->body = 'Ne pare rău, confirmarea nu a putut fi efectuată cu succes.';
    }

    public function ProcessDelete() : void
    {
        if (session_status() == PHP_SESSION_ACTIVE && 
            isset($_SESSION['UtilizatorTip']) && 
            strtoupper($_SESSION['UtilizatorTip']) != 'ADMIN')   // client autentificat
        {
            $db = new DatabaseOps($this->errorLogContext . '->Delete');
            $actTime = date('Y-m-d H:i:s');
            $actTime = "STR_TO_DATE($actTime, '%Y%m%d %h%i%s')";
            $codUtiliz = $_SESSION['UtilizatorCod'];

            $db->query("INSERT INTO activitate VALUES (NULL, $codUtiliz, 'CONT_STERS', default)");
            $db->query("UPDATE utilizator SET stare = 'STERS' WHERE cod_utilizator = $codUtiliz");

            unset($_SESSION['UtilizatorID']);
            unset($_SESSION['UtilizatorTip']);
            unset($_SESSION['UtilizatorCod']);
        }

        header('Location: /', true, 301);
        die();   
    }

    private function SendVerifEmail(string $to, string $firstName, string $lastName, string $emailToken) : void
    {   
        $recipientName = $firstName . ' ' . $lastName;
        $subject = 'Confirmare Inregistrare';

        $body = file_get_contents('views/Email.html');
        $bodyTitle = 'Confirmare Inregistrare';
        $bodyContent = '<p style="font-family: sans-serif; font-size: larger;">
                            Salut ' . $firstName . ', <br>
                            Înregistrarea ta pe site-ul nostru este aproape gata. <br>
                            Te rugăm să accesezi link-ul următor pentru ca înregistrarea ta să fie validă.
                        </p>
                        '. "https://daw-casaryiad.000webhostapp.com/inregistrare/confirmare?email=$to&token=$emailToken" .'
                        <p style="font-family: sans-serif; font-size: larger;">
                            Îți mulțumim!
                        </p>';
        $body = str_replace('{TITLE}', $bodyTitle, $body);
        $body = str_replace('{CONTENT}', $bodyContent, $body);
        
        $altBody = "Confirmare Inregistrare\n\n" .  
                    "Salut " . $firstName . ", \n
                    Înregistrarea ta pe site-ul nostru este aproape gata.\n
                    Te rugăm să accesezi link-ul următor pentru ca înregistrarea ta să fie validă.\n\n 
                    https://daw-casaryiad.000webhostapp.com/inregistrare/confirmare?email=$to&token=$emailToken"
                    . "\n\nÎți mulțumim!\n"; 

        $mailerModel = new Mailer($this->errorLogContext . '->SendVerifEmail');
        $mailerModel->Mail($to, $recipientName, $subject, $body, $altBody);
    } 

    private function GetEmailToken() : string
    {
        return bin2hex(random_bytes(32));
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