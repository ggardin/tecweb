<?php

require_once("php/tools.php");
require_once("php/database.php");

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

$query = (isset($_GET["q"])) ? $_GET["q"] : "";
$per = (isset($_GET["t"])) ? $_GET["t"] : "film";

$tipo = $per;
if ($per == "collezione" || $per == "genere") $tipo = "film";

$title = "Cerca " . $tipo . ($tipo != $per ? " per " . $per : "");
if ($query) $title = $query . " — " . $title;
Tools::replaceAnchor($page, "title", $title);

$err = "Questa ricerca non ha prodotto risultati";

$db_ok = false;

try {
	$connessione = new Database();
	$tipo = $per;
	if ($per == "film")
		$cerca = $connessione->searchFilm($query);
	else if ($per == "collezione")
		$cerca = $connessione->searchCollezione($query);
	else if ($per == "genere")
		$cerca = $connessione->searchFilmByGenere($query);
	else if ($per == "paese")
		$cerca = $connessione->searchFilmByPaese($query);
	else
		$cerca = array();
	$db_ok = true;
} catch (Exception $e) {
	Tools::replaceAnchor($page, "intestazione", $e->getMessage());
	Tools::replaceSection($page, "content", "");
} finally {
	unset($connessione);
}
if ($db_ok) {
	Tools::replaceAnchor($page, "cerca", ($tipo . ($tipo != $per ? " per " . $per : "") . ': "' . $query . '"'));
	if (!empty($cerca)) {
		Tools::replaceAnchor($page, "intestazione", $tipo);
		$card = Tools::getSection($page, "card");
		$r = "";
		foreach ($cerca as $c) {
			$t = $card;
			$copertina = ($c["copertina"] ? ("https://www.themoviedb.org/t/p/w500/" . $c["copertina"]) : "img/placeholder.svg");
			Tools::replaceAnchor($t, "copertina", $copertina);
			Tools::replaceAnchor($t, "link", ($tipo . ".php?id=" . $c["id"]));
			Tools::replaceAnchor($t, "nome", Tools::langToTag($c["nome"]));
			if (isset($c["data_rilascio"])) {
				Tools::replaceAnchor($t, "data_rilascio", $c["data_rilascio"]);
			} else
				Tools::replaceSection($t, "data", "");
			$r .= $t;
		}
		Tools::replaceSection($page, "card", $r);
	} else {
		Tools::replaceAnchor($page, "intestazione", $err);
		Tools::replaceSection($page, "results", "");
	}
}

Tools::showPage($page);

?>
