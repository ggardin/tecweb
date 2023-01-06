<?php

require_once("php/tools.php");

session_start();

if (! isset($_SESSION["id"])) {
	header ("location: login.php");
	exit();
}

$page = Tools::buildPage($_SERVER["SCRIPT_NAME"]);

Tools::showPage($page);

?>