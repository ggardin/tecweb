<?php

require_once("php/page.php");
require_once("php/database.php");

$page = Page::build(basename($_SERVER["PHP_SELF"], ".php"));

$title = "Film";
$content = "";

$err = "Film non esistente";

$id = (isset($_GET["id"])) ? $_GET["id"] : "";

if ($id != "") {
	$db_ok = false;
	try {
		$connessione = new Database();
		$film = $connessione->getFilmById($id);
		if (!empty($film)) {
			$film = $film[0];
			$collezione = $connessione->getCollezioneById($film["collezione"]);
			$genere = $connessione->getGenereByFilmId($id);
			$paese = $connessione->getPaeseByFilmId($id);
			$valutazione = $connessione->getValutazioneByFilmId($id);
		}
		$db_ok = true;
	} catch (Exception $e) {
		$content .= "<h1>" . $e->getMessage() . "</h1>";
	} finally {
		unset($connessione);
	}
	if ($db_ok) {
		if (!empty($film)) {
			$title = Page::langToTag($film["nome"], "") . " -- " . $title;
			$content .= "<h1>" . Page::langToTag($film["nome"]) . "</h1>";
			$content .= '<img height="300" size="200" src="https://www.themoviedb.org/t/p/w500/' . $film["copertina"] . '" alt="copertina" />';
			$content .= "<p>Titolo originale: " . Page::langToTag($film["nome_originale"]) . "</p>";
			$content .= "<p>Durata: " . $film["durata"] . " min</p>";
			$content .= "<p>Descrizione: " . Page::langToTag($film["descrizione"]) . "</p>";
			$content .= "<p>Data rilascio: " . $film["data_rilascio"] . "</p>";
			$content .= "<p>Stato: " . $film["stato"] . "</p>";
			$content .= "<p>Budget: " . $film["budget"] . " $</p>";
			$content .= "<p>Incassi: " . $film["incassi"] . " $</p>";
			$content .= "<p>Voto: " . ($film["voto"] ?: "¯\_(ツ)_/¯") . "</p>";
			if (!empty($collezione)) {
				$collezione = $collezione[0];
				$content .= '<p>Collezione: <a href="collezione.php?id=' . $film["collezione"] . '">' . $collezione["nome"] . '</a></p>';
			}
			if (!empty($paese)) {
				$content .= "<p>Paesi di produzione: </p>";
				$content .= "<ul>";
				foreach ($paese as $p)
					$content .= '<li><a href="cerca.php?q=' . $p["nome"] . '&t=paese">' . $p["nome"] . '</a></li>';
				$content .= "</ul>";
			}
			if (!empty($genere)) {
				$content .= "<p>Generi: </p>";
				$content .= "<ul>";
				foreach ($genere as $g)
					$content .= '<li><a href="cerca.php?q=' . $g["nome"] . '&t=genere">' . $g["nome"] . '</a></li>';
				$content .= "</ul>";
			}
			if (!empty($valutazione)) {
				$content .= "<p>valutazioni: </p>";
				$content .= "<ol>";
				foreach ($valutazione as $v) {
					$content .= "<li><ul>";
						$content .= '<li>Utente: ' . $v["username"] . '</li>';
						$content .= '<li>Valore: ' . $v["valore"] . '</li>';
						$content .= '<li>Testo: ' . Page::langtoTag($v["testo"]) . '</li>';
					$content .= "</ul></li>";
				}
				$content .= "</ol>";
			}
		} else {
			$content .= "<h1>" . $err . "</h1>";
		}
	}
} else {
	$content .= "<h1>" . $err . "</h1>";
}

Page::replaceAnchor($page, "title", $title);
Page::replaceAnchor($page, "film", $content);

echo($page);

?>
