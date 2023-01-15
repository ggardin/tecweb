<?php

require_once("php/tools.php");
require_once("php/database.php");

if (! isset($_SESSION["id"])) {
	header ("location: login.php");
	exit();
}

try {
	$connessione = new Database();
	$username = $connessione->getUsernameByUtenteId($_SESSION["id"]);
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

$page = Tools::buildPage($_SERVER["SCRIPT_NAME"]);

Tools::toHtml($username[0]["username"]);
Tools::replaceAnchor($page, "username", $username[0]["username"]);

if ($_SESSION["is_admin"] == 0) Tools::replaceSection($page, "admin", "");

Tools::showPage($page);

?>