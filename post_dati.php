<?php

require_once("php/tools.php");
require_once("php/database.php");

$user_id = isset($_SESSION["id"]) ? $_SESSION["id"] : "";

if ($user_id == "") {
	header("location: login.php");
	exit();
}

$username = isset($_POST["username"]) ? $_POST["username"] : "";
$mail = isset($_POST["mail"]) ? $_POST["mail"] : "";
$nome = isset($_POST["nome"]) ? $_POST["nome"] : "";
$data_nascita = isset($_POST["data_nascita"]) ? $_POST["data_nascita"] : "";
$old_password = isset($_POST["old_password"]) ? $_POST["old_password"] : "";
$new_password = isset($_POST["new_password"]) ? $_POST["new_password"] : "";
$new_password_confirm = isset($_POST["new_password_confirm"]) ? $_POST["new_password_confirm"] : "";
$gender = isset($_POST["gender"]) ? $_POST["gender"] : "";

$valid = true;

if (strlen($username) <= 3) {
	$valid = false;
	$_SESSION["error"] = "[en]Username[/en] deve avere almeno 3 caratteri.";
} elseif ($nome != "" && strlen($nome) <= 3) {
	$valid = false;
	$_SESSION["error"] = "Il nome deve avere almeno 3 caratteri.";
} elseif ($new_password != "") {
	if ($old_password == "") {
		$valid = false;
		$_SESSION["error"] = "Per cambiare password devi inserire la corrente.";
	} elseif ($new_password != $new_password_confirm) {
		$valid = false;
		$_SESSION["error"] = "Le nuove [en]password[/en] non coincidono.";
	}
}

if (! $valid) {
	header("location: dati.php");
	exit();
}

try {
	$connessione = new Database();
	$pw_err = false;
	if ($new_password != "" && empty($connessione->login($username, $old_password)))
		$pw_err = true;
	else
		$res = $connessione->updateUtente($user_id, $username, $mail, $nome, $gender, $data_nascita, $new_password)[0];
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if ($pw_err) {
	$_SESSION["error"] = "Password corrente errata. Nessuna modifica apportata.";
	header("location: dati.php");
} elseif (! $res) {
	$_SESSION["success"] = "Nessuna modifica apportata.";
	header("location: dati.php");
} else {
	$_SESSION["success"] = "Dati aggiornati correttamente.";
	header("location: dati.php");
}

?>