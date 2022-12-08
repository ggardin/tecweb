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
			$title = Page::langToTag($film["nome"], "") . " — " . $title;
			Page::replaceAnchor($page, "nome", Page::langToTag($film["nome"]));
			$copertina = ($film["copertina"] ? ("https://www.themoviedb.org/t/p/w500/" . $film["copertina"]) : "img/placeholder.svg");
			Page::replaceAnchor($page, "copertina", $copertina);
			Page::replaceAnchor($page, "data_rilascio", $film["data_rilascio"]);
			Page::replaceAnchor($page, "durata", $film["durata"] . " min");
			if ($film["voto"]) Page::replaceAnchor($page, "voto", $film["voto"]);
			Page::replaceAnchor($page, "descrizione", Page::langToTag($film["descrizione"]));
			if (!empty($genere)) {
				$t = "";
				foreach ($genere as $g)
					$t .= '<li><a href="cerca.php?query=' . $g["nome"] . '&per=genere">' . $g["nome"] . '</a></li>';
				Page::replaceAnchor($page, "genere", $t);
			}
			if (!empty($paese)) {
				$t = "";
				foreach ($paese as $p)
					$t .= '<li><a href="cerca.php?query=' . $p["nome"] . '&per=paese">' . $p["nome"] . '</a></li>';
				Page::replaceAnchor($page, "paese_produzione", $t);
			}
			Page::replaceAnchor($page, "nome_originale", Page::langToTag($film["nome_originale"]));
			Page::replaceAnchor($page, "stato", $film["stato"]);
			Page::replaceAnchor($page, "budget", ($film["budget"] . " $"));
			Page::replaceAnchor($page, "incassi", ($film["incassi"] . " $"));
			if (!empty($collezione))
				Page::replaceAnchor($page, "collezione", ('<a href="collezione.php?id=' . $film["collezione"] . '">' . $collezione[0]["nome"] . '</a>'));
			if (!empty($valutazione)) {
				$t = "";
				foreach ($valutazione as $v) {
					$t .= "<li><ul>";
						$t .= '<li>Utente: ' . $v["username"] . '</li>';
						$t .= '<li>Valore: ' . $v["valore"] . '</li>';
						$t .= '<li>Testo: ' . Page::langtoTag($v["testo"]) . '</li>';
					$t .= "</ul></li>";
				}
				Page::replaceAnchor($page, "valutazione", $t);
			}
		} else {
			$content .= "<h1>" . $err . "</h1>";
		}
	}
} else {
	$content .= "<h1>" . $err . "</h1>";
}

Page::replaceAnchor($page, "title", $title);
// Page::replaceAnchor($page, "film", $content);

echo($page);

?>
