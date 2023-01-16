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
$immagine = "";
$submit = isset($_POST["submit"]) ? $_POST["submit"] : "";

if (! in_array($submit, ["aggiungi", "modifica", "elimina"]) || ($submit != "aggiungi" && $id == "")) {
	Tools::errCode(500);
	exit();
}

$valid = true;

if (strlen($nome) <= 3) {
	$valid = false;
	$_SESSION["message"] = "Nome troppo corto";
} elseif (isset($_FILES["immagine"]) && $_FILES["immagine"]["tmp_name"]) {
	$img = Tools::uploadImg($_FILES["immagine"]);
	if ($img[0]) $immagine = $img[1];
	else {
		$valid = false;
		$_SESSION["message"] = $img[1];
	}
}

if (! $valid) {
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
	$_SESSION["message"] = "Nessuna modifica apportata.";
	header("location: persona.php?id=" . $id);
} elseif ($submit == "aggiungi") {
	$_SESSION["message"] = "Persona aggiunta correttamente.";
	header("location: persona.php?id=" . $id);
} elseif ($submit == "modifica") {
	$_SESSION["message"] = "Persona modificata correttamente.";
	header("location: persona.php?id=" . $id);
} else {
	$_SESSION["message"] = "Persona eliminata correttamente. Aggiungine un'altra.";
	header("location: gest_persona.php");
}


?>