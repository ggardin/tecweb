<?php

require_once("php/tools.php");
require_once("php/database.php");

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"), "auth");

$username = "";
$password = "";

$content = "";

if (isset($_POST["submit"])) {
	$username = $_POST["username"];
	$password = $_POST["password"];

	$db_ok = false;
	try {
		$connessione = new Database();
		$res = $connessione->login($username, $password);
		$db_ok = true;
	} catch (Exception $e) {
		$content .= "<p>" . $e->getMessage() . "</p>";
	} finally {
		unset($connessione);
	}
	if ($db_ok) {
		$content .= "<p>" . ($res ? "OK: boomer" : "ERRORE: Credenziali errate") . "</p>";
	}
}

Tools::replaceAnchor($page, "form_username", $username);
Tools::replaceAnchor($page, "form_password", $password);

Tools::replaceAnchor($page, "form_messages", $content);

Tools::showPage($page);

?>
