<?php

require_once "controllers/Controller.php";
require_once "models/HomeAds.php";
require_once "models/ContactProgramDetails.php";
require_once "models/DatabaseOps.php";

class PresentationController extends Controller
{
    public function index() : void  // pagina acasa 
    {
        $getAdsModel = new HomeAds("PresentCtrl");
        $getAdsModel->LoadAdsFile();
        $titles = $getAdsModel->GetTitles();
        $contents = $getAdsModel->GetContents();

        $ads = '';
        $buttons = '';
        for ($i = 0; $i < count($titles); ++$i)
        {
            $buttons .= '<button type="button" data-bs-target="#news" data-bs-slide-to="' . (string)($i + 1) . '"></button>';

            $ads .= '<div class="carousel-item" data-bs-interval="10000">
                        <div class="carousel-caption anunt">
                            <h1>' . $titles[$i] . '</h1>
                            ' . $contents[$i] . '
                        </div>
                    </div>';
        }

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