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
	$collezione = $connessione->getCollezioneById($id);
	if (!empty($collezione))
		$film = $connessione->getFilmByCollezioneId($id);
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if (empty($collezione)) {
	Tools::errCode(404);
	exit();
}

$page = Tools::buildPage($_SERVER["SCRIPT_NAME"]);

$collezione = $collezione[0];
$meta = $collezione["nome"]; Tools::toHtml($meta, 1);
Tools::replaceAnchor($page, "desc_nomecollezione", $meta);
Tools::replaceAnchor($page, "keys_nomecollezione", $meta);
$title = $meta . " • Collezione";
Tools::replaceAnchor($page, "title", $title);
Tools::toHtml($collezione);
Tools::replaceAnchor($page, "breadcrumb", $collezione["nome"]);
Tools::replaceAnchor($page, "nome", $collezione["nome"]);
if (isset($collezione["descrizione"]))
	Tools::replaceAnchor($page, "descrizione", $collezione["descrizione"]);
else
	Tools::replaceSection($page, "descrizione", "");
if (isset($_SESSION["id"]) && $_SESSION["is_admin"] != 0)
	Tools::replaceAnchor($page, "gest_id", $id);
else
	Tools::replaceSection($page, "edit", "");
if (isset($collezione["locandina"])) {
	Tools::replaceAnchor($page, "immagine_webp", "pics/w500_" . $collezione["locandina"] . ".webp");
	Tools::replaceAnchor($page, "immagine", "pics/w500_" . $collezione["locandina"] . ".jpg");
} else {
	Tools::replaceSection($page, "pic_source", "");
	Tools::replaceAnchor($page, "immagine", "img/placeholder.svg");
}
if (!empty($film)) {
	Tools::toHtml($film);
	$card = Tools::getSection($page, "card");
	$res = "";
	foreach ($film as $f) {
		$c = $card;
		Tools::replaceAnchor($c, "id", $f["id"]);
		Tools::replaceAnchor($c, "nome", $f["nome"]);
		if (isset($f["data_rilascio"]))
			Tools::replaceAnchor($c, "data_rilascio", date_format(date_create_from_format('Y-m-d', $f["data_rilascio"]), 'd/m/Y'));
		else
			Tools::replaceSection($c, "data_rilascio", "");
		if (isset($f["locandina"])) {
			Tools::replaceAnchor($c, "immagine_webp", "pics/w200_" . $f["locandina"] . ".webp");
			Tools::replaceAnchor($c, "immagine", "pics/w200_" . $f["locandina"] . ".jpg");
		} else {
			Tools::replaceSection($c, "pic_source", "");
			Tools::replaceAnchor($c, "immagine", "img/placeholder.svg");
		}
		$res .= $c;
	}
	Tools::replaceSection($page, "card", $res);
} else
	Tools::replaceSection($page, "film", "");

Tools::showPage($page);

?>
