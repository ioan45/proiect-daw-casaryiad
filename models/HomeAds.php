<?php

class HomeAds
{
    private array $adsTitles = array();  // [titlu]
    private array $adsParagraphs = array();  // [index_titlu => [paragraf_continut]]
    
    public function ParseAdsFile(string $filePath) : void
    {
        $file = fopen($filePath, 'r');

        $line = "";
        while (!feof($file))  // cat timp exista anunturi
        {
            $title = "";
            $paragraphs = array();

            // Titlu
            while (!feof($file) && $line != "<TITLU>")  // cat timp nu am ajuns la un anunt (intai apare titlul)
                $line = trim(fgets($file));
            while (!feof($file) && $line != "</TITLU>")
            {
                $line = trim(fgets($file));
                if ($line != "</TITLE>")
                    $title = $title . $line . " ";
            }

            // Continut
            while (!feof($file) && $line != "<CONTINUT>")
                $line = trim(fgets($file));
            while (!feof($file))
            {
                while(!feof($file) && $line != "<P>" && $line != '</CONTINUT>')
                    $line = trim(fgets($file));
                if ($line == '</CONTINUT>')
                    break;
    
                // Paragraf din continut
                $p = "";
                while(!feof($file) && $line != "</P>")
                {
                    $line = trim(fgets($file));
                    if ($line != "</P>")
                        $p = $p . $line . " ";
                }
            
                if ($p != "")
                    $paragraphs[] = $p;
            }
            
            if ($title != "" && !empty($paragraphs))
            {
                $this->adsTitles[] = $title;
                $this->adsParagraphs[] = $paragraphs;
            }
        }

        fclose($file);
    }

    public function GetTitles() : array
    {
        return $this->adsTitles;
    }

    public function GetParagraphs() : array
    {
        return $this->adsParagraphs;
    }
}

?>