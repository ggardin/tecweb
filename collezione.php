<?php

require_once("php/tools.php");
require_once("php/database.php");

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

$content = "";

$err = "Errore: Collezione non presente";

$id = (isset($_GET["id"])) ? $_GET["id"] : "";

if ($id != "") {
	$db_ok = false;
	try {
		$connessione = new Database();
		$collezione = $connessione->getCollezioneById($id);
		if (!empty($collezione)) {
			$collezione = $collezione[0];
			$film = $connessione->getFilmByCollezioneId($id);
		}
		$db_ok = true;
	} catch (Exception $e) {
		Tools::replaceAnchor($page, "title", $e->getMessage());
		Tools::replaceAnchor($page, "breadcrumb", "Errore");
		$content .= "<h1>" . $e->getMessage() . "</h1>";
	} finally {
		unset($connessione);
	}
	if ($db_ok) {
		if (!empty($collezione)) {
			Tools::replaceAnchor($page, "title", Tools::langToTag($collezione["nome"], "") . " · Collezione");
			Tools::replaceAnchor($page, "breadcrumb", Tools::langToTag($collezione["nome"]));
			$content .= "<h1>" . Tools::langToTag($collezione["nome"]) . "</h1>";
			$content .= '<img width="250" height="375" src="' . ($collezione["locandina"] ? ("https://www.themoviedb.org/t/p/w300/" . $collezione["locandina"]) : "img/placeholder.svg") . '" alt="" />';
			if ($collezione["descrizione"])
				$content .= "<p>Descrizione: " . Tools::langToTag($collezione["descrizione"]) . "</p>";
			$content .= "<p>film: </p>";

			$content .= "<ol>";
			foreach ($film as $f) {
				$content .= "<li><ul>";
					$content .= '<img width="250" height="375" src="' . ($f["locandina"] ? ("https://www.themoviedb.org/t/p/w300/" . $f["locandina"]) : "img/placeholder.svg") . '" alt="" />';
					$content .= '<li>Link: <a href="film.php?id=' . $f["id"] . '">' . Tools::langToTag($f["nome"]) . '</a></li>';
					$content .= '<li>Data rilascio: ' . $f["data_rilascio"] . '</li>';
				$content .= "</ul></li>";
			}
			$content .= "</ol>";
		} else {
			Tools::replaceAnchor($page, "title", $err);
			Tools::replaceAnchor($page, "breadcrumb", "Errore");
			$content .= "<h1>" . $err . "</h1>";
		}
	}
} else {
	Tools::replaceAnchor($page, "title", $err);
	Tools::replaceAnchor($page, "breadcrumb", "Errore");
	$content .= "<h1>" . $err . "</h1>";
}

Tools::replaceAnchor($page, "collezione", $content);

Tools::showPage($page);

?>
