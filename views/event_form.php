<?php include_once "../models/event_form_processing.php" ?>

<!DOCTYPE html>
<html lang="ro">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css\bootstrap.min.css">
        <link rel="stylesheet" href="css\common.css">
        <link rel="stylesheet" href="css\event_form.css">
        <title>Fă o rezervare! | Casa Ryiad</title>
    </head>
    <body>
        <img id="fundal" src="../images/events2.jpg" alt="main_img" class="position-fixed">
        
        <?php include_once "common_parts/header_and_nav.html" ?>

        <h2 id="antet_form" class="font-sans-serif">FORMULAR DE CONTACT</h2>

        <form class="font-sans-serif" action="event_form.php" method="POST">
            <div class="row g-3 g-sm-4 pb-3">
                <div class="col-sm-6">
                    <label for="Nume" class="form-label">Nume</label>
                    <input type="text" class="form-control" id="Nume" maxlength="50" name="Nume" required>
                </div>
                <div class="col-sm-6">
                    <label for="Prenume" class="form-label">Prenume</label>
                    <input type="text" class="form-control" id="Prenume" maxlength="50" name="Prenume" required>
                </div>
            </div>
            <div class="row g-3 g-sm-4 pb-3">
                <div class="col-sm-6">
                    <label for="Email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="Email" maxlength="320" placeholder="exemplu@domeniu.com" name="Email" required>
                </div>
                <div class="col-sm-6">
                    <label for="Telefon" class="form-label">Telefon (doar cifre)</label>
                    <input type="tel" class="form-control" id="Telefon" pattern="[0-9]{10}" maxlength="10" name="telefon" required>
                </div>
            </div>
            <div class="row g-3 pb-4">
                <div class="col-sm-4">
                    <label for="Tip_Ev" class="form-label">Tip eveniment</label>
                    <select id="Tip_Ev" class="form-select" name="tip_event" required>
                        <option value="Nunta">Nuntă</option>
                        <option value="Botez">Botez</option>
                        <option value="Privat">Petrecere privată</option>
                        <option value="Corporate">Eveniment corporate</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <label for="Telefon" class="form-label">Data</label>
                    <input type="date" class="form-control" id="Data" min="<?= date("Y-m-d", strtotime('tomorrow')); ?>" name="data" required>
                </div>
                <div class="col-sm-4">
                    <label for="nr_inv" class="form-label"> Nr. maxim de invitați</label>
                    <input type="number" class="form-control" id="nr_inv" min="0" max="300" name="nr_inv">
                </div>
                <div class="col-sm-12">
                    <label for="observatii" class="form-label">Observații</label>
                    <textarea class="form-control" id="observatii" rows="3" placeholder="Informații suplimentare ..." name="obs"></textarea>
                </div>
            </div>
            <div class="row justify-content-center">
                    <button type="submit" class="btn col-sm-6" name="submit">Trimite!</button>
            </div>
            <p>
                <span>Notă:</span><br>
                - Rezervările sunt valide numai în urma unei confirmări prin e-mail sau telefonice din partea Casa Ryiad.<br>
                - Rezervările nu pot fi făcute pentru ziua curentă.
            </p>
        </form>

        <?php include_once "common_parts/footer.php" ?>

        <script src="js\bootstrap.min.js"></script>
    </body>
</html>