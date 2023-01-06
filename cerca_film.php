<?php

$tipo = "film";
$f_nome = (isset($_GET["fn"])) ? $_GET["fn"] : "";
$f_val = (isset($_GET["fv"])) ? $_GET["fv"] : "";

include ("cerca.php");

?>