<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

if (isset($_GET["id"]) && $_GET["id"] != "") $id = $_GET["id"];
else {
	Tools::errCode(404);
	exit();
}

try {
	$connessione = new Database();
	$film = $connessione->getFilmById($id);
	if (!empty($film)) {
		$collezione = $connessione->getCollezioneById($film[0]["collezione"]);
		$crew = $connessione->getCrewByFilmId($id);
		$genere = $connessione->getGenereByFilmId($id);
		$paese = $connessione->getPaeseByFilmId($id);
		$valutazione = $connessione->getValutazioneByFilmId($id);
		if (isset($_SESSION["id"])) {
			$can_review = $connessione->canReview($_SESSION["id"], $id);
			$lista = $connessione->getListsByUserId($_SESSION["id"]);
		}
	}
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if (empty($film)) {
	Tools::errCode(404);
	exit();
}

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

$film = $film[0];
$title = $film["nome"] . " • Film"; Tools::toHtml($title, 0);
Tools::replaceAnchor($page, "title", $title);
Tools::toHtml($film);
Tools::replaceAnchor($page, "breadcrumb", $film["nome"]);
Tools::replaceAnchor($page, "nome", $film["nome"]);
$locandina = (isset($film["locandina"]) ? ("https://www.themoviedb.org/t/p/w300/" . $film["locandina"]) : "img/placeholder.svg");
Tools::replaceAnchor($page, "locandina", $locandina);
$sub = false;
if (isset($film["data_rilascio"])) {
	$sub = true;
	Tools::replaceAnchor($page, "data_rilascio", date_format(date_create_from_format('Y-m-d', $film["data_rilascio"]), 'd/m/Y'));
} else
	Tools::replaceSection($page, "data_rilascio", "");
if (isset($film["durata"])) {
	$sub = true;
	$h = floor($film["durata"]/60);
	$m = $film["durata"]%60;
	$d = "";
	if ($h)
		$d .= $h . ($h>1 ? " ore" : " ora");
	if ($m)
		$d .= ($h ? " " : "") . $m . ($m>1 ? " minuti" : " minuto");
	Tools::replaceAnchor($page, "durata", $d);
} else
	Tools::replaceSection($page, "durata", "");
if (isset($film["voto"])) {
	$sub = true;
	Tools::replaceAnchor($page, "voto", $film["voto"]);
} else
	Tools::replaceSection($page, "voto", "");
if (! $sub)
	Tools::replaceSection($page, "sottotitolo", "");
if (isset($film["descrizione"]))
	Tools::replaceAnchor($page, "descrizione", $film["descrizione"]);
else
	Tools::replaceSection($page, "descrizione", "");
if (!empty($crew)) {
	Tools::toHtml($crew);
	$ruolo = Tools::getSection($page, "ruolo");
	$persona = Tools::getSection($page, "persona");
	$res = "";
	$last_ruolo = "";
	foreach ($crew as $c) {
		if ($c["ruolo"] != $last_ruolo) {
			$r = $ruolo;
			Tools::replaceAnchor($r, "ruolo", $c["ruolo"]);
			$res .= $r;
			$last_ruolo = $c["ruolo"];
		}
		$p = $persona;
		Tools::replaceAnchor($p, "nome", $c["p_nome"]);
		Tools::replaceAnchor($p, "id", $c["p_id"]);
		$res .= $p;
	}
	Tools::replaceSection($page, "ruoli", $res);
} else
	Tools::replaceSection($page, "crew", "");
if (!empty($genere)) {
	Tools::toHtml($genere);
	$list = Tools::getSection($page, "genere");
	$r = "";
	foreach ($genere as $g) {
		$t = $list;
		Tools::replaceAnchor($t, "id", $g["nome"]); // TODO
		Tools::replaceAnchor($t, "nome", $g["nome"]);
		$r .= $t;
	}
	Tools::replaceSection($page, "genere", $r);
} else
	Tools::replaceSection($page, "generi", "");
if (!empty($paese)) {
	Tools::toHtml($paese);
	$list = Tools::getSection($page, "paese");
	$r = "";
	foreach ($paese as $p) {
		$t = $list;
		Tools::replaceAnchor($t, "id", $p["nome"]); // TODO
		Tools::replaceAnchor($t, "nome", $p["nome"]);
		$r .= $t;
	}
	Tools::replaceSection($page, "paese", $r);
} else
	Tools::replaceSection($page, "paesi", "");
Tools::replaceAnchor($page, "nome_originale", $film["nome_originale"]);
Tools::replaceAnchor($page, "stato", $film["stato"]);
if (isset($film["budget"])) {
	Tools::replaceAnchor($page, "budget", $film["budget"] . " $");
} else
	Tools::replaceSection($page, "budget", "");
if (isset($film["incassi"])) {
	Tools::replaceAnchor($page, "incassi", $film["incassi"] . " $");
} else
	Tools::replaceSection($page, "incassi", "");
if (!empty($collezione)) {
	Tools::toHtml($collezione);
	$c = Tools::getSection($page, "collezione");
	Tools::replaceAnchor($c, "id", $film["collezione"]);
	Tools::replaceAnchor($c, "nome", $collezione[0]["nome"]);
	Tools::replaceSection($page, "collezione", $c);
} else
	Tools::replaceSection($page, "collezione", "");
$val = false;
if (isset($_SESSION["id"]) && $can_review) {
	$val = true;
	Tools::replaceAnchor($page, "review_film_id", $id);
} else
	Tools::replaceSection($page, "add_review", "");
if (!empty($valutazione)) {
	$val = true;
	Tools::toHtml($valutazione);
	$list = Tools::getSection($page, "valutazione");
	$r = "";
	foreach ($valutazione as $v) {
		$t = $list;
		Tools::replaceAnchor($t, "utente", $v["utente"]);
		Tools::replaceAnchor($t, "voto", $v["voto"]);
		if (isset($v["testo"])) {
			Tools::replaceAnchor($t, "testo", $v["testo"]);
		} else
			Tools::replaceSection($t, "testo", "");
		$r .= $t;
	}
	Tools::replaceSection($page, "valutazione", $r);
} else
	Tools::replaceSection($page, "valutazioni", "");
if (! $val)
	Tools::replaceSection($page, "sect_valutazioni", "");
if (isset($_SESSION["id"]) && !empty($lista)) {
	Tools::toHtml($lista);
	$list = Tools::getSection($page, "lista");
	$r = "";
	foreach ($lista as $l) {
		$t = $list;
		Tools::replaceAnchor($t, "id", $l["id"]);
		Tools::replaceAnchor($t, "nome", $l["nome"]);
		$r .= $t;
	}
	Tools::replaceSection($page, "lista", $r);
	Tools::replaceAnchor($page, "list_film_id", $id);
} else
	Tools::replaceSection($page, "user", "");
if (isset($_SESSION["id"]) && $_SESSION["is_admin"] == 0)
	Tools::replaceSection($page, "admin", "");
else
	Tools::replaceAnchor($page, "gest_film_id", $id);

Tools::showPage($page);

?>