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
    echo "<p>SESSIONI DISABILITATE!!!</p>";
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
            <a href="New.php"><li class="elementoMenu" id="NEW" ><h2>NEW</h2></li></a>

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
        <?php
        if(isset($_REQUEST["username"]) && isset($_REQUEST["pwd"])) {

                $username = $_REQUEST["username"];
                $pwd = $_REQUEST["pwd"];
                $pwd2=$_REQUEST["pwd2"];

            if (preg_match('/^[a-zA-Z0-9%]{3,6}$/', $username) && preg_match('/^[a-zA-Z%]/', $username) && preg_match('/[a-zA-Z%]/', $username) && preg_match('/[0-9]/', $username) &&
                preg_match('/^[a-zA-Z]{4,8}$/', $pwd) && preg_match('/[a-z]/', $pwd) && preg_match('/[A-Z]/', $pwd) &&  $pwd2===$pwd) {

                error_reporting(0);
                $conn = mysqli_connect("localhost", "uReadWrite", "SuperPippo!!!", "biblioteca");
                if (!mysqli_connect_errno()) {

                    $query = "INSERT INTO users(username,pwd) VALUES (?,?)";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "ss", $username, $pwd);
                    $result = mysqli_stmt_execute($stmt);

                    if ($result) {

                        echo "<h3>Registrazione avvenuta con successo.</h3>";
                    }
                    else {
                        echo "<h3>Si è verificato un errore, siamo spiacenti. Errore: " . mysqli_error($conn) . "</h3>";
                        $query = "SELECT COUNT(*) FROM users WHERE username=?";
                        $stmt = mysqli_prepare($conn, $query);
                        mysqli_stmt_bind_param($stmt, "s", $username);
                        $result = mysqli_stmt_execute($stmt);

                        if ($result) {
                            mysqli_stmt_bind_result($stmt, $ok);
                            mysqli_stmt_fetch($stmt);
                        }

                        if ($ok == 1) {
                            echo "<h3>Username già esistente, si prega di riprovare tornando <a href='New.php'>indietro</a></h3>";
                        }

                    }


                    mysqli_stmt_close($stmt);

                    mysqli_close($conn);
                } else {//ERRORE CONNESSIONE DB
                    echo "<h3>Errore Connessione al DBMS: " . mysqli_connect_error() . "si prega di riprovare tornando <a href='New.php'>indietro</a></h3>";
                }
            }
            else{//FORMATO PARAMETRI SBAGLIATO PERCHE' MANIPOLATI
                echo "<h3>Errore nel formato dei parametri, si prega di riprovare tornando <a href='New.php'>indietro</a></h3>";

            }

        } else {//$_REQUEST NON SETTATA
            echo "<h3>Errore nel trasferimento dei parametri, si prega di riprovare tornando <a href='New.php'>indietro</a></h3>";
        }


        ?>

    </div>
    <?php
    include("InclusionFooter.php");
}
?>
</body>
</html>
