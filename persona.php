<?php

require_once("php/tools.php");
require_once("php/database.php");

$id = (isset($_GET["id"]) ? ($_GET["id"]) : "");

if ($id == "") {
	Tools::errCode(404);
	exit();
}

try {
	$connessione = new Database();
	$persona = $connessione->getPersonaById($id);
	if (!empty($persona)) {
		$film = $connessione->getFilmByPersonaId($id);
		$gender = $connessione->getGenderById($persona[0]["gender"]);
	}
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if (empty($persona)) {
	Tools::errCode(404);
	exit();
}

$page = Tools::buildPage($_SERVER["SCRIPT_NAME"]);

$persona = $persona[0];
$title = $persona["nome"] . " • Persona"; Tools::toHtml($title, 0);
Tools::replaceAnchor($page, "title", $title);
Tools::toHtml($persona);
Tools::replaceAnchor($page, "breadcrumb", $persona["nome"]);
Tools::replaceAnchor($page, "nome", $persona["nome"]);
Tools::replaceAnchor($page, "gender", $gender[0]["nome"]);
if (isset($persona["data_nascita"]))
	Tools::replaceAnchor($page, "data_nascita", date_format(date_create_from_format('Y-m-d', $persona["data_nascita"]), 'd/m/Y'));
else
	Tools::replaceSection($page, "data_nascita", "");
if (isset($persona["data_morte"]))
	Tools::replaceAnchor($page, "data_morte", date_format(date_create_from_format('Y-m-d', $persona["data_morte"]), 'd/m/Y'));
else
	Tools::replaceSection($page, "data_morte", "");
if (isset($_SESSION["id"]) && $_SESSION["is_admin"] != 0)
	Tools::replaceAnchor($page, "gest_id", $id);
else
	Tools::replaceSection($page, "admin", "");
$immagine = (isset($persona["immagine"]) ? ("https://www.themoviedb.org/t/p/w300/" . $persona["immagine"]) : "img/placeholder.svg");
Tools::replaceAnchor($page, "immagine", $immagine);
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
				Tools::replaceAnchor($c, "data_rilascio", date_format(date_create_from_format('Y-m-d', $f["data_rilascio"]), 'd/m/Y'));
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

?>
