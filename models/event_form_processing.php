<?php

$file = fopen("../files/forms.txt", 'a');

if (!empty($_POST))
{
    fwrite($file, "\n\n-----------------------------------------\n");

    date_default_timezone_set("Europe/Bucharest");
    fwrite($file, "\nTrimis la: " . date("Y-m-d h:i:sa"). "\n");
    
    fwrite($file, "\n Nume: ". $_POST['Nume']);
    fwrite($file, "\n Prenume: ". $_POST['Prenume']);
    fwrite($file, "\n E-mail: ". $_POST['Email']);
    fwrite($file, "\n Telefon: ". $_POST['telefon']);
    fwrite($file, "\n Tip eveniment: ". $_POST['tip_event']);
    fwrite($file, "\n Data evenimentului: ". $_POST['data']);
    fwrite($file, "\n Numar invitati: ". $_POST['nr_inv']);
    fwrite($file, "\n Observatii: ". $_POST['obs'] . "\n");
    fwrite($file, "\n-----------------------------------------\n");

    echo "<script> alert('Formularul a fost trimis'); </script>";
}

fclose($file);

?>