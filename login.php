<?php

require_once("php/tools.php");
require_once("php/database.php");

if (isset($_SESSION["id"])) {
	header("location: user.php");
	exit();
}

$page = Tools::buildPage($_SERVER["SCRIPT_NAME"], "auth");

Tools::showPage($page);

?>