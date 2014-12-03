<?php
include('usefulfunctions.inc.php');
/**
 *
 *
 * @version $Id$
 * @copyright 2013
 */
$antwort = sendSMS(1, "Das ist eine einfache TestSMS");
print $antwort;

?>