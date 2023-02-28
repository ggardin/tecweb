<?php

require_once("php/tools.php");
require_once("php/database.php");

if (! isset($_SESSION["id"])) {
	header ("location: login.php");
	exit();
}

try {
	$connessione = new Database();
	$valutazione = $connessione->getValutazioniPerUtente($_SESSION["id"]);
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

$page = Tools::buildPage($_SERVER["SCRIPT_NAME"]);

if (!empty($valutazione)) {
	Tools::replaceSection($page, "message", "");
	Tools::toHtml($valutazione);
	$list = Tools::getSection($page, "valutazione");
	$r = "";
	foreach ($valutazione as $v) {
		$t = $list;
		Tools::replaceAnchor($t, "titolo", $v["nome"]);
		Tools::replaceAnchor($t, "id", $v["id"]);
		Tools::replaceAnchor($t, "voto", $v["voto"]);
		Tools::replaceAnchor($t, "testo", $v["testo"]);
		$r .= $t;
	}
	Tools::replaceSection($page, "valutazione", $r);
} else {
	Tools::replaceAnchor($page, "message", "Non hai ancora lasciato una valutazione, vai alla pagina di un film per lasciarne una.");
	Tools::replaceSection($page, "valutazioni", "");
}

Tools::showPage($page);

?>