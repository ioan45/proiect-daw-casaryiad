<?php

require_once "controllers/Controller.php";
require_once "models/ContactProgramDetails.php";
require_once "models/DatabaseOps.php";

class EventReservController extends Controller
{
    public function index() : void  // pagina formularului
    {
        // Incarca datele de contact/program afisate la finalul paginii
        extract(ContactProgramDetails::Get());
        
        require_once "views/EventForm.php";
    }

    public function FormSent() : void
    {
        // Daca exista cel mult campul specific butonului Submit
        if (count($_POST) <= 1)
        {
            $FailedMsg = "Formularul nu a fost completat.";
            require_once "views/EventFormFailed.php";
        }
        else if (session_status() != PHP_SESSION_ACTIVE || !isset($_SESSION['UtilizatorID']))  // daca nu exista sesiune/utilizator autentificat
        {
            $FailedMsg = "Trebuie să vă autentificați înainte de a trimite acest formular.";
            require_once "views/EventFormFailed.php";
        }
        else
        {
            $db = new DatabaseOps();
            
            $fieldEventType = $db->EscapeString($_POST['Tip_event']);
            $fieldDate = $db->EscapeString($_POST['Data']);
            $fieldNrGuests = $db->EscapeString($_POST['Nr_inv']);
            $fieldObs = $db->EscapeString($_POST['Obs']);

            $fieldDate = "STR_TO_DATE('$fieldDate', '%Y-%m-%d')";
            $dayTaken = $db->query("SELECT 1 FROM rezervare WHERE data_eveniment=$fieldDate AND stare = 'CONFIRMATA'");
            if (!empty($dayTaken))  // se verifica daca exista deja un eveniment confirmat pentru data specificata
            {
                $FailedMsg = "Ne pare rău, data specificată este ocupată de un alt eveniment.";
                require_once "views/EventFormFailed.php";
            }
            else
            {   
                $client = $db->query("SELECT cod_client FROM client, utilizator WHERE client.cod_utilizator=utilizator.cod_utilizator");
                if (!empty($client))
                {
                    $clientID = $client[0]['cod_client'];
                    $query = "INSERT INTO rezervare values (NULL, $clientID, 'ASTEPTARE', default, '$fieldEventType', $fieldDate, $fieldNrGuests, '$fieldObs')";
                    if ($db->query($query))
                        require_once "views/EventFormOK.php";
                    else
                    {
                        $FailedMsg = "Ne pare rău, a intervenit o problemă in transmiterea formularului. Incercați mai târziu.";
                        require_once "views/EventFormFailed.php";
                    }
                }
                else
                {
                    $FailedMsg = "Ne pare rău, a intervenit o problemă in transmiterea formularului. Incercați mai târziu.";
                    require_once "views/EventFormFailed.php";
                }
            }
            
            unset($db);
        }
    }
}

?>