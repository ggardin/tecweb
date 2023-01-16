<?php

require_once("php/tools.php");
require_once("php/database.php");

if (isset($_SESSION["id"])) {
	header("location: user.php");
	exit();
}

$username = isset($_POST["username"]) ? $_POST["username"] : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";

$valid = true;

if (empty($username)) {
	$valid = false;
	$_SESSION["message"] = "[en]Username[/en] non valido.";
} elseif (empty($password)) {
	$valid = false;
	$_SESSION["message"] = "La [en]password[/en] non è valida.";
}

if (! $valid) {
	header("location: login.php");
	exit();
}

try {
	$connessione = new Database();
	$login = $connessione->login($username, $password);
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if (! empty($login)) {
	$_SESSION["id"] = $login["id"];
	$_SESSION["is_admin"] = $login["is_admin"];
	header("location: user.php");
	exit();
} else {
	$_SESSION["message"] = "Credenziali errate. Riprova.";
	header("location: login.php");
	exit();
}

?>