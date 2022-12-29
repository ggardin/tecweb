<?php

require_once("php/tools.php");
require_once("php/database.php");

if (isset($_GET["id"]) && $_GET["id"] != "") $id = $_GET["id"];
else {
	Tools::errCode(404);
	exit();
}

$db_ok = false;
try {
	$connessione = new Database();
	$persona = $connessione->getPersonaById($id);
	if (!empty($persona)) {
		$film = $connessione->getFilmByPersonaId($id);
	}
	unset($connessione);
	$db_ok = true;
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}
if ($db_ok) {
	if (empty($persona)) {
		Tools::errCode(404);
		exit();
	}
	// else
	$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));
	$persona = $persona[0];
	$title = $persona["nome"] . " • Persona"; Tools::toHtml($title, 0);
	Tools::replaceAnchor($page, "title", $title);
	Tools::toHtml($persona);
	Tools::replaceAnchor($page, "breadcrumb", $persona["nome"]);
	Tools::replaceAnchor($page, "nome", $persona["nome"]);
	$immagine = (isset($persona["immagine"]) ? ("https://www.themoviedb.org/t/p/w300/" . $persona["immagine"]) : "img/placeholder.svg");
	Tools::replaceAnchor($page, "immagine", $immagine);
	if (isset($persona["data_nascita"]))
		Tools::replaceAnchor($page, "data_nascita", $persona["data_nascita"]);
	else
		Tools::replaceSection($page, "data_nascita", "");
	if (isset($persona["data_morte"]))
		Tools::replaceAnchor($page, "data_morte", $persona["data_morte"]);
	else
		Tools::replaceSection($page, "data_morte", "");
	Tools::replaceAnchor($page, "gender", $persona["gender"]);
	if (!empty($film)) {
		Tools::toHtml($film);
		$card = Tools::getSection($page, "card");
		$ruolo = Tools::getSection($page, "ruolo");
		$res = "";
		$last_film_id = -1;
		$last_film = "";
		$last_film_ruoli = "";
		foreach ($film as $f) {
			if ($f["id"] != $last_film_id) {
				Tools::replaceSection($last_film, "ruolo", $last_film_ruoli);
				$res .= $last_film;
				$c = $card;
				Tools::replaceAnchor($c, "id", $f["id"]);
				$immagine = (isset($f["locandina"]) ? ("https://www.themoviedb.org/t/p/w300/" . $f["locandina"]) : "img/placeholder.svg");
				Tools::replaceAnchor($c, "immagine", $immagine);
				Tools::replaceAnchor($c, "nome", $f["nome"]);
				if (isset($f["data_rilascio"]))
					Tools::replaceAnchor($c, "data_rilascio", $f["data_rilascio"]);
				else
					Tools::replaceSection($c, "data_rilascio", "");
				$last_film = $c;
				$last_film_ruoli = "";
				$last_film_id = $f["id"];
			}
			$r = $ruolo;
			Tools::replaceAnchor($r, "ruolo", $f["ruolo"]);
			$last_film_ruoli .= $r;
		}
		Tools::replaceSection($last_film, "ruolo", $last_film_ruoli);
		$res .= $last_film;
		Tools::replaceSection($page, "card", $res);
	} else
		Tools::replaceSection($page, "partecipazioni", "");
	Tools::showPage($page);
}

?>
