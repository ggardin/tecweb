<?php

require_once("php/tools.php");
require_once("php/database.php");

$id = (isset($_GET["id"])) ? $_GET["id"] : "";

$content = "";
$err = "Errore: Persona non presente";
$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

if ($id != "") {
	$db_ok = false;
	try {
		$connessione = new Database();
		$persona = $connessione->getPersonaById($id);
		if (!empty($persona)) {
			$persona = $persona[0];
			$film = $connessione->getFilmByPersonaId($id);
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
		if (!empty($persona)) {
			Tools::replaceAnchor($page, "title", Tools::stripSpanLang($persona["nome"]) . " · Persona");
			Tools::replaceAnchor($page, "breadcrumb", $persona["nome"]);
			$content .= "<h1>" . $persona["nome"] . "</h1>";
			$content .= '<img width="250" height="375" src="' . (isset($persona["immagine"]) ? ("https://www.themoviedb.org/t/p/w300/" . $persona["immagine"]) : "img/placeholder.svg") . '" alt="" />';
			$content .= '<p><span lang="en">Gender</span>: ' . $persona["gender"] . "</p>";
			if (isset($persona["data_nascita"]))
				$content .= "<p>Data nascita: " . $persona["data_nascita"] . "</p>";
			if (isset($persona["data_morte"]))
				$content .= "<p>Data morte: " . $persona["data_morte"] . "</p>";
			$content .= "<p>film: </p>";
			$content .= "<ol>";
			foreach ($film as $f) {
				$content .= "<li><ul>";
					$content .= '<img width="250" height="375" src="' . (isset($f["locandina"]) ? ("https://www.themoviedb.org/t/p/w300/" . $f["locandina"]) : "img/placeholder.svg") . '" alt="" />';
					$content .= '<li>Link: <a href="film.php?id=' . $f["id"] . '">' . $f["nome"] . '</a></li>';
					$content .= '<li>Ruolo: ' . $f["ruolo"] . '</li>';
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

Tools::replaceAnchor($page, "persona", $content);

Tools::showPage($page);

?>
