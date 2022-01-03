<?php

require_once "controllers/Controller.php";
require_once "models/AdminPanelProcessing.php";

class AdminController extends Controller
{
    public function index() : void
    {
        $getArgs = array_merge($_GET);
        $postArgs = array_merge($_POST);

        $adminOpsModel = new AdminPanelProcessing("AdminCtrl");
        $content = $adminOpsModel->MakePanelContent($getArgs, $postArgs);
        if (empty($content))
        {
            $title = $adminOpsModel->GetMsgTitle();
            $head = $adminOpsModel->GetMsgHead();
            $body = $adminOpsModel->GetMsgBody();
            $this->MessagePage($title, $head, $body);
        }
        else
            require_once "views/AdminPanel.php";
    }
}

?>