<?php

require_once "controllers/Controller.php";
require_once "models/HomeAds.php";
require_once "models/ContactProgramDetails.php";
require_once "models/DatabaseOps.php";

class PresentationController extends Controller
{
    public function index() : void  // pagina acasa 
    {
        $getAdsModel = new HomeAds;
        $getAdsModel->ParseAdsFile('files/HomeAds.txt');
        $titles = $getAdsModel->GetTitles();
        $paragraphs = $getAdsModel->GetParagraphs();

        // Incarca datele de contact/program afisate la finalul paginii
        extract(ContactProgramDetails::Get());

        require_once "views/Home.php";
    }

    public function menu() : void
    {
        $db = new DatabaseOps();
        $foodRows = $db->query("SELECT denumire, pret, cantitate, unit_masura FROM element_meniu WHERE upper(tip) = 'M'");
        $alcRows = $db->query("SELECT denumire, pret, cantitate, unit_masura FROM element_meniu WHERE upper(tip) = 'BA'");
        $nonAlcRows = $db->query("SELECT denumire, pret, cantitate, unit_masura FROM element_meniu WHERE upper(tip) = 'BNA'");
        unset($db);

        extract(ContactProgramDetails::Get());
        require_once "views/Menu.php";
    }

    public function events() : void
    {
        extract(ContactProgramDetails::Get());
        require_once "views/Events.php";
    }

    public function about() : void
    {
        extract(ContactProgramDetails::Get());
        require_once "views/About.php";
    }
}

?>