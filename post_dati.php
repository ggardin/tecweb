<?php

require_once("php/tools.php");
require_once("php/database.php");

$user = isset($_SESSION["id"]) ? $_SESSION["id"] : "";

if ($user == "") {
	header ("location: login.php");
	exit();
}

$username = isset($_POST["username"]) ? $_POST["username"] : "";
$mail = isset($_POST["mail"]) ? $_POST["mail"] : "";
$nome = isset($_POST["nome"]) ? $_POST["nome"] : "";
$data_nascita = isset($_POST["data_nascita"]) ? $_POST["data_nascita"] : "";
$gender = isset($_POST["gender"]) ? $_POST["gender"] : "";

try {
	$connessione = new Database();
	$res = $connessione->updateUtente($user, $username, $mail, $nome, $gender, $data_nascita, ""); // TODO : update password
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if ($res) {
	$_SESSION["message"] = "Dati modificati correttamente";
} else {
	$_SESSION["message"] = "Qualcosa è andato storto";
}

header("location: dati.php");

?>