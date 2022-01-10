<?php

require_once "controllers/Controller.php";
require_once "models/ContactProgramDetails.php";
require_once "models/AccountProcessing.php";
require_once "models/Mailer.php";

class AccountController extends Controller
{
    public function index() : void { }

    public function LoginPage() : void
    {
        $this->PageAccessDbLog('PagAutentificare', 'AccountCtrl');

        $AccProcModel = new AccountProcessing('AccountCtrl');
        $formToken = $AccProcModel->NewFormToken();

        $siteKey = reCAPTCHA::GetSiteKey();

        // (Nu) Se afiseaza un chenar rosu in jurul reCAPTCHA
        $NoCAPTCHA = '';
        if (isset($_SESSION['lipsaCAPTCHA']))
        {
            $NoCAPTCHA = 'lipsaCAPTCHA p-2';
            unset($_SESSION['lipsaCAPTCHA']);
        }

        // Se afiseaza un mesaj in caz ca autentificarea a esuat
        $message = '';
        if (isset($_SESSION['AutEsuata']))
        {
            $message = $_SESSION['AutEsuata'];
            $message = "<div class=\"col my-4 mesaj-eroare\">$message</div>";
            unset($_SESSION['AutEsuata']);
        }

        extract(ContactProgramDetails::Get());
        require_once "views/Login.php";
    }

    public function RegistrationPage() : void
    {
        $this->PageAccessDbLog('PagInregistrare', 'AccountCtrl');

        $AccProcModel = new AccountProcessing('AccountCtrl');
        $formToken = $AccProcModel->NewFormToken();

        $siteKey = reCAPTCHA::GetSiteKey();

        // (Nu) Se afiseaza un chenar rosu in jurul reCAPTCHA
        $NoCAPTCHA = '';
        if (isset($_SESSION['lipsaCAPTCHA']))
        {
            $NoCAPTCHA = 'lipsaCAPTCHA p-2';
            unset($_SESSION['lipsaCAPTCHA']);
        }

        // Se afiseaza un mesaj in caz ca autentificarea a esuat
        $message = '';
        if (isset($_SESSION['InregEsuata']))
        {
            $message = $_SESSION['InregEsuata'];
            $message = "<div class=\"col my-4 mesaj-eroare\">$message</div>";
            unset($_SESSION['InregEsuata']);
        }

        extract(ContactProgramDetails::Get());
        require_once "views/Registration.php";
    }

    public function LoginProcessing() : void
    {
        $this->PageAccessDbLog('ProcesAutentif', 'AccountCtrl');

        $AccProcModel = new AccountProcessing('AccountCtrl');
        $AccProcModel->ProcessLogin();

        $title = $AccProcModel->GetMsgTitle();
        $head = $AccProcModel->GetMsgHead();
        $body = $AccProcModel->GetMsgBody();
        $this->MessagePage($title, $head, $body);
    }

    public function LogoutProcessing() : void
    {
        $this->PageAccessDbLog('ProcesDeconect', 'AccountCtrl');

        $AccProcModel = new AccountProcessing('AccountCtrl');
        $AccProcModel->ProcessLogout();
    }

    public function RegistrationProcessing() : void
    {
        $this->PageAccessDbLog('ProcesInreg', 'AccountCtrl');

        $AccProcModel = new AccountProcessing('AccountCtrl');
        $succesful = $AccProcModel->ProcessRegistration();
        if ($succesful)
        {
            // Trimitere email de inregistrare realizata cu succes
            
            $to = $_POST['Email'];
            $recipientName = $_POST['Prenume'] . ' ' . $_POST['Nume'];
            $subject = 'Inregistrare reusita';

            $body = file_get_contents('views/Email.html');
            $bodyTitle = 'Înregistrare reușită!';
            $bodyContent = '<p style="font-family: sans-serif; font-size: larger;">
                                Salut ' . $_POST['Prenume'] . ', <br>
                                Înregistrarea ta pe site-ul nostru a fost efectuată cu succes! <br>
                                De acum, poți profita la maxim de serviciile noastre.
                            </p>
                            <p style="font-family: sans-serif; font-size: larger;">
                                Îți mulțumim!
                            </p>';
            $body = str_replace('{TITLE}', $bodyTitle, $body);
            $body = str_replace('{CONTENT}', $bodyContent, $body);
            
            $altBody = "Înregistrare reușită\n\n" .  
                        "Salut " . $_POST['Prenume'] . ", \n
                         Înregistrarea ta pe site-ul nostru a fost efectuată cu succes! 
                         De acum, poți profita la maxim de serviciile noastre. \n\n 
                         https://daw-casaryiad.000webhostapp.com/"; 

            $mailerModel = new Mailer('AccountCtrl');
            $mailerModel->Mail($to, $recipientName, $subject, $body, $altBody);
        }

        $title = $AccProcModel->GetMsgTitle();
        $head = $AccProcModel->GetMsgHead();
        $body = $AccProcModel->GetMsgBody();
        $this->MessagePage($title, $head, $body);
    }

    public function DeleteProcessing() : void
    {
        $this->PageAccessDbLog('ProcesStergCont', 'AccountCtrl');

        $AccProcModel = new AccountProcessing('AccountCtrl');
        $AccProcModel->ProcessDelete();
    }
}

?>