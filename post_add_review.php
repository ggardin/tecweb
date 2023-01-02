<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

if (! isset($_SESSION["id"])) {
	header("location: index.php");
	exit();
}

$user_id = $_SESSION["id"];
$film_id = $_POST["film_id"];
$voto = isset($_POST["voto"]) ? $_POST["voto"] : "";
$testo = isset($_POST["testo"]) ? $_POST["testo"] : "";

try {
	$connessione = new Database();
	$res = $connessione->addReview($user_id, $film_id, $voto, $testo);
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

header("location: film.php?id=$film_id");
exit();

?>