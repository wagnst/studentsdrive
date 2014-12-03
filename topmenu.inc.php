<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

//uncomment for testing only...
//if ($_SESSION["logged_in"])
//	echo "<br>TRUE";
//else
//	echo "<br>FALSE";

if(session_id() == '')
 session_start();// NEVER FORGET TO START THE SESSION!!!
 
?>

 <div class="navbar navbar-fixed-top">
   <div class="navbar-inner">
     <div class="container">
       <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
         <span class="icon-bar"></span>
         <span class="icon-bar"></span>
         <span class="icon-bar"></span>
       </a>
	   <a class="brand" href="./">StudentsDrive<small> - Drive Smart</small></a>
       <div class="nav-collapse collapse" id="main-menu">
        <ul class="nav pull-right" id="main-menu-left">
		  <li class="divider-vertical"></li>
          <li><a href="framework.php?id=start"><i class="icon-home"></i> Home</a></li>
			<?php
				if ((isset($_SESSION["logged_in"])) AND ($_SESSION["logged_in"]))
				{
					echo '
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-road"></i> Fahrten
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li><a href="framework.php?id=fahrten&amp;sektion=biete"><i class="icon-plus"></i> Biete</a></li>
								<li><a href="framework.php?id=fahrten&amp;sektion=suche"><i class="icon-search"></i> Suche</a></li>
							</ul>
						</li>	
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-user"></i> Profil
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li><a href="framework.php?id=profile&amp;aktion=zeigen"><i class="icon-eye-open"></i> Anzeigen</a></li>
								<li><a href="framework.php?id=profile&amp;aktion=bearbeiten"><i class="icon-pencil"></i> Bearbeiten</a></li>
							</ul>
						</li>';
				}
			?>
			<li class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-info-sign"></i> Info
					<b class="caret"></b>
				</a>
				<ul class="dropdown-menu">	
					<li><a href="framework.php?id=info&amp;sektion=about">Über uns</a></li>
					<li><a href="framework.php?id=info&amp;sektion=kontakt">Kontakt</a></li>
					<li><a href="framework.php?id=info&amp;sektion=faq">FAQ</a></li>
					<li class="divider"></li>
					<li><a href="framework.php?id=info&amp;sektion=richtlinien">Richtlinien</a></li>
					<li><a href="framework.php?id=info&amp;sektion=datenschutz">Datenschutz</a></li>
					<li><a href="framework.php?id=info&amp;sektion=impressum">Impressum</a></li>
					<li class="divider"></li>
					<?php
						if ((isset($_SESSION["logged_in"])) AND($_SESSION["logged_in"]))			
							echo '<li class=""><a title="Ausloggen nicht moeglich, da du noch bei Facebook angemeldet bist. Wenn du dich aus Facebook ausloggst, wirst du hier ebenfalls ausgeloggt." href="'.$_SESSION["logout_url"].'">Ausloggen</a></li>';
						else
							echo '<li><a href="./framework.php?id=login" title="Login" data-content="Einloggen">Einloggen</a></li>';
					?>			
				</ul>
			</li>		
        </ul>	
       </div>
     </div>
   </div>
 </div>		
 