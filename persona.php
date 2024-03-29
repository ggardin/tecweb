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
	if (!empty($persona))
		$film = $connessione->getFilmByPersonaId($id);
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
$meta = $persona["nome"]; Tools::toHtml($meta, 1);
Tools::replaceAnchor($page, "desc_nomepersona", $meta);
Tools::replaceAnchor($page, "keys_nomepersona", $meta);
$title = $meta . " • Persona";
Tools::replaceAnchor($page, "title", $title);
Tools::toHtml($persona);
Tools::replaceAnchor($page, "breadcrumb", $persona["nome"]);
Tools::replaceAnchor($page, "nome", $persona["nome"]);
Tools::replaceAnchor($page, "gender", $persona["gender_nome"]);
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
	Tools::replaceSection($page, "edit", "");
if (isset($persona["immagine"])) {
	Tools::replaceAnchor($page, "immagine_webp", "pics/w500_" . $persona["immagine"] . ".webp");
	Tools::replaceAnchor($page, "immagine", "pics/w500_" . $persona["immagine"] . ".jpg");
} else {
	Tools::replaceSection($page, "pic_source", "");
	Tools::replaceAnchor($page, "immagine", "img/placeholder.svg");
}
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
			if (isset($f["locandina"])) {
				Tools::replaceAnchor($c, "immagine_webp", "pics/w200_" . $f["locandina"] . ".webp");
				Tools::replaceAnchor($c, "immagine", "pics/w200_" . $f["locandina"] . ".jpg");
			} else {
				Tools::replaceSection($c, "pic_source", "");
				Tools::replaceAnchor($c, "immagine", "img/placeholder.svg");
			}
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
