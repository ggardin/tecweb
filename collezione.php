<?php

require_once("php/page.php");
require_once("php/database.php");

$page = Page::build(basename($_SERVER["PHP_SELF"], ".php"));

$title = "Collezione";
$content = "";

$err = "Collezione non esistente";

$id = (isset($_GET["id"])) ? $_GET["id"] : "";

if ($id != "") {
	$db_ok = false;
	try {
		$connessione = new Database();
		$collezione = $connessione->getCollezioneById($id);
		if (!empty($collezione)) {
			$collezione = $collezione[0];
			$film = $connessione->getFilmInCollezioneById($id);
		}
		$db_ok = true;
	} catch (Exception $e) {
		$content .= "<h1>" . $e->getMessage() . "</h1>";
	} finally {
		unset($connessione);
	}
	if ($db_ok) {
		if (!empty($collezione)) {
			$title = Page::langToTag($collezione["nome"], "") . " -- " . $title;
			$content .= "<h1>" . Page::langToTag($collezione["nome"]) . "</h1>";
			$content .= '<img height="300" size="200" src="https://www.themoviedb.org/t/p/w500/' . $collezione["copertina"] . '" alt="copertina" />';
			$content .= "<p>Descrizione: " . Page::langToTag($collezione["descrizione"]) . "</p>";
			$content .= "<p>film: </p>";

			$content .= "<ol>";
			foreach ($film as $f) {
				$content .= "<li><ul>";
					$content .= '<li>Copertina: ' . '<img height="150" size="100" src="https://www.themoviedb.org/t/p/w500/' . $f["copertina"] . '" alt="copertina" />' . '</li>';
					$content .= '<li>Link: <a href="film.php?id=' . $f["id"] . '">' . Page::langToTag($f["nome"]) . '</a></li>';
					$content .= '<li>Data rilascio: ' . $f["data_rilascio"] . '</li>';
				$content .= "</ul></li>";
			}
			$content .= "</ol>";
		} else {
			$content .= "<h1>" . $err . "</h1>";
		}
	}
} else {
	$content .= "<h1>" . $err . "</h1>";
}

Page::replaceAnchor($page, "title", $title);
Page::replaceAnchor($page, "collezione", $content);

echo($page);

?>
