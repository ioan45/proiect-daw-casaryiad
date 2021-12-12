<?php

require_once "models/ErrorCollector.php";

abstract class Controller
{
    abstract public function index() : void;

    protected function MessagePage(string $msgTitle, string $msgHead, string $msgBody) : void
    {
        require_once "views/MessagePage.php";
    }
}

?>