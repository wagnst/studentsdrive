<script type="text/javascript">
  $(function() {
    $('#datepicker1').datepicker();
});	
</script>
<?php

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

include('sessiontest.inc.php');
include('includes.inc.php');
require_once('usefulfunctions.inc.php');

echo'<title>'.$main_site_title.'Profil</title>';
$aktion=addslashes($_GET["aktion"]);
if(!isset($_GET['user']))//wenn keine Schüler_ID übergeben-->eigenes Profil anzeigen
  {
  $user_id=$s_user_id;
  }
else
  {
  $user_id=$_GET['user'];//sonst eingegebenes profil
  }
 
//wenn Profil angezeigt werden soll
if($aktion=="zeigen")
{
	$abfrage = "SELECT * FROM local_users WHERE user_id='$user_id'";
	$ergebnis = mysql_query($abfrage);
	$anzahl = mysql_num_rows($ergebnis);
	$row = mysql_fetch_object($ergebnis);
	
	$empty_text="Keine Angabe";
	
	$facebook_id=$row->facebook_id;
	
	$activation_code=$row->activation_code;
	//vorname
	if($row->vorname<>"")
		$vorname=$row->vorname;
	else
		$vorname=$empty_text;
	
	//nachname
	if($row->nachname<>"")
		$nachname=$row->nachname;
	else
		$nachname=$empty_text;
		
	//alter
	if($row->birth<>"0000-00-00")
		$geb_datum=date("d.m.Y", strtotime($row->birth));
	else	
		$geb_datum=$empty_text;
	
	//registrationsdatum
	if($row->reg_date<>"0000-00-00")
		$registrationsdatum=date("d.m.y", strtotime($row->reg_date));
	else
		$registrationsdatum=$empty_text;

	//email
	if($row->email<>"")
		$email=$row->email;
	else
		$email=$empty_text;
	
	//phone
	if($row->phonenumber<>"")
		$telefon=$row->phonenumber;
	else
		$telefon=$empty_text;
		
	//sex
	if($row->sex<>"")
		$sex=$row->sex;
	else
		$sex=$empty_text;
				
	$profilbild_link=$row->profilbild;
	
	//schulname
	if($row->school_name<>"")
		$schulname=$row->school_name;
	else
		$schulname=$empty_text;
		
	//studiengang
	if($row->school_course<>"")
		$studiengang=$row->school_course;
	else
		$studiengang=$empty_text;

	//wohnort
	if($row->homeplace<>"")
		$wohnort=$row->homeplace;
	else
		$wohnort=$empty_text;
		
	//about
	if($row->about<>"")
		$about=$row->about;
	else
		$about=$empty_text;
		
	//auto_farbe
	if($row->car_colour<>"")
		$auto_farbe=$row->car_colour;
	else
		$auto_farbe=$empty_text;
	
	//auto_kennzeichen
	if($row->car_license_plate<>"")
		$auto_kennzeichen=$row->car_license_plate;
	else
		$auto_kennzeichen=$empty_text;
	
	//auto_sitzplaetze
	if($row->car_seats<>"")
		$auto_sitzplaetze=$row->car_seats;
	else
		$auto_sitzplaetze=$empty_text;

	
	if($sex=="male")
		$geschlecht="männlich";
	else
		$geschlecht="weiblich";

	// Alter berechnen	
	if($geb_datum<>$empty_text)
	{
		$array = explode(".",$geb_datum); 
		$seconds_since_birth = mktime(0,0,0,$array[1],$array[0],$array[2]); 
		$today = time(); 
		 
		$age = $today - $seconds_since_birth; 
		$age_in_years = date("Y",$age) - 1970; 
	}
	else
		$age_in_years = "?";
	 
		
	//5 random user ziehen
    $query = "SELECT user_id, vorname, nachname FROM local_users WHERE user_id <> '$user_id' ORDER BY RAND() LIMIT 5";
    $result = mysql_query($query);
	//$count = mysql_num_rows($result);
	//$row1 = mysql_fetch_object($result);	
	
	//Profil ausgeben
	echo '
		<div class="container">
			<br>
			<div class="row-fluid">
				<div class="span12" id="content">
					<div>
						<div id="details" class="span7">
							<div>
								<div class="row-fluid">
									<div class="span4">
										<img src="'.getProfilePicture($user_id, 150, 150).'" class="img-polaroid">								
									</div>									
									<div class="span1"></div>
									<div class="span6">';
											if($row->verification==1)
											{
												echo '<p><img title="Benutzer wurde durch uns verifizert" src="img/verifiziert.png" height="60" width="60"/> <span class="label label-success">Verifiziert</span></p>';
											}
											else
											{
												echo '<p><a href="framework.php?id=info&amp;sektion=verifizierung"><img title="Benutzer wurde durch uns noch NICHT verifizert" src="img/unverifiziert.png" height="60" width="60"></a> <a href="framework.php?id=info&amp;sektion=verifizierung"><span class="label label-warning">Nicht verifiziert</span></a></p>';
											}
										echo '			
									</div>											
								</div>
								
								<h4><b>'.$vorname.' '.$nachname.'</b></h4>
								<h5>'.$schulname.'</h5>
								<br>
								<!-- Tabs für verschiedene Infos -->
								<ul class="nav nav-tabs">
								  <li class="active"><a href="#home" data-toggle="tab"><i class="icon-info-sign"></i> Persönliches</a></li>
								  <li><a href="#car" data-toggle="tab"><i class="icon-road"></i> Auto</a></li>
								  <li><a href="#reputation" data-toggle="tab"><i class="icon-star"></i> Bewertungen</a></li>
								</ul>	
								<div id="myTabContent" class="tab-content">
									<div class="tab-pane active in" id="home">							
										<table class="table table-striped">		
											<tbody>
												<tr>
													<td>Geburtsdatum:</td>
													<td><i class="icon-calendar"></i> '.$geb_datum.' ('.$age_in_years.' Jahre)</td>
												</tr>											
												<tr>
													<td>Wohnort:</td>
													<td><i class="icon-home"></i> '.$wohnort.'</td>
												</tr>													
												<tr>
													<td>Studiengang:</td>
													<td><i class="icon-book"></i> '.$studiengang.'</td>
												</tr>							
												<tr>
													<td>Email:</td>
													<td><i class="icon-envelope"></i> <a href="mailto:'.$email.'">'.$email.'</a></td>
												</tr>												
												<tr>
													<td>Telefon:</td>
													<td><i class="icon-phone"></i> '.$telefon.'</td>
												</tr>
												<tr>
													<td>Geschlecht:</td>
													<td><i class="icon-heart"></i> '.$geschlecht.'</td>
												</tr>
												<tr>
													<td colspan="2">
														<div class="">
															<i class="icon-quote-left"></i>		
															'.$about.'	
															<i class="icon-quote-right"></i>	
														</div>
													</td>
												</tr>												
											</tbody>
										</table>
									</div>
									<div class="tab-pane fade" id="car">							
										<table class="table table-striped">		
											<tbody>
												<tr>
													<td>Automarke:</td>
													<td><i class="icon-truck"></i> '.getCarName($user_id).'</td>
												</tr>
												
												<tr>
													<td>Automodell:</td>
													<td><i class="icon-wrench"></i> '.getModelName($user_id).'</td>
												</tr>
												<tr>
													<td>Autofarbe:</td>
													<td><i class="icon-pencil"></i> '.$auto_farbe.'</td>
												</tr>
												<tr>
													<td>Amtliches Kennzeichen:</td>
													<td><i class="icon-barcode"></i> '.$auto_kennzeichen.'</td>
												</tr>
											</tbody>
										</table>
									</div>	
									<div class="tab-pane fade" id="reputation">	
										<p>Ist in Arbeit</p>
										<div>
											<i class="icon-star icon-2x"></i><i class="icon-star icon-2x"></i><i class="icon-star icon-2x"></i><i class="icon-star icon-2x"></i><i class="icon-star-half-empty icon-2x"></i>
										</div>												
									</div>											
								</div>';
								
								if((strpos($activation_code, 'mailchange')==FALSE) AND ($activation_code<>"done"))
								{
									echo '
										<hr>
										<div class="alert alert-info">
										  <button type="button" class="close" data-dismiss="alert">&times;</button>	
										  <h4 class="alert-heading">Benutzer ist deaktiviert</h4>
											Dieser Benutzer hat seinen Account noch nicht aktiviert. Falls du einen Verdacht auf Fakeaccount hast, melde dich bitte beim Support.
										</div>';
								}
							echo '	
							</div>
						</div>
						<!-- Aktivitäten -->
						<div class="span5">
							<div id="activities" class="well">
								<h4 class="modal-header">Aktivität</h4>
								<table class="table table-hover">		
									<tbody>
										<tr>
											<td>Angebotene Fahrten:</td>
											<td>12</td>
										</tr>
										
										<tr>
												
											<td>Teilnahme Fahrten:</td>
											<td>5</td>
										</tr>
										<tr>
											<td>Mitglied seit:</td>
											<td>'.$registrationsdatum.'</td>
										</tr>
									</tbody>
								</table>								
							</div>
						</div>	
						<!-- Andere Benutzer -->
						<div class="span5">
							<div id="random_users" class="well">
								<h4 class="modal-header">Andere Benutzer</h4>
								<ul class="nav nav-list">';
                                while($row1=mysql_fetch_object($result))
                                {    
									//sql query -> Limit 5
                                    echo '
                                    <li>
										<a href="framework.php?id=profile&amp;aktion=zeigen&amp;user='.$row1->user_id.'">
											<p class="list-item"><img src="'.getProfilePicture($row1->user_id, 50, 50).'" style="float:left;margin-right: 10px;" height="30" width="30">'.$row1->vorname.' '.$row1->nachname.'<br></p>
										</a>
                                    </li>';
                                }						
								echo '
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<hr>
		</div>	
	';

}
//wenn eigenes Profil bearbeitet werden soll
elseif($aktion=="bearbeiten")
{
	$abfrage = "SELECT * FROM local_users WHERE user_id='$s_user_id'";
	$ergebnis = mysql_query($abfrage);
	$anzahl = mysql_num_rows($ergebnis);
	$row = mysql_fetch_object($ergebnis);
	
	$facebook_id=$row->facebook_id;
	$vorname=$row->vorname;
	$nachname=$row->nachname;
	//alter
	if($row->birth<>"0000-00-00")
		$geb_datum=$row->birth;
	else
		$geb_datum="";
	$email=$row->email;
	$telefon=$row->phonenumber;
	$sex=$row->sex;
	$profilbild_link=$row->profilbild;
	$schulname=$row->school_name;
	$studiengang=$row->school_course;
	$wohnort=$row->homeplace;
	$about=$row->about;
	$auto_marke=$row->car_brand;
	$auto_model=$row->car_model;
	$auto_farbe=$row->car_colour;
	$auto_kennzeichen=$row->car_license_plate;
	$auto_sitzplaetze=$row->car_seats;
	
	// Automarken holen
	// $abfrage_marke = "SELECT * FROM car_brands";
	// $ergebnis_marke = mysql_query($abfrage_marke);
	// $row_marke = mysql_fetch_object($ergebnis_marke);	
	
	//fit license plate format for output
	$auto_kennzeichen_array = split( '-', $auto_kennzeichen );

	//check Geschlecht
	if($sex=="male") 
	{	$selectedSex='<option selected value="male">männlich</option><option value="female">weiblich</option>';	}
	elseif($sex=="female") 
	{	$selectedSex='<option value="male">männlich</option><option selected value="female">weiblich</option>';	}
	
	//check Sitzplätze
	if($auto_sitzplaetze=="1") 
	{	$auto_sitzplaetze_selected='	<option selected value="1">1 freier Sitzplatz</option>
			<option value="2">2 freie Sitzplätze</option>
			<option value="3">3 freie Sitzplätze</option>
			<option value="4">4 freie Sitzplätze</option>
			<option value="5">5 freie Sitzplätze</option>
			<option value="6">6 freie Sitzplätze</option>';	}
	elseif($auto_sitzplaetze=="2") 
	{	$auto_sitzplaetze_selected='	<option value="1">1 freier Sitzplatz</option>
			<option selected value="2">2 freie Sitzplätze</option>
			<option value="3">3 freie Sitzplätze</option>
			<option value="4">4 freie Sitzplätze</option>
			<option value="5">5 freie Sitzplätze</option>
			<option value="6">6 freie Sitzplätze</option>';	}
	elseif($auto_sitzplaetze=="3") 
	{	$auto_sitzplaetze_selected='	<option value="1">1 freier Sitzplatz</option>
			<option value="2">2 freie Sitzplätze</option>
			<option selected value="3">3 freie Sitzplätze</option>
			<option value="4">4 freie Sitzplätze</option>
			<option value="5">5 freie Sitzplätze</option>
			<option value="6">6 freie Sitzplätze</option>';	}
	elseif($auto_sitzplaetze=="4") 
	{	$auto_sitzplaetze_selected='	<option value="1">1 freier Sitzplatz</option>
			<option value="2">2 freie Sitzplätze</option>
			<option value="3">3 freie Sitzplätze</option>
			<option selected value="4">4 freie Sitzplätze</option>
			<option value="5">5 freie Sitzplätze</option>
			<option value="6">6 freie Sitzplätze</option>';	}
	elseif($auto_sitzplaetze=="5") 
	{	$auto_sitzplaetze_selected='	<option value="1">1 freier Sitzplatz</option>
			<option value="2">2 freie Sitzplätze</option>
			<option value="3">3 freie Sitzplätze</option>
			<option value="4">4 freie Sitzplätze</option>
			<option selected value="5">5 freie Sitzplätze</option>
			<option value="6">6 freie Sitzplätze</option>';	}
	elseif($auto_sitzplaetze=="6") 
	{	$auto_sitzplaetze_selected='	<option value="1">1 freier Sitzplatz</option>
			<option value="2">2 freie Sitzplätze</option>
			<option value="3">3 freie Sitzplätze</option>
			<option value="4">4 freie Sitzplätze</option>
			<option value="5">5 freie Sitzplätze</option>
			<option selected value="6">6 freie Sitzplätze</option>';	}	
	else
	{	$auto_sitzplaetze_selected='	<option selected value="1">1 freier Sitzplatz</option>
			<option value="2">2 freie Sitzplätze</option>
			<option value="3">3 freie Sitzplätze</option>
			<option value="4">4 freie Sitzplätze</option>
			<option value="5">5 freie Sitzplätze</option>
			<option value="6">6 freie Sitzplätze</option>';	}				
			
	//Nach Fehler oder Erfolg checken
	if(isset($_GET['msg']))
	{
		$alertbar="";
		if(($_GET['msg'])=="fehler")
		{
		$alertbar='
			<div class="alert alert-error">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4>Oops, da ist etwas schiefgelaufen!</h4>
				Bitte überprüfe deine eben getätigten Änderungen und versuche es erneut.<br>Folgende Fehler sind aufgetreten:<br><br>
				<ul>'.$_SESSION["post_fehler"].'</ul>
			</div>		
		';
		}
		elseif(($_GET['msg'])=="erfolg")
		{
		$alertbar='
			<div class="alert alert-success">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4>Sehr gut, Änderung war erfolgreich!</h4>
				Deine eben getätigten Änderungen waren korrekt und wurden veröffentlicht.
			</div>		
		';		
		}
		elseif(($_GET['msg'])=="firsttime")
		{
		$alertbar='
			<div class="alert alert-info">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4>Vielen Dank für deine Registrierung!</h4>
				Um den vollen Funktionsumfang nutzen zu können, musst du alle mit * gekennzeichneten Felder ausfüllen, damit das System einwandfrei funktioniert. <br><br>Bei Fragen kannst du uns <a href="./framework.php?id=info&amp;sektion=kontakt">hier</a> kontaktieren.<br><br>Viel Spass!
			</div>		
		';		
		}
		else
			$alertbar='';
	}
	else
		$alertbar='';	


	//dynamic dropdowns bei auto/modelwahl
	?>
		<script language="javascript">
		function setOptions(chosen) {
		  var selbox = document.car.selectmodel;
		   
		  selbox.options.length = 0;
		  if (chosen == "0") {
			selbox.options[selbox.options.length] = new Option('Erst eine Automarke wählen','0');
		   
		  }
		  <?php
		  $car_result = mysql_query("SELECT * FROM car_brands") or die(mysql_error());
		  while(@($c=mysql_fetch_array($car_result)))
		  {
		  ?>
			if (chosen == "<?=$c['makes_id'];?>") {
			
			<?php
			$c_id = $c['makes_id'];
			$mod_result = mysql_query("SELECT * FROM car_models WHERE makes_id='$c_id'") or die(mysql_error());
			while(@($m=mysql_fetch_array($mod_result)))
			{
			?>
			  selbox.options[selbox.options.length] = new
			  Option('<?=$m['models_name'];?>','<?=$m['models_id'];?>');
			<?php
			}
			?>
			}
		  <?php
		  }
		  ?>
		}
		</script>			
	<?php	
	//Datenauswahl für Auto/Marke
	$car_model_select = '<label>Automarke*</label>';
	$car_model_select .= '<select required class="input-xlarge" name="selectcar" onchange="setOptions(document.car.selectcar.options[document.car.selectcar.selectedIndex].value);">';
	$car_model_select .= '<option value="0" selected>Wähle eine Automarke</option>';
		$result = mysql_query("SELECT * FROM car_brands") or die(mysql_error());
		while(@($r=mysql_fetch_array($result)))
		{
				$car_model_select .= '<option value="'.$r["makes_id"].'">'.$r["makes_name"].'</option>';
		}
	$car_model_select .= '</select>';
	$car_model_select .= '<label>Automodell*</label>';
	$car_model_select .= '<select required class="input-xlarge" name="selectmodel">';
	$car_model_select .= '<option value="" selected>Erst eine Automarke wählen</option>';
	$car_model_select .= '</select>';
	
	//Profil ausgeben
	echo '
			<ul class="nav nav-tabs">
			  <li class="active"><a href="#home" data-toggle="tab"><i class="icon-info-sign"></i> Persönliches</a></li>
			  <li><a href="#education" data-toggle="tab"><i class="icon-book"></i> Meine Bildung</a></li>
			  <li><a href="#car" data-toggle="tab"><i class="icon-road"></i> Mein Auto</a></li>
			  <li><a href="#password" data-toggle="tab"><i class="icon-lock"></i> Passwort ändern</a></li>
			</ul>
			'.$alertbar.'
			<div id="myTabContent" class="tab-content">
			  <!-- Tab for personal things-->
			  <div class="tab-pane active in" id="home">
				<form name="personal" action="post.php" method="post" id="tab">
					<div class="row-fluid">
						<div class="span7">
							<label>Vorname*</label>
							<input name="vorname" type="text" value="'.$vorname.'" class="input-xlarge" required>
							<label>Nachname</label>
							<input name="nachname" type="text" value="'.$nachname.'" class="input-xlarge">
							<label>Geburtsdatum</label>	
							<div class="input-append date" id="datepicker1" data-date="'.date("d.m.Y", strtotime($geb_datum)).'" data-date-format="dd.mm.yyyy">
								<input placeholder="dd.mm.yyyy" name="geb_datum" class="input-xlarge" type="text" value="'.date("d.m.Y", strtotime($geb_datum)).'">
								<span class="add-on"><i class="icon-calendar"></i></span>
							</div>											
							<label>Email*</label>
							<input name="email" type="text" value="'.$email.'" class="input-xlarge" required>';
							if($row->verification==1)
							{
								echo '<label>Telefon / Handy (verifiziert)</label><input name="" type="text" value="'.$telefon.'" class="input-xlarge uneditable-input" disabled>';
							}
							else
							{
								echo '<label>Telefon / Handy (unverifiziert)</label><input name="telefon" type="text" value="'.$telefon.'" class="input-xlarge">';
							}	
							echo '
											
							<label>Geschlecht*</label>
							<select name="selectSex" id="selectSex" class="input-xlarge">
								'.$selectedSex.'
							</select>		
							<label>Wohnort</label>
							<input name="wohnort" class="input-xlarge" id="googleAutoCompleteCity" value="'.$wohnort.'"type="text" size="50" placeholder="Wohnort eingeben" autocomplete="on">
							<label>Über mich</label>
							<textarea name="about" value="Über mich" rows="3" class="input-xlarge">'.$about.'</textarea>
						</div>
						<div class="span5">
							<label>Profilbild</label>
							<!-- Recent Picture -->
							<img src="'.getProfilePicture($s_user_id, 200, 200).'" class="img-polaroid" width="200" height="200" ></br></br>
							<!-- Upload new Picture -->
							<a href="#upload_profile_picture" role="button" class="btn" data-toggle="modal"><i class="icon-camera"></i> Profilbild hochladen</a>									
						</div>
					</div>
					<div class="form-actions">
						<button class="btn btn-primary" type="submit">Speichern</button>
						<button class="btn" type="reset">Abbrechen</button>
					</div>					
					<input type="hidden" name="form_personal" value="1">
				</form>
			  </div>
			  <!-- Tab for school and education things-->
			  <div class="tab-pane fade" id="education">
				<form name="education" action="post.php" method="post" id="tab2">
					<label>Universität / Hochschule*</label>
					<input name="schulname" class="input-xlarge" id="googleAutoCompleteEducation" value="'.$schulname.'"type="text" size="50" placeholder="Einrichtungsname eingeben" autocomplete="on" required>
					<label>Studiengang</label>
					<input name="studiengang" type="text" value="'.$studiengang.'" class="input-xlarge">					
					<div class="form-actions">
						<button class="btn btn-primary" type="submit">Speichern</button>
						<button class="btn" type="reset">Abbrechen</button>
					</div>	
					<input type="hidden" name="form_education" value="1">
				</form>
			  </div>	
			  <!-- Tab for car settings -->	
			  <div class="tab-pane fade" id="car">
			  
				<form name="car" action="post.php" method="post" id="tab3">
					<!--
					<label>Automarke*</label>
					<input name="automarke" type="text" value="'.$auto_marke.'" class="input-xlarge" required>					
					<label>Automodell*</label>
					<input name="automodell" type="text" value="'.$auto_model.'" class="input-xlarge">-->
					'.$car_model_select.'			
					<label>Autofarbe*</label>
					<input name="autofarbe" type="text" value="'.$auto_farbe.'" class="input-xlarge required">						
					<label>Verfügbare Sitzplätze*</label>
					<select name="selectSeats" id="selectSeats" class="input-xlarge">
						'.$auto_sitzplaetze_selected.'
					</select>
					<label>Amtliches Kennzeichen</label>
					<div class="controls controls-row">
						<input name="nummernschild_1" maxlength="3" class="span1" type="text" value="'.$auto_kennzeichen_array[0].'" placeholder="B">
						<input name="nummernschild_2" maxlength="3" class="span1" type="text" value="'.$auto_kennzeichen_array[1].'" placeholder="MW">
						<input name="nummernschild_3" maxlength="3" class="span1" type="text" value="'.$auto_kennzeichen_array[2].'" placeholder="123">
					</div>					
					<div class="form-actions">
						<button class="btn btn-primary" type="submit">Speichern</button>
						<button class="btn" type="reset">Abbrechen</button>
					</div>	
					<input type="hidden" name="form_car" value="1">
				</form>
			  </div>			  
			  <!-- Tab for password change -->	
			  <div class="tab-pane fade" id="password">
				<form name="password" action="post.php" method="post" id="tab4">
					<label>Altes Passwort eingeben*</label>
					<input type="password" name="password_old" class="input-xlarge" required>
					<label>Neues Passwort eingeben*</label>
					<input type="password" name="password1" class="input-xlarge" required>
					<label>Neues Passwort wiederholen*</label>
					<input type="password" name="password2" class="input-xlarge" required>					
					<div class="form-actions">
						<button class="btn btn-primary" type="submit">Speichern</button>
						<button class="btn" type="reset">Abbrechen</button>
					</div>	
					<input type="hidden" name="form_password" value="1">
				</form>
			  </div>
			  <hr>
			  <small>mit * gekennzeichnete Felder sind Pflichtfelder</small>
		  </div>


			<!-- Modal -->
			<div id="upload_profile_picture" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="ImageUpload" aria-hidden="true">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3 id="myModalLabel">Neues Profilbild hochladen</h3>
				</div>
				<div class="modal-body">
					<div class="imgupload">
						<!-- upload form -->
						<form id="upload_form" enctype="multipart/form-data" method="post" action="upload.php" onsubmit="return checkForm()">
							<!-- hidden crop params -->
							<input type="hidden" id="x1" name="x1" />
							<input type="hidden" id="y1" name="y1" />
							<input type="hidden" id="x2" name="x2" />
							<input type="hidden" id="y2" name="y2" />

							<h4>Schritt 1: Wähle ein Bild</h4>
							<div class="alert alert-info">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								Die Datei darf nicht größer als 250 kilobyte sein und muss im Format JPEG oder PNG hochgeladen werden.
							</div>
							<div><input type="file" name="image_file" id="image_file" onchange="fileSelectHandler()" /></div>

							<div class="alert alert-error error"></div>

							<div class="step2">
								<hr>
								<h4>Schritt 2: Optimiere das Bild</h4>
								<img id="preview" />
								<hr>
								<div class="info">
									<div class="row-fluid">
											<div class="span6">
												<label>Größe</label> <input class="input-small uneditable-input" type="text" id="filesize" name="filesize"/>
												<label>Typ</label> <input class="input-small uneditable-input" type="text" id="filetype" name="filetype"/>
												<label>Ausmessungen</label> <input class="input-small uneditable-input" type="text" id="filedim" name="filedim"/>
											</div>

											<div class="span6">
												<div class="row-fluid">
													<label>Aktuelle Breite</label> <input class="input-small uneditable-input" type="text" id="w" name="w"/>
													<label>Aktuelle Höhe</label> <input class="input-small uneditable-input" type="text" id="h" name="h"/>
												</div>
											</div>
									</div>
								</div>							
								<div class="modal-footer">
									<button class="btn" data-dismiss="modal" aria-hidden="true">Zurück</button>
									<button value="Upload" type="submit" class="btn btn-primary">Hochladen</button>
								</div>
							</div>
						</form>
					</div>		
				</div>
			</div>		  
	  
	';
}
else
{
	echo showError404();
}


?>