<?php

require_once "controllers/Controller.php";
require_once "models/ContactProgramDetails.php";
require_once "models/DatabaseOps.php";

class EventReservController extends Controller
{
    public function index() : void  // pagina formularului
    {
        // Se creeaza o 'sesiune' a formularului (se seteaza $_SESSION['FormularSID'] cu $formSID) 
        // ce îi va corespunde formularul din pagina incarcata (formularul are un camp invizibil in care retine $formSID)
        $formSID = random_int(PHP_INT_MIN, PHP_INT_MAX);
        if (session_status() == PHP_SESSION_ACTIVE)
            $_SESSION['FormularSID'] = $formSID;

        // Incarca datele de contact/program afisate la finalul paginii
        extract(ContactProgramDetails::Get());

        require_once "views/EventForm.php";
    }

    public function FormSent() : void
    {
        if (empty($_POST['Tip_event']) || empty($_POST['Data']))  // Lipsesc campuri obligatorii din formular
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
            // Verificare retrimitere formular
            //  -- Daca sesiunea formularului este creata (exista $_SESSION['FormularSID'])
            //     si daca formularul transmis corespunde acesteia atunci se trece la procesarea formularului
            if (!isset($_POST['FormularSID']) || !isset($_SESSION['FormularSID']) || $_POST['FormularSID'] != $_SESSION['FormularSID'])
            {
                $title = 'Formular respins';
                $head = 'Trimitere eșuată';
                $body = 'Formularul dvs. a fost respins. <br>' .
                        '(O posibilă cauză ar putea fi încercarea de a retrimite un formular ce a fost deja transmis)';
                $this->MessagePage($title, $head, $body);
                return;
            }

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
                    $fieldNrGuests = empty($fieldNrGuests) ? 'null' : $fieldNrGuests;
                    $fieldObs = empty($fieldObs) ? 'null' : ("'" . $fieldObs . "'");  // ghilimele adaugate aici deoarece campul poate fi nul 
                                                                                      // (se evita inserarea sirului 'null' in loc de valoarea null)
                    $query = "INSERT INTO rezervare values (NULL, $clientCode, 'ASTEPTARE', default, '$fieldEventType', $fieldDate, $fieldNrGuests, $fieldObs)";
                    if ($db->query($query))
                    {
                        unset($db);
                        unset($_SESSION['FormularSID']);  // dupa transmitere cu succes, sesiunea formularului se incheie

                        $title = 'Formular Trimis!';
                        $head = 'Trimitere reușită :)';
                        $body = 'Formularul a fost trimis. În următoarea perioada vă vom contacta prin telefon/email ' . 
                                'pentru a vă transmite un răspuns cu privire la rezervarea dorită.';
                        $this->MessagePage($title, $head, $body);

                        return;
                    }
                }
                $title = 'Formular Netrimis!';
                $head = 'Trimitere eșuată :(';
                $body = 'Ne pare rău, a intervenit o problemă in transmiterea formularului. Incercați mai târziu.';
                $this->MessagePage($title, $head, $body);
            }
            unset($db);
        }
    }
}

?>