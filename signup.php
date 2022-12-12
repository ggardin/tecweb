<?php

require_once("php/tools.php");
require_once("php/database.php");

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"), "auth");

$username = "";
$password = "";
$password_confirm = "";

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
			Tools::replaceAnchor($page, "message", $e->getMessage());
		} finally {
			unset($connessione);
		}
		if ($db_ok) {
			Tools::replaceAnchor($page, "message", ($res ? "Registrazione eseguita" : "Errore: Utente già registrato"));
		}
	} else
		Tools::replaceAnchor($page, "message", "Errore: Le password non coincidono");
} else
	Tools::replaceSection($page, "message", "");

Tools::replaceAnchor($page, "form_username", $username);
Tools::replaceAnchor($page, "form_password", $password);
Tools::replaceAnchor($page, "form_password_confirm", $password_confirm);

Tools::showPage($page);

?>
