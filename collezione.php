<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

if (isset($_GET["id"])) $id = $_GET["id"];
else {
	Tools::errCode(404);
	exit();
}

$content = "";
$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));


// so che è da cambiare l'indentazione, ma è da spostare comunque tutto in html
	$db_ok = false;
	try {
		$connessione = new Database();
		$collezione = $connessione->getCollezioneById($id);
		if (!empty($collezione))
			$film = $connessione->getFilmByCollezioneId($id);
		unset($connessione);
		$db_ok = true;
	} catch (Exception) {
		unset($connessione);
		Tools::errCode(500);
		exit();
	}
	if ($db_ok) {
		if (!empty($collezione)) {
			$collezione = $collezione[0];
			$title = $collezione["nome"] . " • Collezione"; Tools::toHtml($title, 0);
			Tools::replaceAnchor($page, "title", $title);
			Tools::toHtml($collezione);
			Tools::toHtml($film);
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
			Tools::errCode(404);
			exit();
		}
	}

Tools::replaceAnchor($page, "collezione", $content);

Tools::showPage($page);

?>
