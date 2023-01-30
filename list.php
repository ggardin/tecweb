<?php

require_once("php/tools.php");
require_once("php/database.php");

if (! isset($_SESSION["id"])) {
	header ("location: login.php");
	exit();
}

$id = (isset($_GET["id"]) ? ($_GET["id"]) : "");

if ($id == "") {
	Tools::errCode(404);
	exit();
}

try {
	$connessione = new Database();
	$own = false;
	if ($connessione->isListaDiUtente($id, $_SESSION["id"])) {
		$own = true;
		$nome = $connessione->getNomeListaById($id);
		$lista = $connessione->getFilmInLista($id);
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

$page = Tools::buildPage($_SERVER["SCRIPT_NAME"]);

$nome = $nome[0];
$meta = $nome["nome"]; Tools::toHtml($meta, 1);
Tools::replaceAnchor($page, "desc_nomelista", $meta);
$title = $meta . " • Lista";
Tools::replaceAnchor($page, "title", $title);
Tools::toHtml($nome);
Tools::replaceAnchor($page, "breadcrumb", $nome["nome"]);
Tools::replaceAnchor($page, "intestazione", $nome["nome"]);
Tools::replaceAnchor($page, "gest_id", $id);
if (!empty($lista)) {
	Tools::toHtml($lista);
	$elemento = Tools::getSection($page, "elemento");
	$r = "";
	foreach ($lista as $l) {
		$t = $elemento;
		Tools::replaceAnchor($t, "id", $l["id"]);
		if (isset($l["locandina"])) {
			Tools::replaceAnchor($t, "immagine_webp", "pics/w200_" . $l["locandina"] . ".webp");
			Tools::replaceAnchor($t, "immagine", "pics/w200_" . $l["locandina"] . ".jpg");
		} else {
			Tools::replaceSection($t, "pic_source", "");
			Tools::replaceAnchor($t, "immagine", "img/placeholder.svg");
		}
		Tools::replaceAnchor($t, "nome", $l["nome"]);
		if (isset($l["data_rilascio"]))
			Tools::replaceAnchor($t, "data_rilascio", $l["data_rilascio"]);
		else
			Tools::replaceSection($t, "data_rilascio", "");
		Tools::replaceAnchor($t, "list_id", $id);
		Tools::replaceAnchor($t, "film_id", $l["id"]);
		$r .= $t;
	}
	Tools::replaceSection($page, "elemento", $r);
	Tools::replaceAnchor($page, "message", (count($lista) . " film in questa lista"));
} else {
	Tools::replaceAnchor($page, "message", "Questa lista non contiene film");
	Tools::replaceSection($page, "lista", "");
}

Tools::showPage($page);

?>