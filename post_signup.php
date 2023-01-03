<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

if (isset($_SESSION["id"])) {
	header("location: user.php");
	exit();
}

// TODO controlli
$username = isset($_POST["username"]) ? $_POST["username"] : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";
$password_confirm = isset($_POST["password_confirm"]) ? $_POST["password_confirm"] : "";

if (! $username || ! $password || ! $password_confirm || $password != $password_confirm) {
	header("location: signup.php");
	exit();
}

try {
	$connessione = new Database();
	// TODO : transaction
	if ($connessione->insertUtente($username, $password)) {
		$user_id = $connessione->insertId();
		if ($connessione->insertLista($user_id, "Da vedere") &&
			$connessione->insertLista($user_id, "Visti"));
			$res = $connessione->login($username, $password);
	}
	unset($connessione);
} catch (Exception) {
	unset($connessione);
	Tools::errCode(500);
	exit();
}

if (isset($res) && !empty($res)) {
	$_SESSION["id"] = $res["id"];
	$_SESSION["is_admin"] = $res["is_admin"];
	header("location: user.php");
	exit();
} else {
	header("location: signup.php");
	exit();
}


// } else
// Tools::replaceAnchor($page, "message", "Errore: Utente già registrato");
// } else
// Tools::replaceAnchor($page, "message", "Errore: Le password non coincidono");
// } else
// Tools::replaceSection($page, "message", "");

?>