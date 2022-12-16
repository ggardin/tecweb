<?php

require_once("php/tools.php");
require_once("php/database.php");

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

$query = (isset($_GET["q"])) ? $_GET["q"] : "";
$tipo = (isset($_GET["t"])) ? $_GET["t"] : "film";
$f_nome = (isset($_GET["fn"])) ? $_GET["fn"] : "";
$f_val = (isset($_GET["fv"])) ? $_GET["fv"] : "";

$db_ok = false;

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
	$db_ok = true;
} catch (Exception $e) {
	Tools::replaceAnchor($page, "title", $e->getMessage());
	Tools::replaceAnchor($page, "intestazione", $e->getMessage());
	Tools::replaceSection($page, "message", "");
	Tools::replaceSection($page, "results", "");
} finally {
	unset($connessione);
}
if ($db_ok) {
	if (in_array($tipo, ["film", "collezione", "persona"])) {
		if ($query != "") {
			$intestazione = "Cerca $tipo";
			if ($tipo == "film" && $f_nome != "")
				$intestazione .= " filtrati per $f_nome ($f_val)";
			$titolo = $query . " · " . $intestazione;
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
		$intestazione = "Errore: parametri di ricerca sbagliati";
		$titolo = $intestazione;
	}
	Tools::replaceAnchor($page, "title", $titolo);
	Tools::replaceAnchor($page, "intestazione", $intestazione);
	if (!empty($cerca)) {
		$card = Tools::getSection($page, "card");
		$r = "";
		foreach ($cerca as $c) {
			$t = $card;
			if ($tipo != "persona")
				$immagine = ($c["locandina"] ? ("https://www.themoviedb.org/t/p/w300/" . $c["locandina"]) : "img/placeholder.svg");
			else
				$immagine = ($c["immagine"] ? ("https://www.themoviedb.org/t/p/w300/" . $c["immagine"]) : "img/placeholder.svg");
			Tools::replaceAnchor($t, "immagine", $immagine);
			Tools::replaceAnchor($t, "link", ($tipo . ".php?id=" . $c["id"]));
			Tools::replaceAnchor($t, "nome", Tools::langToTag($c["nome"]));
			if ($tipo == "film" && $c["data_rilascio"]) {
				Tools::replaceAnchor($t, "data_rilascio", $c["data_rilascio"]);
			} else
				Tools::replaceSection($t, "data", "");
			$r .= $t;
		}
		Tools::replaceSection($page, "card", $r);
		Tools::replaceAnchor($page, "message", (count($cerca) . " risultati"));
	} else {
		Tools::replaceAnchor($page, "message", "Questa ricerca non ha prodotto risultati");
		Tools::replaceSection($page, "results", "");
	}
}

Tools::showPage($page);

?>
