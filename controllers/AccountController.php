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
            // aici, ar fi mai ok o pagina de eroare 
            header('Location: /autentificare', true, 301);
            die();
        }
        else if (isset($_SESSION['UtilizatorID']))  // sesiune activa si utilizator autentificat
        {
            header('Location: /', true, 301);
            die();
        }
        else if (!isset($_POST['Utilizator']) || !isset($_POST['Parola']))  // sesiune activa si utilizator neidentificat, dar fara formular
        {
            header('Location: /autentificare', true, 301);
            die();
        }
        else  // sesiune activa, utilizator neidentificat si formular trimis
        {
            $db = new DatabaseOps();

            $id = $db->EscapeString($_POST['Utilizator']);
            $password = $db->EscapeString($_POST['Parola']);

            $exist = $db->query("SELECT cod_utilizator, tip FROM utilizator WHERE id='$id' AND parola='$password'");
            if (empty($exist))
            {
                $FailedMsg = "Datele introduse sunt incorecte.";
                require_once "views/LoginFailed.php";
                unset($db);
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
            // aici, ar fi mai ok o pagina de eroare 
            header('Location: /inregistrare', true, 301);
            die();
        }
        else if (isset($_SESSION['UtilizatorID']))  // sesiune activa si utilizator autentificat
        {
            header('Location: /', true, 301);
            die();
        }
        else if (!isset($_POST['Utilizator']) || !isset($_POST['Parola']) || !isset($_POST['Nume']) || !isset($_POST['Prenume'])
                 || !isset($_POST['Email']) || !isset($_POST['Telefon']))
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
                $FailedMsg = "Exista deja un cont cu acest nume de utilizator";
                require_once "views/RegistrationFailed.php";
                unset($db);
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

                            require_once "views/RegistrationOK.php";
                            return;
                        }
                    }
                }
                $FailedMsg = 'Ne pare rău, înregistrarea nu a reușit. Încearcă mai târziu.';
                require_once "views/RegistrationFailed.php";
            }
        }
    }
}

?>