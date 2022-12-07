<?php

require_once("php/page.php");
require_once("php/database.php");

$page = Page::build(basename($_SERVER["PHP_SELF"], ".php"));

$title = "Collezione";
$content = "";

if (isset($_GET["id"])) {
	$db_ok = false;
	try {
		$connessione = new Database();
		$collezione = $connessione->getCollezioneById($_GET["id"])[0];
		$film = $connessione->getFilmInCollezioneById($_GET["id"]);
		$db_ok = true;
	} catch (Exception $e) {
		$content = "<h2>" . $e->getMessage() . "</h2>";
	} finally {
		unset($connessione);
	}
	if ($db_ok) {
		$title = Page::langToNone($collezione["nome"]) . " - " . $title;
		$collezione["nome"] = Page::langToSpan($collezione["nome"]);
		$content = "<h1>" . $collezione["nome"] . "</h1>";
		$content .= '<img height="300" size="200" src="https://www.themoviedb.org/t/p/w500/' . $collezione["copertina"] . '" alt="copertina" />';
		$content .= '<p>Descrizione: ' . $collezione["descrizione"] . '</p>';
		$content .= '<p>film: </p><ol>';
		foreach ($film as $f) {
			$f["nome"] = Page::langToSpan($f["nome"]);
			$content .= '<li><ul>';
				$content .= '<li>Copertina: ' . '<img height="150" size="100" src="https://www.themoviedb.org/t/p/w500/' . $f["copertina"] . '" alt="copertina" />' . '</li>';
				$content .= '<li>Link: <a href="film.php?id=' . $f["id"] . '">' . $f["nome"] . '</a></li>';
				$content .= '<li>Data rilascio: ' . $f["data_rilascio"] . '</li>';
			$content .= '</ul></li>';
		}
		$content .= '</ol>';
	}
} else {
	$content = "<p>¯\_(ツ)_/¯</p>";
}

Page::replaceAnchor($page, "title", $title);
Page::replaceAnchor($page, "collezione", $content);

echo($page);

?>
