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
	header("location: gest_collezione.php?id=" . $id);
	exit();
}

try {
	$connessione = new Database();
	if ($submit == "aggiungi" || $submit == "modifica") {
		$res = $connessione->updateCollezione($id, $titolo, $descrizione, $locandina);
		if($submit == "aggiungi") $id = $res[1];
		$res = $res[0];
	} elseif ($submit == "elimina") {
		$res = $connessione->deleteCollezione($id);
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
	$_SESSION["message"] = "Collezione aggiunta correttamente.";
elseif ($submit == "modifica")
	$_SESSION["message"] = "Collezione modificata correttamente.";
else
	$_SESSION["message"] = "Collezione eliminata correttamente. Aggiungine un altro.";

header("location: gest_collezione.php?id=" . $id);

?>