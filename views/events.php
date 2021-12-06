<!DOCTYPE html>
<html lang="ro">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/views/css/bootstrap.min.css">
        <link rel="stylesheet" href="/views/css/common.css">
        <link rel="stylesheet" href="/views/css/events.css">
        <title>Evenimente | Casa Ryiad</title>
    </head>
    <body>
        <img id="fundal" src="/images/events2.jpg" alt="main_img" class="position-fixed">
        
        <?php include_once "views/common_parts/header_and_nav.php" ?>
        
        <section class="container-sm">
            <div class="card">
                <div class="row g-0">
                    <div class="col-lg-6">
                        <img src="/images/wedding.jpg" class="rounded" alt="wedding.jpg">
                    </div>
                    <div class="col-lg-6 text-center d-flex align-items-center">
                        <div class="card-body">
                            <h3 class="card-title fs-1">Nuntă</h3>
                            <p class="card-text font-sans-serif">
                                Căutați restaurant nuntă în București? Casa Ryiad este amplasat într-un decor unic in București,
                                cu o atmosferă ideală pentru un eveniment de neuitat.
                            </p>
                            <p>
                                Casa Ryiad reprezintă apogeul conceptului unui restaurant de nuntă: o locație de poveste, mâncare
                                rafinată tradițională și internațională care poate satisface cele mai rafinate gusturi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="row g-0">
                    <div class="col-lg-6 text-center d-flex align-items-center">
                        <div class="card-body">
                            <h3 class="card-title fs-1">Botez</h3>
                            <p class="card-text font-sans-serif">
                                Casa Ryiad are o tradiție îndelungată pe nișa de organizări evenimente, astfel încât dacă ești în
                                căutarea unui restaurant botez în București suntem o alegere ideală.
                            </p>
                            <p>
                                Pornind de la atenția cu carene tratăm oaspeții, de la atenția pentru detalii și până la meniurile
                                cu care vă vom delecta reușim sa ieșim din tiparele obișnuite.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <img src="/images/christening.jpg" class="rounded" alt="christening.jpg">
                    </div>
                </div>
            </div>
        </section>

        <section class="container-sm">
            <div class="card">
                <div class="row g-0">
                    <div class="col-lg-6">
                        <img src="/images/private.jpg" class="rounded" alt="private.jpg">
                    </div>
                    <div class="col-lg-6 text-center d-flex align-items-center">
                        <div class="card-body">
                            <h3 class="card-title fs-1">Petreceri private</h3>
                            <p class="card-text font-sans-serif">
                                Ferit de agitația cotidiană, restaurantul Casa Ryiad poate găzdui petreceri private deosebite.
                            </p>
                            <p>
                                Cât de încântat ai fi ca oaspete invitat la un lichior, un cocktail sau un 
                                dineu în Casa cu povești felurite! Cum ți-ar gâdila papilele gustative pieptul de rață 
                                pregătit după o rețetă veche de o sută de ani? Sau un grătar pregătit pe cărbune și împreunat 
                                cu un vin bun și vechi? Să uiți de tine, să-ți fie bine.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="row g-0">
                    <div class="col-lg-6 text-center d-flex align-items-center">
                        <div class="card-body">
                            <h3 class="card-title fs-1">Evenimente corporate</h3>
                            <p class="card-text font-sans-serif">
                                Evenimentele corporate trebuie să iasă perfect, acestea pot influența imaginea pe care
                                și-o creează invitații despre dumneavoastră, iar la un astfel de eveniment, persoanele prezente pot fi
                                viitori parteneri de afaceri.
                            </p>
                            <p>
                                Pentru a vă putea păstra reputația și pentru a vă putea face remarcat, astfel încât să impresionați 
                                persoanele pe care le doriți ca parteneri de afaceri, trebuie să alegeți un local ideal, unde servirea
                                să fie de excepție și să vă asigurați că toată lumea este satisfăcută.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <img src="/images/corporate.jpg" class="rounded" alt="corporate.jpg">
                    </div>
                </div>
            </div>
        </section>

        <div class="row justify-content-center">
            <a id="buton_catre_form" class="btn btn-dark btn-lg col-11 col-sm-6" href="/rezervare" role="button">Fă o rezervare!</a>
        </div>

        <?php include_once "views/common_parts/footer.php" ?>

        <script src="/views/js/bootstrap.min.js"></script>
    </body>
</html>