<?php

require_once "models/ErrorCollector.php";

abstract class Controller
{
    abstract public function index() : void;
}

?>