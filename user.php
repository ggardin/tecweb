<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

if (! isset($_SESSION["id"])) {
	header ("location: login.php");
	exit();
}

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

try {
	$connessione = new Database();
	$username = $connessione->getUsernameByUserId($_SESSION["id"]);
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

Tools::replaceAnchor($page, "username", $username[0]["username"]);

if ($_SESSION["is_admin"] == 0) Tools::replaceSection($page, "admin", "");

Tools::showPage($page);

?>