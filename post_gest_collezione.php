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
$submit = isset($_POST["submit"]) ? $_POST["submit"] : "";

if (isset($_FILES["locandina"])) {
	$img = Tools::uploadImg($_FILES['locandina']);
	if ($img[0])
		$locandina = $img[1];
	else
		$locandina = "";
} else {
	$locandina = "";
}

// TODO controlli

if (! in_array($submit, ["aggiungi", "modifica", "elimina"]) || ($submit != "aggiungi" && $id == "")) {
	header("location: index.php");
	exit();
}

if ($titolo == "") {
	header("location: gest_collezione.php?id=$id");
	exit();
}

try {
	$connessione = new Database();
	if ($submit == "aggiungi" || $submit = "modifica")
		$res = $connessione->updateCollezione($id, $titolo, $descrizione, $locandina);
	elseif ($submit == "elimina")
		$res = $connessione->deleteCollezione($id);
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
		header("location: gest_collezione.php?id=" . $id);
	elseif ($submit == "aggiungi")
		header("location: gest_collezione.php?id=" . $res[1]);
	elseif ($submit == "elimina")
		header("location: cerca_collezione.php");
} else {
	header("location: gest_collezione.php?id" . $id);
	$_SESSION["error"] = "qualcosa è andato storto";
}

?>