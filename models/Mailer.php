<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once "APIs/phpmailer/src/Exception.php";
require_once "APIs/phpmailer/src/PHPMailer.php";
require_once "APIs/phpmailer/src/SMTP.php";
require_once "models/ErrorCollector.php";

class Mailer
{
    private ?string $host = null;
    private ?int $port = null;
    private ?string $smtpSecure = null;
    private ?bool $smtpAuth = null;
    private ?string $user = null;
    private ?string $password = null;
    private ?bool $smtpDebug = null;
    
    private string $FromName = 'Casa Ryiad';

    private $errCollector;
    private string $errorLogContext;

    public function __construct(string $errorLogContext)
    {
        $this->errorLogContext = $errorLogContext . '->' . 'Mailer';
        $this->errCollector = new ErrorCollector($this->errorLogContext);

        $this->parseConfigFile('files/MailConnectInfo.cfg');
    }

    public function Mail(string $to,
                         string $recipientName, 
                         string $subject, 
                         string $body, 
                         string $altBody = '', 
                         bool $isHTML = true) : void
    {
        $mailerAPI = new PHPMailer(true); 
        $mailerAPI->IsSMTP();
        try 
        {
            $mailerAPI->Host = $this->host;      
            $mailerAPI->Port = $this->port;   
            $mailerAPI->SMTPDebug = $this->smtpDebug;                     
            $mailerAPI->SMTPAuth = $this->smtpAuth;
            $mailerAPI->SMTPSecure = $this->smtpSecure;                                 
            $mailerAPI->Username = $this->user;
            $mailerAPI->Password = $this->password;

            $mailerAPI->SetFrom($this->user, $this->FromName);
            $mailerAPI->addAddress($to, $recipientName);

            $mailerAPI->IsHTML($isHTML);
            $mailerAPI->Subject = $subject;
            $mailerAPI->Body = $body;
            $mailerAPI->AltBody = $altBody; 

            $isSent = $mailerAPI->Send();
            if ($isSent === false)
                $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Mail: ' . $mailerAPI->ErrorInfo);
        } 
        catch (Exception $e)
        {
            $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Mail: ' . $e->errorMessage());
        }
    }

    private function parseConfigFile(string $configFile) : void
    {
        $file = fopen($configFile, 'r');
        if (!$file)
            $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Fisierul de configurare nu a putut fi deschis');

        while (!feof($file))
        {
            $line = fgets($file);
            $parts = explode('=', $line, 2);
            switch (trim($parts[0]))
            {
                case 'HOST':
                    $this->host = trim($parts[1]);
                    break;
                case 'PORT':
                    $this->port = (int)trim($parts[1]);
                    break;
                case 'SMTP_SECURE':
                    $this->smtpSecure = trim($parts[1]);
                    break;
                case 'SMTP_AUTH':
                    $this->smtpAuth = (bool)trim($parts[1]);
                    break;
                case 'UTILIZATOR':
                    $this->user = trim($parts[1]);
                    break; 
                case 'PAROLA':
                    $this->password = trim($parts[1]);
                    break;
                case 'SMTP_DEBUG':
                    $this->smtpDebug = (bool)trim($parts[1]);
                    break;                          
            }
        }

        if (!fclose($file))
            $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Fisierul de configurare nu a putut fi inchis');

        if (is_null($this->host) || is_null($this->port) || is_null($this->smtpSecure) || is_null($this->smtpAuth) ||
            is_null($this->user) || is_null($this->password) || is_null($this->smtpDebug))
            $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Parametru lipsa din fisierul de configurare!');
    }
}

?>