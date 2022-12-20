<?php

require_once("php/tools.php");

session_start();

if (! isset($_SESSION["user_id"])) {
	header ("location: login.php");
	exit();
}

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

Tools::showPage($page);

?>
