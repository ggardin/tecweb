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
	$gender = $connessione->getGenders();
	if ($id != "") {
		$persona = $connessione->getPersonaById($id);
	}
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

$page = Tools::buildPage($_SERVER["SCRIPT_NAME"]);

if ($id != "" && !empty($persona)) {
	$persona = $persona[0];
	$title = $persona["nome"] . " • Modifica persona"; Tools::toHtml($title, 0);
	Tools::replaceAnchor($page, "title", $title);
	Tools::replaceAnchor($page, "bc_id", $id);
	$bc_nome = $persona["nome"]; Tools::toHtml($bc_nome, 2);
	Tools::replaceAnchor($page, "bc_nome", $bc_nome);
	Tools::replaceAnchor($page, "intestazione", "Modifica persona");
	Tools::replaceAnchor($page, "gest_id", $id);
	Tools::toHtml($persona, 1);
	Tools::replaceAnchor($page, "nome", $persona["nome"]);
	$option = Tools::getSection($page, "gender");
	$res = "";
	foreach ($gender as $g) {
		$t = $option;
		Tools::replaceAnchor($t, "gender_id", $g["id"]);
		Tools::replaceAnchor($t, "gender_nome", $g["nome"]);
		Tools::replaceAnchor($t, "sel", (($g["id"] == $persona["gender"]) ? "selected" : ""));
		$res .= $t;
	}
	Tools::replaceSection($page, "gender", $res);
	Tools::replaceAnchor($page, "data_nascita", (isset($persona["data_nascita"]) ? $persona["data_nascita"] : ""));
	Tools::replaceAnchor($page, "data_morte", (isset($persona["data_morte"]) ? $persona["data_morte"] : ""));
	Tools::replaceAnchor($page, "submit_value", "modifica");
	Tools::replaceAnchor($page, "submit", "Modifica");
} else {
	Tools::replaceAnchor($page, "title", "Aggiungi persona");
	Tools::replaceSection($page, "breadcrumb", "Aggiungi");
	Tools::replaceAnchor($page, "nome", "");
	$option = Tools::getSection($page, "gender");
	$res = "";
	foreach ($gender as $g) {
		$t = $option;
		Tools::replaceAnchor($t, "gender_id", $g["id"]);
		Tools::replaceAnchor($t, "gender_nome", $g["nome"]);
		Tools::replaceAnchor($t, "sel", "");
		$res .= $t;
	}
	Tools::replaceSection($page, "gender", $res);
	Tools::replaceAnchor($page, "data_nascita", "");
	Tools::replaceAnchor($page, "data_morte", "");
	Tools::replaceAnchor($page, "intestazione", "Aggiungi persona");
	Tools::replaceAnchor($page, "submit_value", "aggiungi");
	Tools::replaceAnchor($page, "submit", "Aggiungi");
}

Tools::showPage($page);

?>