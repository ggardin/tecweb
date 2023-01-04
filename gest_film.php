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
		$film = $connessione->getFilmById($id);
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

if ($id != "" && !empty($film)) {
	$film = $film[0];
	$title = $film["nome"] . " • Modifica Film"; Tools::toHtml($title, 0);
	Tools::replaceAnchor($page, "title", $title);
	Tools::replaceAnchor($page, "bc_id", $id);
	$bc_nome = $film["nome"]; Tools::toHtml($bc_nome, 2);
	Tools::replaceAnchor($page, "bc_nome", $bc_nome);
	Tools::replaceAnchor($page, "intestazione", "Modifica film");
	Tools::toHtml($film, 1);
	Tools::replaceAnchor($page, "nome", $film["nome"]);
	Tools::replaceAnchor($page, "descrizione", $film["descrizione"]);
	Tools::replaceAnchor($page, "data_rilascio", $film["data_rilascio"]);
	Tools::replaceAnchor($page, "durata", $film["durata"]);
	Tools::replaceAnchor($page, "incassi", $film["incassi"]);
	Tools::replaceAnchor($page, "budget", $film["budget"]);
	Tools::replaceAnchor($page, "nome_originale", $film["nome_originale"]);
	Tools::replaceAnchor($page, "submit", "Modifica");
} else {
	Tools::replaceAnchor($page, "title", "Aggiungi film");
	Tools::replaceSection($page, "breadcrumb", "Aggiungi");
	Tools::replaceAnchor($page, "intestazione", "Aggiungi film");
	Tools::replaceAnchor($page, "submit", "Aggiungi");
}

Tools::showPage($page);

?>