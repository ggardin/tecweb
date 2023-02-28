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

try {
	$connessione = new Database();
	$res = $connessione->deleteRecensione($user_id, $film_id);
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