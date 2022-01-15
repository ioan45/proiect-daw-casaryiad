<?php

require_once "controllers/Controller.php";
require_once "models/ContactProgramDetails.php";
require_once "models/ReservFormProcessing.php";
require_once "models/DatabaseOps.php";
require_once "models/Mailer.php";

class EventReservController extends Controller
{
    public function index() : void  // pagina formularului
    {
        $this->PageAccessDbLog('PagFormEven', 'EventRsvCtrl');


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
        $this->PageAccessDbLog('ProcesFormEven', 'EventRsvCtrl');

        $formProcModel = new ReservFormProcessing('EventRsvCtrl');
        $succesful = $formProcModel->ProcessForm();
        if ($succesful)
        {
            // Trimitere email de instiintare cum ca formularul a fost trimis

            $db = new DatabaseOps('EventRsvCtrl');
            $userCode = $_SESSION['UtilizatorCod'];
            $clientInfo = $db->query("SELECT nume, prenume FROM client WHERE cod_utilizator = $userCode")[0];

            if (!empty($clientInfo))
            {
                $to = $_SESSION['UtilizatorEmail'];
                $recipientName = $clientInfo['prenume'] . ' ' . $clientInfo['nume'];
                $subject = 'Formular eveniment';

                $eventType = '';
                switch ($_POST['Tip_event'])
                {
                    case 'Privat':
                        $eventType = 'Petrecere Privată';
                        break;
                    case 'Corporate':
                        $eventType = 'Eveniment Corporate';
                        break;
                    case 'Nunta':
                        $eventType = 'Nuntă';
                        break;
                    case 'Botez':
                        $eventType = 'Botez';
                        break;          
                }

                $body = file_get_contents('views/Email.html');
                $bodyTitle = 'Formularul a fost trimis!';
                $bodyContent = '<p style="font-family: Tahoma; font-size: larger;">
                                    Salut ' . $clientInfo['prenume'] . ', <br>
                                    Formularul pentru organizarea unui eveniment a fost trimis cu succes! <br>
                                </p>
                                <p style="font-family: Tahoma; font-size: larger;">
                                    <b>Iată ce s-a primit: </b><br>
                                </p>
                                <p style="font-family: Tahoma; font-size: larger;">
                                    Tip eveniment: &nbsp;' . $eventType . '<br>
                                    Data: &nbsp;' . $_POST['Data'] . '<br>
                                    Salon: &nbsp;' . $_POST['Salon'] . '<br>
                                    Meniu: &nbsp;' . $_POST['Meniu'] . '<br>
                                    Nr. maxim de invitați: &nbsp;' . $_POST['Nr_inv'] . '<br>
                                    Observații: <br>' . $_POST['Obs'] . '<br>
                                </p>
                                <p style="font-family: Tahoma; font-size: larger;">
                                    În următoarea perioadă te vom contacta prin telefon/email ' . 
                                    'pentru a-ți transmite un răspuns cu privire la rezervarea dorită.
                                </p>
                                <p style="font-family: Tahoma; font-size: larger;">
                                    Îți mulțumim!
                                </p>';
                $body = str_replace('{TITLE}', $bodyTitle, $body);
                $body = str_replace('{CONTENT}', $bodyContent, $body);
                
                $altBody =  "Formularul a fost trimis!\n\n" . 
                            "Salut " . $clientInfo['prenume'] . ", \n" .
                            "Formularul pentru organizarea unui eveniment a fost trimis cu succes!" .
                            "\n\nIată ce s-a primit: \n" .
                            "Tip eveniment: " . $eventType . "\n" .
                            "Data: " . $_POST['Data'] . "\n" .
                            "Salon: " . $_POST['Salon'] . "\n" .
                            "Meniu: " . $_POST['Meniu'] . "\n" .
                            "Nr. maxim de invitați: " . $_POST['Nr_inv'] . "\n" .
                            "Observații: " . $_POST['Obs'] . 
                            "\n\nÎn următoarea perioadă te vom contacta prin telefon/email " . 
                            "pentru a-ți transmite un răspuns cu privire la rezervarea dorită." . 
                            "\n\nÎți mulțumim!"; 

                $mailerModel = new Mailer('AccountCtrl');
                $mailerModel->Mail($to, $recipientName, $subject, $body, $altBody);
            }
        }

        $title = $formProcModel->GetMsgTitle();
        $head = $formProcModel->GetMsgHead();
        $body = $formProcModel->GetMsgBody();
        $this->MessagePage($title, $head, $body);
    }
}

?>