<?php

require_once("php/page.php");
require_once("php/database.php");

$page = Page::build(basename($_SERVER["PHP_SELF"], ".php"));

$content = "";

if (isset($_GET["id"])) {
	$db_ok = false;
	try {
		$connessione = new Database();
		$coll = $connessione->getCollezioneById($_GET["id"]);
		$film = $connessione->getFilmInCollezioneById($_GET["id"]);
		$db_ok = true;
	} catch (Exception $e) {
		$content = "<h2>" . $e->getMessage() . "</h2>";
	} finally {
		unset($connessione);
	}
	if ($db_ok) {
		$content = "<pre>" . print_r($coll, true) . "</pre>";
		$content .= "<pre>" . print_r($film, true) . "</pre>";
	}
}

Page::replaceAnchor($page, "collezione", $content);

echo($page);

?>
