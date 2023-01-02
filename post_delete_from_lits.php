<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

if (! isset($_SESSION["id"])) {
	header("location: index.php");
	exit();
}

$user_id = $_SESSION["id"];
$list_id = $_POST["list_id"];
$film_id = $_POST["film_id"];

try {
	$connessione = new Database();
	$res = $connessione->deleteFromList($list_id, $user_id, $film_id);
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

header("location: list.php?id=$list_id");
exit();

?>