<?php

require_once("php/tools.php");
require_once("php/database.php");

$id = (isset($_GET["id"]) ? ($_GET["id"]) : "");

if ($id == "") {
	Tools::errCode(404);
	exit();
}

try {
	$connessione = new Database();
	$film = $connessione->getFilmById($id);
	if (!empty($film)) {
		$stato = $connessione->getStatoById($film[0]["stato"]);
		$collezione = $connessione->getCollezioneById($film[0]["collezione"]);
		$crew = $connessione->getCrewByFilmId($id);
		$genere = $connessione->getGenereByFilmId($id);
		$paese = $connessione->getPaeseByFilmId($id);
		$valutazione = $connessione->getValutazioneByFilmId($id);
		if (isset($_SESSION["id"])) {
			$can_review = $connessione->canUtenteValutare($_SESSION["id"], $id);
			$valutazioneUtente = $connessione->getValutazioneFilmPerUtente($_SESSION["id"], $id);
			$lista = $connessione->getListeSenzaFilm($_SESSION["id"], $id);
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

$page = Tools::buildPage($_SERVER["SCRIPT_NAME"]);

$film = $film[0];
$meta = $film["nome"]; Tools::toHtml($meta, 1);
Tools::replaceAnchor($page, "desc_nomefilm", $meta);
Tools::replaceAnchor($page, "keys_nomefilm", $meta);
$title = $meta. " • Film";
Tools::replaceAnchor($page, "title", $title);
Tools::toHtml($film);
Tools::replaceAnchor($page, "breadcrumb", $film["nome"]);
Tools::replaceAnchor($page, "nome", $film["nome"]);
if (isset($film["locandina"])) {
	Tools::replaceAnchor($page, "immagine_webp", "pics/w500_" . $film["locandina"] . ".webp");
	Tools::replaceAnchor($page, "immagine", "pics/w500_" . $film["locandina"] . ".jpg");
} else {
	Tools::replaceSection($page, "pic_source", "");
	Tools::replaceAnchor($page, "immagine", "img/placeholder.svg");
}
$sub = false;
if (isset($film["data_rilascio"])) {
	$sub = true;
	Tools::replaceAnchor($page, "data_rilascio", date_format(date_create_from_format('Y-m-d', $film["data_rilascio"]), 'd/m/Y'));
} else
	Tools::replaceSection($page, "data_rilascio", "");
if (isset($film["durata"])) {
	$sub = true;
	Tools::replaceAnchor($page, "durata", Tools::minutiAStringa($film["durata"]));
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
		if ($c["r_nome"] != $last_ruolo) {
			$r = $ruolo;
			Tools::replaceAnchor($r, "ruolo", $c["r_nome"]);
			$res .= $r;
			$last_ruolo = $c["r_nome"];
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
		Tools::replaceAnchor($t, "id", $g["id"]);
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
		Tools::replaceAnchor($t, "id", $p["id"]);
		Tools::replaceAnchor($t, "nome", $p["nome"]);
		$r .= $t;
	}
	Tools::replaceSection($page, "paese", $r);
} else
	Tools::replaceSection($page, "paesi", "");
Tools::replaceAnchor($page, "nome_originale", (isset($film["nome_originale"]) ? $film["nome_originale"] : ""));
Tools::replaceAnchor($page, "stato", $stato[0]["nome"]);
if (isset($film["budget"])) {
	Tools::replaceAnchor($page, "budget", number_format($film["budget"],0,',','.') . " $");
} else
	Tools::replaceSection($page, "budget", "");
if (isset($film["incassi"])) {
	Tools::replaceAnchor($page, "incassi", number_format($film["incassi"],0,',','.') . " $");
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
if (isset($_SESSION["id"])) {
	$val = true;
	if ($can_review) {
		Tools::replaceAnchor($page, "review_film_id", $id);
	} else {
		Tools::replaceSection($page, "skip_add_review", "");
		Tools::replaceSection($page, "add_review", "");
	}
	if (!empty($valutazioneUtente)) {
		Tools::toHtml($valutazioneUtente);
		Tools::replaceAnchor($page, "review_delete_film_id", $id);
		Tools::replaceAnchor($page, "tuoutente", $valutazioneUtente[0]["utente"]);
		Tools::replaceAnchor($page, "tuovoto", $valutazioneUtente[0]["voto"]);
		Tools::replaceAnchor($page, "tuotesto", $valutazioneUtente[0]["testo"]);
	} else {
		Tools::replaceSection($page, "valutazione_utente", "");
	}
} else {
	Tools::replaceSection($page, "skip_add_review", "");
	Tools::replaceSection($page, "add_review", "");
	Tools::replaceSection($page, "valutazione_utente", "");
}
if (!empty ($valutazione)) {
	$val = true;
	Tools::toHtml($valutazione);
	$list = Tools::getSection($page, "valutazione");
	$r = "";
	foreach ($valutazione as $v) {
		if(! isset($_SESSION["id"]) || $_SESSION["id"] != $v["utente_id"]) {
			$t = $list;
			Tools::replaceAnchor($t, "utente", $v["utente"]);
			Tools::replaceAnchor($t, "voto", $v["voto"]);
			Tools::replaceAnchor($t, "testo", $v["testo"]);
			$r .= $t;
		}
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
} else {
	Tools::replaceSection($page, "skip_add_movie", "");
	Tools::replaceSection($page, "add_movie", "");
}
if (isset($_SESSION["id"]) && $_SESSION["is_admin"] != 0)
	Tools::replaceAnchor($page, "gest_id", $id);
else
	Tools::replaceSection($page, "edit", "");

Tools::showPage($page);

?>