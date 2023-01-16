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
$locandina = "";
$submit = isset($_POST["submit"]) ? $_POST["submit"] : "";

if (! in_array($submit, ["aggiungi", "modifica", "elimina"]) || ($submit != "aggiungi" && $id == "")) {
	Tools::errCode(500);
	exit();
}

$valid = true;

if (isset($_FILES["locandina"])) {
	$img = Tools::uploadImg($_FILES["locandina"]);
	if ($img[0]) $locandina = $img[1];
	else {
		$valid = false;
		$_SESSION["message"] = $img[1];
	}
} elseif (strlen($titolo) <= 3) {
	$valid = false;
	$_SESSION["message"] = "Il titolo deve avere almeno 3 caratteri";
}

if (! $valid) {
	header("location: gest_film.php?id=" . $id);
	exit();
}

try {
	$connessione = new Database();
	// TODO : transaction
	if ($submit == "aggiungi" || $submit = "modifica") {
		$res = $connessione->updateFilm($id, $titolo, $titolo_originale, $durata, $locandina, $descrizione, $stato, $data_rilascio, $budget, $incassi, $collezione);
		if($submit == "aggiungi") $id = $res[1];
		$res = $res[0];
		$connessione->setFilmCrew($id, $crew_persona, $crew_ruolo);
		$connessione->setFilmGeneri($id, $genere);
		$connessione->setFilmPaesi($id, $paese);
	} elseif ($submit == "elimina") {
		$res = $connessione->deleteFilm($id);
		$id = "";
	} else
		$res = false;
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if (! $res) {
	Tools::errCode(500);
	exit();
}

if ($submit == "aggiungi")
	$_SESSION["message"] = "Film aggiunto correttamente.";
elseif ($submit == "modifica")
	$_SESSION["message"] = "Film modificato correttamente.";
else
	$_SESSION["message"] = "Film eliminato correttamente. Aggiungine un altro.";

header("location: gest_film.php?id=" . $id);

?>