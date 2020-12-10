<?php
$session = true;

if (session_status() === PHP_SESSION_DISABLED)
    $session = false;
else if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
    $errorquery1="";
    $errorquery2="";
    $errordb1="";
    $errordb2="";
    $success="";
    $errorreq="";
    $errorpreg="";


    if(isset($_REQUEST["libri"])) {
        //Andrebbe fatto foreach ma ho una sola chiave
        $idbook = key($_REQUEST["libri"]);
        $counter=0;
        $counter2=0;
        foreach($_REQUEST["libri"] as $value){
            if($value=="Restituisci")
                $counter++; //controllo se vengono aggiunti altri parametri alla query stirng
            if($value!="Restituisci")
                $counter2++; //Controllo per parametri diversi da restituisci

        }

        error_reporting(0);
        $conn = mysqli_connect("localhost", "uReadOnly", "posso_solo_leggere", "biblioteca");
        if (!mysqli_connect_errno()) {
            $query = "SELECT COUNT(*) FROM books WHERE prestito=? AND id=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "si", $_SESSION["username"], $idbook);
            $result = mysqli_stmt_execute($stmt);
            if ($result) {
                mysqli_stmt_bind_result($stmt, $qty);
                mysqli_stmt_fetch($stmt);


            } else {//PROBLEMI NELL'ELABORAZIONE DELLA QUERYs
                $errorquery1 = "<h3>Si è verificato un errore, siamo spiacenti. Errore: " . mysqli_error($conn) . "</h3>";
            }
            mysqli_stmt_close($stmt);


        } else {//CONNESSIONE FALLITA
            $errodb1 = "<h3>Errore Connessione al DBMS: " . mysqli_connect_error() . "</h3>";
        }
        //DEVO CONTROLLARE CHE L'ID NON VENGA MANOMESSO (SIA UN NUMERO), CHE CI SIA IL RIFERIMENTO A UN SOLO LIBRO (VOCE RESTITUISCI)
        //NESSUN RIFERIMENTO AD ALTRO E CHE ALL'ID (EVENTUALMENTE MANOMESSO) SIA ASSoCIATO UN LIBRO
        if(preg_match('/^\d{1,}$/', $idbook) && $counter===1 && $counter2===0 && $qty===1){
            //CALCOLO LA DURATA DEL PRESTITO E L'INIZIO
            if (!mysqli_connect_errno()) {
                $query = "SELECT day(data),month(data),year(data),hour(data),minute(data),second(data) FROM books WHERE prestito=? AND id=?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "si", $_SESSION["username"], $idbook);
                $result = mysqli_stmt_execute($stmt);
                if ($result) {
                    mysqli_stmt_bind_result($stmt, $day, $month, $year, $hour, $minute, $second);
                    mysqli_stmt_fetch($stmt);
                    $inizioprestito = mktime(intval($hour), intval($minute), intval($second), intval($month), intval($day), intval($year));
                    $datainizioprestito=new DateTime(date("Y-m-d H:i:s",$inizioprestito));
                    $dataodierna= new DateTime(date("Y-m-d H:i:s",time()));

                    $durataprestito=$datainizioprestito->diff($dataodierna);

                } else {//PROBLEMI NELL'ELABORAZIONE DELLA QUERYs
                    $errorquery1 = "<h3>Si è verificato un errore, siamo spiacenti. Errore: " . mysqli_error($conn) . "</h3>";
                }
                mysqli_stmt_close($stmt);

                mysqli_close($conn);
            } else {//CONNESSIONE FALLITA
                $errodb1 = "<h3>Errore Connessione al DBMS: " . mysqli_connect_error() . "</h3>";
            }

            //AGGIORNO IL DB
            $conn = mysqli_connect("localhost", "uReadWrite", "SuperPippo!!!", "biblioteca");
            if (!mysqli_connect_errno()) {
                $query = "UPDATE books SET prestito='', giorni=0, data='0000-00-00 00:00:00' WHERE id=?";
                $stmt = mysqli_prepare($conn, $query);
                $id = key($_REQUEST["libri"]);
                mysqli_stmt_bind_param($stmt, "i", $id);
                $result = mysqli_stmt_execute($stmt);
                if ($result) {
                    $success = "<h3>Restituzione avvenuta con successo. Il prestito del libro è avvenuto dal " . $day . "/" . $month . "/" . $year . "<br>Durata del prestito: " . ($durataprestito)->format('%a') . " giorni, ". ($durataprestito)->format('%h') ." ore,". ($durataprestito)->format('%i') ." minuti,". ($durataprestito)->format('%s') ." secondi.<br>Torna al <a href='Libri.php'>profilo</a></h3> ";
                    $_SESSION["numLibri"]--;
                } else {
                    $errorquery2 = "<h3>Si è verificato un errore, siamo spiacenti. Errore: " . mysqli_error($conn) . "</h3>";

                }
            } else {//CONNESSIONE FALLITA
                $errordb2 = "<h3>Errore Connessione al DBMS: " . mysqli_connect_error() . "</h3>";
            }
        }
        else{
            $errorpreg="<h3>Errore nel formato dei dati, si prega di riprovare tornando <a href='Libri.php'>indietro</a></h3>";
        }

    }
    else{//$_REQUEST NON SETTATA
        $errorreq="<h3>Errore nel trasferimento dei parametri, si prega di riprovare tornando <a href='Libri.php'>indietro</a></h3>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" >
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
    <div id="infogenerali">
        <?php
        if($success!=="")
            echo $success;
        if($errordb1!=="")
            echo $errodb1;
        if($errordb2!=="")
            echo $errordb2;
        if($errorquery1!=="")
            echo $errorquery1;
        if($errorquery2!=="")
            echo $errorquery2;
        if($errorreq!=="")
            echo $errorreq;
        if($errorpreg!=="")
            echo $errorpreg;

    ?>
    </div>
    <?php
    include("InclusionFooter.php");
}
?>
</body>
</html>
