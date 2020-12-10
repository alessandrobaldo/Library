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
    <meta name="viewport" content="width=device-width" >
    <title>Home RentBook</title>
    <link rel="stylesheet" type="text/css" href="stile.css">
    <script src="funzioni.js"></script>
</head>
<body>
<?php
if(!$session){
    echo "<p>SESSIONI DISABILITATE!!!</p>\n";
}
else {
    ?>
    <?php
    include("InclusionParametriLogin.php");

    include("InclusionHeader.php");
    ?>
    <div id="menu">
        <ul class="elementoMenu">
            <li class="elementoMenu" id="hereC"><h2>HOME</h2></li>
            <?php
            if (!isset($_SESSION["username"])) {
                echo "<a href='Login.php'><li class='elementoMenu' id='LOGIN'><h2>LOGIN</h2></li></a>";
            } else {
                echo "<li class='elementoMenu' id='LOGIN' onclick='msgErrLogin()'><h2>LOGIN</h2></li>";

            }
            ?>
            <a href="New.php"><li class="elementoMenu" id="NEW"><h2>NEW</h2></li></a>

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
    <div id="infogenerali">
        <h1>Naviga il portale di RentBook per poter esaudire ogni tuo desiderio!</h1><br>
        <p>Puoi verificare fin da subito la <a href="Libri.php">disponibilit&agrave;</a> della nostra biblioteca<br>
            <a href="New.php">Registrati</a> se non lo hai ancora fatto o vai direttamente all'area di <a href="Login.php">Login</a></p><br>
        Tra i nostri titoli puoi trovare:
        <ul class="libro">

            <img src="Dante.png" class="images" alt="foto_divina_commedia">
            <li class="libro">La Divina Commedia di Dante</li><br>

            <img src="PromessiSposi.png" class="images" alt="foto_promessi">
            <li class="libro">I Promessi Sposi di A. Manzoni</li><br>

            <img src="CBook.png" class="images" alt="foto_kernighanritchie">
            <li class="libro">The C Programming Language di Kernighan & Ritchie</li><br>
        </ul>

        E molto altro ancora!
    </div>
    <?php
    include("InclusionFooter.php");
}
?>
</body>
</html>