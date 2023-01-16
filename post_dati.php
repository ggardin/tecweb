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
$data_nascita = isset($_POST["data_nascita"]) ? $_POST["data_nascita"] : "";
$gender = isset($_POST["gender"]) ? $_POST["gender"] : "";

$valid = true;

if (strlen($username) <= 3) {
	$valid = false;
	$_SESSION["message"] = "Username deve avere almeno 3 caratteri.";
} elseif (strlen($nome) <= 3) {
	$valid = false;
	$_SESSION["message"] = "Il nome deve avere almeno 3 caratteri.";
} elseif (! empty($new_password) && $new_password != $new_password_confirm) {
	$valid = false;
	$_SESSION["message"] = "Le password non coincidono.";
}

if (! $valid) {
	header("location: gest_list.php?id=" . $id);
	exit();
}

try {
	$connessione = new Database();
	$res = $connessione->updateUtente($user_id, $username, $mail, $nome, $gender, $data_nascita, ""); // TODO : update password
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