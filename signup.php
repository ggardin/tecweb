<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

if (isset($_SESSION["id"])) {
	header("location: user.php");
	exit();
}

$username = isset($_POST["username"]) ? $_POST["username"] : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";
$password_confirm = isset($_POST["password_confirm"]) ? $_POST["password_confirm"] : "";

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"), "auth");

if (isset($_POST["submit"])) {
	if ($password == $password_confirm) {
		try {
			$connessione = new Database();
			$res = $connessione->signup($username, $password);
			unset($connessione);
		} catch (Exception) {
			unset($connessione);
			Tools::errCode(500);
			exit();
		}
		if (! empty($res)) {
			$_SESSION["id"] = $res["id"];
			header("location: user.php");
			exit();
		} else
			Tools::replaceAnchor($page, "message", "Errore: Utente già registrato");
	} else
		Tools::replaceAnchor($page, "message", "Errore: Le password non coincidono");
} else
	Tools::replaceSection($page, "message", "");

Tools::replaceAnchor($page, "form_username", $username);
Tools::replaceAnchor($page, "form_password", $password);
Tools::replaceAnchor($page, "form_password_confirm", $password_confirm);

Tools::showPage($page);

?>