<?php
$session = true;


if (session_status() === PHP_SESSION_DISABLED)
    $session = false;
elseif (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if(!isset($_SESSION["username"])){
    $_SESSION["error"]=true;
    header("Location: Login.php");
}
else {
    $_SESSION[] = array();

    $params = session_get_cookie_params();
    setcookie(session_name(), "", time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);

    session_destroy();
    header("Location: Login.php");
}
?>