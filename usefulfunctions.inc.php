<?php

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

function humanTime ($olddate){
	$olddate=strtotime($olddate);
	$elapsedtime = time() - $olddate; // to get the time since that moment
		$datetokens = array (
			31536000 => 'Jahr',
			2592000 => 'Monat',
			604800 => 'Woche',
			86400 => 'Tag',
			3600 => 'Stunde',
			60 => 'Minute',
			1 => 'Sekunde'
		);

		foreach ($datetokens as $unit => $timetext) {
			if ($elapsedtime < $unit) continue;
			$numberOfUnits = floor($elapsedtime / $unit);
			$textTime = 'vor '.$numberOfUnits.' '.$timetext;
			if ($numberOfUnits>1){
				switch ($timetext) {
					case "Jahr": 	$textTime=$textTime."en";
									break;
					case "Monat": 	$textTime=$textTime."en";
									break;
					case "Woche": 	$textTime=$textTime."n";
									break;
					case "Tag": 	$textTime=$textTime."en";
									break;
					case "Stunde": 	$textTime=$textTime."n";
									break;
					case "Minute": 	$textTime=$textTime."n";
									break;
					case "Sekunde": 	$textTime=$textTime."n";
									break;
				}
			}
			break;
		};
		return $textTime;
}

function smart_wordwrap($string, $width = 75, $break = "\n") {
    // split on problem words over the line length
    $pattern = sprintf('/([^ ]{%d,})/', $width);
    $output = '';
    $words = preg_split($pattern, $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

    foreach ($words as $word) {
        if (false !== strpos($word, ' ')) {
            // normal behaviour, rebuild the string
            $output .= $word;
        } else {
            // work out how many characters would be on the current line
            $wrapped = explode($break, wordwrap($output, $width, $break));
            $count = $width - (strlen(end($wrapped)) % $width);

            // fill the current line and add a break
            $output .= substr($word, 0, $count) . $break;

            // wrap any remaining characters from the problem word
            $output .= wordwrap(substr($word, $count), $width, $break, true);
        }
    }

    // wrap the final output
    return wordwrap($output, $width, $break);
}

function startFacebook() {
	// FACEBOOK START
	/**
	 * Copyright 2011 Facebook, Inc.
	 *
	 * Licensed under the Apache License, Version 2.0 (the "License"); you may
	 * not use this file except in compliance with the License. You may obtain
	 * a copy of the License at
	 *
	 *     http://www.apache.org/licenses/LICENSE-2.0
	 *
	 * Unless required by applicable law or agreed to in writing, software
	 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
	 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
	 * License for the specific language governing permissions and limitations
	 * under the License.
	 */
	require 'src/facebook.php';

	// Create our Application instance (replace this with your appId and secret).
	$facebook = new Facebook(array(
	  'appId'  => '133915026785817',
	  'secret' => '***',
	  'cookie' => TRUE,
	));
	// Get User ID
	$user = $facebook->getUser();
	 // We may or may not have this data based on whether the user is logged in.
	//
	// If we have a $user id here, it means we know the user is logged into
	// Facebook, but we don't know if the access token is valid. An access
	// token is invalid if the user logged out of Facebook.
	return $facebook;

}

// Subject, Recipient(User id), Body, if getRecipient is user id(1) or email adress(0)
function sendMail($getSubject, $getRecipient, $getBodytext, $getMemberOrNot) {
	require_once('sessiontest.inc.php');
	include('includes.inc.php');
	//check if getMemberOrNot
	if($getMemberOrNot==0)
	{
		$email = $getRecipient;
	}
	elseif($getMemberOrNot==1)
	{
		//get username and email from recipient id
		$abfrage = "SELECT vorname,email FROM local_users WHERE user_id='$getRecipient'";
		$ergebnis = mysql_query($abfrage);
		$row = mysql_fetch_object($ergebnis);

		$vorname=$row->vorname;
		$email=$row->email;
	}
	else
	{
		$email=$admin_mail;
	}

	//settings
	$to = $email;
    $from = $admin_mail;
    $subject = "StudentsDrive 2.0 - ".$getSubject;

    //begin of message
    $message = "
Hallo $vorname,\n
$getBodytext
\nBitte nicht auf diese Mail antworten!\n
\nMit freundlichen Grüßen\nAutomailer - StudentsDrive 2.0\n
--
dradda UG (haftungsbeschränkt)
Auf dem Teich 19
55459 Grolsheim
\n
Telefon:  +49 151 611 03 581
Telefax:  +49 6727 95 24 70
E-Mail:   info@dradda.de
\n
Registergericht: Amtsgericht Mainz
Registernummer: HRB - 44244
Steuernummer: 08/650/13038
";

	//prepare mail
	require("phpmailer/class.phpmailer.php");
	$mail = new PHPMailer();

	$mail->IsSMTP();  // telling the class to use SMTP
	$mail->Host     = "smtp.steffenwagner.com"; // SMTP server

	$mail->FromName = "StudentsDrive";
	$mail->From     = $from;
	$mail->AddAddress($to);

	$mail->Subject  = $subject;
	$mail->Body     = $message;
	$mail->WordWrap = 50;

    //send the email.
	if(!$mail->Send()) {
	  mail($email, "StudentsDrive 2.0 - ERROR", "Es ist ein Fehler aufgetreten\n\n".$mail->ErrorInfo."\n\nBitte beim Support melden!");
	  return false;
	} else {
	  return true;
	}


}

function showError404() {
	$errortext='
		<title>Error #404</title>
		<div class="page-header">
				<blockquote>
					<span class="badge badge-important"><h2>Error #404</h2></span>
					<small>Die aufgerufene Seite wurde nicht gefunden.<br><br>Kehren Sie zur Startseite zurück und versuchen Sie es bitte erneut.<br>Falls Sie denken, einen Fehler gefunden zu haben, so kontaktieren sie uns bitte <a href="framework.php?id=info&amp;sektion=kontakt">hier</a><br><br><a href="./index.php">Zur Startseite zurückkehren</a></small>
				</blockquote>
		</div>

	';
	return $errortext;

}

function checkLoggedIn() {

	session_start();

	if ((isset($_SESSION["logged_in"])) and ($_SESSION["logged_in"]==TRUE))//überprüft, ob Nutzer eingeloggt ist
	{
		return true;
	}
	else//weiterleitung zur login-seite
	{
		return false;
	}

}

function create_uniqueid() {

	$token1 = md5(uniqid(rand(), true));
	$token2 = md5(uniqid(rand(), true));

	$token3 = md5(uniqid(rand(), true));
	$token4 = md5(uniqid(rand(), true));

	$zufall = $token1.”-”.$token2;
	$zufall = md5($zufall);

	$zufall2 = $token3.”-”.$token4;
	$zufall2 = md5($zufall2);

	$id = md5($zufall2.$zufall);

	return $id;
}

/*
 * @param integer  $length
 * @param boolean  $capitals
 * @param boolean  $specialSigns
 *
 * @return string
 */
function CreatePassword($length = 7, $capitals = true, $specialSigns = false)
{
	$array = array();

	if($length < 8)
	  $length = mt_rand(8,20);

	# Zahlen
	for($i=48;$i<58;$i++)
	  $array[] = chr($i);

	# kleine Buchstaben
	for($i=97;$i<122;$i++)
	  $array[] = chr($i);

	# Großbuchstaben
	if($capitals )
	  for($i=65;$i<90;$i++)
		$array[] = chr($i);

	# Sonderzeichen:
	if($specialSigns)
	{
	  for($i=33;$i<47;$i++)
		$array[] = chr($i);
	  for($i=59;$i<64;$i++)
		$array[] = chr($i);
	  for($i=91;$i<96;$i++)
		$array[] = chr($i);
	  for($i=123;$i<126;$i++)
		$array[] = chr($i);
	}

	mt_srand((double)microtime()*1000000);
	$passwort = '';

	for ($i=1; $i<=$length; $i++)
	{
	  $rnd = mt_rand( 0, count($array)-1 );
	  $passwort .= $array[$rnd];
	}

	return $passwort;
}

//Klasse zum ver- und entschlüsseln von Daten
class Encryption {

	//Sicheres Passwort das zum verschlüsseln der Daten genutzt wird
	//define("PASSPHRASE", "4x,bj]8c92v7/#Nt4P[3V3nUKqo6fg");

	var $skey = "***";

    public  function safe_b64encode($string) {

        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }

	public function safe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    public  function encode($value){

      if(!$value){return false;}
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->skey, $text, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext));
    }

    public function decode($value){

        if(!$value){return false;}
        $crypttext = $this->safe_b64decode($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->skey, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }
}

/**
 * Checks date if matches given format and validity of the date.
 * Examples:
 * <code>
 * is_date('22.22.2222', 'mm.dd.yyyy'); // returns false
 * is_date('11/30/2008', 'mm/dd/yyyy'); // returns true
 * is_date('30-01-2008', 'dd-mm-yyyy'); // returns true
 * is_date('2008 01 30', 'yyyy mm dd'); // returns true
 * </code>
 * @param string $value the variable being evaluated.
 * @param string $format Format of the date. Any combination of <i>mm<i>, <i>dd<i>, <i>yyyy<i>
 * with single character separator between.
 */
function is_valid_date($value, $format = 'dd.mm.yyyy'){
    if(strlen($value) >= 6 && strlen($format) == 10){

        // find separator. Remove all other characters from $format
        $separator_only = str_replace(array('m','d','y'),'', $format);
        $separator = $separator_only[0]; // separator is first character

        if($separator && strlen($separator_only) == 2){
            // make regex
            $regexp = str_replace('mm', '(0?[1-9]|1[0-2])', $format);
            $regexp = str_replace('dd', '(0?[1-9]|[1-2][0-9]|3[0-1])', $regexp);
            $regexp = str_replace('yyyy', '(19|20)?[0-9][0-9]', $regexp);
            $regexp = str_replace($separator, "\\" . $separator, $regexp);
            if($regexp != $value && preg_match('/'.$regexp.'\z/', $value)){

                // check date
                $arr=explode($separator,$value);
                $day=$arr[0];
                $month=$arr[1];
                $year=$arr[2];
                if(@checkdate($month, $day, $year))
                    return true;
            }
        }
    }
    return false;
}

function getProfilePicture($user_id, $heigth, $width) {
	require_once('sessiontest.inc.php');
	include('includes.inc.php');

	//get username and email from recipient id
	$abfrage = "SELECT facebook_id,profilbild FROM local_users WHERE user_id='$user_id'";
	$ergebnis = mysql_query($abfrage);
	$row = mysql_fetch_object($ergebnis);

	//wenn user per fb auth => facebook_id ist nicht 0 UND wenn kein anderes profilbild hochgeladen
	if(($row->facebook_id<>"0") AND ($row->facebook_id<>"") AND ($row->profilbild==""))
		//facebook graph API Bild
		$returnPicUrl = 'http://graph.facebook.com/'.$row->facebook_id.'/picture?width='.$heigth.'&amp;height='.$width.'';
	else
	{
		if($row->profilbild<>"")
			//bildlink aus DB
			$returnPicUrl = $row->profilbild;
		else
			//gar kein bild
			$returnPicUrl = "img/no_picture.png";
	}
	return 	$returnPicUrl;
}

function getActivationCodeStatus($user_id) {
	require_once('sessiontest.inc.php');
	include('includes.inc.php');

	//get row
	$abfrage = "SELECT activation_code FROM local_users WHERE user_id='$user_id'";
	$ergebnis = mysql_query($abfrage);
	$row = mysql_fetch_object($ergebnis);

	$activation_code=$row->activation_code;

	return $activation_code;
}

function getCarName($user_id)
{
	require_once('sessiontest.inc.php');
	include('includes.inc.php');

	//get id from user
	$abfrage = "SELECT car_brand FROM local_users WHERE user_id='$user_id'";
	$ergebnis = mysql_query($abfrage);
	$row = mysql_fetch_object($ergebnis);
	$car_brand=$row->car_brand;

	//get name from car db
	$abfrage2 = "SELECT * FROM car_brands WHERE makes_id='$car_brand'";
	$ergebnis2 = mysql_query($abfrage2);
	$row2 = mysql_fetch_object($ergebnis2);
	$brand_name = $row2->makes_name;

	//auto_marke
	if($car_brand<>"")
		return $brand_name;
	else
		return "Keine Angabe";
}

function getModelName($user_id)
{
	require_once('sessiontest.inc.php');
	include('includes.inc.php');

	//get row
	$abfrage = "SELECT car_model FROM local_users WHERE user_id='$user_id'";
	$ergebnis = mysql_query($abfrage);
	$row = mysql_fetch_object($ergebnis);
	$car_model=$row->car_model;

	//get name from car db
	$abfrage2 = "SELECT * FROM car_models WHERE models_id='$car_model'";
	$ergebnis2 = mysql_query($abfrage2);
	$row2 = mysql_fetch_object($ergebnis2);
	$model_name = $row2->models_name;

	//auto_model
	if($car_model<>"")
		return $model_name;
	else
		return "Keine Angabe";
}

function getFullUserName($user_id)
{
	require_once('sessiontest.inc.php');
	include('includes.inc.php');

	//get row
	$abfrage = "SELECT vorname,nachname FROM local_users WHERE user_id='$user_id'";
	$ergebnis = mysql_query($abfrage);
	$row = mysql_fetch_object($ergebnis);
	$vorname=$row->vorname;
	$nachname=$row->nachname;

	//auto_model
	if($nachname<>"")
		return "$vorname $nachname";
	else
		return $vorname;
}

function FormatTelefonnummer($in_nr){
    $search = array('|^049|', '|^0049|', '|^0|', '|/|', '| |', '|\.|', '|-|', '[:blank:]', '[:space:]');
    $repl = array('0049', '0', '0049', '', '', '', '', '', '');
    $neu = preg_replace($search, $repl, urldecode($in_nr));
    return $neu;
}

function umlauteErsetzen($str){
	$search  = array ('ä', 'ö', 'ü', 'ß', 'Ä', 'Ö', 'Ü');
	$replace = array ('ae', 'oe', 'ue', 'ss', 'AE', 'OE', 'UE');
	$string  = str_replace($search, $replace, $str);
	return $string;

}

function sendSMS($user_id, $text){

	require_once('sessiontest.inc.php');
	//db login incl.
	include('includes.inc.php');

	//Erst Umlaute ersetzen
	$text = umlauteErsetzen($text);
	//Handynummer von $user_id holen
	$abfrage = "SELECT vorname, phonenumber FROM local_users WHERE user_id='$user_id'";
	$ergebnis = mysql_query($abfrage);
	$row = mysql_fetch_object($ergebnis);
	$vorname=$row->vorname;
	$telefon=$row->phonenumber;

	if ($telefon<>"")
	{
		//Handynummer formatieren 0049 od. 49 od. 0 XXXXXXXXXX
		$telefon_formatted = FormatTelefonnummer($telefon);
		$absender = '004915233544688';
		$url = 'https://gateway.sms77.de/?' .
				'u=' . urlencode('***') .
				'&p=' . urlencode('***') .
				'&to=' . urlencode($telefon_formatted) .
				'&text=' . urlencode($text) .
				'&from=' . urlencode($absender) .
				'&status=1' .
				'&debug=1' . //auf 0, wenn SMS versendet werden sollen!
				'&type=basicplus';
		// Zur Vorsicht lieber ein @ davor, damit im Fehlerfall
		// und falscher php-config die URL nicht publiziert wird
		$response = @file_get_contents($url);
		// nun noch $response auswerten und Rückgabecode prüfen
		if ($response == '100') {
			//SMS erfolgreich versandt
			return "SMS erfolgreich gesendet";
		} else {
			//SMS Gateway sagt senden fehlgeschlagen
			switch($respone)
			{
				case 100:
					$fehler="SMS wurde erfolgreich verschickt";
					break;
				case 101:
					$fehler="Versand an mindestens einen Empfänger fehlgeschlagen";
					break;
				case 201:
					$fehler="Ländercode für diesen SMS-Typ nicht gültig. Bitte als Basic SMS";
					break;
				case 202:
					$fehler="Empfängernummer ungültig";
					break;
				case 300:
					$fehler="Bitte Benutzer/Passwort angeben";
					break;
				case 301:
					$fehler="Variable to nicht gesetzt";
					break;
				case 304:
					$fehler="Variable type nicht gesetzt";
					break;
				case 305:
					$fehler="Variable text nicht gesetzt";
					break;
				case 306:
					$fehler="Absendernummer ungültig. Diese muss vom Format 0049... sein und eine gültige Handynummer darstellen.";
					break;
				case 307:
					$fehler="Variable url nicht gesetzt";
					break;
				case 400:
					$fehler="type ungültig. Siehe erlaubte Werte oben";
					break;
				case 401:
					$fehler="Variable text ist zu lang";
					break;
				case 402:
					$fehler="Reloadsperre – diese SMS wurde bereits innerhalb der letzten 90 Sekunden verschickt";
					break;
				case 500:
					$fehler="Zu wenig Guthaben vorhanden.";
					break;
				case 600:
					$fehler="Carrier Zustellung misslungen";
					break;
				case 700:
					$fehler="Unbekannter Fehler";
					break;
				case 801:
					$fehler="Logodatei nicht angegeben";
					break;
				case 802:
					$fehler="Logodatei existiert nicht";
					break;
				case 803:
					$fehler="Klingelton nicht angegeben";
					break;
				case 900:
					$fehler="Benutzer/Passwort-Kombination falsch";
					break;
				case 902:
					$fehler="http API für diesen Account deaktiviert";
					break;
				case 903:
					$fehler="Server IP ist falsch";
					break;
				default:
					$fehler="FATAL ERROR";
					break;
			}
			return $fehler;
		}
	}
	else
	{
		$fehler = "Keine Handynummer eingetragen";
		return $fehler;
	}

}

?>