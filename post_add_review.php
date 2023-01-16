<?php

require_once("php/tools.php");
require_once("php/database.php");

$user_id = isset($_SESSION["id"]) ? $_SESSION["id"] : "";
$film_id = isset($_POST["film_id"]) ? $_POST["film_id"] : "";
$voto = isset($_POST["voto"]) ? $_POST["voto"] : "";
$testo = isset($_POST["testo"]) ? $_POST["testo"] : "";

if ($user_id == "") {
	header("location: login.php");
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

header("location: film.php?id=$film_id");
exit();

?>