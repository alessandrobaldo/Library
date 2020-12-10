
<?php
$uri=$_SERVER["REQUEST_URI"];
$array=preg_split("/\//",$uri);
if(preg_match("/(InclusionParametriLogin.php)/",$array[sizeof($array)-1])){
    echo "Errore, non puoi accedere a questo elemento";
}
else{
    ?>
    <div id="parametriLogin">
        <p><?php
            if (isset($_SESSION["username"]) && isset($_SESSION["numLibri"])) {
                echo "".$_SESSION['username'];
                ?>

                <img src="icon_people_white.png" alt="img">
                <?php
                echo "<br>";
                echo "Numero Libri: ".$_SESSION['numLibri'];
            } else {
                echo " <p>ANONIMO";
                ?>
                <img src="icon_people_white.png" alt="img">
                <?php
                echo "<br> Numero Libri: 0</p>";
            } ?></p>
    </div>

    <?php
}
?>