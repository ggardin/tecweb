<?php

require_once("php/tools.php");
require_once("php/database.php");

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

$id = (isset($_GET["id"])) ? $_GET["id"] : "";

$err = "Errore: Film non presente";

if ($id != "") {
	$db_ok = false;
	try {
		$connessione = new Database();
		$film = $connessione->getFilmById($id);
		if (!empty($film)) {
			$film = $film[0];
			$collezione = $connessione->getCollezioneById($film["collezione"]);
			$crew = $connessione->getCrewByFilmId($id);
			$genere = $connessione->getGenereByFilmId($id);
			$paese = $connessione->getPaeseByFilmId($id);
			$valutazione = $connessione->getValutazioneByFilmId($id);
		}
		$db_ok = true;
	} catch (Exception $e) {
		Tools::replaceAnchor($page, "title", $e->getMessage());
		Tools::replaceSection($page, "main", ("<h1>" . $e->getMessage() . "</h1>"));
	} finally {
		unset($connessione);
	}
	if ($db_ok) {
		if (!empty($film)) {
			Tools::replaceAnchor($page, "title", Tools::langToTag($film["nome"], "") . " · Film");
			Tools::replaceAnchor($page, "nome_film", Tools::langToTag($film["nome"]));
			$locandina = ($film["locandina"] ? ("https://www.themoviedb.org/t/p/w300/" . $film["locandina"]) : "img/placeholder.svg");
			Tools::replaceAnchor($page, "locandina", $locandina);
			$r = "";
			if (isset($film["data_rilascio"]))
				$r .= $film["data_rilascio"];
			if (isset($film["durata"]))
				$r .= ($r ? " · " : "") . $film["durata"] . " min";
			if (isset($film["voto"]))
				$r .= ($r ? " · " : "") . $film["voto"] . " *";
			if ($r)
				Tools::replaceAnchor($page, "sottotitolo", $r);
			else
				Tools::replaceSection($page, "sottotitolo", "");
			if (isset($film["descrizione"]))
				Tools::replaceAnchor($page, "descrizione", Tools::langToTag($film["descrizione"]));
			else
				Tools::replaceSection($page, "descrizione", "");
			if (!empty($crew)) {
				$ruolo = Tools::getSection($page, "ruolo");
				$persona = Tools::getSection($page, "persona");
				$res = "";
				$last_ruolo = "";
				foreach ($crew as $c) {
					if ($c["ruolo"] != $last_ruolo) {
						$r = $ruolo;
						Tools::replaceAnchor($r, "ruolo", $c["ruolo"]);
						$last_ruolo = $c["ruolo"];
						$res .= $r;
					}
					$p = $persona;
					Tools::replaceAnchor($p, "nome", Tools::langToTag($c["p_nome"]));
					Tools::replaceAnchor($p, "id", $c["p_id"]);
					$res .= $p;
				}
				Tools::replaceSection($page, "ruoli", $res);
			} else
				Tools::replaceSection($page, "crew", "");
			if (!empty($genere)) {
				$list = Tools::getSection($page, "genere");
				$r = "";
				foreach ($genere as $g) {
					$t = $list;
					Tools::replaceAnchor($t, "valore", $g["nome"]);
					Tools::replaceAnchor($t, "nome", $g["nome"]);
					$r .= $t;
				}
				Tools::replaceSection($page, "genere", $r);
			} else
				Tools::replaceSection($page, "generi", "");
			if (!empty($paese)) {
				$list = Tools::getSection($page, "paese");
				$r = "";
				foreach ($paese as $p) {
					$t = $list;
					Tools::replaceAnchor($t, "valore", $p["nome"]);
					Tools::replaceAnchor($t, "nome", $p["nome"]);
					$r .= $t;
				}
				Tools::replaceSection($page, "paese", $r);
			} else
				Tools::replaceSection($page, "paesi", "");
			Tools::replaceAnchor($page, "nome_originale", Tools::langToTag($film["nome_originale"]));
			Tools::replaceAnchor($page, "stato", $film["stato"]);
			if (isset($film["budget"])) {
				Tools::ReplaceAnchor($page, "budget", $film["budget"] . " $");
			} else
				Tools::replaceSection($page, "budget", "");
			if (isset($film["incassi"])) {
				Tools::ReplaceAnchor($page, "incassi", $film["incassi"] . " $");
			} else
				Tools::replaceSection($page, "incassi", "");
			if (isset($film["collezione"])) {
				$c = Tools::getSection($page, "collezione");
				Tools::ReplaceAnchor($c, "id", $film["collezione"]);
				Tools::ReplaceAnchor($c, "nome", Tools::langToTag($collezione[0]["nome"]));
				Tools::ReplaceSection($page, "collezione", $c);
			} else
				Tools::replaceSection($page, "collezione", "");
			if (!empty($valutazione)) {
				$list = Tools::getSection($page, "valutazione");
				$r = "";
				foreach ($valutazione as $v) {
					$t = $list;
					Tools::replaceAnchor($t, "utente", $v["utente"]);
					Tools::replaceAnchor($t, "valore", $v["valore"]);
					if (isset($v["testo"])) {
						Tools::replaceAnchor($t, "testo", Tools::langToTag($v["testo"]));
					} else
						Tools::replaceSection($t, "testo", "");
					$r .= $t;
				}
				Tools::replaceSection($page, "valutazione", $r);
			} else
				Tools::replaceSection($page, "valutazioni", "");
		} else {
			Tools::replaceAnchor($page, "title", $err);
			Tools::replaceSection($page, "main", ("<h1>" . $err . "</h1>"));
		}
	}
} else {
	Tools::replaceAnchor($page, "title", $err);
	Tools::replaceSection($page, "main", ("<h1>" . $err . "</h1>"));
}

Tools::showPage($page);

?>
