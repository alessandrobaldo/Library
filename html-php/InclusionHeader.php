<?php
$uri=$_SERVER["REQUEST_URI"];
$array=preg_split("/\//",$uri);
if(preg_match("/(InclusionHeader.php)/",$array[sizeof($array)-1])){
    echo "Errore, non puoi accedere a questo elemento";
}
else{
    ?>
<div id="header">
    <h1 id="titolo">RentBook</h1>
</div>
<?php
}
?>


