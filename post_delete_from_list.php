<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

$user_id = isset($_SESSION["id"]) ? $_SESSION["id"] : "";
$list_id = isset($_POST["list_id"]) ? $_POST["list_id"] : "";
$film_id = isset($_POST["film_id"]) ? $_POST["film_id"] : "";

if ($user_id == "") {
	header("location: index.php");
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

header("location: list.php?id=$list_id");
exit();

?>