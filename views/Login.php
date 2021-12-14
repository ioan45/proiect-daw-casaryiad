<!DOCTYPE html>

<!-- Trebuie dus in controller --  include_once "../models/event_form_processing.php"  -- Trebuie dus in controller -->

<html lang="ro">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/views/css/bootstrap.min.css">
        <link rel="stylesheet" href="/views/css/Common.css">
        <link rel="stylesheet" href="/views/css/Login.css">
        <title>Autentificare | Casa Ryiad</title>
    </head>
    <body>
        <img id="fundal" src="/images/events2.jpg" alt="main_img" class="position-fixed">
        
        <?php include_once "views/common_parts/header_and_nav.php" ?>

        <h2 id="antet_form" class="font-sans-serif">AUTENTIFICARE</h2>

        <form class="font-sans-serif" action="/autentificare/procesare" method="POST">
            <?php
                if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION['AutEsuata']))
                {
                    echo '<div class="col my-4" id="DateIncorecte">Date incorecte</div>';
                    unset($_SESSION['AutEsuata']);
                }
            ?>
            <div class="col my-2">
                <label for="Utilizator" class="form-label">Utilizator</label>
                <input type="text" class="form-control" id="Utilizator" maxlength="16" name="Utilizator" required>
            </div>
            <div class="col my-3">
                <label for="Parola" class="form-label">Parola</label>
                <input type="password" class="form-control" id="Parola" maxlength="25" name="Parola" required>
            </div>
            <div class="row justify-content-center">
                <button type="submit" class="btn col-6 mt-3" name="Submit">Autentifică-te!</button>
            </div>
        </form>

        <?php include_once "views/common_parts/footer.php" ?>

        <script src="/views/js/bootstrap.min.js"></script>
    </body>
</html>