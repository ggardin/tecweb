<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

$id = (isset($_GET["id"])) ? $_GET["id"] : "";

$content = "";
$err = "Errore: Collezione non presente";
$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

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
			Tools::replaceAnchor($page, "title", Tools::stripSpanLang($collezione["nome"]) . " · Collezione");
			Tools::replaceAnchor($page, "breadcrumb", $collezione["nome"]);
			$content .= "<h1>" . $collezione["nome"] . "</h1>";
			$content .= '<img width="250" height="375" src="' . (isset($collezione["locandina"]) ? ("https://www.themoviedb.org/t/p/w300/" . $collezione["locandina"]) : "img/placeholder.svg") . '" alt="" />';
			if (isset($collezione["descrizione"]))
				$content .= "<p>Descrizione: " . $collezione["descrizione"] . "</p>";
			$content .= "<p>film: </p>";

			$content .= "<ol>";
			foreach ($film as $f) {
				$content .= "<li><ul>";
					$content .= '<img width="250" height="375" src="' . (isset($f["locandina"]) ? ("https://www.themoviedb.org/t/p/w300/" . $f["locandina"]) : "img/placeholder.svg") . '" alt="" />';
					$content .= '<li>Link: <a href="film.php?id=' . $f["id"] . '">' . $f["nome"] . '</a></li>';
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
