<?php

require_once "controllers/Controller.php";
require_once "models/HomeAds.php";
require_once "models/Tips.php";
require_once "models/ContactProgramDetails.php";
require_once "models/DatabaseOps.php";

class PresentationController extends Controller
{
    public function index() : void  // pagina acasa 
    {
        $this->PageAccessDbLog('PagAcasa', 'PresentCtrl');

        // Anunturi

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

        // Sfaturi

        $getTipsModel = new Tips("PresentCtrl");
        $getTipsModel->LoadTips();
        $tipsParts = $getTipsModel->GetTips();

        $tips = '';
        foreach ($tipsParts as $tipParts) 
        {
            $tips .= '<div class="sfat my-4 p-3">
                          <b>'. $tipParts['antet'] .'</b>
                          <p>&emsp;' . $tipParts['text'] . '</p>
                      </div>';
        }

        // Statistici

        $db = new DatabaseOps('PresentCtrl');
        $qresult = $db->query("SELECT count(*) as \"afisari\", 
                                      count(distinct id_sesiune) as \"vizite\", 
                                      count(distinct ip_vizitator) as \"vizitatori\" 
                                FROM accesare
                                WHERE lower(actiune) = 'pagacasa' AND date(data) = CURDATE()");
        $statViews = $qresult[0]['afisari'];
        $statVisits = $qresult[0]['vizite'];
        $statVisitors = $qresult[0]['vizitatori'];
        $statOnlineVisitors = $db->query("SELECT count(distinct ip_vizitator) as \"viz_online\" 
                                          FROM accesare
                                          WHERE lower(actiune) = 'pagacasa' AND data > now() - interval 5 minute")[0]['viz_online'];

        // Incarca datele de contact/program afisate la finalul paginii
        extract(ContactProgramDetails::Get());
        require_once "views/Home.php";
    }
    
    public function events() : void
    {
        $this->PageAccessDbLog('PagEvenimente', 'PresentCtrl');

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
        $this->PageAccessDbLog('PagDespre', 'PresentCtrl');

        extract(ContactProgramDetails::Get());
        require_once "views/About.php";
    }
}

?>