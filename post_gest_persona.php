<?php

require_once("php/tools.php");
require_once("php/database.php");

if (! isset($_SESSION["id"]) || $_SESSION["is_admin"] == 0) {
	header ("location: login.php");
	exit();
}

$id = isset($_POST["gest_id"]) ? $_POST["gest_id"] : "";
$nome = isset($_POST["nome"]) ? $_POST["nome"] : "";
$gender = isset($_POST["gender"]) ? $_POST["gender"] : "";
$data_nascita = isset($_POST["data_nascita"]) ? $_POST["data_nascita"] : "";
$data_morte = isset($_POST["data_morte"]) ? $_POST["data_morte"] : "";
$immagine = isset($_POST["elimina-immagine"]) ? null : "";
$submit = isset($_POST["submit"]) ? $_POST["submit"] : "";

if (! in_array($submit, ["aggiungi", "modifica", "elimina"]) || ($submit != "aggiungi" && $id == "")) {
	Tools::errCode(500);
	exit();
}

$err = [];

if ($nome == "") {
	array_push($err, "Nome è un campo richesto.");
} else {
	if (strlen($nome) > 50)
		array_push($err, "Il nome deve essere lungo al massimo 50 caratteri.");
	if (! preg_match("/^[a-zA-Z\.\s\-\'\[\]\/\x{00C0}-\x{017F}]+$/u", $nome))
		array_push($err, "Il nome inserito contiene caratteri non ammessi.");
}
if (!is_null($immagine) && isset($_FILES["immagine"]) && $_FILES["immagine"]["tmp_name"]) {
	$img = Tools::uploadImg($_FILES["immagine"]);
	if ($img[0]) $immagine = $img[1];
	else {
		array_push($err, $img[1]);
	}
}
$dn = false;
$dm = false;
if ($data_nascita != "") {
	if (preg_match("/^([\d]{4})\-(0[1-9]|1[0-2])\-((0|1)[0-9]|2[0-9]|3[0-1])$/", $data_nascita)) {
		$dn = true;
		$date = date_create_immutable($data_nascita);
		$min = date_create_immutable("1800-01-01");
		$now = date_create_immutable("now");
		if ($date < $min)
			array_push($err, "Data di nascita può partire dal 1800.");
		elseif ($date > $now)
			array_push($err, "Data di nascita deve essere antecedente alla data odierna.");
	} else
		array_push($err, "La data di nascita deve essere nel formato YYYY-MM-DD.");
}
if ($data_morte != "") {
	if (preg_match("/^([\d]{4})\-(0[1-9]|1[0-2])\-((0|1)[0-9]|2[0-9]|3[0-1])$/", $data_morte)) {
		$dm = true;
		$date = date_create_immutable($data_morte);
		$min = date_create_immutable("1800-01-01");
		$now = date_create_immutable("now");
		if ($date < $min)
			array_push($err, "Data di morte può partire dal 1800.");
		elseif ($date > $now)
			array_push($err, "Data di morte deve essere antecedente alla data odierna.");
	} else
		array_push($err, "La data di morte deve essere nel formato YYYY-MM-DD.");
}
if ($dn && $dm) {
	$date_n = date_create_immutable($data_nascita);
	$date_m = date_create_immutable($data_morte);
	$diff = date_diff($date_n, $date_m);
	if ($date_n >= $date_m)
		array_push($err, "Le data di nascita deve essere antecedente a quella di morte.");
	elseif (intval($diff->format("%r%y")) > 120)
		array_push($err, "Le date devono differire per un massimo di 120 anni.");
}

if ($err) {
	$_SESSION["error"] = $err;
	header("location: gest_persona.php?id=" . $id);
	exit();
}

try {
	$connessione = new Database();
	if ($submit == "aggiungi" || $submit == "modifica") {
		$up = $connessione->updatePersona($id, $nome, $gender, $immagine, $data_nascita, $data_morte);
		$res = $up[0];
		if ($res && $submit == "aggiungi") $id = $up[1];
	} elseif ($submit == "elimina") {
		$res = $connessione->deletePersona($id);
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
	$_SESSION["success"] = ["Persona aggiunta correttamente."];
} elseif ($submit == "modifica") {
	$_SESSION["success"] = ["Persona modificata correttamente."];
} else {
	$_SESSION["success"] = ["Persona eliminata correttamente. Aggiungine un'altra."];
}

header("location: gest_persona.php" . ($id != "" ? "?id=$id": "" ));

?>