<footer>
    <div id="Contact">
        <p>CONTACT</p>
        <table class="footer_table">
            <tr>
                <th>Adresă: </th>
                <td>
                    <?= $footerContact["Address"][0] ?><br>
                    <?= $footerContact["Address"][1] ?>
                </td>
            </tr>
            <tr>
                <th>Telefon: &nbsp;</th>
                <td><?= $footerContact["Phone"] ?></td>
            </tr>
            <tr>
                <th>Fax:</th>
                <td><?= $footerContact["Fax"] ?></td>
            </tr>
            <tr>
                <th>E-mail:</th>
                <td><?= $footerContact["Email"] ?></td>
            </tr>
        </table>
    </div>
    <div id="Program">
        <p>PROGRAM</p>
        <table class="footer_table">
            <tr>
                <td><?= $footerProgram["Days"][0] ?> &nbsp;</td>
                <td><?= $footerProgram["Hours"][0] ?></td>
            </tr>
            <tr>
                <td><?= $footerProgram["Days"][1] ?> </td>
                <td><?= $footerProgram["Hours"][1] ?></td>
            </tr>
        </table>
    </div>
</footer>