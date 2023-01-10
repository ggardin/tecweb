<?php

require_once("php/tools.php");
require_once("php/database.php");

if (! isset($_SESSION["id"])) {
	header ("location: login.php");
	exit();
}

try {
	$connessione = new Database();
	$n_liste = $connessione->getNumeroListePerUtente($_SESSION["id"]);
	$n_film = $connessione->getNumeroFilmPerUtente($_SESSION["id"]);
	$generi = $connessione->getNumeroPerGenerePerUtente($_SESSION["id"]);
	$lunghi = $connessione->getFilmPiuLunghiPerUtente($_SESSION["id"], 5);
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

$page = Tools::buildPage($_SERVER["SCRIPT_NAME"]);

Tools::replaceAnchor($page, "n_liste", $n_liste[0]["n"]);
Tools::replaceAnchor($page, "n_film", $n_film[0]["n"]);

$mess = true;

if (! empty($generi)) {
	$mess = false;
	Tools::replaceAnchor($page, "n_generi", count($lunghi) < 5);
	$ummagumma = Tools::getSection($page, "generi_riga", "");
	$res = "";
	foreach ($generi as $g) {
		$t = $ummagumma;
		Tools::replaceAnchor($t, "genere", $g["nome"]);
		Tools::replaceAnchor($t, "numero", $g["n"]);
		$res .= $t;
	}
	Tools::replaceSection($page, "generi_riga", $res);
} else
	Tools::replaceSection($page, "generi", "");

if (! empty($lunghi)) {
	$mess = false;
	Tools::replaceAnchor($page, "n_lunghi", count($lunghi));
	Tools::toHtml($lunghi);
	$ummagumma = Tools::getSection($page, "lunghi_riga", "");
	$res = "";
	foreach ($lunghi as $l) {
		$t = $ummagumma;
		Tools::replaceAnchor($t, "nome", $l["nome"]);
		Tools::replaceAnchor($t, "durata", Tools::minutiAStringa($l["durata"]));
		$res .= $t;
	}
	Tools::replaceSection($page, "lunghi_riga", $res);
} else
	Tools::replaceSection($page, "lunghi", "");

if (! $mess)
	Tools::replaceSection($page, "message", "");

Tools::showPage($page);

?>