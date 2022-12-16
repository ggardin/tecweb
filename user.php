<?php

require_once("php/tools.php");

session_start();

if (! isset($_SESSION["user"]))
	header ("location: login.php");

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

Tools::showPage($page);

?>
