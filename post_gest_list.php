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
	if ($submit == "aggiungi") {
		$res = $connessione->insertLista($user_id, $nome);
		$id = $res[1];
		$res = $res[0];
	} elseif ($submit == "modifica" && $connessione->isListaDiUtente($id, $user_id))
		$res = $connessione->updateLista($id, $nome);
	elseif ($submit == "elimina" && $connessione->isListaDiUtente($id, $user_id)) {
		$res = $connessione->deleteLista($id);
		$id = "";
	} else
		$res = false;
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if (! $res) {
	$_SESSION["message"] = "Nessuna modifica apportata.";
	header("location: list.php?id=" . $id);
} elseif ($submit == "aggiungi") {
	$_SESSION["message"] = "Lista aggiunta correttamente.";
	header("location: list.php?id=" . $id);
} elseif ($submit == "modifica") {
	$_SESSION["message"] = "Lista modificata correttamente.";
	header("location: list.php?id=" . $id);
} else {
	$_SESSION["message"] = "Lista eliminata correttamente.";
	header("location: lists.php");
}

?>