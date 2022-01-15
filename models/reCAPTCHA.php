<?php

require_once "models/ErrorCollector.php";


/// Utilitar pentru operatiile cu reCAPTCHA
/// 
/// reCAPTCHA v2 "I'm not a robot" Checkbox

class reCAPTCHA
{
    static private string $secretKey = '-';
    static private string $siteKey = '-';

    private array $response;  // informatii intoarse (JSON): https://developers.google.com/recaptcha/docs/verify
    private $errCollector;

    public function __construct(string $errorLogContext)
    {
        $this->errCollector = new ErrorCollector($errorLogContext . '->' . 'reCAPTCHA');
    }

    public function VerifyCAPTCHA(string $formCaptchaResponse) : void
    {
        $secretGetArg = urlencode(reCAPTCHA::$secretKey);
        $responseGetArg = urlencode($formCaptchaResponse);
        $verifyURL = "https://www.google.com/recaptcha/api/siteverify?secret=$secretGetArg&response=$responseGetArg";
        $responseJSON = file_get_contents($verifyURL);
        if (empty($responseJSON))
            $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Verificarea nu a intors niciun raspuns din partea google.com');
        else
        {
            $this->response = json_decode($responseJSON, true);
            
            if (!empty($this->response['error-codes']))
                foreach ($this->response['error-codes'] as $code => $descr)
                    $this->errCollector->addError(date("Y-m-d h:i:sa"), "Eroare verificare google.com. Cod: $code; Descriere: $descr");
        }
    }

    public function IsValid() : bool 
    {
        if (!isset($this->response['success']))
            return false; 
        return $this->response['success']; 
    }

    static public function GetSecretKey() : string { return reCAPTCHA::$secretKey; }

    static public function GetSiteKey() : string { return reCAPTCHA::$siteKey; }
}

?>