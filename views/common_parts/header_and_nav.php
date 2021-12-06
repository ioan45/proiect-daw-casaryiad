<header>
    <img src="/images/logo.png" id="logo" alt="header_logo">
</header>
<nav class="nav nav-pills navbar sticky-top navbar-expand-md navbar-dark navbar-bg-color">
    <div class="container-lg">
        <div class="navbar-header"></div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#NAV" aria-controls="NAV" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="NAV">
            <ul class="navbar-nav me-auto mt-3 mt-md-0 bg-md-light">
                <li class="nav-item">
                    <a id="acasa" class="nav-link px-3" href="/">ACASĂ</a>
                </li>
                <hr class="text-light my-1 d-md-none">
                <li class="nav-item ">
                    <a id="meniu" class="nav-link px-3" href="/meniu">MENIU</a>
                </li>
                <hr class="text-light my-1 d-md-none">
                <li class="nav-item">
                    <a id="evenimente" class="nav-link px-3" href="/evenimente">EVENIMENTE</a>
                </li>
                <hr class="text-light my-1 d-md-none">
                <li class="nav-item">
                    <a id="despre" class="nav-link px-3" href="/despre">DESPRE</a>
                </li>
            </ul>
            <ul class="navbar-nav mb-2 mb-md-0 mt-4 mt-md-0">
                <?php
                    if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION['UtilizatorID']))  // utilizator autentificat
                    {
                        echo '<li class="nav-item">';
                        echo '      <a class="nav-link px-3" href="/utilizator/logout">DECONECTEAZĂ-TE</a>';
                        echo '</li>';
                    }
                    else
                    {
                        echo '<li class="nav-item">';
                        echo '      <a class="nav-link px-3" href="/autentificare">AUTENTIFICARE</a>';
                        echo '</li>';
                        echo '<hr class="text-light my-1 d-md-none">';
                        echo '<li class="nav-item ">';
                        echo '      <a class="nav-link px-3" href="/inregistrare">ÎNREGISTRARE</a>';
                        echo '</li>';
                    }
                ?>
            </ul>
        </div>
    </div>
</nav>