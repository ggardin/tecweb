<?php

require_once("php/tools.php");

session_start();

if (! isset($_SESSION["id"])) {
	header ("location: login.php");
	exit();
}

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

// deve essere dello stesso utente che la richiede

Tools::showPage($page);

?>