<?php

require_once("php/tools.php");
require_once("php/database.php");

// controlli admin

$user = isset($_SESSION["id"]) ? $_SESSION["id"] : "";
$id = isset($_POST["gest_id"]) ? $_POST["gest_id"] : "";
$nome = isset($_POST["nome"]) ? $_POST["nome"] : "";
$gender = isset($_POST["gender"]) ? $_POST["gender"] : "";
$immagine = isset($_POST["immagine"]) ? $_POST["immagine"] : null;
$data_nascita = isset($_POST["data_nascita"]) ? $_POST["data_nascita"] : null;
$data_morte = isset($_POST["data_morte"]) ? $_POST["data_morte"] : null;
$submit = isset($_POST["submit"]) ? $_POST["submit"] : "";

$immagine = null;
$data_morte = null;

if ($user == "" || ! in_array($submit, ["aggiungi", "modifica", "elimina"]) || ($submit != "aggiungi" && $id == "")) {
	header("location: index.php");
	exit();
}

if ($nome == "") {
	header("location: gest_persona.php?id=$id");
	exit();
}

try {
	$connessione = new Database();
	if ($submit == "aggiungi")
		$res = $connessione->insertPersona($nome, $gender, $immagine, $data_nascita, $data_morte);
	elseif ($submit == "modifica")
		$res = $connessione->updatePersona($id, $nome, $gender, $immagine, $data_nascita, $data_morte);
	elseif ($submit == "elimina")
		$res = $connessione->deletePersona($id);
	else
		$res = false;
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if ($res) {
	if ($submit == "modifica")
		header("location: persona.php?id=$id");
	else
		header("location: gestione.php");
	exit();
} else {
	// nome duplicato o altro, dare errore
	header("location: gestione.php");
}

?>