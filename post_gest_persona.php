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

$err = "";

if ($nome == "") {
	$err .= "Nome è un campo richesto. ";
} elseif (! preg_match("/^[a-zA-Z\.\s\-\'\[\]\/\x{00C0}-\x{017F}]+$/u", $nome)) {
	$err .= "Il nome inserito contiene caratteri non ammessi. ";
}
// TODO: date
if (!is_null($immagine) && isset($_FILES["immagine"]) && $_FILES["immagine"]["tmp_name"]) {
	$img = Tools::uploadImg($_FILES["immagine"]);
	if ($img[0]) $immagine = $img[1];
	else {
		$err .= $img[1];
	}
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
	$_SESSION["success"] = "Nessuna modifica apportata.";
} elseif ($submit == "aggiungi") {
	$_SESSION["success"] = "Persona aggiunta correttamente.";
} elseif ($submit == "modifica") {
	$_SESSION["success"] = "Persona modificata correttamente.";
} else {
	$_SESSION["success"] = "Persona eliminata correttamente. Aggiungine un'altra.";
}

header("location: gest_persona.php" . ($id != "" ? "?id=$id": "" ));

?>