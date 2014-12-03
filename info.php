<?php

	if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');
	
	$sektion=addslashes($_GET["sektion"]);	
	
	if($sektion=="faq")
	{
		echo '<div class="row-fluid">';
		echo'<title>'.$main_site_title.'Häufig gestellte Fragen</title>';	
		$abfrage='SELECT * FROM info_text WHERE bezeichnung LIKE "faq"';
		$ergebnis=mysql_query($abfrage);
		$row=mysql_fetch_object($ergebnis);
		echo '<div class="row-fluid">
		'.$row->text.'
		</div>
		</div>';	
	}
	elseif($sektion=="richtlinien")
	{
		echo '<div class="row-fluid">';
		echo'<title>'.$main_site_title.'Richtlinien</title>';	
		$abfrage='SELECT * FROM info_text WHERE bezeichnung LIKE "richtlinien"';
		$ergebnis=mysql_query($abfrage);
		$row=mysql_fetch_object($ergebnis);
		echo '<div class="row-fluid">
		'.$row->text.'
		</div>
		</div>';	
	}
	elseif($sektion=="datenschutz")
	{
		echo '<div class="row-fluid">';
		echo'<title>'.$main_site_title.'Datenschutz</title>';	
		$abfrage='SELECT * FROM info_text WHERE bezeichnung LIKE "datenschutz"';
		$ergebnis=mysql_query($abfrage);
		$row=mysql_fetch_object($ergebnis);
		echo '<div class="row-fluid">
		'.$row->text.'
		</div>
		</div>';	
	}
	elseif($sektion=="impressum")
	{
		echo '<div class="row-fluid">';
		echo'<title>'.$main_site_title.'Impressum</title>';	
		$abfrage='SELECT * FROM info_text WHERE bezeichnung LIKE "impressum"';
		$ergebnis=mysql_query($abfrage);
		$row=mysql_fetch_object($ergebnis);
		echo '<div class="row-fluid">
		'.$row->text.'
		</div>
		</div>';
	}
	elseif($sektion=="about")
	{
		echo '<div class="row-fluid">';
		echo'<title>'.$main_site_title.'Über uns</title>';	
		$abfrage='SELECT * FROM info_text WHERE bezeichnung LIKE "about"';
		$ergebnis=mysql_query($abfrage);
		$row=mysql_fetch_object($ergebnis);
		echo '<div class="row-fluid">
		'.$row->text.'
		</div>
		</div>';
	}	
	elseif($sektion=="werbung")
	{
		echo '<div class="row-fluid">';
		echo'<title>'.$main_site_title.'Werbung</title>';	
		$abfrage='SELECT * FROM info_text WHERE bezeichnung LIKE "werbung"';
		$ergebnis=mysql_query($abfrage);
		$row=mysql_fetch_object($ergebnis);
		echo '<div class="row-fluid">
		'.$row->text.'
		</div>
		</div>';
	}	
	elseif($sektion=="verifizierung")
	{
		echo '<div class="row-fluid">';
		echo'<title>'.$main_site_title.'Verifizierung</title>';	
		$abfrage='SELECT * FROM info_text WHERE bezeichnung LIKE "verifizierung"';
		$ergebnis=mysql_query($abfrage);
		$row=mysql_fetch_object($ergebnis);
		echo '<div class="row-fluid">
		'.$row->text.'
		</div>
		</div>';
	}		
	elseif($sektion=="statistik")
	{
		echo '<div class="row-fluid">';
		echo'<title>'.$main_site_title.'Statistik</title>';
		echo '<h2>Statistik</h2>';
		include('chilistats/stats.inc.php');
		echo'</div>';
	}		
	elseif($sektion=="kontakt")
	{
		if(session_id() == '')
			session_start();// NEVER FORGET TO START THE SESSION!!!
		echo '<div class="row-fluid">';
		echo'<title>'.$main_site_title.'Kontakt</title>';
		if((isset($_GET['msg'])) AND (($_GET['msg'])<>"erfolg"))
		{
		$alertbar='
			<div class="alert alert-error">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4>Oops, da ist etwas schiefgelaufen!</h4>
				Folgende Fehler sind aufgetreten:<br><br>
				<ul>'.$_GET["msg"].'</ul>
			</div>		
		';
		}
		elseif(isset($_GET['msg']) AND (($_GET['msg'])=="erfolg"))
		{
		$alertbar='
			<div class="alert alert-success">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4>Nachricht erfolgreich!</h4>
				Die Nachricht wurde erfolgreich an uns übermittelt.<br>Bitte habe etwas Geduld. Wir werden uns sobald wie möglich mit dir in Verbindung setzen.
			</div>		
		';
		}
		else
			$alertbar='';
		echo '
		<div>
			<h3>Kontaktformular</h3>
			<p>Hier kannst du Kontakt zu uns Aufnehmen</p>
		</div>
		<hr>
		<div class="container">		
		'.$alertbar.'
		<form class="form-horizontal" action="post.php" method="post">
			<div class="control-group">
				<label class="control-label">Betreff</label>
				<div class="controls">
					<input class="input-xlarge" type="text" id="subject" placeholder="Themenüberschrift" name="betreff" required>
				</div>
			</div>';
			if (!$_SESSION["logged_in"])
			{
				echo '			
					<div class="control-group">
						<label class="control-label">Dein Name</label>
						<div class="controls">
							<input class="input-xlarge" type="text" id="subject" placeholder="Vor- und Nachname" name="name" required>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Deine E-Mail</label>
						<div class="controls">
							<input class="input-xlarge" type="text" id="subject" placeholder="E-Mail Adresse" name="email" required>
						</div>
					</div>
					<input type="hidden" name="usr_logged_in" value="0">';
			}
			else
			{	
				echo '<input type="hidden" name="usr_logged_in" value="1">';
			}
			echo '
			<div class="control-group">
				<label class="control-label">Text</label>
				<div class="controls">
					<textarea name="text" class="input-xlarge" id="text" rows="6" required></textarea>
				</div>
			</div>
			<div class="control-group">
			<div class="form-actions">
				<button class="btn btn-primary" type="submit">Absenden</button>
				<button class="btn" type="reset">Abbrechen</button>
			</div>
			</div>
			<input type="hidden" name="form_contactformular" value="1">
		</form>	
		</div>
		</div>
		';
	}	
	else
	{
		echo showError404();
	}

	
?>