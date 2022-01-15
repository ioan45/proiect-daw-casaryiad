<!DOCTYPE html>
<html lang="ro">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/APIs/bootstrap/bootstrap.min.css">
        <link rel="stylesheet" href="/views/css/Common.css">
        <link rel="stylesheet" href="/views/css/Accessories.css">
        <title>Casa Ryiad</title>
    </head>
    <body>
        <img id="fundal" src="/images/main6.jpg" alt="main_img" class="img-fluid">
       
        <?php include_once "views/common_parts/header_and_nav.php" ?>

        <section class="accesorii container-sm">
            <form action="/accesorii" method="GET">
                <label for="min">Preț Minim: </label>
                <input id="min" type="number" min="<?=$minPrice?>" max="<?=$maxPrice?>" value="<?=$minGiven?>" name="minim"><br>
                <label for="max">Preț Maxim: </label>
                <input id="max" type="number" min="<?=$minPrice?>" max="<?=$maxPrice?>" value="<?=$maxGiven?>" name="maxim"><br>
                <input type="submit" value="Caută">
            </form>
            <div class="continut">
                <?=$content?>
            </div>
        </section>

        <?php include_once "views/common_parts/footer.php" ?>

        <script src="/APIs/bootstrap/bootstrap.min.js"></script>
    </body>
</html>