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
		$gender = $connessione->getGenders($id);
		$persona = $connessione->getPersonaById($id);
	}
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

if ($id != "" && !empty($persona)) {
	$persona = $persona[0];
	$title = $persona["nome"] . " • Modifica persona"; Tools::toHtml($title, 0);
	Tools::replaceAnchor($page, "title", $title);
	Tools::replaceAnchor($page, "bc_id", $id);
	$bc_nome = $persona["nome"]; Tools::toHtml($bc_nome, 2);
	Tools::replaceAnchor($page, "bc_nome", $bc_nome);
	Tools::replaceAnchor($page, "intestazione", "Modifica persona");
	Tools::toHtml($persona, 1);
	Tools::replaceAnchor($page, "nome", $persona["nome"]);
	$option = Tools::getSection($page, "gender");
	$res = "";
	foreach ($gender as $g) {
		$t = $option;
		Tools::replaceAnchor($t, "id", $g["id"]);
		Tools::replaceAnchor($t, "nome", $g["nome"]);
		$sel = (($g["id"] == $persona["gender_id"]) ? "selected" : "");
		Tools::replaceAnchor($t, "sel", $sel);
		$res .= $t;
	}
	Tools::replaceSection($page, "gender", $res);
	$dn = (isset($persona["data_nascita"]) ? $persona["data_nascita"] : "");
	Tools::replaceAnchor($page, "data_nascita", $dn);
	$dm = (isset($persona["data_morte"]) ? $persona["data_morte"] : "");
	Tools::replaceAnchor($page, "data_morte", $dm);
	Tools::replaceAnchor($page, "submit", "Modifica");
} else {
	Tools::replaceAnchor($page, "title", "Aggiungi persona");
	Tools::replaceSection($page, "breadcrumb", "Aggiungi");
	Tools::replaceAnchor($page, "intestazione", "Aggiungi persona");
	Tools::replaceAnchor($page, "submit", "Aggiungi");
}

Tools::showPage($page);

?>