<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

// TODO controlli
if (isset($_SESSION["id"])) {
	header("location: user.php");
	exit();
}

$username = isset($_POST["username"]) ? $_POST["username"] : null;
$password = isset($_POST["password"]) ? $_POST["password"] : null;

try {
	$connessione = new Database();
	$res = $connessione->login($username, $password);
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if (! empty($res)) {
	$_SESSION["id"] = $res["id"];
	$_SESSION["is_admin"] = $res["is_admin"];
	header("location: user.php");
	exit();
} else {
	header("location: login.php");
	exit();
}

// } else
// 	Tools::replaceAnchor($page, "message", "Errore: Credenziali errate");
// } else
// 	Tools::replaceSection($page, "message", "");

?>