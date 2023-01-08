<?php

require_once("php/tools.php");
require_once("php/database.php");

if (! isset($tipo)) {
	Tools::errCode("404");
	exit();
}

$query = (isset($_GET["q"])) ? $_GET["q"] : "";
$f_nome = (isset($_GET["fn"])) ? $_GET["fn"] : "";
$f_val = (isset($_GET["fv"])) ? $_GET["fv"] : "";

$limit = 15;
$next = (isset($_GET["n"])) ? intval($_GET["n"]) : 0;
$offset = $limit * $next;

try {
	$connessione = new Database();
	if ($tipo == "film") {
		if ($f_nome == "genere" && $f_val) {
			$cerca = $connessione->searchFilmFilteredByGenere($query, $limit, $offset, $f_val);
			$generi = $connessione->getGeneri();
		}
		elseif ($f_nome == "paese" && $f_val) {
			$cerca = $connessione->searchFilmFilteredByPaese($query, $limit, $offset, $f_val);
			$paesi = $connessione->getPaesi();
		}
		else {
			$cerca = $connessione->searchFilm($query, $limit, $offset);
			$f_nome = "";
		}
	} elseif ($tipo == "collezione")
		$cerca = $connessione->searchCollezione($query, $limit, $offset);
	elseif ($tipo == "persona")
		$cerca = $connessione->searchPersona($query, $limit, $offset);
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if (!isset($cerca)) {
	Tools::errCode(404);
	exit();
}

$page = Tools::buildPage("cerca", "std", "cerca_$tipo");

if ($tipo == "film") {
	$breadcrumb = "Film";
}
elseif ($tipo == "collezione")
	$breadcrumb = "Collezioni";
else
	$breadcrumb = "Persone";

$intestazione = $breadcrumb;
if ($tipo == "film" && $f_nome != "")
	$intestazione .= " filtrati per $f_nome ($f_val)";

$titolo = (($query != "") ? ('"' . $query . '" • ') : "") . "Cerca " . $intestazione;
Tools::replaceAnchor($page, "title", $titolo);
Tools::replaceAnchor($page, "breadcrumb", $breadcrumb);
Tools::replaceAnchor($page, "intestazione", $intestazione);
Tools::replaceAnchor($page, "search_value", $query);
Tools::replaceAnchor($page, "search_tipo", $tipo);

$tmp = Tools::getSection($page, "tipo");
$res = "";
foreach (["film", "collezione", "persona"] as $k) {
	$t = $tmp;
	Tools::replaceAnchor($t, "val", $k);
	Tools::replaceAnchor($t, "nome", $k);
	if ($k == $tipo)
		Tools::replaceAnchor($t, "sel", "selected");
	else
		Tools::replaceAnchor($t, "sel", "");
	$res .= $t;
}
Tools::replaceSection($page, "tipo", $res);

// TODO integrare filtri
if ($tipo != "film")
	Tools::replaceSection($page, "filtri", "");

if (!empty($cerca[0])) {
	$shown = count($cerca[0]);
	$tot = $cerca[1]["n"];
	$cerca = $cerca[0];
	Tools::toHtml($cerca);
	$card = Tools::getSection($page, "card");
	$r = "";
	foreach ($cerca as $c) {
		$t = $card;
		if ($tipo != "persona")
			$immagine = (isset($c["locandina"]) ? ("https://www.themoviedb.org/t/p/w300/" . $c["locandina"]) : "img/placeholder.svg");
		else
			$immagine = (isset($c["immagine"]) ? ("https://www.themoviedb.org/t/p/w300/" . $c["immagine"]) : "img/placeholder.svg");
		Tools::replaceAnchor($t, "immagine", $immagine);
		Tools::replaceAnchor($t, "link", ($tipo . ".php?id=" . $c["id"]));
		Tools::replaceAnchor($t, "nome", $c["nome"]);
		if ($tipo == "film" && isset($c["data_rilascio"]))
			Tools::replaceAnchor($t, "data", date_format(date_create_from_format('Y-m-d', $c["data_rilascio"]), 'd/m/Y'));
		else
			Tools::replaceSection($t, "data", "");
		$r .= $t;
	}
	Tools::replaceSection($page, "card", $r);
	Tools::replaceAnchor($page, "message", ("Da " . ($offset+1) . " a " . ($offset+$shown) . " (su " . $tot . " trovati)"));
	$buttons = false;
	if ($next > 0) {
		$buttons = true;
		Tools::replaceAnchor($page, "prev", "cerca_$tipo.php?q=$query" . ($next > 1 ? ("&n=" . ($next-1)): ""));
	} else
		Tools::replaceSection($page, "prev", "");
	if ($tot > $offset + $shown) {
		$buttons = true;
		Tools::replaceAnchor($page, "next", "cerca_$tipo.php?q=$query&n=" . ($next+1));
	} else
		Tools::replaceSection($page, "next", "");
	if ($buttons) {
		Tools::replaceAnchor($page, "res_buttons_bottom", Tools::getSection($page, "res_buttons"), true);
	} else {
		Tools::replaceSection($page, "res_buttons", "");
		Tools::replaceAnchor($page, "res_buttons_bottom", "", true);
	}
} else {
	Tools::replaceAnchor($page, "message", "Questa ricerca non ha prodotto risultati");
	Tools::replaceSection($page, "results", "");
}

Tools::showPage($page);

?>