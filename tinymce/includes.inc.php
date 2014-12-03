<?php
$verbindung = mysql_connect('abi2011ohg.pytalhost.de','phost148581','20ottohahn11');
mysql_select_db('stufenseite');//Datenbank auswählen
mysql_query("SET NAMES utf8");
header('content-type: text/html; charset=utf-8');//auf UTF-8 umstellen
$seitentitel=" - Abi 2010 OHG Landau"//wird im Seitentitel immer angehängt
?>