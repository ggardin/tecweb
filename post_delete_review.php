<?php

require_once("php/tools.php");
require_once("php/database.php");

$user_id = isset($_SESSION["id"]) ? $_SESSION["id"] : "";

if ($user_id == "") {
	header("location: login.php");
	exit();
}

$film_id = isset($_POST["film_id"]) ? $_POST["film_id"] : "";

if ($film_id == "") {
	Tools::errCode(500);
	exit();
}

$err = [];


if ($err) {
	$_SESSION["error"] = $err;
	header("location: film.php?id=" . $film_id);
	exit();
}

try {
	$connessione = new Database();
	$res = $connessione->deleteRecensione($film_id, $user_id);
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if (! $res)
	$_SESSION["error"] = ["Errore durante l'eliminazione della recensione."];
else
	$_SESSION["success"] = ["Recensione eliminata correttamente."];

header("location: film.php?id=" . $film_id);


?>