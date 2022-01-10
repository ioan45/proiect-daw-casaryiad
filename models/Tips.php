<?php

require_once "models/ErrorCollector.php";

class Tips
{
    private array $tips;  // [ ("text" => <textul>, "antet" => <antetul sfatului>) ]

    private $errCollector;
    private string $errorLogContext;

    
    public function __construct(string $errorLogContext)
    {
        $this->errorLogContext = $errorLogContext . '->' . 'TipsModel';
        $this->errCollector = new ErrorCollector($this->errorLogContext);

        $this->tips = array();
    }

    public function LoadTips() : void
    {
        $url = 'https://noblesse-group.com/10-sfaturi-pentru-organizarea-unei-petreceri-corporate-de-succes/';
        
        $source = file_get_contents($url);
        if ($source === false)
            $this->errCollector->addError(date("Y-m-d h:i:sa"), 'LoadTips: Nu s-a putut obtine continutul paginii exterioare');

        $parts = explode('element-content-single', $source, 2);
        $parts = $parts[1];
        $parts = explode('<div class="flex-disp">', $parts, 2);
        $parts = $parts[0];

        $parts = explode('<p>', $parts, 8);
        for ($i = 2; $i < 7; ++$i)
        {
            $tipParts = explode('</stron', $parts[$i]);
            
            $tipHeader = $tipParts[0];
            $tipHeader = explode('strong>', $tipHeader);
            $tipHeader = $tipHeader[1];

            $tipBody = $tipParts[1];
            $tipBody = explode('</p', $tipBody);
            $tipBody = $tipBody[0];
            $tipBody = explode('><br />', $tipBody);
            $tipBody = $tipBody[1];

            $this->tips[] = array("text" => $tipBody, "antet" => $tipHeader);
        }
    }

    public function GetTips() : array { return $this->tips; }
}

?>