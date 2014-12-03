<?php
ob_start();  //Startet Ausgabepuffer
require_once('includes.inc.php');
require_once('usefulfunctions.inc.php');

if (isset($_GET['id'])){
	$site_url = $_GET['id'];
	}
else {
	$site_url = 'index';
	}
$url_filename="startseite.php";
switch($site_url){
	case "login":
		$url_filename="login.php";
		break;
	case "logout":
		include('logout.php');
		//$url_filename="logout.php";
		break;		
	case "start":
		require_once('sessiontest.inc.php');
		$url_filename="startseite.php";
		break;
	case "news":
		require_once('sessiontest.inc.php');
		$url_filename="news.php";
		break;		
	case "profile":
		require_once('sessiontest.inc.php');
		$url_filename="profile.php";
		break;
	//for email codeactivation used (call framework.php?id=activate&code=xxx)
	case "activate":
		$url_filename="post.php";
		break;		
	case "info":
		$url_filename="info.php";
		break;			
}

?>
<!DOCTYPE html>
<!--
/*************************************/
/***   STUDENTSDRIVE FRAMEWORK     ***/
/*************************************/
/*
 * Steffen Wagner
 * http://www.steffenwagner.com
 *
 ***************** Author ******************
 * Copyright 2013, Steffen Wagner
 * mail [AT] steffenwagner [dot] com
 * http://www.steffenwagner.com
 *******************************************
 */
 -->
<html lang="en">
<head>
<link rel="icon" type="image/png" href="img/favicon.ico">
<meta charset="utf-8">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="StudentsDrive - Allein zur Uni? Laaaangweilig!">
<meta name="author" content="Steffen Wagner">
<!-- CSS FILES -->
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/bootstrap-responsive.min.css" rel="stylesheet">
<link href="css/datepicker.css" rel="stylesheet">
<link href="css/font-awesome.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
<link href="css/jquery.Jcrop.min.css" rel="stylesheet" type="text/css" />
<!--[if IE 7]>
  <link rel="stylesheet" href="css/font-awesome-ie7.css">
<![endif]-->
<!-- JS FILES -->
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="js/bootswatch.js"></script>
<!-- Image upload -->    
<script src="js/jquery.Jcrop.min.js"></script>
<script src="js/script.js"></script>
<!-- Google API -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
<script type="text/javascript">
		function autocompleteCity() {
			var options = {
				types: ['(cities)'],
				components: {country: 'de'}
			};
			var input = document.getElementById('googleAutoCompleteCity');
			var autocomplete = new google.maps.places.Autocomplete(input, options);
	   }
		function autocompleteEducation() {
			var options = {
				types: ['establishment']
			};
			var input = document.getElementById('googleAutoCompleteEducation');
			var autocomplete = new google.maps.places.Autocomplete(input, options);
	   }	   
	   google.maps.event.addDomListener(window, 'load', autocompleteEducation);
	   google.maps.event.addDomListener(window, 'load', autocompleteCity);
	 
</script>
<script src="chilistats/counter.php?ref=' + escape(document.referrer) + '" type="text/javascript"></script>
</head>
<body class="main_body">
<?php include('topmenu.inc.php'); ?>
	<div class="wrap">		
			<div class="container">
				<div class="well">
				<?php			
					include($url_filename);	
				?>
				</div>		
			</div>
		<?php include ('footer.inc.php'); ?>	
	</div>
</body>

</html>
<?php
ob_end_flush();  //Beendet Ausgabepuffer und Ausgabe des Inhaltes
?>