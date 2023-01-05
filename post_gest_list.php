<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

$user_id = isset($_SESSION["id"]) ? $_SESSION["id"] : "";
$list_id = isset($_POST["gest_id"]) ? $_POST["gest_id"] : "";
$nome = isset($_POST["nome"]) ? $_POST["nome"] : "";
$submit = isset($_POST["submit"]) ? $_POST["submit"] : "";

if ($user_id == "" || ! in_array($submit, ["aggiungi", "modifica", "elimina"]) || ($submit != "aggiungi" && $list_id == "")) {
	header("location: index.php");
	exit();
}

if ($nome == "") {
	header("location: gest_list.php?id=$list_id");
	exit();
}

try {
	$connessione = new Database();
	if ($submit == "aggiungi")
		$res = $connessione->insertLista($user_id, $nome);
	elseif ($submit == "modifica" && $connessione->isListaDiUtente($list_id, $user_id))
		$res = $connessione->updateLista($list_id, $nome);
	elseif ($submit == "elimina" && $connessione->isListaDiUtente($list_id, $user_id))
		$res = $connessione->deleteLista($list_id);
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
		header("location: list.php?id=$list_id");
	else
		header("location: lists.php");
	exit();
} else {
	// nome duplicato o altro, dare errore
	header("location: lists.php");
}

?>