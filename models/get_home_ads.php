<?php

$file = fopen('../files/home_ads.txt', 'r');
$titles = array();
$contents = array();

$line = "";
while (!feof($file))
{
    while (!feof($file) && strcmp(trim($line), "<TITLE>") != 0)
        $line = fgets($file);
    $titles[] = trim(fgets($file));
    
    while (!feof($file) && strcmp(trim($line), "<CONTENT>") != 0)
        $line = fgets($file);
    
    while (!feof($file) && strcmp(trim($line), "<END>") != 0)
    {
        while(!feof($file) && strcmp(substr(trim($line), 0, 3), "<P>") != 0)
            $line = fgets($file);
        $contents[] = substr(trim($line), 3);
        $line = fgets($file);
    }
}

fclose($file);

?>