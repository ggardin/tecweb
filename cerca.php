<?php

require_once("php/tools.php");
require_once("php/database.php");

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

$query = (isset($_GET["q"])) ? $_GET["q"] : "";
$per = (isset($_GET["t"])) ? $_GET["t"] : "film";

$tipo = $per;
if ($per == "genere" || $per == "paese") $tipo = "film";

$title = "Cerca " . $tipo . ($tipo != $per ? " per " . $per : "");
if ($query) $title = $query . " — " . $title;
Tools::replaceAnchor($page, "title", $title);

$err = "Questa ricerca non ha prodotto risultati";

$db_ok = false;

try {
	$connessione = new Database();
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
	Tools::replaceSection($page, "message", "");
	Tools::replaceSection($page, "results", "");
} finally {
	unset($connessione);
}
if ($db_ok) {
	Tools::replaceAnchor($page, "intestazione", "Cerca " . ($tipo . ($tipo != $per ? " per " . $per : "") . ': "' . $query . '"'));
	if (!empty($cerca)) {
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
		Tools::replaceSection($page, "message", "");
	} else {
		Tools::replaceAnchor($page, "message", $err);
		Tools::replaceSection($page, "results", "");
	}
}

Tools::showPage($page);

?>
