<?php
$session = true;

if (session_status() === PHP_SESSION_DISABLED)
    $session = false;
elseif (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Libreria online">
    <meta name="author" content="Alessandro Baldo">
    <meta name="viewport" content="width=device-width">
    <title>New RentBook</title>
    <link rel="stylesheet" type="text/css" href="stile.css">
    <script src="funzioni.js"></script>
</head>
<body>
<?php
if(!$session){
    echo "<p>SESSIONI DISABILITATE!!!</p>\n";
}
else {

    include("InclusionParametriLogin.php");
    include("InclusionHeader.php");
    ?>
    <div id="menu">
        <ul class="elementoMenu">
            <a href="Home.php"><li class="elementoMenu" id="HOME"><h2>HOME</h2></li></a>
            <?php
            if (!isset($_SESSION["username"])) {
                echo "<a href='Login.php'><li class='elementoMenu' id='LOGIN'><h2>LOGIN</h2></li></a>";
            } else {
                echo "<li class='elementoMenu' id='LOGIN' onclick='msgErrLogin()'><h2>LOGIN</h2></li>";
            }
            ?>

            <li class="elementoMenu" id="hereC"><h2>NEW</h2></li>
            <a href="Libri.php"><li class="elementoMenu" id="LIBRI"><h2>LIBRI</h2></li></a>
            <?php
            if (isset($_SESSION["username"])) {
                echo "<a href='Logout.php'><li class='elementoMenu' id='LOGOUT'><h2>LOGOUT</h2></li></a>";
            } else {
                echo "<li class='elementoMenu' id='LOGOUT' onclick='msgErrLogout()'><h2>LOGOUT</h2></li>";
            }
            ?>

        </ul>
    </div>
    <div id="signin">
        <h1>Registrazione Utente</h1>
        <fieldset id="loginSet">
            <form name="sign" method="post" action="EsitoRegistrazione.php" onsubmit="return controllaCredenziali(username.value,pwd.value, pwd2.value)">
                <input type="text" class="input" name="username" placeholder="Username"><br>
                <input type="password" class="input" name="pwd" placeholder="Password"><br>
                <input type="password" class="input" name="pwd2" placeholder="Conferma Password"><br>
                <input type="submit" name="registra" class="button" value="Registrami">


            </form>
        </fieldset>

    </div>
    <?php
    include("InclusionFooter.php");
}
?>
</body>
</html>