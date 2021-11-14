<!DOCTYPE html>
<html lang="ro">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css\bootstrap.min.css">
        <link rel="stylesheet" href="css\common.css">
        <link rel="stylesheet" href="css\about.css">
        <title>Despre proiect</title>
    </head>
    <body>
        <img id="fundal" src="../images/main6.jpg" alt="main_img" class="position-fixed">
        
        <?php include_once "common_parts/header_and_nav.html" ?>

        <section class="container-sm bg-light p-4">
            <h2>Dezvoltarea aplicatiilor web in PHP</h2><br>
            <p class="fs-4">
               Nume: Culea Ionel-Alexandru<br>
               Grupa: 234<br>
               Tema aleasa: Organizare de evenimente
            </p><br>
            <p class="fs-5">
                &emsp;&emsp; Se prezinta un site al unui restaurant ce ofera posibilitatea clientilor de a rezerva restaurantul pentru a organiza
                evenimente de tipul Nunta, Botez, Petreceri private sau Evenimente Corporate. Pe langa o scurta prezentare a acestora,
                clientii au la dispozitie si un formular prin care pot transmite date cu privire la rezervarea dorita.
            </p>
            <p class="fs-5">
                &emsp; Momentan, aplicatia web ofera doar o interfata de vizitator, prin urmare, intreg continutul este accesibil oricui.
                Aplicatia web este planificata astfel incat sa aiba 3 roluri: vizitator, client, administrator. Vizitatorul va putea 
                doar sa priveasca ceea ce ofera restaurantul. Clientul, pe langa capacitatile unui vizitator, va putea sa trimita formularul
                cu privire la organizarea de eveniment; Astfel, anumite date din formular vor fi deja completate (nume, prenume, e-mail, telefon).
                Administratorul are posibilitatile clientului si poate face modificari asupra bazei de date (insert, update, delete).
            </p>
            <p class="fs-5">
                Diagrama Entitate-Relatie a bazei de date:
            </p>
            <img id="diagrama" src="other_resources/diagrama-er.jpg" class="img-fluid"><br>
            <p class="fs-5">
                &emsp;Baza de date este compusa din 5 tabele. Dupa cum se observa, organizarile de evenimente sunt facute doar de clienti, iar
                pentru a fi client este necesara pozitia de utilizator, acesta din urma fiind o persoana ce s-a inregistrat (Aplicatia web
                va oferi posibilitatea crearii de conturi utilizator). Pe langa partea de evenimente, baza de date retine si datele de contact
                ale restaurantului impreuna cu meniul oferit.
            </p>
            <p class="fs-5">
                &emsp;Aplicatia web este asa construita astfel incat sa preia informatiile legate de meniu, anunturi, datele de contact si alte 
                informatii legate de clienti din baza de date. Clientul va putea interactiona cu baza de date prin completarea formularului
                pentru organizarea de evenimente. Administratorul va putea, printr-o interfata anumita, sa modifice baza de date daca este nevoie,
                dar si sa actualizeze sectiunea de anunturi.
            </p>
            <p class="fs-5">
                &emsp;Codul sursa al site-ului presupune impartirea codului in 3 categorii: partea de pagini afisate clientilor, partea de procesare
                ce va oferi paginilor o buna parte din informatiile de afisat si va rula diverse functionalitati, si partea de control ce va 
                lansa in executie partea de procesare si afisarea paginilor.
            </p>
            <p class="fs-5">
                &emsp;Planuind o astfel de implementare, momentan site-ul nu are un fisier de index, prin urmare site-ul se acceseaza de la adresa /home.
                Pe viitor se va implementa acest lucru si, posibil, se vor face si alte modificari.
            </p>

        </section>

        <script src="js\bootstrap.min.js"></script>
    </body>
</html>