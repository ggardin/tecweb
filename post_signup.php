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
$err = "";

if (! preg_replace("/^[A-Za-z0-9]+$/", $username)) {
	$valid = false;
	$err .= "[en]Username[/en] non valido, usa solo lettere o numeri. ";
}
if (strlen($password) < 8) {
	$valid = false;
	$err .= "La [en]password[/en] deve essere lunga almeno 8 caratteri. ";
}
if (! $preg_match("/\d/", $password) || ! $preg_match("/[a-zA-Z]/", $password)) {
	$valid = false;
	$err .= "La [en]password[/en] deve contenere almeno una lettera e un numero. ";
}
if ($password != "" || $password != $password_confirm) {
	$valid = false;
	$err .= "Le [en]password[/en] non coincidono. ";
}

if (! $valid) {
	$_SESSION["error"] = $err;
	header("location: signup.php");
	exit();
}

try {
	$connessione = new Database();
	$signup = $connessione->signup($username, $password);
	$res = $signup[0];
	if ($res) {
		$user_id = $signup[1];
		$connessione->insertLista($user_id, "Da vedere");
		$connessione->insertLista($user_id, "Visti");
		$login = $connessione->login($username, $password);
	}
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if ($res && !empty($login)) {
	$_SESSION["id"] = $login["id"];
	$_SESSION["is_admin"] = $login["is_admin"];
	header("location: user.php");
	exit();
} else {
	$_SESSION["error"] = "Questo [en]username[/en] è in uso da un altro utente. Scegline uno diverso.";
	header("location: signup.php");
	exit();
}

?>