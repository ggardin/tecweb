<?php

require_once("php/page.php");
require_once("php/database.php");

$page = Page::build(basename($_SERVER["PHP_SELF"], ".php"), "auth");

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
		unset($connessione);
		$db_ok = true;
	} catch (Exception $e) {
		$content = "<h2>" . $e->getMessage() . "</h2>";
	}
	if ($db_ok) {
		$content = $res ? "OK: boomer" : "ERRORE: Credenziali errate";
	}
}

Page::replaceAnchor($page, "form_username", $username);
Page::replaceAnchor($page, "form_password", $password);

Page::replaceAnchor($page, "form_messages", $content);

echo($page);

?>
