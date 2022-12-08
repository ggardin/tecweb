<?php

require_once("php/page.php");
require_once("php/database.php");

$page = Page::build(basename($_SERVER["PHP_SELF"], ".php"), "auth");

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

Page::replaceAnchor($page, "form_username", $username);
Page::replaceAnchor($page, "form_password", $password);
Page::replaceAnchor($page, "form_password_confirm", $password_confirm);

Page::replaceAnchor($page, "form_messages", $content);

echo($page);

?>
