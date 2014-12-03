<?php

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

include('sessiontest.inc.php');

?>
<title><?php echo $main_site_title.'Home'; ?></title>
<div class="row-fluid">
<h3>Die nächsten anstehenden Fahrten...</h3>
<table class="table table-hover">
  <thead>
	<tr>
	  <th><i class="icon-bell"></i> Abfahrt</th>
	  <th><i class="icon-map-marker"></i> Von</th>
	  <th><i class="icon-road"></i> Nach</th>
	  <th><i class="icon-user"></i> Mitglied</th>
	  <th><i class="icon-envelope"></i> Kontakt</th>
	</tr>
  </thead>
  <tbody>
	<tr>
	  <td><span class="badge">1:00 Uhr</span></td>
	  <td>Landau</td>
	  <td>Mainz</td>
	  <td>Steffen</td>
	  <td><button href="#myModal" role="button" class="btn btn-success" data-toggle="modal">Anschreiben</button></td>

	</tr>
	<tr>
	  <td><span class="badge">1:00 Uhr</span></td>
	  <td>Berlin</td>
	  <td>Köln</td>
	  <td>Fritz</td>
	  <td><button href="#myModal" role="button" class="btn btn-success" data-toggle="modal">Anschreiben</button></td>
	</tr>
	<tr>
	  <td><span class="badge">1:00 Uhr</span></td>
	  <td>Hannover</td>
	  <td>Buxdehude</td>
	  <td>Kalmund</td>
	  <td><button href="#myModal" role="button" class="btn btn-success" data-toggle="modal">Anschreiben</button></td>
	</tr>
  </tbody>
</table>
<button class="btn btn-large " type="button">Zeige alle Fahrten</button>
</div>
<hr>
<?php
	//neueste 5 Mitteilungen anzeigen
	$abfrage="SELECT news_id,autor_id,text,vorname,erstellungsdatum,title FROM news,local_users WHERE user_id=autor_id AND untereintrag_zu=0 ORDER BY erstellungsdatum DESC";
	$ergebnis=mysql_query($abfrage);
	$anzahl_beitraege = mysql_num_rows($ergebnis);
	$anzahl_seiten=round(($anzahl_beitraege+4.5)/10);//anzahl seiten bestimmen
	if (isset($_GET['page']))
	  $page=$_GET['page'];
	else
	  $page=1;

	echo '<h3>Timeline - Was gibts neues?</h3>

	<form accept-charset="UTF-8" action="news.php" method="POST">
		<textarea maxlength="255" class="span6" id="new_message" name="text" placeholder="Was gibts neues?" rows="2"></textarea>
		<input type="hidden" name="bearbeiten" value="n"/>
		<button class="btn btn-primary btn-large" type="submit">Absenden</button>
	</form>
	';

	$forumnav='<div class="pager"><ul>';
	$forumnav.= '<li><a href="framework.php?id=news&amp;aktion=zeigen&amp;page='.($page+1).'">Vorwärts</a></li>';
	$forumnav.='</ul></div>';

	$abfrage1="SELECT news_id,autor_id,text,vorname,erstellungsdatum,title FROM news,local_users WHERE user_id=autor_id AND untereintrag_zu=0 ORDER BY erstellungsdatum DESC LIMIT ".($page*10-10).",10";
	$ergebnis1=mysql_query($abfrage1);


	while($row=mysql_fetch_object($ergebnis1))
	{
		$news_id=$row->news_id;
		//get untereinträge zu Eintrag
		$abfrage2="SELECT * FROM news WHERE untereintrag_zu='$news_id' ORDER BY erstellungsdatum DESC";
		$ergebnis2=mysql_query($abfrage2);
		$anzahl=mysql_num_rows($ergebnis2);

		?>
		<script>
			function openPopup() {
			 window.open("http://www.facebook.com/share.php?u=<?php echo $full_site_url ?>framework.php?id=news&aktion=detail&newsid=<?php echo $row->news_id; ?>", "popup_id", "scrollbars,resizable,width=500,height=400");
			}
		</script>
		<?php

		//shorten text
		$eintragstext = strip_tags(smart_wordwrap($row->text, 10));
		echo '
		<div class="row">
		  <div class="span8">
			<div class="row">
			  <div class="span8">
				<h4><strong><a href="#">'.$row->title.'</a></strong></h4>
			  </div>
			</div>
			<div class="row">
			  <div class="span2">
				<a href="./framework.php?id=profile&amp;aktion=zeigen&amp;user='.$row->autor_id.'" class="thumbnail">
					<img src="'.getProfilePicture($row->autor_id, 150, 150).'" alt="">
				</a>
			  </div>
			  <div class="span5">
				<p>
					'.$eintragstext.'
				</p>
			  </div>
			</div>
			<div class="row">
			  <div class="span8">
				<p></p>
				<p>
				  <i class="icon-user"></i> von <a href="./framework.php?id=profile&amp;aktion=zeigen&amp;user='.$row->autor_id.'">'.$row->vorname.'</a>
				  | <i class="icon-calendar"></i> '.humanTime($row->erstellungsdatum).'
				  | <i class="icon-comment"></i> <a href="./framework.php?id=news&amp;aktion=detail&amp;newsid='.$row->news_id.'">'.$anzahl.' Kommentare</a>
				  | <i class="icon-share"></i> <a href="#" onclick="openPopup();"> Teilen </a>';
					if ($s_user_id==$row->autor_id)
						echo '| <i class="icon-pencil"></i> <a href="./framework.php?id=news&amp;aktion=bearbeiten&amp;newsid='.$row->news_id.'"> editieren</a>';
					echo'
				</p>
			  </div>
			</div>
		  </div>
		</div>
		<hr>';
	}
	echo $forumnav;
	echo '
	<hr>
    <!-- Modal -->
    <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Kontakt für Fahrt nach XyZ</h3>
			</div>
			<div class="modal-body">
				<p>Hier kommt dann Feld und Bildchen und blah rein</p>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Abbrechen</button>
				<button class="btn btn-primary">Absenden</button>
			</div>
	</div>';


?>
