<?php

require_once("php/tools.php");
require_once("php/database.php");

$user_id = isset($_SESSION["id"]) ? $_SESSION["id"] : "";

if ($user_id == "") {
	header("location: login.php");
	exit();
}

$list_id = isset($_POST["list_id"]) ? $_POST["list_id"] : "";
$film_id = isset($_POST["film_id"]) ? $_POST["film_id"] : "";

if ($film_id == "" || $list_id == "") {
	Tools::errCode(500);
	exit();
}

try {
	$connessione = new Database();
	$res = $connessione->isListaDiUtente($list_id, $user_id);
	if ($res) $res = $connessione->insertFilmInLista($list_id, $film_id);
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if (! $res) {
	$_SESSION["error"] = "Errore durante l'inserimento nella lista.";
	header("location: film.php?id=" . $film_id);
} else {
	$_SESSION["success"] = "Aggiunto correttamente alla lista.";
	header("location: film.php?id=" . $film_id);
}

?>