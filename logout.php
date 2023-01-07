<?php

require_once("php/tools.php");

session_unset();
session_destroy();

header("location: index.php");

?>
