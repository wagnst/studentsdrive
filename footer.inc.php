<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

if(session_id() == '')
 session_start();// NEVER FORGET TO START THE SESSION!!!
?>
<div id="footer">
<div class="container">	
<div class="row-fluid">
		<div class="span12">
			<div class="span2" style="width: 15%;">
				<ul class="unstyled">
					<li>StudentsDrive<li>
					<li><a href="framework.php?id=info&amp;sektion=about">Über uns</a></li>
					<li><a href="framework.php?id=info&amp;sektion=werbung">Werbung</a></li>
					<li><a href="framework.php?id=info&amp;sektion=statistik">Statistik</a></li>
				</ul>
			</div>
			<div class="span2" style="width: 15%;">
				<ul class="unstyled">
					<li>Hilfe<li>
					<li><a href="framework.php?id=info&amp;sektion=faq">FAQ</a></li>
					<li><a href="framework.php?id=info&amp;sektion=kontakt">Kontaktform</a></li>
					<li><a href="framework.php?id=info&amp;sektion=verifizierung">Verifizierung</a></li>
					<?php
						if ((isset($_SESSION["logged_in"])) AND!($_SESSION["logged_in"]))
						{
							echo '<li><a href="framework.php?id=login#reset">Passwort?</a></li>';
						}
					?>
				</ul>
			</div>	
			<div class="span2" style="width: 15%;">
				<ul class="unstyled">
					<li>Social<li>
					<li><a href="http://facebook.de">Facebook</a></li>
					<li><a href="http://twitter.de">Twitter</a></li>
				</ul>
			</div>						
			<?php
				if ((isset($_SESSION["logged_in"])) AND !($_SESSION["logged_in"]))
				{	
					echo '
					<div class="span2" style="width: 15%;">
						<ul class="unstyled">
							<li>Start<li>
							<li><a href="framework.php?id=start">Einloggen</a></li>				
							<li><a href="framework.php?id=activate&amp;code">Codeeingabe</a></li>					
						</ul>
					</div>';
				}
				if ((isset($_SESSION["logged_in"])) AND!($_SESSION["logged_in"]))
				{
					echo '					
						<div class="span2" style="width: 15%;">
							<ul class="unstyled">
								<li>Fahrten<li>
								<li><a href="framework.php?id=fahrten&amp;sektion=biete">Bieten</a></li>	
								<li><a href="framework.php?id=fahrten&amp;sektion=suche">Suchen</a></li>						
							</ul>
						</div>';		
				}
			?>
		</div>
	</div>
	<hr>
	<div class="row-fluid">
		<div class="span12">
			<div class="span8">
				<a href="framework.php?id=info&amp;sektion=richtlinien">Richtlinien</a>    
				<a href="framework.php?id=info&amp;sektion=datenschutz">Datenschutz</a>  
			</div>
			<div class="span4">
				<p class="muted pull-right">© 2013 <a href="http://steffenwagner.com">Steffen Wagner</a><br><small>All rights reserved.</small></p>
			</div>
		</div>
	</div>
</div>
</div>