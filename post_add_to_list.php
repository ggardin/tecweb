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

try {
	$connessione = new Database();
	$own = false;
	if ($connessione->isListaDiUtente($list_id, $user_id)) {
		$own = true;
		$res = $connessione->insertFilmInLista($list_id, $film_id);
	}
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

header("location: film.php?id=$film_id");
exit();

?>