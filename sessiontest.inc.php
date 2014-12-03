<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

if(session_id() == '')
 session_start();// NEVER FORGET TO START THE SESSION!!!

$inactive = 900; // 600 = 10 min

if(isset($_SESSION['timeout']) ) {
  $session_life = time() - $_SESSION['timeout'];
  if($session_life > $inactive) { 
    header("Location: framework.php?id=logout"); 
  }else
  {
 	// session ok
	if ((isset($_SESSION["logged_in"])) and ($_SESSION["logged_in"]==TRUE) and ($_SESSION["user_id"]<>""))//überprüft, ob Nutzer eingeloggt ist
	{
		$s_user_id=$_SESSION["user_id"];
		$s_user_facebook=$_SESSION["facebook_id"];
		$s_user_logouturl=$_SESSION["logout_url"];
			
		require_once('usefulfunctions.inc.php');
		//check ob user sich das erste mal angemeldet
		//wenn true -> weiterleitung zur Profilbearbeitungsseite
		$redirecturl="framework.php?id=profile&aktion=bearbeiten&msg=firsttime";
		$posturl="post.php";
		if((getActivationCodeStatus($s_user_id)=="first_login") AND (basename($_SERVER['REQUEST_URI'])<>$redirecturl) AND (basename($_SERVER['REQUEST_URI'])<>$posturl))
			header('Location: ./'.$redirecturl);
	}
	else//weiterleitung zur login-seite
	{
		session_unset();
		$_SESSION=array();
		$_SESSION["logged_in"]=FALSE;
		header('Location: ./framework.php?id=login');
	} 
  }
}
$_SESSION['timeout'] = time();
?>