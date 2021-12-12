<!DOCTYPE html>

<!-- Trebuie dus in controller --  include_once "../models/event_form_processing.php"  -- Trebuie dus in controller -->

<html lang="ro">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/views/css/bootstrap.min.css">
        <link rel="stylesheet" href="/views/css/Common.css">
        <link rel="stylesheet" href="/views/css/EventForm.css">
        <title>Fă o rezervare! | Casa Ryiad</title>
    </head>
    <body>
        <img id="fundal" src="/images/events2.jpg" alt="main_img" class="position-fixed">
        
        <?php include_once "views/common_parts/header_and_nav.php" ?>

        <h2 id="antet_form" class="font-sans-serif">FORMULAR DE CONTACT</h2>

        <form class="font-sans-serif" action="/rezervare/trimite" method="POST">
            <div class="row g-3 pb-4">
                <div class="col-sm-4">
                    <label for="Tip_Ev" class="form-label">Tip eveniment</label>
                    <select id="Tip_Ev" class="form-select" name="Tip_event" required>
                        <option value="Nunta">Nuntă</option>
                        <option value="Botez">Botez</option>
                        <option value="Privat">Petrecere privată</option>
                        <option value="Corporate">Eveniment corporate</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <label for="Data" class="form-label">Data</label>
                    <input type="date" class="form-control" id="Data" min="<?= date("Y-m-d", strtotime('tomorrow')); ?>" name="Data" required>
                </div>
                <div class="col-sm-4">
                    <label for="nr_inv" class="form-label"> Nr. maxim de invitați</label>
                    <input type="number" class="form-control" id="nr_inv" min="0" max="300" name="Nr_inv">
                </div>
                <div class="col-sm-12">
                    <label for="observatii" class="form-label">Observații</label>
                    <textarea class="form-control" id="observatii" rows="3" placeholder="Informații suplimentare ..." name="Obs"></textarea>
                </div>
            </div>
            <div class="row justify-content-center">
                    <button type="submit" class="btn col-sm-6" name="Submit">Trimite!</button>
            </div>
            <p>
                <span>Notă:</span><br>
                - Rezervările sunt valide numai în urma unei confirmări prin e-mail sau telefonice din partea Casa Ryiad.<br>
                - Rezervările nu pot fi făcute pentru ziua curentă.
            </p>
        </form>

        <?php include_once "views/common_parts/footer.php" ?>

        <script src="/views/js/bootstrap.min.js"></script>
    </body>
</html>