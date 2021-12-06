<!DOCTYPE html>
<html lang="ro">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/views/css/bootstrap.min.css">
        <link rel="stylesheet" href="/views/css/common.css">
        <link rel="stylesheet" href="/views/css/LoginFailed.css"> 
        <title>Autentificare eșuată! | Casa Ryiad</title>
    </head>
    <body>
        <img id="fundal" src="/images/notfound_bg.jpg" alt="main_img" class="img-fluid">
       
        <?php include_once "views/common_parts/header_and_nav.php" ?>

        <div id="text" class="container-sm">
            <h1>Autentificare eșuată</h1>
            <p class="fs-3">
                <?=$FailedMsg?>
            </p>
        </div>

        <script src="/views/js/bootstrap.min.js"></script>
    </body>
</html>