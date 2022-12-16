<?php

require_once("php/tools.php");

session_start();
$last = (isset($_SESSION["last"]) ? $_SESSION["last"] : "index.php");

session_unset();
session_destroy();

header("refresh:5; url=" . $last);
echo ("Ritorno in 5 secondi.");

?>
