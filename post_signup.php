<?php

require_once("php/tools.php");
require_once("php/database.php");

if (isset($_SESSION["id"])) {
	header("location: user.php");
	exit();
}

$username = isset($_POST["username"]) ? $_POST["username"] : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";
$password_confirm = isset($_POST["password_confirm"]) ? $_POST["password_confirm"] : "";

$valid = true;

if (empty($username)) {
	$valid = false;
	$_SESSION["message"] = "[en]Username[/en] non valido.";
} elseif (empty($password) || $password != $password_confirm) {
	$valid = false;
	$_SESSION["message"] = "Le [en]password[/en] non coincidono.";
}

if (! $valid) {
	header("location: signup.php");
	exit();
}

try {
	$connessione = new Database();
	// TODO : transaction
	$signup = $connessione->signup($username, $password);
	if ($signup[0]) {
		$user_id = $signup[1];
		$connessione->insertLista($user_id, "Da vedere");
		$connessione->insertLista($user_id, "Visti");
		$res = $connessione->login($username, $password);
	}
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if (isset($res) && !empty($res)) {
	$_SESSION["id"] = $res["id"];
	$_SESSION["is_admin"] = $res["is_admin"];
	header("location: user.php");
	exit();
} else {
	$_SESSION["message"] = "Questo [en]username[/en] è in uso da un altro utente. Scegline uno diverso.";
	header("location: signup.php");
	exit();
}

?>