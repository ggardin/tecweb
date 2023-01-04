<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

if (! isset($_SESSION["id"])) {
	header ("location: login.php");
	exit();
}

$id = isset($_GET["id"]) ? $_GET["id"] : "";

try {
	$connessione = new Database();
	if ($id != "")
		$lista = $connessione->getListNameById($id);
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

if ($id != "" && !empty($lista)) {
	$lista = $lista[0];
	$title = $lista["nome"] . " • Modifica lista"; Tools::toHtml($title, 0);
	Tools::replaceAnchor($page, "title", $title);
	Tools::replaceAnchor($page, "bc_id", $id);
	$bc_nome = $lista["nome"]; Tools::toHtml($bc_nome, 2);
	Tools::replaceAnchor($page, "bc_nome", $bc_nome);
	Tools::replaceAnchor($page, "intestazione", "Modifica lista");
	Tools::toHtml($lista, 1);
	Tools::replaceAnchor($page, "list_id", $id);
	Tools::replaceAnchor($page, "nome", $lista["nome"]);
	Tools::replaceAnchor($page, "submit_val", "modifica");
	Tools::replaceAnchor($page, "submit", "Modifica");
} else {
	Tools::replaceAnchor($page, "title", "Aggiungi lista");
	Tools::replaceSection($page, "breadcrumb", "Aggiungi");
	Tools::replaceAnchor($page, "intestazione", "Aggiungi lista");
	Tools::replaceAnchor($page, "nome", "");
	Tools::replaceSection($page, "delete", "");
	Tools::replaceAnchor($page, "submit_val", "aggiungi");
	Tools::replaceAnchor($page, "submit", "Aggiungi");
}

Tools::showPage($page);

?>