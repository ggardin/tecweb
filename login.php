<?php

require_once("php/tools.php");
require_once("php/database.php");

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"), "auth");

$username = "";
$password = "";

if (isset($_POST["submit"])) {
	$username = $_POST["username"];
	$password = $_POST["password"];

	$db_ok = false;
	try {
		$connessione = new Database();
		$res = $connessione->login($username, $password);
		$db_ok = true;
	} catch (Exception $e) {
		Tools::replaceAnchor($page, "message", $e->getMessage());
	} finally {
		unset($connessione);
	}
	if ($db_ok) {
		Tools::replaceAnchor($page, "message", ($res ? "Accesso eseguito" : "Errore: Credenziali errate"));
	}
} else
	Tools::replaceSection($page, "message", "");

Tools::replaceAnchor($page, "form_username", $username);
Tools::replaceAnchor($page, "form_password", $password);

Tools::showPage($page);

?>
