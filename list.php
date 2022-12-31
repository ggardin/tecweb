<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

if (! isset($_SESSION["id"])) {
	header ("location: login.php");
	exit();
}

if (isset($_GET["id"]) && $_GET["id"] != "") $id = $_GET["id"];
else {
	Tools::errCode(404);
	exit();
}

try {
	$connessione = new Database();
	$own = false;
	if ($connessione->checkListOwnership($_SESSION["id"], $id)) {
		$own = true;
		$nome = $connessione->getListNameById($id);
		$lista = $connessione->getListItemsById($id);
	}
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if (!$own) {
	Tools::errCode(404);
	exit();
}

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

$nome = $nome[0];
$title = $nome["nome"] . " • Lista"; Tools::toHtml($title, 0);
Tools::replaceAnchor($page, "title", $title);
Tools::toHtml($nome);
Tools::replaceAnchor($page, "intestazione", $nome["nome"]);
Tools::replaceAnchor($page, "breadcrumb", $nome["nome"]);
if (!empty($lista)) {
	Tools::toHtml($lista);
	$elemento = Tools::getSection($page, "elemento");
	$r = "";
	foreach ($lista as $l) {
		$t = $elemento;
		Tools::replaceAnchor($t, "link", ($l["tipo"] . ".php?id=" . $l["id"]));
		$immagine = (isset($l["locandina"]) ? ("https://www.themoviedb.org/t/p/w300/" . $l["locandina"]) : "img/placeholder.svg");
		Tools::replaceAnchor($t, "immagine", $immagine);
		Tools::replaceAnchor($t, "nome", $l["nome"]);
		if ($l["tipo"] == "film" && isset($l["data_rilascio"]))
			Tools::replaceAnchor($t, "data_rilascio", $l["data_rilascio"]);
		else
			Tools::replaceSection($t, "data_rilascio", "");
		$r .= $t;
	}
	Tools::replaceSection($page, "elemento", $r);
	Tools::replaceAnchor($page, "message", (count($lista) . (count($lista) != 1 ? " elementi" : " elemento") . " in questa lista"));
} else {
	Tools::replaceAnchor($page, "message", "Questa lista non ha elementi");
	Tools::replaceSection($page, "lista", "");
}

Tools::showPage($page);

?>