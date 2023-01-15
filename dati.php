<?php

require_once("php/tools.php");
require_once("php/database.php");

if (! isset($_SESSION["id"])) {
	header ("location: login.php");
	exit();
}

try {
	$connessione = new Database();
	$utente = $connessione->getUtenteById($_SESSION["id"]);
	$gender = $connessione->getGenders();
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

$page = Tools::buildPage($_SERVER["SCRIPT_NAME"]);

$utente = $utente[0];
Tools::toHtml($utente);

Tools::replaceAnchor($page, "username", $utente["username"]);
Tools::replaceAnchor($page, "mail", (isset($utente["mail"]) ? $utente["mail"] : ""));
Tools::replaceAnchor($page, "nome", (isset($utente["nome"]) ? $utente["nome"] : ""));
Tools::replaceAnchor($page, "data_nascita", (isset($utente["data_nascita"]) ? $utente["data_nascita"] : ""));

Tools::toHtml($gender, 1);
$option = Tools::getSection($page, "gender");
$res = "";
foreach ($gender as $g) {
	$t = $option;
	Tools::replaceAnchor($t, "id", $g["id"]);
	Tools::replaceAnchor($t, "nome", $g["nome"]);
	Tools::replaceAnchor($t, "sel", ($g["id"] == $utente["gender"] ? "selected" : ""));
	$res .= $t;
}
Tools::replaceSection($page, "gender", $res);

Tools::showPage($page);

?>