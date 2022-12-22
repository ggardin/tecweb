<?php

require_once("php/tools.php");

session_start();

if (! isset($_SESSION["id"]) || $_SESSION["is_admin"] == 0) {
	header ("location: index.php");
	exit();
}

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

Tools::showPage($page);

?>