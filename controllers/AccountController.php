<?php

require_once "controllers/Controller.php";
require_once "models/ContactProgramDetails.php";
require_once "models/DatabaseOps.php";

class AccountController extends Controller
{
    public function index() : void { }

    public function LoginPage() : void
    {
        extract(ContactProgramDetails::Get());
        require_once "views/Login.php";
    }

    public function RegistrationPage() : void
    {
        extract(ContactProgramDetails::Get());
        require_once "views/Registration.php";
    }

    public function LoginProcessing() : void
    {
        if (session_status() != PHP_SESSION_ACTIVE)  // sesiune inactiva/dezactivata
        {
            $title = 'Autentificare eșuată';
            $body = 'Ne pare rău, autentificarea nu a putut fi efectuată cu succes. Încearcă mai târziu.';
            $this->MessagePage($title, $title, $body);
        }
        else if (isset($_SESSION['UtilizatorID']))  // sesiune activa si utilizator autentificat
        {
            header('Location: /', true, 301);
            die();
        }
        else if (!isset($_POST['Utilizator']) || !isset($_POST['Parola']))  // sesiune activa si utilizator neautentificat, dar fara formular
        {
            header('Location: /autentificare', true, 301);
            die();
        }
        else  // sesiune activa, utilizator neautentificat si formular trimis
        {
            $db = new DatabaseOps();

            $id = $db->EscapeString($_POST['Utilizator']);
            $password = $db->EscapeString($_POST['Parola']);

            $exist = $db->query("SELECT cod_utilizator, tip FROM utilizator WHERE id='$id' AND parola='$password'");
            if (empty($exist))
            {
                unset($db);

                $title = 'Autentificare eșuată';
                $body = 'Datele introduse sunt incorecte.';
                $this->MessagePage($title, $title, $body);
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

                header('Location: /', true, 301);
                die();
            }
        }
    }

    public function LogoutProcessing() : void
    {
        if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION['UtilizatorID']))  // sesiune activa si utilizator autentificat
        {
            $db = new DatabaseOps();
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

    public function RegistrationProcessing() : void
    {
        if (session_status() != PHP_SESSION_ACTIVE)  // sesiune inactiva/dezactivata
        {
            $title = 'Înregistrare eșuată';
            $body = 'Ne pare rău, înregistrarea nu a putut fi efectuată cu succes. Încearcă mai târziu.';
            $this->MessagePage($title, $title, $body);
        }
        else if (isset($_SESSION['UtilizatorID']))  // sesiune activa si utilizator autentificat
        {
            header('Location: /', true, 301);
            die();
        }
        else if (!isset($_POST['Utilizator']) || !isset($_POST['Parola']) || !isset($_POST['Nume']) || !isset($_POST['Prenume'])
                 || !isset($_POST['Email']) || !isset($_POST['Telefon']))  // Formular incorect
        {
            header('Location: /inregistrare', true, 301);
            die();   
        }
        else
        {
            $db = new DatabaseOps();

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

                $title = 'Înregistrare eșuată';
                $body = 'Exista deja un cont cu acest nume de utilizator.';
                $this->MessagePage($title, $title, $body);
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

                            $title = 'Înregistrare reușită';
                            $head = 'Înregistrarea a fost efectuată cu succes! :)';
                            $body = 'De acum, poți profita la maxim de serviciile noastre.';
                            $this->MessagePage($title, $head, $body);

                            return;
                        }
                    }
                }
                $title = 'Înregistrare eșuată';
                $body = 'Ne pare rău, înregistrarea nu a putut fi efectuată cu succes. Încearcă mai târziu.';
                $this->MessagePage($title, $title, $body);
            }
        }
    }
}

?>