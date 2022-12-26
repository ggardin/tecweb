<?php

require_once("php/tools.php");
require_once("php/database.php");

$query = (isset($_GET["q"])) ? $_GET["q"] : "";
$tipo = (isset($_GET["t"])) ? $_GET["t"] : "film";
$f_nome = (isset($_GET["fn"])) ? $_GET["fn"] : "";
$f_val = (isset($_GET["fv"])) ? $_GET["fv"] : "";

$db_ok = false;

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

try {
	$connessione = new Database();
	if ($tipo == "film") {
		if ($f_nome == "genere" && $f_val)
			$cerca = $connessione->searchFilmFilteredByGenere($query, $f_val);
		elseif ($f_nome == "paese" && $f_val)
			$cerca = $connessione->searchFilmFilteredByPaese($query, $f_val);
		elseif ($f_nome == "data" && $f_val)
			$cerca = $connessione->searchFilmFilteredByData($query, $f_val);
		else {
			$cerca = $connessione->searchFilm($query);
			$f_nome = "";
		}
	} elseif ($tipo == "collezione")
		$cerca = $connessione->searchCollezione($query);
	elseif ($tipo == "persona")
		$cerca = $connessione->searchPersona($query);
	else
		$cerca = array();
	unset($connessione);
	$db_ok = true;
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
}
if ($db_ok) {
	if (in_array($tipo, ["film", "collezione", "persona"])) {
		if ($query != "") {
			$intestazione = "Cerca $tipo";
			if ($tipo == "film" && $f_nome != "")
				$intestazione .= " filtrati per $f_nome ($f_val)";
			$titolo = $query . " • " . $intestazione;
			$intestazione .= ': "' . $query . '"';
		} else {
			if ($tipo == "film") {
				$intestazione = "Tutti i film";
				if ($f_nome != "")
					$intestazione .= " filtrati per $f_nome ($f_val)";
			}
			elseif ($tipo == "collezione")
				$intestazione = "Tutte le collezioni";
			else
				$intestazione = "Tutte le persone";
			$titolo = $intestazione;
		}
	} else {
		Tools::errCode(404);
	}
	Tools::replaceAnchor($page, "title", $titolo);
	Tools::replaceAnchor($page, "intestazione", $intestazione);
	if (isset($cerca)) {
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
			if ($tipo == "film" && isset($c["data_rilascio"])) {
				Tools::replaceAnchor($t, "data_rilascio", date_format(date_create_from_format('Y-m-d', $c["data_rilascio"]), 'd/m/Y'));
			} else
				Tools::replaceSection($t, "data", "");
			$r .= $t;
		}
		Tools::replaceSection($page, "card", $r);
		Tools::replaceAnchor($page, "message", (count($cerca) . (count($cerca) != 1 ? " risultati" : " risultato")));
	} else {
		Tools::replaceAnchor($page, "message", "Questa ricerca non ha prodotto risultati");
		Tools::replaceSection($page, "results", "");
	}
}

Tools::showPage($page);

?>