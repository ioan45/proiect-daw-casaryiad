<?php

require_once "models/ErrorCollector.php";

date_default_timezone_set('Europe/Bucharest');

if (session_status() != PHP_SESSION_DISABLED && session_status() == PHP_SESSION_NONE)
    if (!session_start())
    {
        $errCollector = new ErrorCollector("Index->Session");
        $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Inițializare eșuată a sesiunii');
    }



require_once "Router.php";
require_once "controllers/NotFoundController.php";
require_once "controllers/PresentationController.php";
require_once "controllers/EventReservController.php";
require_once "controllers/AccountController.php";
require_once "controllers/AdminController.php";



$router = new Router('index', 'NotFoundController');

$router->AddRoute('/', 'PresentationController');
$router->AddRoute('/evenimente', 'PresentationController', 'events');
$router->AddRoute('/despre', 'PresentationController', 'about');

$router->AddRoute('/rezervare', 'EventReservController');
$router->AddRoute('/rezervare/trimite', 'EventReservController', 'FormSent');

$router->AddRoute('/autentificare', 'AccountController', 'LoginPage');
$router->AddRoute('/autentificare/procesare', 'AccountController', 'LoginProcessing');
$router->AddRoute('/inregistrare', 'AccountController', 'RegistrationPage');
$router->AddRoute('/inregistrare/procesare', 'AccountController', 'RegistrationProcessing');
$router->AddRoute('/utilizator/logout', 'AccountController', 'LogoutProcessing');

$router->AddRoute('/administrator', 'AdminController');


$router->ProcessURL($_SERVER['REQUEST_URI']);

$reqController =  $router->GetController();
$reqAction = $router->GetAction();

$controller = new $reqController();
$controller->$reqAction();

?>