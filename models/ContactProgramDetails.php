<?php

class ContactProgramDetails
{
    // [
    //   'footerContact' => [
    //                        'Address' => [linii de text]
    //                          'Phone' => string
    //                            'Fax' => string
    //                          'Email' => string
    //                      ]
    //   'footerProgram' => [
    //                           'Days' => [linii de text]
    //                          'Hours' => [linii de text]
    //                      ]
    // ]
    private array $details = array();


    public static function Get() : array
    {
        $getContProgModel = new ContactProgramDetails();
        $getContProgModel->ParseContactProgramFile("files/ContactProgramDetails.txt");
        return $getContProgModel->GetDetails();
    }

    public function ParseContactProgramFile(string $filePath) : void
    {
        $file = fopen($filePath, 'r');

        // Contact
        $line = "";
        while (!feof($file) && $line != "<CONTACT>")
            $line = trim(fgets($file));
        while (!feof($file) && $line != "<PROGRAM>")
        {
            $line = trim(fgets($file));

            // Daca mai sunt linii, in caz ca linia curenta e un tag cautat atunci
            // citirea urmatoare, pentru a lua datele, este sigura
            if (!feof($file))
            {
                if ($line == "<Adresa>")
                {
                    $this->details["footerContact"]["Address"][] = trim(fgets($file));
                    if (!feof($file))
                        $this->details["footerContact"]["Address"][] = trim(fgets($file));
                }
                if ($line == "<Telefon>")
                    $this->details["footerContact"]["Phone"] = trim(fgets($file));
                if ($line == "<Fax>")
                    $this->details["footerContact"]["Fax"] = trim(fgets($file));
                if ($line == "<Email>")
                    $this->details["footerContact"]["Email"] = trim(fgets($file));
            }
        }

        // Program
        while (!feof($file))
        {
            $line = trim(fgets($file));
            $parts = explode("|", $line);
            if (count($parts) == 2)
            {
                if ($parts[0] == '<Z>')
                    $this->details["footerProgram"]["Days"][] = $parts[1];
                if ($parts[0] == '<O>')
                    $this->details["footerProgram"]["Hours"][] = $parts[1];
            }
        }
        
        fclose($file);
    }

    public function GetDetails() : array { return $this->details; }
}
