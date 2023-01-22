<?php

require_once("php/tools.php");
require_once("php/database.php");

if (! isset($_SESSION["id"])) {
	header ("location: login.php");
	exit();
}

try {
	$connessione = new Database();
	$liste = $connessione->getListeByUtenteId($_SESSION["id"]);
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

$page = Tools::buildPage($_SERVER["SCRIPT_NAME"]);

if (!empty($liste)) {
	Tools::toHtml($liste);
	$lista = Tools::getSection($page, "lista");
	$r = "";
	foreach ($liste as $l) {
		$t = $lista;
		Tools::replaceAnchor($t, "id", $l["id"]);
		Tools::replaceAnchor($t, "nome", $l["nome"]);
		$r .= $t;
	}
	Tools::replaceSection($page, "lista", $r);
	Tools::replaceAnchor($page, "message", ("Hai " . count($liste) . (count($liste) > 1 ? " liste" : " lista")));
} else {
	Tools::replaceAnchor($page, "message", "Non hai nessuna lista, inizia creandone una");
	Tools::replaceSection($page, "liste", "");
}

Tools::showPage($page);

?>