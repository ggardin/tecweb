<?php

require_once("php/tools.php");
require_once("php/database.php");

if (! isset($_SESSION["id"]) || $_SESSION["is_admin"] == 0) {
	header ("location: login.php");
	exit();
}

$id = isset($_POST["gest_id"]) ? $_POST["gest_id"] : "";
$titolo = isset($_POST["titolo"]) ? $_POST["titolo"] : "";
$descrizione = isset($_POST["descrizione"]) ? $_POST["descrizione"] : "";
$data_rilascio = isset($_POST["data"]) ? $_POST["data"] : "";
$durata = isset($_POST["durata"]) ? $_POST["durata"] : "";
$crew_persona = isset($_POST["crew-person"]) ? $_POST["crew-person"] : [];
$crew_ruolo = isset($_POST["crew-role"]) ? $_POST["crew-role"] : [];
$genere = isset($_POST["genere"]) ? $_POST["genere"] : [];
$paese = isset($_POST["nation"]) ? $_POST["nation"] : [];
$titolo_originale = isset($_POST["titolo_originale"]) ? $_POST["titolo_originale"] : "";
$stato = isset($_POST["stato"]) ? $_POST["stato"] : "";
$budget = isset($_POST["budget"]) ? $_POST["budget"] : "";
$incassi = isset($_POST["incassi"]) ? $_POST["incassi"] : "";
$collezione = isset($_POST["collezione"]) ? $_POST["collezione"] : "";
$locandina = isset($_POST["elimina-locandina"]) ? null : "";
$submit = isset($_POST["submit"]) ? $_POST["submit"] : "";

if (! in_array($submit, ["aggiungi", "modifica", "elimina"]) || ($submit != "aggiungi" && $id == "")) {
	Tools::errCode(500);
	exit();
}

$err = [];

if ($titolo == "") {
	array_push($err, "Titolo è un campo richesto.");
} else {
	if (strlen($titolo) > 200)
		array_push($err, "Il titolo deve essere lungo al massimo 200 caratteri.");
	if (! preg_match("/^[^<>{}]*$/", $titolo))
		array_push($err, "Il titolo inserito contiene caratteri non ammessi.");
}
if (strlen($descrizione) > 10000) {
	array_push($err, "La descrizione deve essere lunga al massimo 10.000 caratteri.");
}
if (! preg_match("/^[^<>]*$/", $descrizione)) {
	array_push($err, "La descrizione inserita contiene caratteri non ammessi.");
}
if (!is_null($locandina) && isset($_FILES["locandina"]) && $_FILES["locandina"]["tmp_name"]) {
	$img = Tools::uploadImg($_FILES["locandina"]);
	if ($img[0]) $locandina = $img[1];
	else {
		array_push($err, $img[1]);
	}
}
if ($data_rilascio != "") {
	if (preg_match("/^([\d]{4})\-(0[1-9]|1[0-2])\-((0|1)[0-9]|2[0-9]|3[0-1])$/", $data_rilascio)) {
		$date = date_create_immutable($data_rilascio);
		$min = date_create_immutable("1800-01-01");
		$max = date_create_immutable("2099-12-31");
		if ($date < $min)
			array_push($err, "Data di rilascio può partire dal 1800.");
		elseif ($date > $max)
			array_push($err, "Data di rilascio può arrivare fino al 2099.");
	} else
		array_push($err, "La data di rilascio deve essere nel formato YYYY-MM-DD.");
}
if ($durata != "" && (intval($durata) <= 0 || intval($durata) > 1000)) {
	array_push($err, "La durata deve essere tra 1 e 1000 minuti.");
}
if (strlen($titolo_originale) > 200) {
	array_push($err, "Il titolo originale deve essere lungo al massimo 200 caratteri.");
}
if (! preg_match("/^[^<>{}]*$/", $titolo_originale)) {
	array_push($err, "Il titolo originale inserito contiene caratteri non ammessi.");
}
if ($budget != "" && (intval($budget) <= 0)) {
	array_push($err, "Il [en]budget[/en] deve essere superiore a 0.");
}
if ($incassi != "" && (intval($incassi) <= 0)) {
	array_push($err, "Gli incassi devono essere superiori a 0.");
}

if ($err) {
	$_SESSION["error"] = $err;
	header("location: gest_film.php?id=" . $id);
	exit();
}

try {
	$connessione = new Database();
	if ($submit == "aggiungi" || $submit == "modifica") {
		$up = $connessione->updateFilm($id, $titolo, $titolo_originale, $durata, $locandina, $descrizione, $stato, $data_rilascio, $budget, $incassi, $collezione);
		$res = $up[0];
		if($submit == "aggiungi") $id = $up[1];
		$res = $connessione->setFilmCrew($id, $crew_persona, $crew_ruolo) || $res;
		$res = $connessione->setFilmGeneri($id, $genere) || $res;
		$res = $connessione->setFilmPaesi($id, $paese) || $res;
	} elseif ($submit == "elimina") {
		$res = $connessione->deleteFilm($id);
		$id = "";
	}
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if (! $res) {
	$_SESSION["success"] = ["Nessuna modifica apportata."];
} elseif ($submit == "aggiungi") {
	$_SESSION["success"] = ["Film aggiunto correttamente."];
} elseif ($submit == "modifica") {
	$_SESSION["success"] = ["Film modificato correttamente."];
} else {
	$_SESSION["success"] = ["Film eliminato correttamente. Aggiungine un altro."];
}

header("location: gest_film.php" . ($id != "" ? "?id=$id": "" ));

?>