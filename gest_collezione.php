<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

if (! isset($_SESSION["id"]) || $_SESSION["is_admin"] == 0) {
	header ("location: user.php");
	exit();
}

$id = isset($_GET["id"]) ? $_GET["id"] : "";

try {
	$connessione = new Database();
	if ($id != "")
		$collezione = $connessione->getCollezioneById($id);
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

if ($id != "" && !empty($collezione)) {
	$collezione = $collezione[0];
	$title = $collezione["nome"] . " • Modifica Collezione"; Tools::toHtml($title, 0);
	Tools::replaceAnchor($page, "title", $title);
	Tools::replaceAnchor($page, "bc_id", $id);
	$bc_nome = $collezione["nome"]; Tools::toHtml($bc_nome, 2);
	Tools::replaceAnchor($page, "bc_nome", $bc_nome);
	Tools::toHtml($collezione, 1);
	Tools::replaceAnchor($page, "nome", $collezione["nome"]);
	Tools::replaceAnchor($page, "descrizione", $collezione["descrizione"]);
	Tools::replaceAnchor($page, "submit", "Modifica");
} else {
	Tools::replaceAnchor($page, "title", "Aggiungi Collezione");
	Tools::replaceSection($page, "breadcrumb", "Aggiungi");
	Tools::replaceAnchor($page, "submit", "Aggiungi");
}

Tools::showPage($page);

?>