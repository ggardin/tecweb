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
	$own = false;
	if ($connessione->isListaDiUtente($list_id, $user_id)) {
		$own = true;
		$res = $connessione->deleteFromList($list_id, $film_id);
	}
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if (! $res) {
	$_SESSION["message"] = "Errore durante l'eliminazione dalla lista.";
	header("location: list.php?id=$list_id");
} else {
	$_SESSION["message"] = "Rimosso correttamente dalla lista.";
	header("location: list.php?id=$list_id");
}

?>