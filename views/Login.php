<!DOCTYPE html>

<!-- Trebuie dus in controller --  include_once "../models/event_form_processing.php"  -- Trebuie dus in controller -->

<html lang="ro">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/APIs/bootstrap/bootstrap.min.css">
        <link rel="stylesheet" href="/views/css/Common.css">
        <link rel="stylesheet" href="/views/css/Login.css">
        <title>Autentificare | Casa Ryiad</title>
    </head>
    <body>
        <img id="fundal" src="/images/events2.jpg" alt="main_img" class="position-fixed">
        
        <?php include_once "views/common_parts/header_and_nav.php" ?>

        <h2 id="antet_form" class="font-sans-serif">AUTENTIFICARE</h2>

        <form class="font-sans-serif" action="/autentificare/procesare" method="POST">
            <?=$message?>

            <div class="col pb-3">
                <label for="Utilizator" class="form-label">Utilizator</label>
                <input type="text" class="form-control" id="Utilizator" maxlength="16" name="Utilizator" required>
            </div>
            <div class="col pb-3">
                <label for="Parola" class="form-label">Parola</label>
                <input type="password" class="form-control" id="Parola" maxlength="25" name="Parola" required>
            </div>

            <div class="text-center">
                <div class="g-recaptcha <?=$NoCAPTCHA?>" data-sitekey="<?=$siteKey?>"></div>
            </div>

            <input type="hidden" name="tokenFormular" value="<?=$formToken?>">

            <div class="row justify-content-center">
                <button type="submit" class="btn col-sm-6" name="Submit">Autentifică-te!</button>
            </div>
        </form>

        <?php include_once "views/common_parts/footer.php" ?>

        <script src="/APIs/bootstrap/bootstrap.min.js"></script>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </body>
</html>