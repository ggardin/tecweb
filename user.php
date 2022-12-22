<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

if (! isset($_SESSION["user_id"])) {
	header ("location: login.php");
	exit();
}

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

$connessione = new Database();
$res = $connessione->getUsernameByUserId($_SESSION["user_id"]);
if (!empty($res)) Tools::replaceAnchor($page, "username", $res[0]["username"]);

Tools::showPage($page);

?>
