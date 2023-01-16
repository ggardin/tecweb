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
	$_SESSION["message"] = "Username non valido.";
} elseif (empty($password) || $password != $password_confirm) {
	$valid = false;
	$_SESSION["message"] = "Le password non coincidono.";
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
	$_SESSION["message"] = "Questo username è già stato preso. Scegline un'altro.";
	header("location: signup.php");
	exit();
}

?>