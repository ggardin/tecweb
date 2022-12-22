<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

if (! isset($_SESSION["id"])) {
	header ("location: login.php");
	exit();
}

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

$db_ok = false;
try {
	$connessione = new Database();
	$liste = $connessione->getListsByUserId($_SESSION["id"]);
	$db_ok = true;
} catch (Exception $e) {
	Tools::replaceSection($page, "message", $e->getMessage());
} finally {
	unset($connessione);
}
if ($db_ok) {
	if (!empty($liste)) {
		$lista = Tools::getSection($page, "lista");
		$r = "";
		foreach ($liste as $l) {
			$t = $lista;
			Tools::replaceAnchor($t, "id", $l["id"]);
			Tools::replaceAnchor($t, "nome", $l["nome"]);
			$r .= $t;
		}
		Tools::replaceSection($page, "lista", $r);
		Tools::replaceAnchor($page, "message", ("Ne hai " . count($liste)));
	} else {
		Tools::replaceAnchor($page, "message", "Inizia creandone una.");
		Tools::replaceSection($page, "liste", "");
	}
}

Tools::showPage($page);

?>