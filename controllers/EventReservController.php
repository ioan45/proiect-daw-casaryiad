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
            $title = 'Formular Netrimis!';
            $head = 'Trimitere eșuată :(';
            $body = 'Formularul nu a fost completat.';
            $this->MessagePage($title, $head, $body);
        }
        else if (session_status() != PHP_SESSION_ACTIVE || !isset($_SESSION['UtilizatorID']))  // daca nu exista sesiune/utilizator autentificat
        {
            $title = 'Formular Netrimis!';
            $head = 'Trimitere eșuată :(';
            $body = 'Trebuie să vă autentificați înainte de a trimite acest formular.';
            $this->MessagePage($title, $head, $body);
        }
        else  // sesiune activa + utilizator autentificat
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
                $title = 'Formular Netrimis!';
                $head = 'Trimitere eșuată :(';
                $body = 'Ne pare rău, data specificată este ocupată de un alt eveniment.';
                $this->MessagePage($title, $head, $body);
            }
            else
            {   
                $userCode = $_SESSION['UtilizatorCod'];
                $queryResult = $db->query("SELECT cod_client FROM client WHERE cod_utilizator=$userCode");
                if (!empty($queryResult))
                {
                    $clientCode = $queryResult[0]['cod_client'];
                    $query = "INSERT INTO rezervare values (NULL, $clientCode, 'ASTEPTARE', default, '$fieldEventType', $fieldDate, $fieldNrGuests, '$fieldObs')";
                    if ($db->query($query))
                    {
                        unset($db);

                        $title = 'Formular Trimis!';
                        $head = 'Trimitere reușită :)';
                        $body = 'Formularul a fost trimis. În următoarea perioada vă vom contacta prin telefon/email ' . 
                                'pentru a vă transmite un răspuns cu privire la rezervarea dorită.';
                        $this->MessagePage($title, $head, $body);

                        return;
                    }
                }
            }
            unset($db);

            $title = 'Formular Netrimis!';
            $head = 'Trimitere eșuată :(';
            $body = 'Ne pare rău, a intervenit o problemă in transmiterea formularului. Incercați mai târziu.';
            $this->MessagePage($title, $head, $body);
        }
    }
}

?>