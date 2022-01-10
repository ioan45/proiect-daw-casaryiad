<?php

include_once "models/DatabaseOps.php";

abstract class Controller
{
    abstract public function index() : void;

    protected function MessagePage(string $msgTitle, string $msgHead, string $msgBody) : void
    {
        require_once "views/MessagePage.php";
    }

    /// Inregistreaza, in baza de date, accesul la pagina cu denumirea data
    protected function PageAccessDbLog(string $pageName, string $errorLogContext) : void
    {
        if (session_status() == PHP_SESSION_ACTIVE)
        {
            $db = new DatabaseOps($errorLogContext . '->PageAccessLog');

            $ip = $_SERVER['REMOTE_ADDR'];
            $sessionID = session_id();

            $db->query("INSERT INTO accesare VALUES (null, default, '$ip', '$sessionID', '$pageName')");
        }
    }
}

?>