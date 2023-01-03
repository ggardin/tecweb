<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

$user_id = isset($_SESSION["id"]) ? $_SESSION["id"] : "";
$list_name = isset($_POST["list_name"]) ? $_POST["list_name"] : "";

if ($user_id == "") {
	header("location: index.php");
	exit();
}

try {
	$connessione = new Database();
	$res = $connessione->insertLista($user_id, $list_name);
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

header("location: lists.php");
exit();

?>