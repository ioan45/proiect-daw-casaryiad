<!DOCTYPE html>
<html lang="ro">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/views/css/bootstrap.min.css">
        <link rel="stylesheet" href="/views/css/Common.css">
        <link rel="stylesheet" href="/views/css/AdminPanel.css">
        <title>Administrator | Casa Ryiad</title>
    </head>
    <body>
        <img id="fundal" src="/images/notfound_bg.jpg" alt="main_img" class="img-fluid">
       
        <?php include_once "views/common_parts/header_and_nav.php" ?>

        <section class="taburi container-sm mt-5 mb-5">
            <nav class="nav nav-pills navbar navbar-expand-sm navbar-dark navbar-bg-color rounded-top">
                <div class="container-lg">
                    <div class="navbar-header"></div>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#NAVadmin" aria-controls="NAVadmin" aria-expanded="false">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="NAVadmin">
                        <ul class="navbar-nav me-auto mt-3 mt-sm-0 bg-md-light">
                            <li class="nav-item ms-3">
                                <a id="acasa" class="nav-link px-1" href="/administrator?section=db">BAZĂ DE DATE</a>
                            </li>
                            <hr class="text-light my-1 d-sm-none">
                            <li class="nav-item ms-3">
                                <a id="evenimente" class="nav-link px-1" href="/administrator?section=ads">ANUNȚURI</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="container-sm admin-content rounded-bottom text-center">
                <?=$content?>
            </div>
        </section>

        <script src="/views/js/bootstrap.min.js"></script>
    </body>
</html>