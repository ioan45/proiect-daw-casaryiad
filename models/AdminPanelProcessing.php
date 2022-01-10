<?php

require_once "models/ErrorCollector.php";
require_once "models/DatabaseOps.php";
require_once "models/HomeAds.php";

class AdminPanelProcessing
{
    private string $title;
    private string $head;
    private string $body;

    private ErrorCollector $errCollector;
    private string $errorLogContext;

    public function __construct(string $errorLogContext)
    {
        $this->errorLogContext = $errorLogContext . '->' .'AdminPanelProc';
        $this->errCollector = new ErrorCollector($this->errorLogContext);
    }

    public function MakePanelContent(array $getArgs, array $postArgs) : string
    {
        // daca pagina nu este accesata de un admin..
        if (session_status() != PHP_SESSION_ACTIVE || !isset($_SESSION['UtilizatorTip']) || strtoupper($_SESSION['UtilizatorTip']) != 'ADMIN') 
        {
            $this->title = 'Acces interzis';
            $this->head = 'Acces interzis';
            $this->body = 'Nu ai permisiunea necesară pentru a accesa această pagină.';
            return '';
        }
        if (!$this->ValidRequestReferer())  // Se verifica daca cererea HTTP a fost trimisa prin mijloace de pe acest site (acelasi HOST)
        {
            $this->title = 'Acces interzis';
            $this->head = 'Acces interzis';
            $this->body = 'Nu ai permisiunea necesară pentru a accesa această pagină.';
            return '';
        }
        else if (!empty($_POST) && (
                 !isset($_POST['tokenFormular']) || !isset($_SESSION['tokenFormular']) || 
                 $_POST['tokenFormular'] != $_SESSION['tokenFormular']))
        {
            // Se verifica daca cererea a fost trimisa folosind formularul (daca exista) de pe site (acelasi TOKEN)

            $this->title = 'Acces interzis';
            $this->head = 'Acces interzis';
            $this->body = 'Nu ai permisiunea necesară pentru a accesa această pagină.';
            return '';
        }
        else
        {
            $content = '';
            $formsToken = $this->NewFormToken();

            if (!empty($getArgs['section']))
            {
                $section = strtoupper($getArgs['section']);
                if ($section == 'DB')
                    $content = $this->DbSectionContent($postArgs, $formsToken);
                else if ($section == 'ADS')
                    $content = $this->AdsContentSection($getArgs, $postArgs, $formsToken);
            }

            if (empty($content))
                $content = '<h1 id="mesaj_intrare">PANOUL ADMINISTRATORULUI</h1>';

            return $content;
        }
    }

    private function DbSectionContent(array $postArgs, string $formsToken) : string
    {
        $content = '';

        $content .=  
            '<form class="formular-sql font-sans-serif" action="/administrator?section=db" method="POST">
                <label for="sql" class="form-label">Comanda SQL (SELECT / INSERT / UPDATE / DELETE)</label>
                <textarea class="form-control" id="sql" rows="7" name="comandaSQL" required></textarea>
                <div class="row justify-content-center">
                    <button type="submit" class="btn col-sm-6" name="Submit">Execută</button>
                </div>
                <input type="hidden" name="tokenFormular" value="' . $formsToken . '">
            </form>';

        if (!empty($postArgs['comandaSQL']))
        {
            $adminQuery = trim($postArgs['comandaSQL']);

            if (!in_array(strtoupper(substr($adminQuery, ($adminQuery[0] == '(' ? 1 : 0), 6)), array('SELECT', 'INSERT', 'UPDATE', 'DELETE')))
            {
                $content .= "<h3>COMANDA NU A PUTUT FI EXECUTATĂ.</h3>";
                $content .= "<p>EROARE: Comenzile SQL de acest tip sunt interzise.</p>";
            }
            else
            {
                $db = new DatabaseOps($this->errorLogContext . '->InputSQL');
                $result = $db->query($adminQuery);
                
                if ($result === true)
                    $content .= '<h3>COMANDA A FOST EXECUTATĂ CU SUCCES.</h3>';
                else if ($result === false)
                {
                    $errorMsg = $db->GetQueryErrorMsg();
                    $content .= "<h3>COMANDA NU A PUTUT FI EXECUTATĂ.</h3>";
                    $content .= "<p>EROARE: $errorMsg.</p>";
                }
                else
                {
                    $rowsCount = count($result);
                    $content .= '<h3>COMANDA A FOST EXECUTATĂ CU SUCCES.</h3>';
                    $content .= "<p>$rowsCount LINII RETURNATE</p>";

                    if ($rowsCount > 0)
                    {
                        $content .=
                            '<div class="table-responsive text-start">
                                <table class="tabel-sql table table-striped table-bordered table-sm align-middle">
                                    <thead class="table-dark">
                                        <tr>';
                        foreach ($result[0] as $column => $value)
                                $content .= "<th scope=\"col\">$column</th>";
                        $content .=     '</tr>
                                    </thead>
                                    <tbody>';
                        foreach ($result as $idx => $row)
                        {
                            $content .= '<tr>';
                            foreach ($row as $column => $value)
                            {
                                $val = is_null($value) ? '<em>NULL</em>' : $value;
                                $content .= "<td>$val</td>";
                            }
                            $content .= '</tr>';
                        }
                        $content .= '</tbody>
                                </table>
                            </div>';
                    }
                }
            }
        }

        return $content;
    }

    private function AdsContentSection(array $getArgs, array $postArgs, string $formsToken) : string
    {
        $adsModel = new HomeAds($this->errorLogContext);
        $toModifyID = -1;

        if (!empty($getArgs['op']))
        {
            $op = strtoupper($getArgs['op']);

            if ($op == 'INS' && !empty($_POST['adInsertT']) && !empty($_POST['adInsertC']))
            {
                $adsModel->NewAd($_POST['adInsertT'], $_POST['adInsertC']);
                $adsModel->WriteCurrentAdsInFile();
            }
            else if (!empty($getArgs['id']) || $getArgs['id'] == '0')
            {
                $id = (int)$getArgs['id'];

                if ($op == 'MOD')
                {
                    if (!empty($_POST['adModifyT']) && !empty($_POST['adModifyC']))
                    {
                        $adsModel->ModifyAd($id, $_POST['adModifyT'], $_POST['adModifyC']);
                        $adsModel->WriteCurrentAdsInFile();
                    }
                    else
                        $toModifyID = $id;
                }
                else if ($op == 'DEL')
                {
                    $adsModel->DeleteAd($id);
                    $adsModel->WriteCurrentAdsInFile();
                }
            }
        }

        return $this->AdsMakeContent($toModifyID, $adsModel, $formsToken);
    }

    private function AdsMakeContent(int $toModifyID, HomeAds $adsModel, string $formsToken) : string
    {
        $adsModel->LoadAdsFile();
        $adsTitles = $adsModel->GetTitles();
        $adsContents = $adsModel->GetContents();
        $content = '<div class="anunturi font-sans-serif"><h1>ANUNȚURI</h1>';

        for ($i = 0; $i < count($adsTitles); ++$i)
        {
            if ($i == $toModifyID)
            {
                $content .= '<form class="cadru container-sm d-flex" action="/administrator?section=ads&op=mod&id=' . $i . '" method="POST">
                                <div class="anunt container-fluid">
                                    <label for="modTitle" class="form-label">Titlu</label>
                                    <textarea class="form-control" id="modTitle" rows="2" name="adModifyT" required>' . $adsTitles[$i] . '</textarea>
                                    <label for="modContent" class="form-label mt-4">Conținut</label>
                                    <textarea class="form-control" id="modContent" rows="10" name="adModifyC" required>' . $adsContents[$i] . '</textarea>
                                    <input type="hidden" name="tokenFormular" value="' . $formsToken . '">
                                </div>
                                <div class="d-flex flex-column ps-2">
                                    <button type="submit" class="btn btn-sm my-1" name="Submit">
                                        <img src="/images/save_icon.png" title="Salvează">
                                    </button>
                                </div>
                            </form>';
            }
            else
            {
                $content .= '<div class="cadru container-sm d-flex">
                                <div class="anunt container-fluid">
                                    <h5>Titlu</h5><p>' .
                                    $adsTitles[$i] .
                                    '</p><h5>Conținut</h5>' .
                                    $adsContents[$i] .
                                '</div>
                                <div class="d-flex flex-column ps-2">
                                    <a class="btn btn-sm my-1" href="/administrator?section=ads&op=mod&id=' . $i . '">
                                        <img src="/images/edit_icon.png" title="Modifică">
                                    </a>
                                    <a class="btn btn-sm my-1" href="/administrator?section=ads&op=del&id=' . $i . '">
                                        <img src="/images/delete_icon.png" title="Elimină">
                                    </a>
                                </div>
                            </div>';
            }
        }

        // Formular pentru inserare
        
        $content .= '<form class="cadru container-sm d-flex" action="/administrator?section=ads&op=ins" method="POST">
                        <div class="anunt container-fluid">
                            <label for="insTitle" class="form-label">Titlu</label>
                            <textarea class="form-control" id="insTitle" rows="2" name="adInsertT" required></textarea>
                            <label for="insContent" class="form-label mt-4">Conținut</label>
                            <textarea class="form-control" id="insContent" rows="10" name="adInsertC" required></textarea>
                            <input type="hidden" name="tokenFormular" value="' . $formsToken . '">
                        </div>
                        <div class="d-flex flex-column ps-2">
                            <button type="submit" class="btn btn-sm my-1" name="Submit">
                                <img src="/images/save_icon.png" title="Salvează">
                            </button>
                        </div>
                    </form>';

        $content .= '</div>';
        return $content;
    }

    private function ValidRequestReferer() : bool
    {
        if (!isset($_SERVER['HTTP_REFERER']))
            return false;

        $refererHost = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
        $correctHost = $_SERVER['HTTP_HOST'];
        return $refererHost == $correctHost;
    }

    private function NewFormToken()
    {
        // Se creeaza o 'sesiune' a formularului (se seteaza $_SESSION['tokenFormular'] cu $formToken) 
        // ce îi va corespunde formularul din pagina incarcata (formularul are un camp invizibil in care retine $formToken)

        $formToken = bin2hex(random_bytes(64));
        if (session_status() == PHP_SESSION_ACTIVE)
            $_SESSION['tokenFormular'] = $formToken;
        else
            $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Creare Token esuata: Sesiunea nu este activa!');
        return $formToken;
    }

    public function GetMsgTitle() : string { return $this->title; }

    public function GetMsgHead() : string { return $this->head; }

    public function GetMsgBody() : string { return $this->body; }
}

?>