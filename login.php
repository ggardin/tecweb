<?php

require_once("php/tools.php");
require_once("php/database.php");

session_start();

if (isset($_SESSION["id"])) {
	header("location: user.php");
	exit();
}

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"), "auth");

Tools::showPage($page);

?>