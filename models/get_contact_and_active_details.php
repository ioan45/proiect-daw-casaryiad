<?php

$file = fopen("../files/contact_and_active_details.txt", 'r');

$Adresa = array();
$Telefon = "";
$Fax = "";
$Email = "";

$Zile = array();
$Ore = array();

$line = "";
while (!feof($file) && strcmp(trim($line), "<CONTACT>") != 0)
    $line = fgets($file);

while (!feof($file) && strcmp(trim($line), "<PROGRAM>") != 0)
{
    if (strcmp(trim($line), "<Adresa>") == 0)
    {
        $line = trim(fgets($file));
        $Adresa[] = $line;
        while (!feof($file) && strcmp(substr($line, -1), '`') == 0)
        {
            $line = trim(fgets($file));
            $Adresa[] = $line;
        }
    }
    if (strcmp(trim($line), "<Telefon>") == 0)
        $Telefon = trim(fgets($file));
    if (strcmp(trim($line), "<Fax>") == 0)
        $Fax = trim(fgets($file));
    if (strcmp(trim($line), "<E-mail>") == 0)
        $Email = trim(fgets($file));
    
    $line = fgets($file);
}

while (!feof($file))
{
    $line = trim(fgets($file));
    if (strcmp(substr($line, 0, 3), '<Z>') == 0)
        $Zile[] = substr($line, 3);
    else if (strcmp(substr($line, 0, 3), '<O>') == 0)
        $Ore[] = substr($line, 3);
}

fclose($file);

?>