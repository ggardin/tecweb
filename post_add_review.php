<?php

require_once("php/tools.php");
require_once("php/database.php");

$user_id = isset($_SESSION["id"]) ? $_SESSION["id"] : "";

if ($user_id == "") {
	header("location: login.php");
	exit();
}

$film_id = isset($_POST["film_id"]) ? $_POST["film_id"] : "";
$voto = isset($_POST["voto"]) ? $_POST["voto"] : "";
$testo = isset($_POST["testo"]) ? $_POST["testo"] : "";

if ($film_id == "") {
	Tools::errCode(500);
	exit();
}

$err = "";

if (intval($voto) < 1 || intval($voto) > 10) {
	$err .= "Devi esprimere un voto da 1 a 10. ";
}
if (strlen($testo) < 3 || strlen($testo) > 1000) {
	$err .= "La recensione deve contenere tra i 3 e i 1000 caratteri. ";
}
if (! preg_match("/^[^<>{}]*$/", $testo)) {
	$err .= "La recensione contiene caratteri non ammessi. ";
}

if ($err) {
	$_SESSION["error"] = $err;
	header("location: film.php?id=" . $film_id);
	exit();
}

try {
	$connessione = new Database();
	$res = $connessione->insertValutazione($user_id, $film_id, $voto, $testo);
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if (! $res) {
	$_SESSION["error"] = "Errore durante l'inserimento della recensione.";
	header("location: film.php?id=" . $film_id);
} else {
	$_SESSION["success"] = "Recensione inserita correttamente.";
	header("location: film.php?id=" . $film_id);
}

?>