<?php

require_once("php/page.php");

$page = Page::build(basename($_SERVER["PHP_SELF"], ".php"));

echo($page);

?>