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
		if (! empty($res)) {
			$_SESSION["id"] = $res["id"];
			$_SESSION["is_admin"] = $res["is_admin"];
			header("location: user.php");
			exit();
		} else
			Tools::replaceAnchor($page, "message", "Errore: Credenziali errate");
	}
} else
	Tools::replaceSection($page, "message", "");

Tools::replaceAnchor($page, "form_username", $username);
Tools::replaceAnchor($page, "form_password", $password);

Tools::showPage($page);

?>