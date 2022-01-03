<?php

require_once "controllers/Controller.php";
require_once "models/ContactProgramDetails.php";
require_once "models/ReservFormProcessing.php";
require_once "models/DatabaseOps.php";

class EventReservController extends Controller
{
    public function index() : void  // pagina formularului
    {
        $formProcModel = new ReservFormProcessing('EventRsvCtrl');
        $formToken = $formProcModel->NewFormToken();

        $siteKey = reCAPTCHA::GetSiteKey();

        // (Nu) Se afiseaza un chenar rosu in jurul reCAPTCHA
        $NoCAPTCHA = '';
        if (isset($_SESSION['lipsaCAPTCHA']))
        {
            $NoCAPTCHA = 'lipsaCAPTCHA p-2';
            unset($_SESSION['lipsaCAPTCHA']);
        }

        // Se afiseaza un mesaj in caz ca trimiterea formularului a esuat
        $message = '';
        if (isset($_SESSION['RzvEsuata']))
        {
            $message = $_SESSION['RzvEsuata'];
            $message = "<div class=\"col my-4 mesaj-eroare\">$message</div>";
            unset($_SESSION['RzvEsuata']);
        }

        $db = new DatabaseOps('EventRsvCtrl');
        $halls = $db->query("SELECT denumire, capacitate FROM salon");
        $menus = $db->query("SELECT denumire FROM meniu");
        $maxCapacity = 0;
        foreach ($halls as $idx => $hall)
            if ($maxCapacity < $hall['capacitate'])
                $maxCapacity = $hall['capacitate'];
        unset($db);

        // Incarca datele de contact/program afisate la finalul paginii
        extract(ContactProgramDetails::Get());

        require_once "views/EventForm.php";
    }

    public function FormSent() : void
    {
        $formProcModel = new ReservFormProcessing('EventRsvCtrl');
        $formProcModel->ProcessForm();

        $title = $formProcModel->GetMsgTitle();
        $head = $formProcModel->GetMsgHead();
        $body = $formProcModel->GetMsgBody();
        $this->MessagePage($title, $head, $body);
    }
}

?>