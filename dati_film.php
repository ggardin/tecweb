<?php

require_once("php/tools.php");

$page = Tools::buildPage($_SERVER["SCRIPT_NAME"]);

Tools::showPage($page);

?>
