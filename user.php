<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

if (! isset($_SESSION["id"])) {
	header ("location: login.php");
	exit();
}

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

$db_ok = false;
try {
	$connessione = new Database();
	$username = $connessione->getUsernameByUserId($_SESSION["id"]);
	$db_ok = true;
} catch (Exception $e) {
	Tools::replaceAnchor($page, "title", $e->getMessage());
	Tools::replaceSection($page, "main", ("<h1>" . $e->getMessage() . "</h1>"));
} finally {
	unset($connessione);
}
if ($db_ok) {
	Tools::replaceAnchor($page, "username", $username[0]["username"]);
	if ($_SESSION["is_admin"] == 0) Tools::replaceSection($page, "admin", "");
}

Tools::showPage($page);

?>