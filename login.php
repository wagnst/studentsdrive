<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');
?>
<title><?php echo $main_site_title.' Willkommen'; ?></title>	
<?php
if(session_id() == '')
	session_start();

include('includes.inc.php');

if ((isset ($_SESSION["logged_in"])) and ($_SESSION["logged_in"]==TRUE))//überprüft, ob Nutzer eingeloggt ist
  {
	header('Location: ./framework.php?id=start');
  }

$fehler=FALSE;	

if ((isset($_POST['benutzer'])) OR (isset($_POST['passwort'])))//wenn Logindaten über POST gesendet, dann überprüfen ODER Facebook Login
  {
  $logoutUrl="./framework.php?id=logout";
  $benutzer=mysql_real_escape_string($_POST['benutzer']);
  $passwort=mysql_real_escape_string($_POST['passwort']);
  $abfrage = "SELECT * FROM local_users WHERE (email LIKE '$benutzer')";

  $ergebnis = mysql_query($abfrage);
  if (mysql_num_rows($ergebnis)>0)
    {
    $row= mysql_fetch_object($ergebnis);
	  if(($row->activation_code=="done") OR ($row->activation_code=="first_login") OR (strpos($row->activation_code,'mailchange') !== false))
	   {
		$Encrypt = new Encryption();
		if ($passwort==($Encrypt->decode($row->password)))//Passwort-Prüfsumme vergleichen; wenn gültig zur indexseite
		  {
		  $_SESSION["user_id"]=$row->user_id;
		  $_SESSION["facebook_id"]=$row->facebook_id;
		  $_SESSION["logout_url"]=$logoutUrl;	
		  $_SESSION["logged_in"]=TRUE;
		  header('Location: ./framework.php?id=start');
		  }
		else//ungültige login-kombi
		  {
		  $fehler=TRUE;
		  $fehlermsg='<b>Falsche Email-Passwort Kombination<b>';
		  }
	   } 
	  else
	   {
		//Account noch nicht aktiviert
		$fehler=TRUE;
		$fehlermsg='<b>Bitte aktivieren Sie zuerst ihren Account. Den Link dazu finden Sie in der ihnen zugesandten Email.<b>';		
	   }
	}
  else//benutzername fehlerhaft
    {
	$fehler=TRUE;
	$fehlermsg='<b>E-Mail nicht bekannt<b>';
	}
  } 	
//start facebook from functions
$facebook = startFacebook();
//retrieving user
$user = $facebook->getUser();

if ($user) {
  try {
	// Proceed knowing you have a logged in user who's authenticated.
	// Set Logout URL
	$token = $facebook->getAccessToken();
	$logoutUrl = 'https://www.facebook.com/logout.php?next=http://steffenwagner.com/studentsdrive/framework/framework.php?id=logout&amp;access_token='.$token;	
	//$logoutUrl = $facebook->getLogoutUrl(array('next' => 'http://steffenwagner.com/studentsdrive/framework/framework.php?id=logout'));
	//$logoutUrl = $facebook->getLogoutUrl(array( 'next' => ($fbconfig['baseurl'].'logout.php') ));
	
	
	$fql_query  =   array(
		'method' => 'fql.query',
		'query' => 'SELECT uid, email, first_name, last_name, sex FROM user WHERE uid = '.$facebook->getUser()
	);
	$fql_info = $facebook->api($fql_query);	

	//$user_profile = $facebook->api('/me');
	// Check if user exists in local_users; else create a new account for him
	// https://developers.facebook.com/docs/reference/fql/user/
	$fb_id=$fql_info[0][uid];
	$fb_mail=$fql_info[0][email];
	$fb_vorname=$fql_info[0][first_name];
	$fb_nachname=$fql_info[0][last_name];
	$fb_sex=$fql_info[0][sex];
	
	$abfrage = "SELECT * FROM local_users WHERE facebook_id = '$fb_id'";
	$ergebnis = mysql_query($abfrage);
	if (mysql_num_rows($ergebnis)>0)
	{	
		//found a user with this id
		$row= mysql_fetch_object($ergebnis);
		// !!!!!!!!!!!!!!!!!CHECK FOR THE FACEBOOK AUTH TICKET!!!!!!!!!!!!!!!!!
		// !!!!!!!!!!!!!!!!!CHECK FOR THE FACEBOOK AUTH TICKET!!!!!!!!!!!!!!!!!
		// !!!!!!!!!!!!!!!!!CHECK FOR THE FACEBOOK AUTH TICKET!!!!!!!!!!!!!!!!!
		// !!!!!!!!!!!!!!!!!CHECK FOR THE FACEBOOK AUTH TICKET!!!!!!!!!!!!!!!!!
		// !!!!!!!!!!!!!!!!!CHECK FOR THE FACEBOOK AUTH TICKET!!!!!!!!!!!!!!!!!
		// !!!!!!!!!!!!!!!!!CHECK FOR THE FACEBOOK AUTH TICKET!!!!!!!!!!!!!!!!!
		if ($row->email=$fb_mail) 
		{
			//correct user found
			//update profilinformationen
			$abfrage1 = "UPDATE `local_users` SET `vorname` = '".$fb_vorname."', `nachname` = '".$fb_nachname."', `sex` = '".$fb_sex."' WHERE `facebook_id` = ".$fb_id." LIMIT 1 ;";
			if (mysql_query($abfrage1) == true)
			{
				//set php session
				$_SESSION["user_id"]=$row->user_id;
				$_SESSION["facebook_id"]=$row->facebook_id;
				$_SESSION["logout_url"]=$logoutUrl;
				$_SESSION["logged_in"]=TRUE;
				header('Location: ./framework.php?id=start');					
			}
			else
			{
				$fehler=TRUE;
				$fehlermsg='<li>Facebook FQL Update failed. Please contact support.</li>';
			}
		}
		else
		{
			//wrong user
			//continue later....
			$fehler=TRUE;
			$fehlermsg='<li>Deine Email-Adressen (Facebook & StudendtsDrive) stimmen nicht überein. Bitte Support kontaktieren.</li>';
		}
	}
	else
	{
		//Register date
		$reg_datum = date('Y-m-d', strtotime(str_replace('-', '/', $date)));	
		
		//user logged in for the first time
		//create a new local_users account
		$abfrage = "INSERT INTO `local_users` (
		  `facebook_id` ,
		  `vorname`,
		  `nachname`,
		  `email`,
		  `sex`
		   )
		   VALUES ('" . $fb_id . "', '" . $fb_vorname . "', '" . $fb_nachname . "', `reg_date` = '".$reg_datum."', '" . $fb_mail . "', '" . $fb_sex . "');";	
		$eintragen = mysql_query( $abfrage );	
		if ( $eintragen == true )
		{
			//set php session
			$_SESSION["user_id"]=$row->user_id;
			$_SESSION["facebook_id"]=$row->facebook_id;
			$_SESSION["logout_url"]=$logoutUrl;
			$_SESSION["logged_in"]=TRUE;
			header('Location: ./framework.php?id=start');	
		}
		else
		{
			//SQL Error
			//continue later....
			$fehler=TRUE;
			$fehlermsg='<li>Facebook SQL create new User failed. Please contact support.</li>';
		}	
		//retry login now		
	}
  } 
  catch (FacebookApiException $e) 
  {
	error_log($e);
	$user = null;
  }
}
  
?>

<div id="fb-root"></div> 
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId: '<?php echo $facebook->getAppID() ?>',
      cookie: true,
      xfbml: true,
      oauth: true
    });
    FB.Event.subscribe('auth.login', function(response) {
      window.location.reload();
    });
    FB.Event.subscribe('auth.logout', function(response) {
      window.location.reload();
    });
  };
  (function() {
    var e = document.createElement('script'); e.async = true;
    e.src = document.location.protocol +
      '//connect.facebook.net/en_US/all.js';
    document.getElementById('fb-root').appendChild(e);
  }());
</script>
<div class="jumbotron">
	<div class="hero-unit">
		<img class="img-polaroid img-rounded" src="img/login_car.jpg"/>
		<h1>Allein zur Uni? Laaaangweilig</h1>
		<p>Suchst du eine günstige Möglichkeit zur Uni zu kommen? Bist du auch nicht gerne alleine Unterwegs? <br/>Dann starte jetzt durch!</p>
	</div>
</div>
<?php
		if(isset($_GET["register"]))
			$registration=addslashes($_GET["register"]);
		if(isset($_GET["forgot"]))
			$forgot=addslashes($_GET["forgot"]);
		if($fehler)
		{
			echo '
				<div class="alert alert-error">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<div align="center">Oops, da ist etwas schiefgelaufen!<br/><br/><big>'.$fehlermsg.'</big><br/><br/>Versuche es erneut!</div>
				</div>	  
			';
		}	
		elseif(isset($registration))
		{
			if($registration=="code_sent")
			{
				echo '
					<div class="alert alert-info">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
						<div align="center">Vielen Dank für deine Registrierung!<br/><br/>Um deinen Account zu aktivieren, musst du auf den Link in der Email, die dir soeben von uns zugesandt wurde, klicken. Überprüfe bitte auch deinen Spam-Ordner. Anschließend kannst du unser volles Angebot nutzen!</div>
					</div>	  
				';			
			}
			elseif($registration=="code_erfolg")
			{
				echo '
					<div class="alert alert-success">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
						<div align="center">Registrierung erfolgreich!<br/><br/>Dein Account wurde soeben aktiviert. Du kannst dich nun unten einloggen!</div>
					</div>	  
				';			
			}	
			// code Fehler ist in post.php, da erneute eingabe möglich sein soll...
			else
			{
				echo '
					<div class="alert alert-error">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
						<div align="center">Oops, da ist etwas schiefgelaufen!<br/><br/><big>'.$registration.'</big><br/><br/>Versuche es erneut!</div>
					</div>	  
				';
			}
		}	
		elseif(isset($forgot))
		{
			if($forgot=="erfolg")
			{
				echo '
					<div class="alert alert-success">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
						<div align="center">Passwort zurückgesetzt!<br/><br/>Du hast eine Email mit einem neuen Passwort zugeschickt bekommen. Mit diesem kannst du dich nun einloggen.</div>
					</div>	  
				';			
			}	
			// code Fehler ist in post.php, da erneute eingabe möglich sein soll...
			else
			{
				echo '
					<div class="alert alert-error">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
						<div align="center">Oops, da ist etwas schiefgelaufen!<br/><br/><big>'.$forgot.'</big><br/><br/>Versuche es erneut!</div>
					</div>	  
				';
			}
		}		
	?>	
	  <div class="tabbable">
		<div class="well">
		  <ul class="nav nav-tabs">
			<!-- Tab Controls -->
			<li class="active"><a href="#login" data-toggle="tab">Einloggen</a></li>
			<li><a href="#create" data-toggle="tab">Registrieren</a></li>
			<li><a href="#reset" data-toggle="tab">Passwort vergessen?</a></li>
		  </ul>
		  <!-- Overall Content -->
		  <div id="myTabContent" class="tab-content">
			<!-- Login Content -->
			<div class="tab-pane active in" id="login">
				<div id="legend">
						<legend class="">Mit Facebook anmelden</legend>
				</div>  
				<?php if ($user) { ?>
					<a href="<?php echo $logoutUrl; ?>">Ausloggen</a>	
				<?php } else { ?>
					<div align="center">
						<fb:login-button size="large" show-faces="true" width="200" max-rows="1" perms="email,user_education_history,user_location" >Mit Facebook einloggen</fb:login-button>
					</div>
				<?php } ?>	
			  <form class="form-horizontal" action="framework.php?id=login" method="POST">
				<fieldset>
				  <div id="legend">
					<legend class="">Ohne Facebook anmelden</legend>
				  </div>  					  
				  <div class="control-group">
					<!-- Username -->
					<label class="control-label"  for="username">E-Mail</label>
					<div class="controls">
					  <div class="input-append">
					    <input placeholder="Deine E-Mail Adresse" type="text" id="username" name="benutzer" class="input-xlarge" required>
						<span class="add-on">
							<i class="icon-envelope"></i>
						</span>			
					  </div>
					</div>
				  </div>

				  <div class="control-group">
					<!-- Password-->
					<label class="control-label" for="password">Passwort</label>
					<div class="controls">
					  <div class="input-append">
					    <input type="password" id="password" name="passwort" placeholder="Dein Passwort" class="input-xlarge" required>
						<span class="add-on">
							<i class="icon-lock"></i>
						</span>			
					  </div>					  
					</div>
				  </div>

				  <div class="control-group">
					<!-- Button -->
					<div class="controls">
					  <button class="btn btn-success btn-large">Einloggen</button>
					</div>
				  </div>
				</fieldset>
			  </form>  	  
			</div>
			<!-- Account create Content -->
			<div class="tab-pane fade" id="create">
			  <form action="post.php" method="POST" >
				<div class="row-fluid">
					<div class="row-fluid">
						<div class="span6">
							<label>Vorname*</label>
							<input type="text" name="vorname" value="" placeholder="Gebe deinen Vornamen ein" class="input-xlarge" required>
						</div>
						<div class="span6">
							<label>Nachname</label>
							<input type="text" name="nachname" value="" placeholder="Gebe deinen Nachnamen ein" class="input-xlarge">
						</div>
					</div>
					<label>Email*</label>
					<input type="text" name="email" value="" placeholder="Gebe deine Email Adresse ein" class="input-xlarge" required>
					<label>Geschlecht*</label>
					<select name="selectSex" id="selectSex" class="input-xlarge">
						<option selected value="male">Männlich</option>
						<option value="female">weiblich</option>
					</select>
					<label>Universität / Hochschule*</label>
					<input name="schulname" class="input-xlarge" id="googleAutoCompleteEducation" type="text" size="50" placeholder="Einrichtungsname eingeben" autocomplete="on" required>
					<div class="row-fluid">
						<div class="span6">
							<label>Passwort*</label>
							<input type="password" name="password1" value="" placeholder="Passwort mit mindestens 6 Stellen" class="input-xlarge" required>
						</div>
						<div class="span6">
							<label>Passwort wiederholen*</label>
							<input type="password" name="password2" value="" placeholder="Eingegebenes Passwort wiederholen" class="input-xlarge" required>	
						</div>
					</div>
					<!-- START Secureimage Captcha -->
					<div class="row-fluid">
						<div class="row-fluid">
							<div class="span6">
								<label>Captcha Abfrage</label>
								<img  align="left" id="captcha1" src="securimage/securimage_show.php" alt="CAPTCHA Image" style="border: 1px solid #000; margin-right: 10px" />
								<object type="application/x-shockwave-flash" data="securimage/securimage_play.swf?audio_file=securimage/securimage_play.php&amp;bgcol=#f5f5f5&icon_file=securimage/images/audio_icon.png" width="32" height="32">
								  <param name="movie" value="securimage/securimage_play.swf?audio_file=/securimage/securimage_play.php&amp;bgColor1=#f5f5f5&icon_file=securimage/images/audio_icon.png" />
								</object>						
								<a href="#" onclick="document.getElementById('captcha1').src = 'securimage/securimage_show.php?sid=' + Math.random(); this.blur; return false" title="Neues Bild" style="border-style: none;">
									<img width="32" height="32" border="0" onclick="this.blur()" alt="Reload Image" src="securimage/images/refresh.png">
								</a>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span6">
								<label>Captcha eingeben*</label>
								<input type="text" name="captcha_code1" placeholder="Gebe hier das Captcha ein" class="input-xlarge" maxlength="8" required/>
							</div>
						</div>
					</div>
					<!-- END Secureimage Captcha -->
					<input type="hidden" name="form_register" value="1">
					<div>
					  <br>	
						<div class="form-actions">
							<button class="btn btn-primary btn-large" type="submit">Absenden</button>
							<button class="btn btn-large" type="reset">Abbrechen</button>
						</div>						  
					</div>
				</div>
			  </form>
			</div>
			<!-- Passwort Reset Content -->
			<div class="tab-pane fade" id="reset">
			  <form action="post.php" method="POST" >
				<div class="row-fluid">
					<div class="row-fluid">
						<div class="span6">
							<label>Email*</label>
							<input type="text" name="email" value="" placeholder="Gebe deine Email ein" class="input-xlarge" required>
						</div>
					</div>
					<!-- START Secureimage Captcha -->
					<div class="row-fluid">
						<div class="row-fluid">
							<div class="span6">
								<label>Captcha Abfrage</label>
								<img  align="left" id="captcha2" src="securimage/securimage_show.php" alt="CAPTCHA Image" style="border: 1px solid #000; margin-right: 10px" />
								<object type="application/x-shockwave-flash" data="securimage/securimage_play.swf?audio_file=securimage/securimage_play.php&amp;bgcol=#f5f5f5&icon_file=securimage/images/audio_icon.png" width="32" height="32">
								  <param name="movie" value="securimage/securimage_play.swf?audio_file=/securimage/securimage_play.php&amp;bgColor1=#f5f5f5&icon_file=securimage/images/audio_icon.png" />
								</object>						
								<a href="#" onclick="document.getElementById('captcha2').src = 'securimage/securimage_show.php?sid=' + Math.random(); this.blur; return false" title="Neues Bild" style="border-style: none;">
									<img width="32" height="32" border="0" onclick="this.blur()" alt="Reload Image" src="securimage/images/refresh.png">
								</a>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span6">
								<label>Captcha eingeben*</label>
								<input type="text" name="captcha_code2" class="input-xlarge" placeholder="Gebe hier das Captcha ein" maxlength="8" required/>
							</div>
						</div>
					</div>
					<!-- END Secureimage Captcha -->
					<input type="hidden" name="form_forgot" value="1">
					<div>
					  <br>	
						<div class="form-actions">
							<button class="btn btn-primary btn-large" type="submit">Absenden</button>
							<button class="btn btn-large" type="reset">Abbrechen</button>
						</div>		
					</div>
				</div>
			  </form>
			</div>	
			<small>mit * gekennzeichnete Felder sind Pflichtfelder</small>
		</div>
	  </div>
	</div>	
<hr>

