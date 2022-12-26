<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

if (! isset($_SESSION["id"]) || $_SESSION["is_admin"] == 0) {
	header ("location: user.php");
	exit();
}

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

/*
if post id :
	breadcrumb: film > nome > modifica
	dati: compila
	button: modifica
else :
	breadcrumb: film > aggiungi
	button: aggiungi

*/

Tools::showPage($page);

?>