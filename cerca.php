<?php

require_once("php/page.php");
require_once("php/database.php");

$page = Page::build(basename($_SERVER["PHP_SELF"], ".php"));

$title = "Cerca";
$content = "";

$err = "Questa ricerca non ha prodotto risultati";

$q = (isset($_GET["q"])) ? $_GET["q"] : "";
$t = (isset($_GET["t"])) ? $_GET["t"] : "film";
$db_ok = false;

try {
	$connessione = new Database();
	if ($t == "film")
		$cerca = $connessione->searchFilm($q);
	else if ($t == "collezione")
		$cerca = $connessione->searchCollezione($q);
	else if ($t == "genere")
		$cerca = $connessione->searchFilmByGenere($q);
	else if ($t == "paese")
		$cerca = $connessione->searchFilmByPaese($q);
	else
		$cerca = array();
	$db_ok = true;
} catch (Exception $e) {
	$content .= "<h1>" . $e->getMessage() . "</h1>";
} finally {
	unset($connessione);
}
if ($db_ok) {
	if ($q) $title = $q . " -- " . $title;
	$title .= " " . $t;
	if (!empty($cerca)) {
		$content .= "<ol>";
		foreach ($cerca as $c) {
			$content .= "<li><ul>";
				$content .= '<li>Copertina: ' . '<img height="150" size="100" src="https://www.themoviedb.org/t/p/w500/' . $c["copertina"] . '" alt="copertina" />' . '</li>';
				if ($t == "film" || $t == "genere" || $t == "paese") {
					$content .= '<li>Link: <a href="film.php?id=' . $c["id"] . '">' . Page::langToTag($c["nome"]) . '</a></li>';
					$content .= '<li>Data rilascio: ' . $c["data_rilascio"] . '</li>';
				} else if ($t == "collezione") {
					$content .= '<li>Link: <a href="collezione.php?id=' . $c["id"] . '">' . Page::langToTag($c["nome"]) . '</a></li>';
				}
			$content .= "</ul></li>";
		}
		$content .= "</ol>";
	} else {
		$content .= "<h1>" . $err . "</h1>";
	}
}

Page::replaceAnchor($page, "title", $title);
Page::replaceAnchor($page, "cerca", $content);

echo($page);

?>
