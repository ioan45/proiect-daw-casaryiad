<?php

require_once "controllers/Controller.php";

class NotFoundController extends Controller
{
    public function index() : void
    {
        require_once "views/Error404.php";
    }
}

?>