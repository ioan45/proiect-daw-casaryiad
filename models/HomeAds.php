<?php

require_once "models/ErrorCollector.php";

class HomeAds
{
    private array $adsTitles = array();  // [titlu]
    private array $adsContents = array();  // [continut]

    private string $filePath = 'files/HomeAds.txt';
    private bool $fileLoaded = false;
    private bool $fileUpToDate = true;

    private ErrorCollector $errCollector;
    private string $errorLogContext;

    public function __construct(string $errorLogContext)
    {
        $this->errorLogContext = $errorLogContext . '->' .'AdsModel';
        $this->errCollector = new ErrorCollector($this->errorLogContext);
    }

    public function LoadAdsFile() : void
    {
        if ($this->fileLoaded)
            return;

        $file = fopen($this->filePath, 'r');
        if ($file === false)
        {
            $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Citire: Fisierul cu anunturi nu a putut fi deschis');
            return;
        }

        $line = "";
        while (!feof($file))  // cat timp exista anunturi
        {
            $title = "";
            $content = "";

            // Titlu
            while (!feof($file) && $line != "<TITLU>")  // cat timp nu am ajuns la un anunt (intai apare titlul)
                $line = trim(fgets($file));
            while (!feof($file))
            {
                $line = fgets($file);  // nu se face trim() aici, se pastreaza formatul textului
                if (trim($line) == "</TITLU>")
                    break;
                $title .= htmlspecialchars($line);
            }

            // Continut
            while (!feof($file) && $line != "<CONTINUT>")
                $line = trim(fgets($file));
            while (!feof($file))
            {
                $line = fgets($file);  // nu se face trim() aici, se pastreaza formatul textului
                if (trim($line) == '</CONTINUT>')
                    break;
                $line = htmlspecialchars($line);
                $line = str_replace('&lt;p&gt;', '<p>', $line);
                $line = str_replace('&lt;/p&gt;', '</p>', $line);
                $content .= $line;
            }
            
            if (!empty($title) && !empty($content))
            {
                $this->adsTitles[] = $title;
                $this->adsContents[] = $content;
            }
        }
        $this->fileLoaded = true;
        $this->fileUpToDate = true;
        
        fclose($file);
        if ($file === false)
            $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Citire: Fisierul cu anunturi nu a putut fi inchis');
    }

    public function NewAd(string $title, string $content) : bool
    {
        if (!$this->fileLoaded)
            $this->LoadAdsFile();

        $title = str_replace(array('<TITLU>', '</TITLU>', '<CONTINUT>', '</CONTINUT>'), '', $title);
        $content = str_replace(array('<TITLU>', '</TITLU>', '<CONTINUT>', '</CONTINUT>'), '', $content);

        $this->adsTitles[] = $title;
        $this->adsContents[] = $content;
        $this->fileUpToDate = false;
        return true;
    }

    public function ModifyAd(int $id, string $newTitle, string $newContent) : bool
    {
        /// $id - pozitia anuntului in fisierul text

        if (!$this->fileLoaded)
            $this->LoadAdsFile();

        if (!isset($this->adsTitles[$id]) || !isset($this->adsContents[$id]))
            return false;

        $newTitle = str_replace(array('<TITLU>', '</TITLU>', '<CONTINUT>', '</CONTINUT>'), '', $newTitle);
        $newContent = str_replace(array('<TITLU>', '</TITLU>', '<CONTINUT>', '</CONTINUT>'), '', $newContent);

        $this->adsTitles[$id] = $newTitle;
        $this->adsContents[$id] = $newContent;
        $this->fileUpToDate = false;
        return true;
    }

    public function DeleteAd(int $id) : bool
    {
        /// $id - pozitia anuntului in fisierul text

        if (!$this->fileLoaded)
            $this->LoadAdsFile();

        if (!isset($this->adsTitles[$id]) || !isset($this->adsContents[$id]))
            return false;
        
        unset($this->adsTitles[$id]);
        $this->adsTitles = array_values($this->adsTitles);
        unset($this->adsContents[$id]);
        $this->adsContents = array_values($this->adsContents);
        $this->fileUpToDate = false;
        return true;
    }

    public function GetTitles() : array
    {
        return $this->adsTitles;
    }

    public function GetContents() : array
    {
        return $this->adsContents;
    }

    public function WriteCurrentAdsInFile() : void
    {
        if ($this->fileUpToDate)
            return;

        $file = fopen($this->filePath, 'w');
        if ($file === false)
        {
            $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Scriere: Fisierul cu anunturi nu a putut fi deschis');
            return;
        }

        for ($i = 0; $i < count($this->adsTitles); ++$i)
        {
            $title = htmlspecialchars_decode($this->adsTitles[$i]);
            $content = htmlspecialchars_decode($this->adsContents[$i]);
            if (substr($title, -1) !== "\n")
                $title .= "\n";
            if (substr($content, -1) !== "\n")
                $content .= "\n";

            $ad = "\n<TITLU>\n" . $title . "</TITLU>";
            $ad .= "\n<CONTINUT>\n" . $content . "</CONTINUT>\n";
            fwrite($file, $ad);
        }
        $this->fileUpToDate = true;
        
        fclose($file);
        if ($file === false)
            $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Scriere: Fisierul cu anunturi nu a putut fi inchis');
    }
}

?>