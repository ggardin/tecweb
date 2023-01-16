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

$valid = true;

if (intval($voto) < 1 || intval($voto) > 10) {
	$valid = false;
	$_SESSION["message"] = "Voto non valido.";
} elseif (strlen($testo) < 3 || strlen($testo) > 1000) {
	$valid = false;
	$_SESSION["message"] = "Il testo non è valido.";
}

if (! $valid) {
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
	$_SESSION["message"] = "Errore durante l'inserimento della recensione.";
	header("location: film.php?id=" . $film_id);
} else {
	$_SESSION["message"] = "Recensione inserita correttamente.";
	header("location: film.php?id=" . $film_id);
}

?>