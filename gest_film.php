<?php

require_once("php/tools.php");
require_once("php/database.php");

if (! isset($_SESSION["id"]) || $_SESSION["is_admin"] == 0) {
	header ("location: login.php");
	exit();
}

$id = isset($_GET["id"]) ? $_GET["id"] : "";

try {
	$connessione = new Database();
	$generi = $connessione->getGeneri();
	$collezioni = $connessione->getCollezioni();
	$stati = $connessione->getStati();
	$paesi = $connessione->getPaesi();
	$persone = $connessione->getPersone();
	$ruoli = $connessione->getRuoli();
	if ($id != "") {
		$film = $connessione->getFilmById($id);
		$film_generi = $connessione->getGenereByFilmId($id);
		$film_paesi = $connessione->getPaeseByFilmId($id);
		$crew = $connessione->getCrewByFilmId($id);
	}
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

$page = Tools::buildPage($_SERVER["SCRIPT_NAME"]);

if ($id != "" && !empty($film)) {
	$film = $film[0];
	$title = $film["nome"] . " • Modifica film"; Tools::toHtml($title, 0);
	Tools::replaceAnchor($page, "title", $title);
	Tools::replaceAnchor($page, "bc_id", $id);
	$bc_nome = $film["nome"]; Tools::toHtml($bc_nome);
	Tools::replaceAnchor($page, "bc_nome", $bc_nome);
	Tools::replaceAnchor($page, "intestazione", "Modifica film");
	Tools::replaceAnchor($page, "gest_id", $id);
	Tools::toHtml($film, 0);
	Tools::replaceAnchor($page, "nome", $film["nome"]);
	Tools::replaceAnchor($page, "descrizione", (isset($film["descrizione"]) ? $film["descrizione"] : ""));
	Tools::replaceAnchor($page, "data_rilascio", (isset($film["data_rilascio"]) ? $film["data_rilascio"] : ""));
	Tools::replaceAnchor($page, "durata", (isset($film["durata"]) ? $film["durata"] : ""));

	// $option = Tools::getSection($page, "persona");
	// $res = "";
	// foreach ($persone as $p) {
	// 	$t = $option;
	// 	Tools::replaceAnchor($t, "nome", $p["id"]);
	// 	Tools::replaceAnchor($t, "id", $p["nome"]);
	// 	$res .= $t;
	// }
	// Tools::replaceSection($page, "persona", $res);

	$sample = Tools::getSection($page, "crew_sample");
	$option = $sample;
	Tools::replaceAnchor($sample, "crew_name_label_id", "");
	Tools::replaceAnchor($sample, "crew_name_input_id", "");
	// Tools::replaceAnchor($sample, "crew_name_input_name", "");
	Tools::replaceAnchor($sample, "value", "");
	Tools::replaceAnchor($sample, "crew_role_label_id", "");
	Tools::replaceAnchor($sample, "crew_role_select_id", "");
	// Tools::replaceAnchor($sample, "crew_role_select_name", "");
	Tools::replaceSection($page, "crew_sample", $sample);

	$t = Tools::getSection($page, "persone_presenti");
	Tools::replaceAnchor($t, "persona_presente", $option, true);
	$option = $t;


	$res = "";
	for ($i = 0; $i < count($crew); $i++) {
		$t = $option;
		Tools::replaceAnchor($t, "crew_name_label_id", $i);
		Tools::replaceAnchor($t, "crew_name_input_id", $i);
		// Tools::replaceAnchor($t, "crew_name_input_name", $i);
		Tools::replaceAnchor($t, "value", ('value="' . $crew[$i]["p_id"] . '"'));
		Tools::replaceAnchor($t, "crew_role_label_id", $i);
		Tools::replaceAnchor($t, "crew_role_select_id", $i);
		// Tools::replaceAnchor($t, "crew_role_select_name", $i);

		$ruolo = Tools::getSection($page, "ruolo");
		$tmp = "";
		foreach ($ruoli as $r) {
			$k = $ruolo;
			Tools::replaceAnchor($k, "id", $r["id"]);
			Tools::replaceAnchor($k, "nome", $r["nome"]);
			Tools::replaceAnchor($k, "sel", ($crew[$i]["r_id"] == $r["id"] ? "selected" : ""));
			$tmp .= $k;
		}
		Tools::replaceSection($t, "ruolo", $tmp);

		$res .= $t;
	}
	Tools::replaceSection($page, "persone_presenti", $res);

	// Tools::replaceAnchor($page, "crew_count", count($crew));

	Tools::toHtml($persone, 1);
	$option = Tools::getSection($page, "persona");
	$res = "";
	foreach ($persone as $p) {
		$t = $option;
		Tools::replaceAnchor($t, "id", $p["id"]);
		Tools::replaceAnchor($t, "nome", $p["nome"]);
		$res .= $t;
	}
	Tools::replaceSection($page, "persona", $res);

	$ruolo = Tools::getSection($page, "ruolo");
	$res = "";
	foreach ($ruoli as $r) {
		$t = $ruolo;
		Tools::replaceAnchor($t, "id", $r["id"]);
		Tools::replaceAnchor($t, "nome", $r["nome"]);
		Tools::replaceAnchor($t, "sel", "");
		$res .= $t;
	}
	Tools::replaceSection($page, "ruolo", $res);



	$fg = [];
	foreach ($film_generi as $t) {
		array_push($fg, $t["id"]);
	}
	$option = Tools::getSection($page, "genere");
	$res = "";
	foreach ($generi as $g) {
		$t = $option;
		Tools::replaceAnchor($t, "genere_label_id", $g["id"]);
		Tools::replaceAnchor($t, "genere_input_id", $g["id"]);
		Tools::replaceAnchor($t, "genere_label_nome", $g["nome"]);
		Tools::replaceAnchor($t, "checked", (in_array($g["id"], $fg) ? "checked" : ""));
		Tools::replaceAnchor($t, "id", $g["id"]);
		$res .= $t;
	}
	Tools::replaceSection($page, "genere", $res);

	$sample = Tools::getSection($page, "nation_sample");
	$option = $sample;
	Tools::replaceAnchor($sample, "paese_label_id", "");
	Tools::replaceAnchor($sample, "paese_input_id", "");
	// Tools::replaceAnchor($sample, "paese_input_name", "");
	Tools::replaceAnchor($sample, "value", "");
	Tools::replaceSection($page, "nation_sample", $sample);

	$t = Tools::getSection($page, "paesi_presenti");
	Tools::replaceAnchor($t, "paese_presente", $option, true);
	$option = $t;

	$res = "";
	for ($i = 0; $i < count($film_paesi); $i++) {
		$t = $option;
		Tools::replaceAnchor($t, "paese_label_id", $i);
		Tools::replaceAnchor($t, "paese_input_id", $i);
		// Tools::replaceAnchor($t, "paese_input_name", $i);
		Tools::replaceAnchor($t, "value", ('value="' . $film_paesi[$i]["id"] . '"'));
		$res .= $t;
	}
	Tools::replaceSection($page, "paesi_presenti", $res);

	// Tools::replaceAnchor($page, "nations_count", count($film_paesi));

	$option = Tools::getSection($page, "paese");
	$res = "";
	foreach ($paesi as $p) {
		$t = $option;
		Tools::replaceAnchor($t, "id", $p["id"]);
		Tools::replaceAnchor($t, "nome", $p["nome"]);
		$res .= $t;
	}
	Tools::replaceSection($page, "paese", $res);

	Tools::replaceAnchor($page, "nome_originale", $film["nome_originale"]);
	$option = Tools::getSection($page, "stato");
	$res = "";
	foreach ($stati as $s) {
		$t = $option;
		Tools::replaceAnchor($t, "id", $s["id"]);
		Tools::replaceAnchor($t, "nome", $s["nome"]);
		Tools::replaceAnchor($t, "sel", (($s["id"] == $film["stato"]) ? "selected" : ""));
		$res .= $t;
	}
	Tools::replaceSection($page, "stato", $res);

	Tools::replaceAnchor($page, "budget", (isset($film["budget"]) ? $film["budget"] : ""));
	Tools::replaceAnchor($page, "incassi", (isset($film["incassi"]) ? $film["incassi"] : ""));

	Tools::toHtml($collezioni, 1);
	$option = Tools::getSection($page, "collezione");
	$res = "";
	foreach ($collezioni as $c) {
		$t = $option;
		Tools::replaceAnchor($t, "id", $c["id"]);
		Tools::replaceAnchor($t, "nome", $c["nome"]);
		Tools::replaceAnchor($t, "sel", (($c["id"] == $film["collezione"]) ? "selected" : ""));
		$res .= $t;
	}
	Tools::replaceSection($page, "collezione", $res);
	Tools::replaceAnchor($page, "submit_value", "modifica");
	Tools::replaceAnchor($page, "submit", "Modifica");
} else {
	Tools::replaceAnchor($page, "title", "Aggiungi film");
	Tools::replaceSection($page, "breadcrumb", "Aggiungi");
	Tools::replaceAnchor($page, "intestazione", "Aggiungi film");
	Tools::replaceAnchor($page, "gest_id", "");
	Tools::replaceAnchor($page, "nome", "");
	Tools::replaceAnchor($page, "descrizione", "");
	Tools::replaceAnchor($page, "data_rilascio", "");
	Tools::replaceAnchor($page, "durata", "");
	Tools::replaceAnchor($page, "nome_originale", "");
	$option = Tools::getSection($page, "stato");
	$res = "";
	foreach ($stati as $s) {
		$t = $option;
		Tools::replaceAnchor($t, "id", $s["id"]);
		Tools::replaceAnchor($t, "nome", $s["nome"]);
		Tools::replaceAnchor($t, "sel", "");
		$res .= $t;
	}
	Tools::replaceSection($page, "stato", $res);
	Tools::replaceAnchor($page, "budget", "");
	Tools::replaceAnchor($page, "incassi", "");
	Tools::toHtml($collezioni);
	$option = Tools::getSection($page, "collezione");
	$res = "";
	foreach ($collezioni as $c) {
		$t = $option;
		Tools::replaceAnchor($t, "id", $c["id"]);
		Tools::replaceAnchor($t, "nome", $c["nome"]);
		Tools::replaceAnchor($t, "sel", "");
		$res .= $t;
	}
	Tools::replaceSection($page, "collezione", $res);
	Tools::replaceAnchor($page, "submit-value", "aggiungi");
	Tools::replaceAnchor($page, "submit", "Aggiungi");
}

Tools::showPage($page);

?>