<?php

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

$admin_mail = "studentsdrive@steffenwagner.com";
$verbindung = mysql_connect('***','***','***');
mysql_select_db('***');//Datenbank auswählen
mysql_query("SET NAMES utf8");
$full_site_url="http://steffenwagner.com/studentsdrive/framework/"; //Wird zb für Mailversand benötigt
$main_site_title="StudentsDrive 2.0 - ";//wird im Seitentitel immer angehängt

?>