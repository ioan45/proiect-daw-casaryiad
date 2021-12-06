<?php

class Router
{
   private string $controller;  // controller-ul si actiunea specifice cererii clientului
   private string $action;      // (sunt setate in urma procesarii url-ului)

   private string $notFoundController;  // controller-ul si actiunea folosite pentru adresa inexistenta
   private string $notFoundAction;      // 
   
   private string $defaultAction;  // Actiune setata pentru controller atunci cand nu se specifica una anume
   private array $routes;  // [calea_url => (controller, action)]


   public function __construct(string $defAct, string $Contr404, string $Act404 = '')
   {
      if ($Act404 == "")
         $Act404 = $defAct;

      $this->defaultAction = $defAct;
      $this->notFoundController = $Contr404;
      $this->notFoundAction = $Act404;

      $this->controller = $Contr404;
      $this->action = $Act404;
   }

   public function AddRoute(string $urlPath, string $controller, string $action = '') : bool
   {
      $urlPath = trim($urlPath, '/');
      if (!isset($this->routes[$urlPath]))
      {
         if ($action == '')
            $action = $this->defaultAction;

         $this->routes[$urlPath] = array($controller, $action);
         
         return true;
      }
      return false;
   }

   public function ProcessURL(string $urlPath) : void
   {  
      $urlPath = trim($urlPath, '/');

      if (isset($this->routes[$urlPath]))
      {
         $this->controller = $this->routes[$urlPath][0];
         $this->action = $this->routes[$urlPath][1];
      }
      else
      {
         $this->controller = $this->notFoundController;
         $this->action = $this->notFoundAction;
      }
   }

   public function GetController() : string
   {
      return $this->controller;
   }

   public function GetAction() : string
   {
      return $this->action;
   }
}

?>