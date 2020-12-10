<?php
$session = true;

if (session_status() === PHP_SESSION_DISABLED)
    $session = false;
else if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();

    $erroreformato="";
    $erroreconn="";
    $errorequery="";
    $erroreparam="";
if((isset($_REQUEST["username"]) || isset($_REQUEST["pwd"])) && !isset($_SESSION["username"])) {

    if(isset($_REQUEST["username"]) && isset($_REQUEST["pwd"])) {
        $username = $_REQUEST["username"];
        $password = $_REQUEST["pwd"];
        if (preg_match('/^[a-zA-Z0-9%]{3,6}$/', $username) && preg_match('/^[a-zA-Z%]/', $username) && preg_match('/[a-zA-Z%]/', $username) && preg_match('/[0-9]/', $username) &&
            preg_match('/^[a-zA-Z]{4,8}$/', $password) && preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password)) {
            error_reporting(0);
            $conn = mysqli_connect("localhost", "uReadOnly", "posso_solo_leggere", "biblioteca");
            if (!mysqli_connect_errno()) {
                $query = "SELECT COUNT(*) FROM users WHERE username=? AND pwd=?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "ss", $_REQUEST["username"], $_REQUEST["pwd"]);
                $result = mysqli_stmt_execute($stmt);
                if ($result) {
                    mysqli_stmt_bind_result($stmt, $ok);
                    mysqli_stmt_fetch($stmt);
                }

                if ($ok == 1) {


                    //Nuovo Utente che esiste in DB e fa Login
                    if (!isset($_SESSION["username"]))
                        $_SESSION["username"] = $_REQUEST["username"];


                    if (!isset($_SESSION["numLibri"]))
                        $_SESSION["numLibri"] = 0;
                    //Nuovo Utente che esiste in DB che fa Login e sovrascrive ultimo utente precedente
                    else if (isset($_SESSION["username"]) && $_SESSION["username"] !== $_REQUEST["username"]) {
                        $_SESSION["username"] = $_REQUEST["username"];

                        $_SESSION["numLibri"] = 0;
                    }

                    if (!isset($_COOKIE["username"])) {
                        $scadenza = time() + 172800; //48 ore
                        setcookie("username", $_REQUEST["username"], $scadenza);
                    } else {
                        $scadenza = time() + 172800; //48 ore
                        setcookie("username", $_REQUEST["username"], $scadenza);
                    }
                    header("Location: Libri.php");
                } else { //NON TROVATE CORRISPONDENZE NEL DB
                    echo "<script>window.alert('Non risulta che tu sia registrato. Per procedere alla registrazione visitare la sezione New');</script>";
                    $errorequery = "<noscript><h2 class='error'>Non risulta che tu sia registrato. Per procedere alla registrazione visitare la sezione <a href='New.php'>New</a></h2></noscript>";
                }

                mysqli_close($conn);
            } else {//ERRORE CONNESSIONE
                echo "<script>window.alert('Errore Connessione al DBMS: " . mysqli_connect_error() . "');</script>";

                $erroreconn = "<noscript><h2 class='error'>Errore Connessione al DBMS: " . mysqli_connect_error() . "</h2></noscript>";

            }
        } else {
            echo "<script>window.alert('Errore nel formato dei parametri trasmessi');</script>";

            $erroreformato = "<noscript><h2 class='error'>Errore nel formato dei parametri trasmessi</h2></noscript>";

        }
    }else{
        echo "<script>window.alert('Parametri mancanti');</script>";

        $erroreparam = "<noscript><h2 class='error'>Parametri mancanti</h2></noscript>";

    }
}

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Libreria online">
    <meta name="author" content="Alessandro Baldo">
    <meta name="viewport" content="width=device-width">
    <title>Login RentBook</title>
    <link rel="stylesheet" type="text/css" href="stile.css">
    <script src="funzioni.js"></script>
</head>
<body>
<?php
if(!$session){
}
else {


    include("InclusionParametriLogin.php");
    include("InclusionHeader.php");
    ?>
    <div id="menu">
        <ul class="elementoMenu">
            <a href="Home.php"><li class="elementoMenu" id="HOME"><h2>HOME</h2></li></a>
            <li class="elementoMenu" id="hereC" ><h2>LOGIN</h2></li>
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
    <div id="auth">

        <?php
        if(isset($_SESSION["error"]))
            if($_SESSION["error"]==true)
                echo "<h2 class='error'>Hai tentato di fare Logout illegalmente</h3>";
        if(!isset($_SESSION["username"])) {
            ?>
            <h1>Login Utente</h1>
            <fieldset id="loginSet">
                <form name="login" method="post" action="Login.php"
                      onsubmit="return controllaCredenziali(username.value, pwd.value)">
                    <input type="text" class="input" name="username"  placeholder="Username"
                           value="<?php
                           if (isset($_COOKIE['username']) && !isset($_REQUEST["reset"])) {
                               echo($_COOKIE['username']);
                           }
                           ?>"><br>
                    <input type="password" class="input" name="pwd" placeholder="Password"><br>
                    <?php
                    if(!isset($_SESSION["username"])) {
                    if($erroreformato!=="")
                        echo $erroreformato;
                    if($erroreconn!=="")
                        echo $erroreconn;
                    if($errorequery!=="")
                        echo $errorequery;
                    if($erroreparam!=="")
                        echo $erroreparam;
                    } ?>
                    <input type="submit" class="button" value="OK">
                </form>
                    <script>
                        document.write("<input type='button' class='button' value='Ripulisci' onclick='resetCredenziali(login)'>");
                    </script>
                    <noscript>
                        <form name="lognoscript" action="Login.php" method="get">
                            <input type="submit" class="button" value="Ripulisci" name="reset">
                        </form>
                    </noscript>


            </fieldset>
            <?php
        }
        else{
            ?>
            <p>Sei già loggato! Clicca <a href="Libri.php">qui</a> per andare alla pagina del tuo profilo personale</p>
        <?php

        }
        ?>
    </div>
    <?php
    include("InclusionFooter.php");
}
?>
</body>
</html>