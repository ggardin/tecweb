<?php

require_once("php/tools.php");
require_once("php/database.php");

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

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
			$title = Tools::langToTag($collezione["nome"], "") . " — " . $title;
			$content .= "<h1>" . Tools::langToTag($collezione["nome"]) . "</h1>";
			$content .= '<img width="250" height="375" src="https://www.themoviedb.org/t/p/w500/' . $collezione["locandina"] . '" alt="" />';
			if (isset($collezione["descrizione"]))
				$content .= "<p>Descrizione: " . Tools::langToTag($collezione["descrizione"]) . "</p>";
			$content .= "<p>film: </p>";

			$content .= "<ol>";
			foreach ($film as $f) {
				$content .= "<li><ul>";
					$content .= '<li>Locandina: ' . '<img width="200" height="300" src="https://www.themoviedb.org/t/p/w500/' . $f["locandina"] . '" alt="" />' . '</li>';
					$content .= '<li>Link: <a href="film.php?id=' . $f["id"] . '">' . Tools::langToTag($f["nome"]) . '</a></li>';
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

Tools::replaceAnchor($page, "title", $title);
Tools::replaceAnchor($page, "collezione", $content);

Tools::showPage($page);

?>
