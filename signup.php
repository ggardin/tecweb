<?php

require_once("php/tools.php");
require_once("php/database.php");

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"), "auth");

$username = "";
$password = "";
$password_confirm = "";

$content = "";

if (isset($_POST["submit"])) {
	$username = $_POST["username"];
	$password = $_POST["password"];
	$password_confirm = $_POST["password_confirm"];

	if ($password == $password_confirm) {
		$db_ok = false;
		try {
			$connessione = new Database();
			$res = $connessione->signup($username, $password);
			$db_ok = true;
		} catch (Exception $e) {
			$content .= "<p>" . $e->getMessage() . "</p>";
		} finally {
			unset($connessione);
		}
		if ($db_ok) {
			$content .= "<p>" . ($res ? "OK: boomer" : "ERRORE: Utente già registrato") . "</p>";
		}
	} else {
		$content .= "ERRORE: Le password non coincidono";
	}
}

Tools::replaceAnchor($page, "form_username", $username);
Tools::replaceAnchor($page, "form_password", $password);
Tools::replaceAnchor($page, "form_password_confirm", $password_confirm);

Tools::replaceAnchor($page, "form_messages", $content);

Tools::showPage($page);

?>
