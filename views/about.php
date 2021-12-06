<!DOCTYPE html>
<html lang="ro">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/views/css/bootstrap.min.css">
        <link rel="stylesheet" href="/views/css/common.css">
        <link rel="stylesheet" href="/views/css/about.css">
        <title>Despre proiect</title>
    </head>
    <body>
        <img id="fundal" src="/images/main6.jpg" alt="main_img" class="position-fixed">
        
        <?php include_once "views/common_parts/header_and_nav.php" ?>

        <section class="container-sm bg-light p-4">
            <h2>Dezvoltarea aplicatiilor web in PHP</h2><br>
            <p class="fs-4">
               Nume: Culea Ionel-Alexandru<br>
               Grupa: 234<br>
               Tema aleasa: Organizare de evenimente
            </p><br>
            <p class="fs-5">
                &emsp;&emsp; Se prezinta un site al unui restaurant ce ofera clientilor posibilitatea de a organiza evenimente de tipul 
                Nunta, Botez, Petreceri private sau Evenimente Corporate intr-un spatiu special amenajat. Pe langa o scurta prezentare a 
                acestora, clientii au la dispozitie si un formular prin care pot transmite date cu privire la rezervarea dorita.
            </p>
            <p class="fs-5">
                &emsp;Interfata client este compusa din cateva pagini de prezentare, pagini corespunzatoare mecanismului de autentificare/inregistrare 
                si o pagina a unui formular de contact. La accesarea site-ului se prezinta
                o sectiune de anunturi si o scurta promovare a restaurantului. Mai departe, se poate naviga catre listarea meniului oferit sau catre 
                prezentarea evenimentelor. La finalul paginii dedicate evenimentelor se afla un buton ce trimite catre formular. La finalul celor mai multe 
                pagini se afla informatiile de contact ale restaurantului si programul de lucru.
            </p>
            <p class="fs-5">
                &emsp;Aplicatia web este planificata astfel incat sa aiba 3 roluri: vizitator, client, administrator. Vizitatorul va putea 
                doar sa priveasca ceea ce ofera restaurantul. Clientul, pe langa posibilitatile unui vizitator, va putea sa trimita formularul
                cu privire la organizarea de eveniment. Astfel, anumite date din formular vor fi deja completate (nume, prenume, e-mail, telefon).
                Administratorul nu va putea trimite acel formular, dar va putea face modificari asupra bazei de date (select, insert, update, delete).
                Rolul de client va putea fi accesibil doar prin intermediul unui cont de utilizator creat printr-o functie de Inregistrare ce va fi
                oferita de aplicatie (ulterior va fi necesara doar autentificarea). Rolul de administrator este valabil doar prin anumite conturi, 
                deja existente in baza de date.
            </p>
            <p class="fs-5">
                Diagrama Entitate-Relatie a bazei de date (baza de date corespunde doar aplicatiei web, nu a intregului restaurant):
            </p>
            <img id="diagrama" src="/images/diagrama-er.png" class="img-fluid"><br>
            <p class="fs-5">
                &emsp;Baza de date este compusa din 5 tabele. Dupa cum se observa, organizarile de evenimente sunt facute doar de clienti, iar
                pentru a fi client este necesara pozitia de utilizator, acesta din urma fiind o persoana ce s-a inregistrat. Un client va putea organiza
                mai multe evenimente, iar un cont de utilizator poate sa corespunda cel mult unui client (0 in cazul administratorului). Starea unui cont
                de utilizator poate fi ACTIV sau STERS (conturile doar se marcheaza ca sterse, nu se sterg efectiv din baza de date). Conturile de administrator 
                nu pot fi setate ca sterse din interfata web. Pentru fiecare utilizator se retine si activitatea avuta. Tipul de activitate poate fi CONT_CREAT,  
                LOGIN, LOGOUT sau CONT_STERS. Pe langa acestea, baza de date va retine si meniul oferit de restaurant.
            </p>
            <p class="fs-5">
                &emsp;Formularul specific rezervarilor de evenimente este doar o solicitare (initial, atributul 'stare' este setat ca ASTEPTARE).
                Mai multe solicitari pot fi facute pentru aceeasi data, totusi doar una va fi definitiva, celelalte vor fi invalidate. 
                Reprezentantii restaurantului vor contacta clientul pentru a cere confirmarea evenimentului (in caz afirmativ, atributul 'stare' devine CONFIRMATA) 
                sau pentru a-i da un raspuns negativ cu privire la solicitare. (atributul 'stare' devine DATA_OCUPATA). Clientii au si posibilitatea de a anula
                evenimentul stabilit (atributul 'stare' devine ANULATA).
            </p>
            <p class="fs-5">
                &emsp;Aplicatia web este planificata astfel incat informatiile legate de anunturi, datele de contact si programul de lucru sunt preluate 
                din fisiere text de pe server, iar datele legate de meniu si utilizatori sunt accesate din baza de date. Clientul va putea interactiona cu 
                baza de date prin completarea formularului pentru organizarea de evenimente. Administratorul va putea, printr-o interfata anumita, sa 
                modifice baza de date, daca este nevoie, dar si sa actualizeze sectiunea de anunturi.
            </p>
            <p class="fs-5">
                &emsp;Codul sursa este impartit in 3 categorii: partea de pagini afisate clientului, partea de procesare ce se va ocupa de datele primite
                de la client si de datele ce trebuie afisate clientului, si partea de control ce va coordona procesarea datelor si va stabili ce se va afisa.
            </p>
            <p class="fs-5">
                &emsp;Pe viitor, posibil, se vor face si alte modificari.
            </p>
        </section>

        <script src="/views/js/bootstrap.min.js"></script>
    </body>
</html>