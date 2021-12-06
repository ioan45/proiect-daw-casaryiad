<?php

// erorile sunt pastrate intr-un vector si adaugate in fisier la distrugerea obiectului

class ErrorCollector
{
    private string $context = "";
    private array $errors = array();

    public function __construct(string $context)
    {
        $this->context = $context;
    }

    public function __destruct()
    {
        if (!empty($this->errors))
        {
            $file = fopen('ErrorLogs.txt', 'a');
            foreach ($this->errors as $msg)
            {
                $outMsg = '[' . $this->context . '] ' . $msg . " .\n";
                fwrite($file, $outMsg);
            }
            fclose($file);
        }
    }

    public function addError(string $currentTime, string $message)
    {
        $this->errors[] = '[' . $currentTime . '] ' . $message;
    }
    
    // public function setContext(string $context) : void
    // {
    //     $this->context = $context;
    // }
}

?>