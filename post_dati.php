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

$err = [];

if (strlen($username) > 30) {
	array_push($err, "[en]Username[/en] può essere lungo al massimo 30 caratteri.");
}
if (! preg_match("/^[A-Za-z0-9]+$/", $username)) {
	array_push($err, "[en]Username[/en] non valido, usa solo lettere o numeri.");
}
if ($mail != "") {
	if (strlen($mail) > 100)
		array_push($err, "L'indirizzo [en]email[/en] può essere lunga al massimo 100 caratteri.");
	if (! filter_var($mail, FILTER_VALIDATE_EMAIL))
		array_push($err, "L'indirizzo [en]email[/en] non è valido.");
}
if ($new_password != "") {
	if ($old_password == "") {
		array_push($err, "Devi inserire la vecchia [en]password[/en] per impostarne una nuova.");
	}
	if (strlen($new_password) < 8) {
		array_push($err, "La nuova [en]password[/en] deve essere lunga almeno 8 caratteri.");
	}
	if (! preg_match("/\d/", $new_password) || ! preg_match("/[a-zA-Z]/", $new_password)) {
		array_push($err, "La nuova [en]password[/en] deve contenere almeno una lettera e un numero.");
	}
	if ($new_password != $new_password_confirm) {
		array_push($err, "Le nuove [en]password[/en] non coincidono.");
	}
}
if (strlen($nome) > 50) {
	array_push($err, "Nome può essere lungo al massimo 50 caratteri.");
}
if (! preg_match("/^[A-Za-z\s']*$/", $nome)) {
	array_push($err, "Nome può contenere solo lettere, spazi e apostrofi.");
}
if ($data_nascita != "") {
	if (preg_match("/^([\d]{4})\-(0[1-9]|1[0-2])\-((0|1)[0-9]|2[0-9]|3[0-1])$/", $data_nascita)) {
		$date = date_create_immutable($data_nascita);
		$min = date_create_immutable("1900-01-01");
		$now = date_create_immutable("now");
		$diff = date_diff($date, $now);
		if ($date < $min)
			array_push($err, "Data di dascita può partire dal 1900.");
		elseif ($diff->format("%r%y") < 13 || $diff->format("%r%y") > 100)
			array_push($err, "Devi avere tra i 13 e i 100 anni. Modifica la data di nascita.");
	} else
		array_push($err, "La data di nascita deve essere nel formato YYYY-MM-DD.");
}

if ($err) {
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

if ($pw_err)
	$_SESSION["error"] = ["Password corrente errata. Nessuna modifica apportata."];
elseif (! $res)
	$_SESSION["success"] = ["Nessuna modifica apportata."];
else
	$_SESSION["success"] = ["Dati aggiornati correttamente."];

header("location: dati.php");

?>