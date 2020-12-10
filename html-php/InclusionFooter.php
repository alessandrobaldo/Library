
<?php
$uri=$_SERVER["REQUEST_URI"];
$array=preg_split("/\//",$uri);
if(preg_match("/(InclusionFooter.php)/",$array[sizeof($array)-1])){
    echo "Errore, non puoi accedere a questo elemento";
}
else{
    ?>
    <div id="footer">
        <p>Autore: Baldo Alessandro</p>
        <p>Email: s236651@studenti.polito.it</p>
        <p>Nome File:
            <?php
            echo basename($_SERVER["PHP_SELF"]);
            ?></p>
    </div>

    <?php
}
?>