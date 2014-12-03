<?php
//file is used to process post queries from different files..

//reset errors
$_SESSION["post_fehler"]="";
$fehler="";

// PROFILE PAGE UPDATE
if((isset($_POST['form_password'])) OR (isset($_POST['form_car'])) OR (isset($_POST['form_education'])) OR (isset($_POST['form_personal'])))
//Profil bearbeiten Formulare (für jeden Tab eigenes form)
  {
	// include important files
	include('sessiontest.inc.php');
	include('includes.inc.php');
	require_once('usefulfunctions.inc.php');
	//passwordänderung
	if(isset($_POST['form_password']))
	{
		$Encrypt = new Encryption();
		//check ob altes passwort korrekt
		$abfrage = "SELECT password FROM local_users WHERE user_id='$s_user_id'";
		$ergebnis = mysql_query($abfrage);
		$row = mysql_fetch_object($ergebnis);
		
		if(!empty($_POST['password_old']))
		{
			if ($_POST['password_old']<>($Encrypt->decode($row->password))){
				$fehler .= "<li>Das alte Passwort war nicht korrekt.</li>";}
		}
		else		
			$fehler .= "<li>Altes Passwort fehlt</li>";
		if(empty($_POST['password1'])) {
			$fehler .= "<li>Neues Passwort fehlt</li>";
		}
		if(empty($_POST['password2'])) {
			$fehler .= "<li>Passwortwiederholung fehlt</li>";
		}	
		if(($_POST['password1']==$_POST['password2'])) 
		{
			if(strlen($_POST['password1'])<6){
				$fehler .= "<li>Das Passwort muss mindestens 6 Zeichen haben</li>";}
		}
		else
			$fehler .= "<li>Die Passwörter stimmen nicht überein</li>";
			
		if(empty($fehler))
		{
			//everything correct
			//crypt new pw
			$password_new=$Encrypt->encode(mysql_real_escape_string($_POST['password1']));
				
			$abfrage = "UPDATE `local_users` SET `password` = '".$password_new."' WHERE `user_id` =".$s_user_id." LIMIT 1 ;";	
		}
	}
	//autoänderung
	elseif(isset($_POST['form_car']))
	{	
		if((empty($_POST['selectcar'])) OR ($_POST['selectcar'])=="0") {
			$fehler .= "<li>Die Automarke fehlt</li>";
		}
		if(empty($_POST['selectmodel'])) {
			$fehler .= "<li>Das Automodell fehlt</li>";
		}
		if(empty($_POST['autofarbe'])) {
			$fehler .= "<li>Die Autofarbe fehlt</li>";
		}	
		if(empty($_POST['selectSeats'])) {
			$fehler .= "<li>Die Anzahl der freien Plätze fehlt</li>";
		}		
		if((!empty($_POST['nummernschild_1'])) OR (!empty($_POST['nummernschild_1'])) OR (!empty($_POST['nummernschild_1']))) {
			if(preg_match('/[^A-Za-zäöüÄÖÜ]/',$_POST['nummernschild_1']))
				$fehler .= "<li>Stadt darf nur aus Buchstaben bestehen</li>";
			if(preg_match('/[^A-Za-zäöüÄÖÜ]/',$_POST['nummernschild_2']))
				$fehler .= "<li>Unterscheidungszeichen darf nur aus Buchstaben bestehen</li>";
			if(preg_match('/[^0-9]/',$_POST['nummernschild_3']))
				$fehler .= "<li>Erkennungsnummer darf nur aus Zahlen bestehen</li>";				
		}	
		
		if(empty($fehler))
		{	
			//data
			$automarke = mysql_real_escape_string($_POST["selectcar"]);	
			$automodell = mysql_real_escape_string($_POST["selectmodel"]);
			$autofarbe = mysql_real_escape_string($_POST["autofarbe"]);	
			$selectSeats = mysql_real_escape_string($_POST["selectSeats"]);	
			$nummernschild_1 = mysql_real_escape_string($_POST["nummernschild_1"]);	
			$nummernschild_2 = mysql_real_escape_string($_POST["nummernschild_2"]);	
			$nummernschild_3 = mysql_real_escape_string($_POST["nummernschild_3"]);	
			$nummernschild = $nummernschild_1."-".$nummernschild_2."-".$nummernschild_3;
			
			//query
			
			$abfrage = "UPDATE `local_users` SET `car_brand` = '".$automarke."',`car_model` = '".$automodell."',`car_colour` = '".$autofarbe."',`car_license_plate` = '".$nummernschild."',`car_seats` = '".$selectSeats."' WHERE `user_id` =".$s_user_id." LIMIT 1 ;";
		}		
	}
	//bildungseinrichtung änderung
	elseif(isset($_POST['form_education']))
	{
		if(empty($_POST['schulname'])) {
			$fehler .= "<li>Die Universtität / Hochschule fehlt</li>";
		}
		//if(empty($_POST['studiengang'])) {
		//	$fehler .= "<li>Das Automodell fehlt</li>";
		//}	
		
		if(empty($fehler))
		{
			//data
			$schulname = mysql_real_escape_string($_POST["schulname"]);	
			$studiengang = mysql_real_escape_string($_POST["studiengang"]);
			
			//query
			$abfrage = "UPDATE `local_users` SET `school_name` = '".$schulname."',`school_course` = '".$studiengang."' WHERE `user_id` =".$s_user_id." LIMIT 1 ;";
		}			
	}
	//private informationen änderung
	elseif(isset($_POST['form_personal']))
	{
		if(empty($_POST['vorname'])) {
			$fehler .= "<li>Der Vorname fehlt</li>";
		}
		// Email Adresse
		if(!empty($_POST['email'])) {
			if(filter_var($_POST['email'],FILTER_VALIDATE_EMAIL) === false)
				$fehler .= "<li>Die Email muss folgendes Format haben: abc@domain.tld</li>";	
		}else{
			$fehler .= "<li>Die Email fehlt</li>";
		}	
		// Geburtsdatum
		if(!empty($_POST['geb_datum'])) {
			if(is_valid_date($_POST['geb_datum']) === false)
				$fehler .= "<li>Das Datum muss folgendes Format haben: dd.MM.yyyy</li>";	
		}	
		
		// Sex
		if(!empty($_POST['selectSex'])) {
			if($_POST['selectSex']=="female")
				$geschlecht="female";
			elseif($_POST['selectSex']=="male")
				$geschlecht="male";
		}		
	
		if(empty($fehler))
		{			
			//data
			$vorname = mysql_real_escape_string($_POST["vorname"]);	
			$nachname = mysql_real_escape_string($_POST["nachname"]);
			$email = mysql_real_escape_string($_POST["email"]);
			if($_POST["telefon"]<>"")
				$telefon = mysql_real_escape_string($_POST["telefon"]);
			$wohnort = mysql_real_escape_string($_POST["wohnort"]);
			$about = mysql_real_escape_string($_POST["about"]);
			$geb_datum = mysql_real_escape_string($_POST["geb_datum"]);
			
			//convert birth date to mysql DATE
			$geb_datum_c = date('Y-m-d', strtotime(str_replace('-', '/', $geb_datum)));
		
			//email mit activation code für emailänderung
			// create unique activation link
			$activationcode=create_uniqueid().'mailchange';						
			
			//mail mit code versenden
			sendMail("Email Adresse bestätigen", $email, "Sie haben soeben ihre Email Adresse geändert.\n\nUm die neue Email verwenden zu können, müssen Sie diese zuerst bestätigen.\n\nDer Link ist zeitlich unbegrenzt gültig.\n\nAktivierungslink:\n\n".$full_site_url."framework.php?id=activate&code=".$activationcode."\n\nAlternativ kannst du den Code: \n\n".$activationcode."\n\nauf dieser Seite eingeben:\n\n".$full_site_url."framework.php?id=activate&code", 0);					
			
			//query
			if($telefon<>"")
				$abfrage = "UPDATE `local_users` SET `vorname` = '".$vorname."',`nachname` = '".$nachname."',`birth` = '".$geb_datum_c."',`new_email` = '".$email."',`activation_code` = '".$activationcode."',`phonenumber` = '".$telefon."',`homeplace` = '".$wohnort."',`about` = '".$about."',`sex` = '".$geschlecht."' WHERE `user_id` =".$s_user_id." LIMIT 1 ;";
			else
				$abfrage = "UPDATE `local_users` SET `vorname` = '".$vorname."',`nachname` = '".$nachname."',`birth` = '".$geb_datum_c."',`new_email` = '".$email."',`activation_code` = '".$activationcode."',`homeplace` = '".$wohnort."',`about` = '".$about."',`sex` = '".$geschlecht."' WHERE `user_id` =".$s_user_id." LIMIT 1 ;";
				
		}	
	}	
	//do query
	$eintragen = mysql_query( $abfrage );
	if (( $eintragen == true ) AND ($fehler==""))
	{
		if(isset($_POST['form_car']))
		{
			//UPDATE PROFIL STATUS -> "activation_code = done"
			$query = "UPDATE `local_users` SET `activation_code` = 'done' WHERE `user_id` =".$s_user_id." LIMIT 1 ;";
			$sentdb = mysql_query ($query) or die(mysql_error());  	
		}
		header('Location: ./framework.php?id=profile&aktion=bearbeiten&msg=erfolg');
	}
	else
	{
		$_SESSION["post_fehler"]=$fehler;
		header('Location: ./framework.php?id=profile&aktion=bearbeiten&msg=fehler');
	}
  }
elseif(isset($_POST['form_contactformular']))   
{
	// include important files
	// NO IMPORT sessiontest!!! Kontaktformular auch ohne login erreichbar....
	include('includes.inc.php'); //für admin email
	require_once('usefulfunctions.inc.php'); //für admin email
	
	if(empty($_POST['betreff'])) {
		$fehler .= "<li>Der Betreff fehlt</li>";
	}	
	if(empty($_POST['text'])) {
		$fehler .= "<li>Der Text fehlt</li>";
	}		
	
	if($_POST['usr_logged_in']=="1")
	{
		//email im system suchen
		$user_id = $s_user_id; // VORHANDEN????????????????????????????? SESSION??????????????????????????????
		
		//check auf fehler
		if(empty($fehler))
		{
			//sende mail
			sendMail("Kontaktformular", $admin_mail, "Name:\n ".$user_id."\nEmail:\n ".$_POST['email']."\nBetreff:\n ".$_POST['betreff']."\nText:\n ".$_POST['text']."\n", 0);			
			header('Location: ./framework.php?id=info&sektion=kontakt&msg=erfolg');
		}
		else
		{
			//header auf fehler
			header('Location: ./framework.php?id=info&sektion=kontakt&msg='.$fehler);	
		}			
	}
	else
	{	
		//email aus form verwenden
		if(empty($_POST['name'])) {
			$fehler .= "<li>Der Name fehlt</li>";
		}	
		// Email Adresse
		if(!empty($_POST['email'])) {
			if(filter_var($_POST['email'],FILTER_VALIDATE_EMAIL) === false)
				$fehler .= "<li>Die Email muss folgendes Format haben: abc@domain.tld</li>";	
		}else{
			$fehler .= "<li>Die Email fehlt</li>";
		}	
		
		//check auf fehler
		if(empty($fehler))
		{
			//sende mail
			sendMail("Kontaktformular", $admin_mail, "Name:\n ".$_POST['name']."\nEmail:\n ".$_POST['email']."\nBetreff:\n ".$_POST['betreff']."\nText:\n ".$_POST['text']."\n", 0);							
			header('Location: ./framework.php?id=info&sektion=kontakt&msg=erfolg');
		}
		else
		{
			//header auf fehler
			header('Location: ./framework.php?id=info&sektion=kontakt&msg='.$fehler);			
		}		
	}
	

	
}
elseif(isset($_POST['form_register']))   
// Registrierungsformular auf Loginseite
{
	//Secureimage 
	session_start();
	include_once('securimage/securimage.php');

	$securimage = new Securimage();
	
	// Check ob Captcha richtig eingegeben
	if($securimage->check($_POST['captcha_code1']) == false) {
	  // the code was incorrect
	  // you should handle the error so that the form processor doesn't continue

	  // or you can use the following code if there is no validation or you do not know how
	  $fehler .= "<li>Das eingegebene Captcha stimmt nicht überein</li>";	
	}
	
	// Alle Posts holen und nach Fehlern untersuchen
	// Vorname
	if(!empty($_POST['vorname'])) {
		if(preg_match('/[^A-Za-zäöüÄÖÜ]/',$_POST['vorname']))
			$fehler .= "<li>Der Vorname darf nur aus Buchstaben bestehen</li>";	
	}else{
		$fehler .= "<li>Dein Vorname fehlt</li>";
	}
	
	// Email Adresse
	if(!empty($_POST['email'])) {
		if(filter_var($_POST['email'],FILTER_VALIDATE_EMAIL) === false)
			$fehler .= "<li>Die Email muss folgendes Format haben: abc@domain.tld</li>";	
	}else{
		$fehler .= "<li>Die Email fehlt</li>";
	}	
	
	// Sex
	if(!empty($_POST['selectSex'])) {
		if($_POST['selectSex']=="female")
			$geschlecht="female";
		elseif($_POST['selectSex']=="male")
			$geschlecht="male";
	}
	
	//Schule
	if(empty($_POST['schulname'])) {
		$fehler .= "<li>Deine Uni / Hochschule fehlt</li>";
	}	
	
	//Passwort 1
	if(empty($_POST['password1'])) {
		$fehler .= "<li>Neues Passwort fehlt</li>";
	}
	//Passwort 2
	if(empty($_POST['password2'])) {
		$fehler .= "<li>Passwortwiederholung fehlt</li>";
	}	
	//Passwörter stimmen nicht überein
	if(($_POST['password1']==$_POST['password2'])) 
	{
		//Länge des Passworts
		if(strlen($_POST['password1'])<6)
			$fehler .= "<li>Das Passwort muss mindestens 6 Zeichen haben</li>";
	}	
	
	if(empty($fehler))
	{
		// include important files
		// NO IMPORT sessiontest!!! Kontaktformular auch ohne login erreichbar....	
		include('includes.inc.php');
		require_once('usefulfunctions.inc.php');
		
		$vorname=mysql_real_escape_string($_POST['vorname']);	
		$nachname=mysql_real_escape_string($_POST['nachname']);	
		$email=mysql_real_escape_string($_POST['email']);	
		$schulname=mysql_real_escape_string($_POST['schulname']);	
		$passwort=mysql_real_escape_string($_POST['password1']);
		
		//crypt pwd
		$Encrypt = new Encryption();
		$new_passwort = $Encrypt->encode($passwort);

		//Register date
		$date = date("Y-m-d", time());
		$reg_datum = date('Y-m-d', strtotime(str_replace('-', '/', $date)));			
		
		//check if email already exists
		$query = "SELECT * FROM local_users WHERE email = '$email'";
		$result = mysql_query($query);
		if(mysql_num_rows($result)==0)		
		{
			// create unique activation link
			$activationcode=create_uniqueid();
			//create a new local_users account
			$abfrage = "INSERT INTO `local_users` (
			  `vorname`,
			  `nachname`,
			  `reg_date`,
			  `email`,
			  `school_name`,
			  `sex`,
			  `password`,
			  `activation_code`
			   )
			   VALUES ('" . $_POST['vorname'] . "', '" . $nachname . "', '".$reg_datum."', '" . $email . "', '" . $schulname . "', '" . $geschlecht . "', '" . $new_passwort . "', '" . $activationcode . "');";	
			$eintragen = mysql_query( $abfrage );	
			if ( $eintragen == true )
			{
				sendMail("Registrierung abschließen", $email, "Vielen Dank für die Registrierung bei uns!\n\nUm deine eben getätigte Registrierung abzuschließen, klicke bitte auf folgenden Link. Anschließend kannst du unser volles Angebot nutzen.\n\nDer Link ist zeitlich unbegrenzt gültig.\n\nAktivierungslink:\n\n".$full_site_url."framework.php?id=activate&code=".$activationcode."\n\nAlternativ kannst du den Code: \n\n".$activationcode."\n\nauf dieser Seite eingeben:\n\n".$full_site_url."framework.php?id=activate&code", 0);
				header('Location: ./framework.php?id=login&register=code_sent');					
			}
			else
			{
				//SQL Error
				$fehler='SQL create new User failed. Please contact support.';
				header('Location: ./framework.php?id=login&register='.$fehler);
			}
		}
		else
		{
			//email schon im system
			$fehler='Email schon vorhanden. Bitte versuche eine Andere, oder setze dein Passwort zurück';
			header('Location: ./framework.php?id=login&register='.$fehler);			
		}
	}
	else
	{
		header('Location: ./framework.php?id=login&register='.$fehler);
	}
}
elseif(isset($_GET['code']))  
// check Link Aktivierungen von Emails... 
{
	// wenn code im link enthalten oder per form gesendet (post)
	if((($_GET['code']<>"") AND ($_GET['code']<>"fehler")) OR (isset($_POST['form_code']))) 
	{
		include('includes.inc.php');
		//kein usefulfunctions includen!! geschieht schon im framework (id?activate=)
		
		// code im link oder per post
		if($_GET['code']<>"")
			$sent_code=mysql_real_escape_string($_GET['code']);
		else
			$sent_code=mysql_real_escape_string($_POST['form_code']);
			
		$query = "SELECT user_id FROM local_users WHERE activation_code = '$sent_code'";
		$result = mysql_query($query);		
		if(mysql_num_rows($result)>0)		
		{		
			$row=mysql_fetch_object($result);
			$user_id=$row->user_id;
			//wenn code korrekt
			//sql update
			if(strpos($activation_code, 'mailchange')==FALSE)
			{
				//nur mailchange
				
				//new_email holen und in email einfügen
				$query = "SELECT new_email FROM local_users WHERE user_id='$user_id'";
				$result = mysql_query($query);
				$row1 = mysql_fetch_object($result);	
				$new_email=$row1->new_email;		
				
				$abfrage = "UPDATE `local_users` SET `activation_code` = 'done', `email` = '$new_email', `new_email` = '' WHERE `user_id` =".$user_id." LIMIT 1 ;";	
				$eintragen = mysql_query( $abfrage );	
				if ( $eintragen == true )
				{
					sendMail("Email Änderung erfolgreich", $user_id, "Die Email Adresse wurde erfolgreich geändert.\n\nDu kannst dich ab sofort mit der neuen Email unter folgendem Link bei uns einloggen.\n\n".$full_site_url."\n\nBei Fragen stehen wir dir gerne zur Verfügung.", 1);
					header('Location: ./framework.php?id=profile&aktion=bearbeiten&msg=erfolg');					
				}
				else
				{
					//SQL Error
					$fehler='SQL create new User failed. Please contact support.';
					header('Location: ./framework.php?id=login&register='.$fehler);
				}	
			}
			else
			{
				//aktivierung
				$abfrage = "UPDATE `local_users` SET `activation_code` = 'first_login' WHERE `user_id` =".$user_id." LIMIT 1 ;";	
				$eintragen = mysql_query( $abfrage );	
				if ( $eintragen == true )
				{
					sendMail("Registrierung erfolgreich", $user_id, "Vielen Dank für die erfolgreiche Registrierung bei uns!\n\nDu kannst dich ab sofort unter folgendem Link bei uns einloggen.\n\n".$full_site_url."\n\nBei Fragen stehen wir dir gerne zur Verfügung.", 1);
					header('Location: ./framework.php?id=login&register=code_erfolg');					
				}
				else
				{
					//SQL Error
					$fehler='SQL create new User failed. Please contact support.';
					header('Location: ./framework.php?id=login&register='.$fehler);
				}
			}
		}
		else
		{
			//code nicht gefunden
			header('Location: ./framework.php?id=activate&code=fehler');
		}
	}		
	else
	{
		//code nicht im link, zeige form
		echo '
			<title>'.$main_site_title.' Aktivierung</title>';
				if($_GET['code']=="fehler")
				{
					echo '
						<div class="alert alert-error">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
							<div align="center">Aktivierung fehlgeschlagen!<br/><br/>Der eingegebene Aktivierungscode war nicht korrekt. Versuche es bitte erneut. Falls du dir ganz sicher bist, dass der Code korrekt war, nehme bitte Kontakt zu uns auf.</div>
						</div>	  
					';			
				}
			echo '	
			<form action="framework.php?id=activate&code" method="POST">
				<fieldset>
					<legend><i class="icon-ok"></i> Aktivierung abschließen</legend>
					<span class="help-block">Bitte kopieren Sie den Aktivierungscode aus der Email in das Feld und bestätigen Sie hiermit die Echtheit ihres Accounts.</span>
					<label>Aktivierungscode</label>
					<input name="form_code" class="input-xxlarge" required type="text" placeholder="Kopieren Sie hier den Aktivierungslink aus der Email.">
					<span class="help-block">Der Code besteht aus Zahlen und Buchstaben.</span>
					<button type="submit" class="btn btn-primary">Absenden</button>
				</fieldset>
			</form>
		';
	}
}
elseif(isset($_POST['form_forgot']))  
// check Link Aktivierungen von Emails... 
{	
	//Secureimage 
	session_start();
	include_once('securimage/securimage.php');

	$securimage = new Securimage();
	
	// Check ob Captcha richtig eingegeben
	if($securimage->check($_POST['captcha_code2']) == false) {
	  // the code was incorrect
	  // you should handle the error so that the form processor doesn't continue

	  // or you can use the following code if there is no validation or you do not know how
	  $fehler .= "<li>Das eingegebene Captcha stimmt nicht überein</li>";	
	}
	
	$email=$_POST['email'];
	// Email Adresse
	if(!empty($email)) {
		if(filter_var($email,FILTER_VALIDATE_EMAIL) === false)
			$fehler .= "<li>Die Email muss folgendes Format haben: abc@domain.tld</li>";	
	}else{
		$fehler .= "<li>Die Email fehlt</li>";
	}
	
	
	if(empty($fehler))
	{
		include('includes.inc.php');
		require_once('usefulfunctions.inc.php');
		
		$email=mysql_real_escape_string($email);
		$query = "SELECT user_id FROM local_users WHERE email = '$email'";
		$result = mysql_query($query);		
		if(mysql_num_rows($result)>0)		
		{		
			//email vorhanden
			$row=mysql_fetch_object($result);
			$user_id=$row->user_id;
			
			//generate a new password 	
			$password = CreatePassword();
			// crypt pw
			$Encrypt = new Encryption();
			$new_password = $Encrypt->encode($password);
			
			//wenn code korrekt
			//sql update
			$abfrage = "UPDATE `local_users` SET `password` = '".$new_password."' WHERE `user_id` =".$user_id." LIMIT 1 ;";	
			$eintragen = mysql_query( $abfrage );	
			if ( $eintragen == true )
			{
				sendMail("Passwort zurückgesetzt", $user_id, "Soeben wurde dein Passwort bei uns zurückgesetzt.\n\nDein neues Passwort lautet:\n\n".$password."\n\nDu kannst dich nun mit diesem Passwort anmelden.\n\nUnter folgendem Link kannst du dein Passwort ändern\n\n".$full_site_url."framework.php?id=profile&aktion=bearbeiten\n\nFalls du dein Passwort nicht zurückgesetzt hast, melde dich bei unserem Support.\n\nBei Fragen stehen wir dir gerne zur Verfügung.", 1);
				header('Location: ./framework.php?id=login&forgot=erfolg');					
			}
			else
			{
				//SQL Error
				$fehler='SQL new password failed. Please contact support.';
				header('Location: ./framework.php?id=login&forgot='.$fehler);
			}			
		}
		else
		{
			//email nicht gefunden
			$fehler .= "Die Email ist nicht im System vorhanden";	
			header('Location: ./framework.php?id=login&forgot='.$fehler);
		}	
	}
	else
	{
		header('Location: ./framework.php?id=login&forgot='.$fehler);
	}
}
else
{
	header('Location: ./framework.php?id=login');
}
?>