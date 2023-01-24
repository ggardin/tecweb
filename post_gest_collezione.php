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
	if (! preg_match("/^[\w\s\-\.\:\'\[\]\,\/\"\x{00C0}-\x{017F}]+$/u", $titolo))
		array_push($err, "Il titolo inserito contiene caratteri non ammessi.");
}
if (strlen($descrizione) > 10000) {
	array_push($err, "La descrizione deve essere lunga al massimo 10.000 caratteri.");
}
if (! preg_match("/^[^<>]*$/", $descrizione)) {
	array_push($err, "La descrizione inserita contiene caratteri non ammessi.");
}
if (!is_null($locandina) && $_FILES["locandina"]["tmp_name"]) {
	$img = Tools::uploadImg($_FILES["locandina"]);
	if ($img[0]) $locandina = $img[1];
	else {
		array_push($err, $img[1]);
	}
}

if ($err) {
	$_SESSION["error"] = $err;
	header("location: gest_collezione.php?id=" . $id);
	exit();
}

try {
	$connessione = new Database();
	if ($submit == "aggiungi" || $submit == "modifica") {
		$up = $connessione->updateCollezione($id, $titolo, $descrizione, $locandina);
		$res = $up[0];
		if ($res && $submit == "aggiungi") $id = $up[1];
	} elseif ($submit == "elimina") {
		$res = $connessione->deleteCollezione($id);
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
	$_SESSION["success"] = ["Collezione aggiunta correttamente."];
} elseif ($submit == "modifica") {
	$_SESSION["success"] = ["Collezione modificata correttamente."];
} else {
	$_SESSION["success"] = ["Collezione eliminata correttamente. Aggiungine un'altra."];
}

header("location: gest_collezione.php" . ($id != "" ? "?id=$id": "" ));

?>