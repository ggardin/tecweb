<?php

require_once("php/tools.php");

$page = Tools::buildPage(basename($_SERVER["PHP_SELF"], ".php"));

Tools::showPage($page);

?>
