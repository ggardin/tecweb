<?php

require_once("php/tools.php");
require_once("php/database.php");

if (! isset($_SESSION["id"]) || $_SESSION["is_admin"] == 0) {
	header ("location: login.php");
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

if ($id != "" && empty($collezione)) {
	Tools::errCode(404);
	exit();
}

$page = Tools::buildPage($_SERVER["SCRIPT_NAME"]);

if ($id != "") {
	$collezione = $collezione[0];
	$title = $collezione["nome"] . " • Modifica collezione"; Tools::toHtml($title, 1);
	Tools::replaceAnchor($page, "title", $title);
	Tools::replaceAnchor($page, "bc_id", $id);
	$bc_nome = $collezione["nome"]; Tools::toHtml($bc_nome);
	Tools::replaceAnchor($page, "bc_nome", $bc_nome);
	Tools::replaceAnchor($page, "intestazione", "Modifica collezione");
	Tools::replaceAnchor($page, "gest_id", $id);
	Tools::toHtml($collezione, 0);
	Tools::replaceAnchor($page, "nome", $collezione["nome"]);
	Tools::replaceAnchor($page, "descrizione", (isset($collezione["descrizione"]) ? $collezione["descrizione"] : ""));
	$immagine = (isset($collezione["locandina"]) ? ("pics/w200_" . $collezione["locandina"] . ".webp") : "img/placeholder.svg");
	Tools::replaceAnchor($page, "locandina", $immagine);
	Tools::replaceAnchor($page, "submit_value", "modifica");
	Tools::replaceAnchor($page, "submit", "Modifica");
} else {
	Tools::replaceAnchor($page, "title", "Aggiungi collezione");
	Tools::replaceSection($page, "breadcrumb", "Aggiungi");
	Tools::replaceAnchor($page, "intestazione", "Aggiungi collezione");
	Tools::replaceAnchor($page, "gest_id", "");
	Tools::replaceAnchor($page, "nome", "");
	Tools::replaceAnchor($page, "descrizione", "");
	Tools::replaceSection($page, "locandina", "");
	Tools::replaceAnchor($page, "submit_value", "aggiungi");
	Tools::replaceAnchor($page, "submit", "Aggiungi");
	Tools::replaceSection($page, "delete", "");
}

Tools::showPage($page);

?>