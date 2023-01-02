<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

if (! isset($_SESSION["id"])) {
	header("location: index.php");
	exit();
}

$user_id = isset($_SESSION["id"]) ? $_SESSION["id"] : "";
$list_id = isset($_POST["list_id"]) ? $_POST["list_id"] : "";

try {
	$connessione = new Database();
	$own = false;
	if ($connessione->checkListOwnership($list_id, $_SESSION["id"])) {
		$own = true;
		$res = $connessione->deleteList($list_id);
	}
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

header("location: lists.php");
exit();

?>