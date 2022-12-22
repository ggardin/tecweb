<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

if (! isset($_SESSION["id"])) {
	header ("location: login.php");
	exit();
}

$id = (isset($_GET["id"])) ? $_GET["id"] : "";

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

if ($id != "") {
	$db_ok = false;
	try {
		$connessione = new Database();
		$own = false;
		if ($connessione->checkListOwnership($_SESSION["id"], $id)) {
			$own = true;
			$nome = $connessione->getListNameById($id);
			$lista = $connessione->getListItemsById($id);
		}
		unset($connessione);
		$db_ok = true;
	} catch (Exception) {
		unset($connessione);
		Tools::errCode(500);
	}
	if ($db_ok) {
		if ($own) {
			Tools::toHtml($nome);
			Tools::replaceAnchor($page, "intestazione", $nome[0]["nome"]);
			Tools::replaceAnchor($page, "breadcrumb", $nome[0]["nome"]);
			Tools::replaceAnchor($page, "title", ($nome[0]["nome"] . " · Lista"));
			if (!empty($lista)) {
				Tools::toHtml($lista);
				$elemento = Tools::getSection($page, "elemento");
				$r = "";
				foreach ($lista as $l) {
					$t = $elemento;
					Tools::replaceAnchor($t, "link", ($l["tipo"] . ".php?id=" . $l["id"]));
					$immagine = (isset($l["locandina"]) ? ("https://www.themoviedb.org/t/p/w300/" . $l["locandina"]) : "img/placeholder.svg");
					Tools::replaceAnchor($t, "immagine", $immagine);
					Tools::replaceAnchor($t, "nome", $l["nome"]);
					if ($l["tipo"] == "film" && isset($l["data_rilascio"]))
						Tools::replaceSection($t, "data", $l["data_rilascio"]);
					else
						Tools::replaceSection($t, "data", "");
					$r .= $t;
				}
				Tools::replaceSection($page, "elemento", $r);
				Tools::replaceAnchor($page, "message", (count($lista) . (count($lista) != 1 ? " elementi" : " elemento") . " in questa lista"));
			} else {
				Tools::replaceAnchor($page, "message", "Questa lista non ha elementi");
				Tools::replaceSection($page, "lista", "");
			}
		} else {
			Tools::errCode(404);
		}
	}
} else {
	Tools::errCode(404);
}

Tools::showPage($page);

?>