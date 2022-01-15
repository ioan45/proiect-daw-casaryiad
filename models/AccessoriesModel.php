<?php

require_once "models/ErrorCollector.php";

class Accessories
{
    private string $url = 'https://www.emag.ro/search/accesorii-pentru-petrecere/accesorii+evenimente/sort-priceasc/c?ref=search_category_3';
    private $source;

    private int $minPrice;
    private int $maxPrice;
    private array $searchResults;  // lista de tupluri de forma (denumire, pret, link)

    private $errCollector;
    private string $errorLogContext;

    
    public function __construct(string $errorLogContext)
    {
        $this->errorLogContext = $errorLogContext . '->' . 'TipsModel';
        $this->errCollector = new ErrorCollector($this->errorLogContext);

        $this->minPrice = 0;
        $this->maxPrice = 0;
        $this->searchResults = array();
        
        $this->source = file_get_contents($this->url);
        if ($this->source === false)
            $this->errCollector->addError(date("Y-m-d h:i:sa"), 'Nu s-a putut extrage pagina sursa');
    }

    public function ParseForPriceRange() : void
    {
        if ($this->source === false)
            return;

        $part = explode('custom-price-input-separator-1', $this->source, 2)[1];
        $min = explode('min="', $part, 2)[1];
        $min = explode('"', $min, 2)[0];
        $max = explode('max="', $part, 2)[1];
        $max = explode('"', $max, 2)[0];

        $this->minPrice = (int)$min;
        $this->maxPrice = (int)$max;
    }

    public function ParseForResults(int $min, int $max) : void
    {
        if ($this->source === false)
            return;

        $this->ParseForPriceRange();
        if ($min < $this->minPrice)
            $min = $this->minPrice;
        if ($max > $this->maxPrice)
            $max = $this->maxPrice;

        $page = 1;
        for (;;)
        {
            $tmpSource = file_get_contents("https://www.emag.ro/search/accesorii-pentru-petrecere/pret,intre-$min-si-$max/accesorii+evenimente/sort-priceasc/p$page/c");
            
            $minPriceFound = $max;
            $maxPriceFound = $min;
            $parts = explode('<div class="pad-hrz-xs">', $tmpSource);
            for ($i = 1; $i < count($parts); ++$i)
            {
                $link = explode('a href="', $parts[$i], 2)[1];
                $link = explode('" class="card-v2-title', $link, 2)[0];

                $title = explode('data-zone="title">', $parts[$i], 2)[1];
                $title = explode('</a>', $title, 2)[0];

                $price = explode('product-new-price">', $parts[$i], 2)[1];
                $price = explode('<sup>', $price, 2)[0];
                if ($price < $minPriceFound)
                    $minPriceFound = $price;
                if ($price > $maxPriceFound)
                    $maxPriceFound = $price;

                $this->searchResults[] = array($title, $price, $link);
            }

            // Pe emag, daca in intervalul de pret dat nu exista niciun rezultat atunci
            // se incarca toate produsele din acea categorie (indiferent de pret)
            //
            // Intr-un astfel de caz, toate produsele gasite sunt eronate => stergere rezultate si incheiere proces
            if ($minPriceFound < $min || $maxPriceFound > $max)
            {
                $this->searchResults = array();
                return;
            }

             // nu mai sunt pagini atunci cand butonul de 'Pagina urmatoare' devine inactiv
            if (str_contains($tmpSource, '<a href="javascript:void(0)">Pagina urmatoare</a>'))
                break;
        
            ++$page;
        }
    }

    public function GetMinPrice() : int { return $this->minPrice; }

    public function GetMaxPrice() : int { return $this->maxPrice; }

    public function GetResults() : array { return $this->searchResults; }
    
}

?>