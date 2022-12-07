<?php

require_once("php/page.php");
require_once("php/database.php");

$page = Page::build(basename($_SERVER["PHP_SELF"], ".php"));

$title = "Film";
$content = "";

if (isset($_GET["id"])) {
	$db_ok = false;
	try {
		$connessione = new Database();
		$film = $connessione->getFilmById($_GET["id"])[0];
		$collezione = $connessione->getCollezioneById($film["collezione"]);
		$genere = $connessione->getGenereByFilmId($_GET["id"]);
		$paese = $connessione->getPaeseByFilmId($_GET["id"]);
		$valutazione = $connessione->getValutazioneByFilmId($_GET["id"]);
		$db_ok = true;
	} catch (Exception $e) {
		$content = "<h2>" . $e->getMessage() . "</h2>";
	} finally {
		unset($connessione);
	}
	if ($db_ok) {
		$title = Page::langToNone($film["nome"]) . " - " . $title;
		$film["nome"] = Page::langToSpan($film["nome"]);
		$film["nome_originale"] = Page::langToSpan($film["nome_originale"]);
		$film["descrizione"] = Page::langToSpan($film["descrizione"]);
		$content = "<h1>" . $film["nome"] . "</h1>";
		$content .= '<img height="300" size="200" src="https://www.themoviedb.org/t/p/w500/' . $film["copertina"] . '" alt="copertina" />';
		$content .= '<p>Titolo originale: ' . $film["nome_originale"] . '</p>';
		$content .= '<p>Durata: ' . $film["durata"] . ' min</p>';
		$content .= '<p>Descrizione: ' . $film["descrizione"] . '</p>';
		$content .= '<p>Data rilascio: ' . $film["data_rilascio"] . '</p>';
		$content .= '<p>Stato: ' . $film["stato"] . '</p>';
		$content .= '<p>Budget: ' . $film["budget"] . ' $</p>';
		$content .= '<p>Incassi: ' . $film["incassi"] . ' $</p>';
		$content .= "<p>Voto: " . ($film["voto"] ?: "¯\_(ツ)_/¯") . "</p>";
		if (!empty($collezione)) {
			$collezione = $collezione[0];
			$content .= '<p>Collezione: <a href="collezione.php?id=' . $film["collezione"] . '">' . $collezione["nome"] . '</a></p>';
		}
		if (!empty($paese)) {
			// $content .= "<p>Paesi produzione: <ul>" . print_r($paese, true) . "</pre>";
			$content .= '<p>Paesi di produzione: </p>';
			$content .= '<ul>';
			foreach ($paese as $p)
				$content .= '<li>' . $p["nome"] . '</li>';
			$content .= '</ul>';
		}
		if (!empty($genere)) {
			$content .= '<p>Generi: </p>';
			$content .= '<ul>';
			foreach ($genere as $g)
				$content .= '<li><a href="cerca.php?q=' . $g["nome"] . '&t=genere">' . $g["nome"] . '</a></li>' ;
			$content .= '</ul>';
		}
		if (!empty($valutazione)) {
			$content .= '<p>Valutazioni: </p><ol>';
			foreach ($valutazione as $v) {
				$content .= '<li><ul>';
					$content .= '<li>Utente: ' . $v["username"] . '</li>';
					$content .= '<li>Valore: ' . $v["valore"] . '</li>';
					$content .= '<li>Testo: ' . $v["testo"] . '</li>';
				$content .= '</ul></li>';
			}
			$content .= '</ol>';
		}
	}
} else {
	$content = "<p>¯\_(ツ)_/¯</p>";
}

Page::replaceAnchor($page, "title", $title);
Page::replaceAnchor($page, "film", $content);

echo($page);

?>
