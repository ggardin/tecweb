<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

if (! isset($_SESSION["id"]) || $_SESSION["is_admin"] == 0) {
	header ("location: login.php");
	exit();
}

$id = isset($_GET["id"]) ? $_GET["id"] : "";

try {
	$connessione = new Database();
	if ($id != "") {
		$film = $connessione->getFilmById($id);
		$collezioni = $connessione->getCollezioni($id);
		$stati = $connessione->getStati($id);
	}
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

$page = Tools::buildPage($_SERVER["SCRIPT_NAME"]);

if ($id != "" && !empty($film)) {
	$film = $film[0];
	$title = $film["nome"] . " • Modifica film"; Tools::toHtml($title, 0);
	Tools::replaceAnchor($page, "title", $title);
	Tools::replaceAnchor($page, "bc_id", $id);
	$bc_nome = $film["nome"]; Tools::toHtml($bc_nome, 2);
	Tools::replaceAnchor($page, "bc_nome", $bc_nome);
	Tools::replaceAnchor($page, "intestazione", "Modifica film");
	Tools::replaceAnchor($page, "gest_id", $id);
	Tools::toHtml($film, 1);
	Tools::replaceAnchor($page, "nome", $film["nome"]);
	Tools::replaceAnchor($page, "descrizione", (isset($film["descrizione"]) ? $film["descrizione"] : ""));
	Tools::replaceAnchor($page, "data_rilascio", (isset($film["data_rilascio"]) ? $film["data_rilascio"] : ""));
	Tools::replaceAnchor($page, "durata", (isset($film["durata"]) ? $film["durata"] : ""));
	Tools::replaceAnchor($page, "nome_originale", $film["nome_originale"]);
	$option = Tools::getSection($page, "stato");
	$res = "";
	foreach ($stati as $s) {
		$t = $option;
		Tools::replaceAnchor($t, "id", $s["id"]);
		Tools::replaceAnchor($t, "nome", $s["nome"]);
		Tools::replaceAnchor($t, "sel", (($s["id"] == $film["stato"]) ? "selected" : ""));
		$res .= $t;
	}
	Tools::replaceSection($page, "stato", $res);
	Tools::replaceAnchor($page, "budget", (isset($film["budget"]) ? $film["budget"] : ""));
	Tools::replaceAnchor($page, "incassi", (isset($film["incassi"]) ? $film["incassi"] : ""));
	Tools::toHtml($collezioni);
	$option = Tools::getSection($page, "collezione");
	$res = "";
	foreach ($collezioni as $c) {
		$t = $option;
		Tools::replaceAnchor($t, "id", $c["id"]);
		Tools::replaceAnchor($t, "nome", $c["nome"]);
		Tools::replaceAnchor($t, "sel", (($c["id"] == $film["collezione"]) ? "selected" : ""));
		$res .= $t;
	}
	Tools::replaceSection($page, "collezione", $res);
	Tools::replaceAnchor($page, "submit_value", "modifica");
	Tools::replaceAnchor($page, "submit", "Modifica");
} else {
	Tools::replaceAnchor($page, "title", "Aggiungi film");
	Tools::replaceSection($page, "breadcrumb", "Aggiungi");
	Tools::replaceAnchor($page, "intestazione", "Aggiungi film");
	Tools::replaceAnchor($page, "submit-value", "aggiungi");
	Tools::replaceAnchor($page, "submit", "Aggiungi");
}

Tools::showPage($page);

?>