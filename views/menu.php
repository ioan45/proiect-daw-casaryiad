<!DOCTYPE html>
<html lang="ro">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/views/css/bootstrap.min.css">
        <link rel="stylesheet" href="/views/css/Common.css">
        <link rel="stylesheet" href="/views/css/Menu.css">
        <title>Meniu | Casa Ryiad</title>
    </head>
    <body>
        <img id="fundal" src="/images/menu3.jpg" alt="main_img" class="position-fixed">
        
        <?php include_once "views/common_parts/header_and_nav.php" ?>
        
        <?php
            function printTable(array $rows)
            {
                $nrRows = count($rows);
                for ($i = 0; $i < $nrRows; ++$i)
                {
                    if ($i % 2 == 0)  // daca elementul curent se afla pe o pozitie para in sir => se incepe o noua linie cu el
                        echo '<tr>';

                    echo '<td>';
                    echo '<span class="item_den">' . $rows[$i]['denumire'] . '</span><br>';
                    echo '<span class="item_info">1 porție / ' . $rows[$i]['cantitate'] . ' ' . $rows[$i]['unit_masura'] . '</span>';
                    echo '<span class="item_pret">' . $rows[$i]['pret'] . ' lei</span>';
                    if ($i < $nrRows - (2 - $nrRows % 2))  // Daca nu ne aflam pe ultima linie din tabelul rezultat
                        echo '<hr>';                       // (indiferent daca acea ultima linie are unul sau doua elemente)
                    echo '</td>';

                    if ($i % 2 == 1)  // dupa afisarea unui element de pe pozitie impara in sir, se incheie linia curenta
                        echo '</tr>';
                }
                if ($nrRows % 2 == 1)  // daca avem un numar impar de elemente in sir => ultima linie va avea doar un elem. =>
                    echo '</tr>';      // => linia se incheie dupa el
            }
        ?>

        <h3 class="tabel_titlu text-center font-sans-serif">MÂNCARE</h3>
        <section class="container-sm container_tabel">
            <table class="table table-borderless align-middle font-sans-serif">
                <tbody>
                    <?php printTable($foodRows); ?>
                </tbody>
            </table>
        </section>
        
        <h3 class="tabel_titlu text-center font-sans-serif">BĂUTURI ALCOOLICE</h3>
        <section class="container-sm container_tabel">
            <table class="table table-borderless align-middle font-sans-serif">
                <tbody>
                    <?php printTable($alcRows); ?>
                </tbody>
            </table>
        </section>
        
        <h3 class="tabel_titlu text-center font-sans-serif">BĂUTURI NON-ALCOOLICE</h3>
        <section class="container-sm container_tabel">
            <table class="table table-borderless align-middle font-sans-serif">
                <tbody>
                    <?php printTable($nonAlcRows); ?>
                </tbody>
            </table>
        </section>

        <?php include_once "views/common_parts/footer.php" ?>

        <script src="/views/js/bootstrap.min.js"></script>
    </body>
</html>