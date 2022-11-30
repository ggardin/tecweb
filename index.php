<?php

require_once("php/page.php");

$pagina = Page::build(basename($_SERVER["PHP_SELF"], ".php"));

echo($pagina);

?>
