<?php

require_once("php/page.php");
require_once("php/database.php");

$page = Page::build(basename($_SERVER["PHP_SELF"], ".php"));

$content = "";

if (isset($_GET["id"])) {
	$db_ok = false;
	try {
		$connessione = new Database();
		$res = $connessione->getCollezioneById($_GET["id"]);
		unset($connessione);
		$db_ok = true;
	} catch (Exception $e) {
		$content = "<h2>" . $e->getMessage() . "</h2>";
	}
	if ($db_ok) {
		$content = "<pre>" . print_r($res, true) . "</pre>";
	}
}

Page::replaceAnchor($page, "collezione", $content);

echo($page);

?>
