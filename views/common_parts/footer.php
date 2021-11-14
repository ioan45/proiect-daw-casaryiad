<?php include_once "__DIR__/../../models/get_contact_and_active_details.php" ?>

<footer>
    <div id="Contact">
        <p>CONTACT</p>
        <table class="footer_table">
            <tr>
                <th>Adresă: </th>
                <td><?= $Adresa[0] ?>,<br><?= $Adresa[1] ?></td>
            </tr>
            <tr>
                <th>Telefon: &nbsp;</th>
                <td><?= $Telefon ?></td>
            </tr>
            <tr>
                <th>Fax:</th>
                <td><?= $Fax ?></td>
            </tr>
            <tr>
                <th>E-mail:</th>
                <td><?= $Email ?></td>
            </tr>
        </table>
    </div>
    <div id="Program">
        <p>PROGRAM</p>
        <table class="footer_table">
            <tr>
                <td><?= $Zile[0] ?> &nbsp;</td>
                <td><?= $Ore[0] ?></td>
            </tr>
            <tr>
                <td><?= $Zile[1] ?> </td>
                <td><?= $Ore[1] ?></td>
            </tr>
        </table>
    </div>
</footer>