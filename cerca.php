<?php

require_once("php/page.php");
require_once("php/database.php");

$page = Page::build(basename($_SERVER["PHP_SELF"], ".php"));

$title = "Cerca";
$content = "";

$err = "Questa ricerca non ha prodotto risultati";

$query = (isset($_GET["query"])) ? $_GET["query"] : "";
$per = (isset($_GET["per"])) ? $_GET["per"] : "film";
$db_ok = false;

try {
	$connessione = new Database();
	$tipo = $per;
	if ($per == "film")
		$cerca = $connessione->searchFilm($query);
	else if ($per == "collezione")
		$cerca = $connessione->searchCollezione($query);
	else if ($per == "genere") {
		$cerca = $connessione->searchFilmByGenere($query);
		$tipo = "film";
	}
	else if ($per == "paese") {
		$cerca = $connessione->searchFilmByPaese($query);
		$tipo = "film";
	}
	else
		$cerca = array();
	$db_ok = true;
} catch (Exception $e) {
	$content .= "<h1>" . $e->getMessage() . "</h1>";
} finally {
	unset($connessione);
}
if ($db_ok) {
	if ($query) $title = $query . " — " . $title;
	$title .= " " . $tipo . ($tipo != $per ? " per " . $per : "");
	if (!empty($cerca)) {
		$t = "";
		foreach ($cerca as $c) {
			$t .= '<div class="card">' . "\n";
			$copertina = ($c["copertina"] ? ("https://www.themoviedb.org/t/p/w500/" . $c["copertina"]) : "img/placeholder.svg");
			$t .= '<img width="200" height="300" width src="' . $copertina . '" alt="Locandina del film" />' . "\n";
			$t .= '<div class="details">' . "\n";
			$t .= '<h2>' . '<a href="' . $tipo . '.php?id=' . $c["id"] . '">' . Page::langToTag($c["nome"]) . '</a>' . '</h2>' . "\n";
			if ($tipo == "film")
				$t .= '<p>' . $c["data_rilascio"] . '</p>' . "\n";
			$t .= '</div>' . "\n";
			$t .= '</div>' . "\n";
		}
		Page::replaceAnchor($page, "card", $t);
	} else {
		$content .= "<h1>" . $err . "</h1>";
	}
}

Page::replaceAnchor($page, "ricerca", ($tipo . ($tipo != $per ? " per " . $per : "") . ': "' . $query . '"'));
Page::replaceAnchor($page, "tipo", $tipo);
Page::replaceAnchor($page, "title", $title);
// Page::replaceAnchor($page, "cerca", $content);

echo($page);

?>
