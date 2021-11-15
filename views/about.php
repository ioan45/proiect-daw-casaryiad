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
                &emsp;&emsp; Se prezinta un site al unui restaurant ce ofera clientilor posibilitatea de a organiza evenimente de tipul 
                Nunta, Botez, Petreceri private sau Evenimente Corporate intr-un spatiu special amenajat. Pe langa o scurta prezentare a 
                acestora, clientii au la dispozitie si un formular prin care pot transmite date cu privire la rezervarea dorita.
            </p>
            <p class="fs-5">
                &emsp;Interfata client este compusa din cateva pagini de prezentare si o pagina a unui formular. La accesarea site-ului se prezinta
                o sectiune de anunturi si o scurta promovare a restaurantului. Mai departe, se poate naviga catre listarea meniului oferit sau catre 
                prezentarea evenimentelor. La finalul paginii dedicate evenimentelor se afla un buton ce trimite catre formular. La finalul fiecarei 
                pagini se afla informatiile de contact ale restaurantului si programul de lucru.
            </p>
            <p class="fs-5">
                &emsp; Momentan, aplicatia web ofera doar o interfata de vizitator, prin urmare, intreg continutul este accesibil oricui.
                Aplicatia web este planificata astfel incat sa aiba 3 roluri: vizitator, client, administrator. Vizitatorul va putea 
                doar sa priveasca ceea ce ofera restaurantul. Clientul, pe langa posibilitatile unui vizitator, va putea sa trimita formularul
                cu privire la organizarea de eveniment. Astfel, anumite date din formular vor fi deja completate (nume, prenume, e-mail, telefon).
                Administratorul va avea posibilitatile clientului (totusi nu este considerat client fiindca pot fi mai multe persoane pe contul 
                de admin., deci va trebui sa introduca date personale) si va putea face modificari asupra bazei de date (insert, update, delete).
                Rolul de client va putea fi accesibil doar prin intermediul unui cont de utilizator creat printr-o functie de Inregistrare ce va fi
                oferita de aplicatie (ulterior va fi necesara doar autentificarea). Rolul de administrator este valabil doar printr-un cont
                anumit, deja existent in baza de date.
            </p>
            <p class="fs-5">
                Diagrama Entitate-Relatie a bazei de date (baza de date corespunde doar aplicatiei web, nu a intregului restaurant):
            </p>
            <img id="diagrama" src="other_resources/diagrama-er.jpg" class="img-fluid"><br>
            <p class="fs-5">
                &emsp;Baza de date va fi compusa din 4 tabele. Dupa cum se observa, organizarile de evenimente sunt facute doar de clienti, iar
                pentru a fi client este necesara pozitia de utilizator, acesta din urma fiind o persoana ce s-a inregistrat. Un client va putea organiza
                mai multe evenimente, iar un cont de utilizator poate sa corespunda cel mult unui client (0 in cazul administratorului). Pe langa 
                acestea, baza de date va retine si meniul oferit.
            </p>
            <p class="fs-5">
                &emsp;Aplicatia web este planificata astfel incat informatiile legate de anunturi, datele de contact si programul de lucru sunt preluate
                din fisiere de pe server, iar datele legate de meniu si utilizatori sunt accesate din baza de date. Clientul va putea interactiona cu 
                baza de date prin completarea formularului pentru organizarea de evenimente. Administratorul va putea, printr-o interfata anumita, sa 
                modifice baza de date daca este nevoie, dar si sa actualizeze sectiunea de anunturi.
            </p>
            <p class="fs-5">
                &emsp;Codul sursa este impartit in 3 categorii: partea de pagini afisate clientului, partea de procesare ce va oferi paginilor o buna 
                parte din informatiile de afisat si va rula diverse functionalitati, si partea de control ce va lansa in executie procesarea datelor 
                si va stabili ce se va afisa.
            </p>
            <p class="fs-5">
                &emsp;Planuind o astfel de implementare, momentan site-ul nu are un script de index, prin urmare site-ul se acceseaza de la adresa /home.
                Pe viitor se va implementa acest lucru si, posibil, se vor face si alte modificari.
            </p><br>
            <h3>Asteptari vs Realitate</h3><br>
            <p class="fs-5">
                &emsp;Un lucru evident este ca am vrut sa implementez o rutare simpla a cererilor si nu am reusit in timp util. Ma documentasem legat de cum
                as putea sa realizez asta insa nu am si incercat, astfel ca aproape de deadline m-am trezit ca nu-mi reusea .htaccess, am gasit ca as
                avea probleme cu procesarea cailor de catre server, ceva legat de cai relative/absolute, totusi n-am mai avut timp sa incerc sa-l fac.
            </p>
            <p class="fs-5">
                &emsp;Ma gandisem sa am facuta deja si baza de date alaturi de cateva functionalitati precum meniul, dar mi-am supraestimat rapiditatea
                lasand lucrul asta pe ultima suta de metri.
            </p>

        </section>

        <script src="js\bootstrap.min.js"></script>
    </body>
</html>