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
        $getAdsModel->LoadAdsFile();
        $titles = $getAdsModel->GetTitles();
        $contents = $getAdsModel->GetContents();

        // Incarca datele de contact/program afisate la finalul paginii
        extract(ContactProgramDetails::Get());

        require_once "views/Home.php";
    }
    
    public function events() : void
    {
        $db = new DatabaseOps('PresentCtrl');
        $menus = $db->query("SELECT denumire, pret FROM meniu");
        $menusElems = $db->query("SELECT m.denumire as 'den_meniu', em.denumire as 'den_elem' " .
                                 "FROM meniu m, element_meniu em, compozitie_meniu cm " .
                                 "WHERE m.cod_meniu = cm.cod_meniu and cm.cod_element = em.cod_element");
        unset($db);

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