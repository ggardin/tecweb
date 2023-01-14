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
$title = $collezione["nome"] . " • Collezione"; Tools::toHtml($title, 0);
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
	Tools::replaceSection($page, "admin", "");
$immagine = (isset($collezione["locandina"]) ? ("pics/w500_" . $collezione["locandina"] . ".webp") : "img/placeholder.svg");
Tools::replaceAnchor($page, "immagine", $immagine);
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
		$immagine = (isset($f["locandina"]) ? ("pics/w200_" . $f["locandina"] . ".webp") : "img/placeholder.svg");
		Tools::replaceAnchor($c, "immagine", $immagine);
		$res .= $c;
	}
	Tools::replaceSection($page, "card", $res);
} else
	Tools::replaceSection($page, "film", "");

Tools::showPage($page);

?>
