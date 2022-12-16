<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();
if (isset($_SESSION["user"])) {
	header("location: user.php");
	exit();
}

$username = isset($_POST["username"]) ? $_POST["username"] : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"), "auth");

if (isset($_POST["submit"])) {
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
		if ($res) {
			$_SESSION["user"] = $username;
			Tools::replaceAnchor($page, "message", "Accesso eseguito. Ritorno in 5 secondi.");
			header("refresh:5; url=" . (isset($_SESSION["last"]) ? $_SESSION["last"] : "index.php"));
		} else
			Tools::replaceAnchor($page, "message", "Errore: Credenziali errate");
	}
} else
	Tools::replaceSection($page, "message", "");

Tools::replaceAnchor($page, "form_username", $username);
Tools::replaceAnchor($page, "form_password", $password);

Tools::showPage($page);

?>
