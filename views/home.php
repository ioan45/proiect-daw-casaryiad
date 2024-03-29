<!DOCTYPE html>
<html lang="ro">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/APIs/bootstrap/bootstrap.min.css">
        <link rel="stylesheet" href="/views/css/Common.css">
        <link rel="stylesheet" href="/views/css/Home.css">
        <title>Casa Ryiad</title>
    </head>
    <body>
        <img id="fundal" src="/images/main6.jpg" alt="main_img" class="img-fluid">
       
        <?php include_once "views/common_parts/header_and_nav.php" ?>

        <div class="container py-3 col-sm-8">
            <div id="news" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#news" data-bs-slide-to="0" class="active" aria-current="true"></button>
                    <?=$buttons?>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active" data-bs-interval="10000">
                        <div id="welcome" class="carousel-caption">
                            <span>- Bun venit la -</span><br>
                            <span id="nume_rest">CASA RYIAD</span>
                        </div>
                    </div>
                    <?=$ads?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#news" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#news" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>

        <section class="container-fluid container_prez font-sans-serif fs-5">
            <section class="container-fluid d-flex flex-wrap flex-md-nowrap py-5 justify-content-center">
                <img src="/images/h_events.jpg" alt="H_Evenimente" class="img-fluid align-self-start w-50 border border-1 border-secondary rounded-3">
                <div class="vr d-none d-md-inline-block mx-3 mx-sm-5"></div>
                <span class="align-self-center">
                    <h3 class="mt-3 mt-md-0">Eveniment Realizat Ca La Carte</h3><br>
                    <p>
                        Evenimentul deosebit din viața voastră merită nu doar o locație specială, ci și o echipă de profesioniști 
                        care să se ocupe de organizarea lui.
                    </p>
                    <p>
                        Cu experiență vastă în domeniul organizării evenimentelor, Casa Ryiad vă oferă suport 
                        profesional în pregătirea și desfășurarea nunților, botezurilor, petrecerilor private sau a întrunirilor business.
                    </p>
                </span>
            </section>
        </section>
        <section class="container-fluid container_prez font-sans-serif fs-5">
            <section class="container-fluid d-flex flex-wrap flex-md-nowrap py-5 justify-content-center">
                <span class="align-self-center">
                    <h3>Aptitudini culinare</h3><br>
                    <p>
                        Certificatul nostru de calitate este dat de părerea clienților. Variantele de meniuri propuse de noi,
                        satisfac și cele mai exigente gusturi.
                    </p>
                </span>
                <div class="vr d-none d-md-inline-block mx-3 mx-sm-5"></div>
                <img src="/images/h_meniu.jpg" alt="H_Meniu" class="img-fluid align-self-start w-50 border border-1 border-secondary rounded-3">
            </section>
        </section>

        <section class="container-sm statistici py-3 rounded-3">
            <div class="row gy-5">
                <div class="col">
                    <span><?=$statOnlineVisitors?></span><br>
                    VIZITATORI<br>ONLINE
                </div>
                <div class="col">
                    <span><?=$statVisitors?></span><br>
                    VIZITATORI<br>ASTĂZI
                </div>
                <div class="col">
                    <span><?=$statVisits?></span><br>
                    VIZITE<br>ASTĂZI
                </div>
                <div class="col">
                    <span><?=$statViews?></span><br>
                    AFIȘĂRI<br>ASTĂZI
                </div>
            </div>
        </section>

        <?php include_once "views/common_parts/footer.php" ?>

        <script src="/APIs/bootstrap/bootstrap.min.js"></script>
    </body>
</html>