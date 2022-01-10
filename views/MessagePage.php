<!DOCTYPE html>
<html lang="ro">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/APIs/bootstrap/bootstrap.min.css">
        <link rel="stylesheet" href="/views/css/Common.css">
        <link rel="stylesheet" href="/views/css/MessagePage.css">
        <title><?=$msgTitle?> | Casa Ryiad</title>
    </head>
    <body>
        <img id="fundal" src="/images/notfound_bg.jpg" alt="main_img" class="img-fluid">
       
        <?php include_once "views/common_parts/header_and_nav.php" ?>

        <div id="text" class="container-sm">
            <h1><?=$msgHead?></h1>
            <p class="fs-4">
                <?=$msgBody?>
            </p>
        </div>

        <script src="/APIs/bootstrap/bootstrap.min.js"></script>
    </body>
</html>