<?php

require_once("php/tools.php");

session_start();
$_SESSION["last"] = $_SERVER["REQUEST_URI"];

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"), "std", true);

Tools::showPage($page);

?>
