<?php

require_once("php/tools.php");
require_once("php/database.php");

$user_id = isset($_SESSION["id"]) ? $_SESSION["id"] : "";

if ($user_id == "") {
	header("location: login.php");
	exit();
}

$id = isset($_POST["gest_id"]) ? $_POST["gest_id"] : "";
$nome = isset($_POST["nome"]) ? $_POST["nome"] : "";
$submit = isset($_POST["submit"]) ? $_POST["submit"] : "";

if (! in_array($submit, ["aggiungi", "modifica", "elimina"]) || ($submit != "aggiungi" && $id == "")) {
	Tools::errCode(500);
	exit();
}

$valid = true;

if (strlen($nome) <= 3) {
	$valid = false;
	$_SESSION["message"] = "Il nome deve avere almeno 3 caratteri.";
}

if (! $valid) {
	header("location: gest_list.php?id=" . $id);
	exit();
}

try {
	$connessione = new Database();
	if ($submit == "aggiungi")
		$res = $connessione->insertLista($user_id, $nome);
	elseif ($submit == "modifica" && $connessione->isListaDiUtente($id, $user_id))
		$res = $connessione->updateLista($id, $nome);
	elseif ($submit == "elimina" && $connessione->isListaDiUtente($id, $user_id))
		$res = $connessione->deleteLista($id);
	else
		$res = false;
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if ($res) {
	if ($submit == "modifica")
		header("location: gest_list.php?id=$id");
	else
		header("location: lists.php");
	exit();
} else {
	// nome duplicato o altro, dare errore
	header("location: lists.php");
}

?>