<?php

require_once("php/tools.php");
require_once("php/database.php");

if (! isset($tipo)) {
	Tools::errCode(404);
	exit();
}

$query = (isset($_GET["q"])) ? $_GET["q"] : "";
$f_nome = (isset($_GET["fn"])) ? $_GET["fn"] : "";
$f_val_genere = (isset($_GET["fvg"])) ? $_GET["fvg"] : "";
$f_val_paese = (isset($_GET["fvp"])) ? $_GET["fvp"] : "";

$limit = 16;
$next = (isset($_GET["n"])) ? intval($_GET["n"]) : 0;
$offset = $limit * $next;

// try {
	$connessione = new Database();
	if ($tipo == "film") {
		if ($f_nome == "genere" && $f_val_genere)
			$cerca = $connessione->searchFilmFilteredByGenere($query, $limit, $offset, $f_val_genere);
		elseif ($f_nome == "paese" && $f_val_paese)
			$cerca = $connessione->searchFilmFilteredByPaese($query, $limit, $offset, $f_val_paese);
		else {
			$cerca = $connessione->searchFilm($query, $limit, $offset);
			$f_nome = "";
		}
		$generi = $connessione->getGeneriConFilm();
		$paesi = $connessione->getPaesiConFilm();
	} elseif ($tipo == "collezione")
		$cerca = $connessione->searchCollezione($query, $limit, $offset);
	elseif ($tipo == "persona")
		$cerca = $connessione->searchPersona($query, $limit, $offset);
	unset($connessione);
// } catch (Exception) {
// 	unset($connessione);
// 	Tools::errCode(500);
// 	exit();
// }

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
if ($tipo == "film" && $f_nome) {
	$intestazione .= " filtrati per $f_nome (";
	if ($f_nome == "genere")
		$intestazione .= $f_val_genere;
	elseif ($f_nome == "paese")
		$intestazione .= $f_val_paese;
	$intestazione .= ")";
}

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

if ($tipo == "film") {
	$filter = Tools::getSection($page, "filter");
	$res = "";
	foreach (["genere", "paese"] as $t) {
		$f = $filter;
		Tools::replaceAnchor($f, "val", $t);
		Tools::replaceAnchor($f, "nome", ucfirst($t));
		Tools::replaceAnchor($f, "sel", (($f_nome == $t) ? "selected" : ""));
		$res .= $f;
	}
	Tools::replaceSection($page, "filter", $res);

	$option = Tools::getSection($page, "genere");
	$res = "";
	foreach ($generi as $g) {
		$t = $option;
		Tools::replaceAnchor($t, "val", $g["nome"]);
		Tools::replaceAnchor($t, "nome", $g["nome"]);
		Tools::replaceAnchor($t, "sel", (($g["nome"] == $f_val_genere) ? "selected" : ""));
		$res .= $t;
	}
	Tools::replaceSection($page, "genere", $res);

	$option = Tools::getSection($page, "paese");
	$res = "";
	foreach ($paesi as $p) {
		$t = $option;
		Tools::replaceAnchor($t, "val", $p["nome"]);
		Tools::replaceAnchor($t, "nome", $p["nome"]);
		Tools::replaceAnchor($t, "sel", (($p["nome"] == $f_val_paese) ? "selected" : ""));
		$res .= $t;
	}
	Tools::replaceSection($page, "paese", $res);
} else
	Tools::replaceSection($page, "filtri", "");

if (!empty($cerca[0])) {
	$tot = $cerca[1]["n"];
	$cerca = $cerca[0];
	Tools::toHtml($cerca);
	$card = Tools::getSection($page, "card");
	$r = "";
	foreach ($cerca as $c) {
		$t = $card;
		if ($tipo != "persona")
			$immagine = (isset($c["locandina"]) ? ("pics/w200_" . $c["locandina"] . ".webp") : "img/placeholder.svg");
		else
			$immagine = (isset($c["immagine"]) ? ("pics/w200_" . $c["immagine"] . ".webp") : "img/placeholder.svg");
		Tools::replaceAnchor($t, "immagine", $immagine);
		Tools::replaceAnchor($t, "link", ($tipo . ".php?id=" . $c["id"]));
		Tools::replaceAnchor($t, "nome", $c["nome"]);
		if ($tipo == "film" && isset($c["data_rilascio"]))
			Tools::replaceAnchor($t, "data", date_format(date_create_from_format('Y-m-d', $c["data_rilascio"]), 'd/m/Y'));
		else
			Tools::replaceSection($t, "data", "");
		if (($tipo == "collezione" || $tipo == "persona") && isset($c["n_film"]))
			Tools::replaceAnchor($t, "n_film", $c["n_film"]);
		else
			Tools::replaceSection($t, "n_film", "");
		$r .= $t;
	}
	Tools::replaceSection($page, "card", $r);
	Tools::replaceAnchor($page, "message", ("Pagina " . ($next+1) . " su " . ceil($tot / $limit)));
	$buttons = false;
	$query = "cerca_$tipo.php?q=$query" . (($tipo == "film" && $f_nome) ? ("&fn=" . $f_nome . "&fvg=" . $f_val_genere . "&fvp=" . $f_val_paese) : "");
	if ($next > 0) {
		$buttons = true;
		Tools::replaceAnchor($page, "prev", ($query . "&n=" . ($next-1)));
	} else
		Tools::replaceSection($page, "prev", "");
	if (($next + 1) < ceil($tot / $limit)) {
		$buttons = true;
		Tools::replaceAnchor($page, "next", ($query . "&n=" . ($next+1)));
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