<?php

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

session_start();
session_unset();
$_SESSION=array();
$_SESSION["logged_in"]=FALSE;
session_destroy();
header('Location: ./framework.php?id=login');


?>
