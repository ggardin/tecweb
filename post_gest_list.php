<?php

require_once("php/tools.php");
require_once("php/database.php");

$user = isset($_SESSION["id"]) ? $_SESSION["id"] : "";
$id = isset($_POST["gest_id"]) ? $_POST["gest_id"] : "";
$nome = isset($_POST["nome"]) ? $_POST["nome"] : "";
$submit = isset($_POST["submit"]) ? $_POST["submit"] : "";

if ($user == "" || ! in_array($submit, ["aggiungi", "modifica", "elimina"]) || ($submit != "aggiungi" && $id == "")) {
	header("location: index.php");
	exit();
}

if ($nome == "") {
	header("location: gest_list.php?id=$id");
	exit();
}

try {
	$connessione = new Database();
	if ($submit == "aggiungi")
		$res = $connessione->insertLista($user, $nome);
	elseif ($submit == "modifica" && $connessione->isListaDiUtente($id, $user))
		$res = $connessione->updateLista($id, $nome);
	elseif ($submit == "elimina" && $connessione->isListaDiUtente($id, $user))
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
		header("location: list.php?id=$id");
	else
		header("location: lists.php");
	exit();
} else {
	// nome duplicato o altro, dare errore
	header("location: lists.php");
}

?>