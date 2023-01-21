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
$err = "";

if (! preg_match("/^[A-Za-z0-9]+$/", $username)) {
	$valid = false;
	$err .= "[en]Username[/en] non valido, usa solo lettere o numeri. ";
}
if ($mail != "" && ! preg_match("/^(([^<>()\[\]\\.,;:\s@\"]+(\.[^<>()\[\]\\.,;:\s@\"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/", $mail)) {
	$valid = false;
	$err .= "Non è un indirizzo [en]email[/en] valido. ";
}
if ($new_password != "" && $old_password == "") {
	$err .= "Devi inserire la vecchia [en]password[/en] per impostarne una nuova. ";
}
if (strlen($new_password) < 8) {
	$valid = false;
	$err .= "La nuova [en]password[/en] deve essere lunga almeno 8 caratteri. ";
}
if ($new_password != "" && (! $preg_match("/\d/", $new_password) || ! $preg_match("/[a-zA-Z]/", $new_password))) {
	$valid = false;
	$err .= "La nuova [en]password[/en] deve contenere almeno una lettera e un numero. ";
}
if ($new_password != "" || $new_password != $new_password_confirm) {
	$valid = false;
	$err .= "Le nuova [en]password[/en] non coincidono. ";
}
if ($nome != "" && ! preg_match("/^[A-Za-z\s'][^\d]*$/", $nome)) {
	$valid = false;
	$err = "Nome non valido. ";
}
// TODO: data

if (! $valid) {
	$_SESSION["error"] = $err;
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