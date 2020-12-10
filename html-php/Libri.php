<?php
$session = true;

if (session_status() === PHP_SESSION_DISABLED)
    $session = false;
elseif (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
    $erroreconn1="";
    $erroreconn2="";
    $erroreformato="";
    $errorenumgiorni="";
    $errorenumlibri="";
    $nessunparametro="";
    if(isset($_SESSION["username"])){
        error_reporting(0);
        $conn = mysqli_connect("localhost", "uReadOnly", "posso_solo_leggere", "biblioteca");
        if(!mysqli_connect_errno()){

            $query="SELECT id, autori, titolo FROM books WHERE prestito=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $_SESSION["username"]);
            mysqli_stmt_execute($stmt);


            $i=0;
            mysqli_stmt_bind_result($stmt, $id, $autore, $titolo);


            while (mysqli_stmt_fetch($stmt)) {
                $i++;
            }

            mysqli_stmt_close($stmt);
            mysqli_close($conn);

            $_SESSION["numLibri"]=$i;

        }
        else{
            echo "<script>window.alert('Errore Connessione al DBMS: ".mysqli_connect_error()."');</script>";
            $erroreconn1="<noscript><h2 class='error'>Errore Connessione al DBMS: " . mysqli_connect_error()."</h2></noscript>";

        }

        if(isset($_REQUEST["numgiorni"]) ) {
            if (isset($_REQUEST["books"])) {
                $books = $_REQUEST["books"];
                $num = 0;
                $qty = 0;

                foreach ($books as $key => $value) {
                    $conn = mysqli_connect("localhost", "uReadOnly", "posso_solo_leggere", "biblioteca");

                    $num++;
                    if (!mysqli_connect_errno()) {

                        $query = "SELECT COUNT(*) FROM books WHERE prestito='' AND id=?";
                        $stmt = mysqli_prepare($conn, $query);

                        mysqli_stmt_bind_param($stmt, "i", $key);
                        $result = mysqli_stmt_execute($stmt);

                        if ($result) {
                            mysqli_stmt_bind_result($stmt, $qty2);
                            mysqli_stmt_fetch($stmt);
                            $qty += $qty2;
                        }


                        mysqli_stmt_close($stmt);
                    }
                    mysqli_close($conn);

                }


                //DEVO CONTROLLARE IL FORMATO DEL NUMERO DEI GIORNI (ACCETTABILE SE L'UTENTE INSERISCE DEGLI SPAZI) OLTRE CHE L'ESISTENZA DI UN LIBRO NON IN PRESTITO ALL'ID ASSOCIATO
                if (preg_match('/\d{1,}$/', trim($_REQUEST["numgiorni"])) && $qty == $num) {
                    $giorni=trim($_REQUEST["numgiorni"]);

                    if ($giorni > 0) {

                        if (sizeof(($books)) + $_SESSION["numLibri"] <= 3) {


                            $now = time();
                            $conn = mysqli_connect("localhost", "uReadWrite", "SuperPippo!!!", "biblioteca");
                            if (!mysqli_connect_errno()) {
                                foreach ($books as $key => $value) {
                                    $query = "UPDATE books SET prestito=?,data=FROM_UNIXTIME(?),giorni=? WHERE id=?";
                                    $stmt = mysqli_prepare($conn, $query);
                                    mysqli_stmt_bind_param($stmt, "siii", $_SESSION["username"], $now, $giorni, $key);
                                    $result = mysqli_stmt_execute($stmt);

                                    if ($result) {
                                        $_SESSION["numLibri"]++;
                                    }


                                    mysqli_stmt_close($stmt);
                                }


                                mysqli_close($conn);
                            } else {
                                echo "<script>window.alert('Errore Connessione al DBMS: " . mysqli_connect_error() . "');</script>";
                                $erroreconn2="<noscript><h2 class='error'>Errore Connessione al DBMS: " . mysqli_connect_error()."</h2></noscript>";

                            }
                        } else {//NUMERO DI LIBRI SELEZIONATI MAGGIORE DI 3
                            echo "<script>window.alert('Puoi avere in prestito al massimo 3 libri');</script>";
                            $errorenumlibri="<noscript><h2 class='error'>Puoi avere attivi al massimo 3 libri</h2></noscript>";

                        }
                    } else {//NUMERO DI GIORNI <=0
                        echo "<script>window.alert('Il numero di giorni da inserire deve essere positivo');</script>";
                        $errorenumgiorni="<noscript><h2 class='error'>Hai inserito un numero di giorni non valido</h2></noscript>";

                    }
                } else {//MODIFICA DEI DATI NELLA URL
                    echo "<script>window.alert('Errore nel formato dei parametri, si prega di riprovare');</script>";
                    $erroreformato="<noscript><h2 class='error'>Errore nel formato dei parametri trasmessi</h2></noscript>";

                }

            } else {
                echo "<script>window.alert('Per procedere devi selezionare almeno un libro');</script>";
                $nessunparametro="<noscript><h2 class='error'>Non hai selezionato nessun libro, riprova selezionandone uno</h2></noscript>";

            }


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
    <title>Libri RentBook</title>
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
                echo "<a href='Login.php'><li class='elementoMenu' id='LOGIN' ><h2>LOGIN</h2></li></a>";
            } else {
                echo "<li class='elementoMenu' id='LOGIN' onclick='msgErrLogin()'><h2>LOGIN</h2></li>";

                 }
            ?>
            <a href="New.php"><li class="elementoMenu" id="NEW"><h2>NEW</h2></li></a>

            <li class="elementoMenu" id="hereC"><h2>LIBRI</h2></li>
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

        if(isset($_SESSION["username"])){//SONO LOGGATO

            if($errorenumlibri!=="")
                echo $errorenumlibri;
            if($errorenumgiorni!=="")
                echo $errorenumgiorni;
            if($erroreconn1!=="")
                echo $erroreconn1;
            if($erroreconn2!=="")
                echo $erroreconn2;
            if($erroreformato!=="")
                echo $erroreformato;
            if($nessunparametro!=="")
                echo $nessunparametro;
            echo "<h1>Profilo Utente</h1>";
            $conn = mysqli_connect("localhost", "uReadOnly", "posso_solo_leggere", "biblioteca");
            if(!mysqli_connect_errno()){
                //QUERY SEZIONE 1:LIBRI IN PRESTITO
                $query="SELECT COUNT(*) FROM books WHERE prestito=?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $_SESSION["username"]);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $i);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);


                $query="SELECT id, autori, titolo FROM books WHERE prestito=?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $_SESSION["username"]);
                mysqli_stmt_execute($stmt);

                echo "<section class='books'><h2>Elenco dei libri in prestito</h2>";

                mysqli_stmt_bind_result($stmt, $id, $autore, $titolo);
                echo "<form name='restituzione' method='get' action='Restituzione.php'>";
                if($i==0){//SE NON C'E' NESSUN LIBRO
                    echo "<p>Nessun libro in prestito al momento</p>";
                }
                else {
                    echo "<table><tr><th>ID</th><th>Autore</th><th>Titolo</th><th>Restituzione</th></tr>";

                    while (mysqli_stmt_fetch($stmt)) {

                        echo "<tr><td>" . $id . "</td><td>" . $autore . "</td><td>" . $titolo . "</td><td><input type='submit' class='button' name='libri[" . $id . "]' value='Restituisci'></td></tr>";
                    }
                    echo "</table>";
                }
                echo "</form>";
                mysqli_stmt_close($stmt);

                echo "</section>";

            }
            else{
                echo "<script>window.alert('Errore Connessione al DBMS: ".mysqli_connect_error()."');</script>";
            }

            //QUERY 2: LIBRI TOTALI
            if(!mysqli_connect_errno()){

                $query="SELECT id,autori, titolo, prestito, day(data),month(data),year(data),hour(data),minute(data),second(data),giorni FROM books";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_execute($stmt);

                echo "<section class='books'><h2>Elenco dei libri disponibili</h2>";

                mysqli_stmt_bind_result($stmt,  $id,$autore, $titolo, $prestito, $day, $month, $year, $hour, $minute, $second, $durata);
                echo "<form name='prestito' method='get' action='Libri.php' onsubmit='return controllaDati(numgiorni.value, prestito)'>";
                echo "<table><tr><th>Autore</th><th>Titolo</th><th>Seleziona</th></tr>";

                while (mysqli_stmt_fetch($stmt)) {
                    if($prestito!=="") {

                        $scadenza = mktime(intval($hour), intval($minute), intval($second), intval($month), intval($day), intval($year)) + $durata * 24 * 3600;
                        $today = time();

                        if ($today > $scadenza) {//Prestito scaduto
                            echo "<tr><td>" . $autore . "</td><td>" . $titolo . "</td><td>SCADUTO</td>";
                        } else if ($today < $scadenza) {//In Prestito
                            echo "<tr><td>" . $autore . "</td><td>" . $titolo . "</td><td>IN PRESTITO</td>";
                        }
                    }
                    else{
                        echo "<tr><td>" . $autore . "</td><td>" . $titolo . "</td><td>Noleggio:<input type='checkbox' name='books[".$id."]' class='ckeck'></td>";


                    }

                }
                echo "</table>";
                echo "Inserisci il numero di giorni della durata del prestito:<input type='number' class='input' name='numgiorni' min='1' placeholder='Numero di giorni..'><input type='submit' class='button' value='Prestito'>";
                mysqli_stmt_close($stmt);
                echo "</form>";


                echo "</section>";
                mysqli_close($conn);
            }
            else{
                echo "<script>window.alert('Errore Connessione al DBMS: ".mysqli_connect_error()."');</script>";
            }

        }
        else{//NON SONO LOGGATO
            $conn = mysqli_connect("localhost", "uReadOnly", "posso_solo_leggere", "biblioteca");
            echo "<h1>Resoconto Biblioteca</h1>";
            if(!mysqli_connect_errno()){
                $query="SELECT COUNT(*) FROM books WHERE prestito=''";
                $stmt = mysqli_prepare($conn, $query);
                $result = mysqli_stmt_execute($stmt);
                if($result){
                    mysqli_stmt_bind_result($stmt, $numLibri);
                    mysqli_stmt_fetch($stmt);
                }
                echo "<section class='books'><h3>Numero di Libri Disponibili per il Prestito: ".$numLibri."</h3></section>";
                mysqli_stmt_close($stmt);


            }
            else{
                echo "<script>window.alert('Errore Connessione al DBMS: ".mysqli_connect_error()."');</script>";
            }

            if(!mysqli_connect_errno()){
                $query="SELECT COUNT(*) FROM books";
                $stmt = mysqli_prepare($conn, $query);
                $result = mysqli_stmt_execute($stmt);
                if($result){
                    mysqli_stmt_bind_result($stmt, $numLibri);
                    mysqli_stmt_fetch($stmt);
                }
                echo "<section class='books'><h3>Numero di Libri Totali: ".$numLibri."</h3></section>";


                mysqli_stmt_close($stmt);

                mysqli_close($conn);
            }
            else{
                echo "<script>window.alert('Errore Connessione al DBMS: ".mysqli_connect_error()."');</script>";
            }

            echo "<h4>Per affittare un libro, effettua prima il <a href='Login.php'>Login</a></h4>";

        }


        ?>


    </div>
    <?php
    include("InclusionFooter.php");
}
?>
</body>
</html>