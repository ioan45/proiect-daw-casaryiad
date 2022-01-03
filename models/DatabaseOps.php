<?php

require_once "models/ErrorCollector.php";

class DatabaseOps
{
    private string $db_server;
    private string $db_username;
    private string $db_password;
    private string $db_name;

    private $db_connection;
    private $queryErrorMsg;
    private $errCollector;
    private string $errorLogContext;


    public function __construct(string $errorLogContext)
    {
        $this->errorLogContext = $errorLogContext . '->' . 'DatabaseOps';
        $this->errCollector = new ErrorCollector($this->errorLogContext);

        $this->parseConfigFile('files/DBConnectInfo.cfg');
        
        $this->db_connection = new mysqli($this->db_server, $this->db_username, $this->db_password, $this->db_name);
        if ($this->db_connection->connect_errno)
            $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Conexiune esuata: ' . $this->db_connection->connect_error);
        else
            $this->db_connection->set_charset("utf8");
        $this->queryErrorMsg = '';

    }

    public function __destruct()
    {
        if (!$this->db_connection->close())
            $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Conexiunea nu a putut fi inchisa: ' . $this->db_connection->error);
    }

    public function query(string $query)
    {
        if (!$this->ping())
            return false;

        $queryResult = $this->db_connection->query($query);

        if (gettype($queryResult) == 'object')
            return $queryResult->fetch_all(MYSQLI_ASSOC);

        if ($queryResult === false)
        {
            $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Query esuat: ' . $this->db_connection->error);
            $this->queryErrorMsg = $this->db_connection->error;
        }

        return $queryResult;
    }

    public function commit() : bool 
    {
        if ($this->db_connection->commit())
            return true;
        
        $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Commit esuat: ' . $this->db_connection->error);
        return false;
    }
    
    public function rollback() : bool 
    {
        if ($this->db_connection->rollback())
            return true;
       
        $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Rollback esuat: ' . $this->db_connection->error);
        return false;
    }

    public function ping() : bool 
    { 
        if ($this->db_connection->ping())
            return true;
        
        $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Ping esuat: ' . $this->db_connection->error);
        return false; 
    }

    public function EscapeString(string $str) : string { return $this->db_connection->escape_string($str); }

    public function GetQueryErrorMsg() : string { return $this->queryErrorMsg; }

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
                case 'SERVER':
                    $this->db_server = trim($parts[1]);
                    break;
                case 'UTILIZATOR':
                    $this->db_username = trim($parts[1]);
                    break;
                case 'PAROLA':
                    $this->db_password = trim($parts[1]);
                    break;
                case 'BAZA_DE_DATE':
                    $this->db_name = trim($parts[1]);
                    break;            
            } 
        }

        if (!fclose($file))
            $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Fisierul de configurare nu a putut fi inchis');
    }
}

?>
