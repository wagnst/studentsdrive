<?php

include('sessiontest.inc.php');
include('includes.inc.php');
echo'<title>'.$main_site_title.'Postings</title>';
$aktion=addslashes($_GET["aktion"]);
if(isset($_POST["text"]))
  {
  $text=addslashes($_POST["text"]);
  $bearbeiten=addslashes($_POST["bearbeiten"]);
  if ($bearbeiten=='j')
    {
	$news_id=$_POST['news_id'];
	$abfrage="SELECT * FROM news WHERE news_id=".$news_id;
    $ergebnis=mysql_query($abfrage);
    $row=mysql_fetch_object($ergebnis);
    if ($row->autor_id==$s_user_id)
		if($text <> "") {
			$abfrage = "UPDATE news SET text='$text' WHERE news_id=" .$news_id;
		}
	else
	  $abfrage='';
	}
  else
	if($text <> "") {
		if ($_POST['untereintrag_zu']<>0)
			$untereintrag_zu = $_POST['untereintrag_zu'];
		else
			$untereintrag_zu = 0;
		$abfrage = "INSERT INTO news (autor_id, text, erstellungsdatum, untereintrag_zu) VALUES ('$s_user_id', '$text', CURRENT_TIMESTAMP, '$untereintrag_zu')";
	}
  mysql_query($abfrage);
  //Hole die ID um dort hin zu headern...

  header('Location: ./framework.php?id=start');
  }

elseif($aktion=="bearbeiten")
{

?>


<?php
$abfrage="SELECT * FROM news WHERE news_id=".$_GET['newsid'];
$ergebnis=mysql_query($abfrage);
$row=mysql_fetch_object($ergebnis);

if ($row->autor_id==$s_user_id)
{
?>
<h2>Eintrag bearbeiten</h2>
<form accept-charset="UTF-8" action="news.php" method="POST">
	<textarea maxlength="255" class="span6" id="new_message" name="text" placeholder="Was gibts neues?" rows="2"><?php echo $row->text ?></textarea>
	<input type="hidden" name="bearbeiten" value="j"/>
	<?php
		echo '<input type="hidden" name="news_id" value="'.$_GET['newsid'].'"/>';
	?>
	<button class="btn btn-primary btn-large" type="submit">Absenden</button>
</form>

<?php
}
else
  header("Location: ./framework.php?id=index");

}

elseif($aktion=="zeigen")
{
	?>
	<script>
		function openPopup(url) {
		 window.open(url, "popup_id", "scrollbars,resizable,width=300,height=400");
		}
	</script>
	<?php

	//neueste 5 Mitteilungen anzeigen
	$abfrage="SELECT news_id,autor_id,facebook_id,text,vorname,erstellungsdatum,title FROM news,local_users WHERE user_id=autor_id AND untereintrag_zu=0 ORDER BY erstellungsdatum DESC";
	$ergebnis=mysql_query($abfrage);
	$anzahl_beitraege = mysql_num_rows($ergebnis);
	$anzahl_seiten=round(($anzahl_beitraege+4.5)/10);//anzahl seiten bestimmen
	if (isset($_GET['page']))
	  $page=$_GET['page'];
	else
	  $page=1;

	//show error if outside bounds
	if(($_GET['page'] < 1) OR ($_GET['page'] > $anzahl_seiten))
	{
		echo showError404();
	}
	else
	{
		echo '<h3>Timeline - Was gibts neues?</h3>

		<form accept-charset="UTF-8" action="news.php" method="POST">
			<textarea maxlength="255" class="span6" id="new_message" name="text" placeholder="Was gibts neues?" rows="2"></textarea>
			<input type="hidden" name="bearbeiten" value="n"/>
			<button class="btn btn-primary btn-large" type="submit">Absenden</button>
		</form>
		';

		$forumnav='<div class="pager"><ul>';
		//wenn mittendrin
		if(($page > 1) AND ($page < $anzahl_seiten)){
			$forumnav.= '<li><a href="framework.php?id=news&amp;aktion=zeigen&page='.($page-1).'">Zurück</a></li>';
			$forumnav.= '<li><a href="framework.php?id=news&amp;aktion=zeigen&amp;page='.($page+1).'">Vorwärts</a></li>';
		}
		//wenn nur vorwärts
		elseif($page <= 1)
			$forumnav.= '<li><a href="framework.php?id=news&amp;aktion=zeigen&amp;page='.($page+1).'">Vorwärts</a></li>';
		//wenn nur zurück
		elseif($page >= $anzahl_seiten)
			$forumnav.= '<li><a href="framework.php?id=news&amp;aktion=zeigen&amp;page='.($page-1).'">Zurück</a></li>';
		$forumnav.='</ul></div>';

		$abfrage1="SELECT news_id,autor_id,facebook_id,text,vorname,erstellungsdatum,title FROM news,local_users WHERE user_id=autor_id AND untereintrag_zu=0 ORDER BY erstellungsdatum DESC LIMIT ".($page*10-10).",10";
		$ergebnis1=mysql_query($abfrage1);


		while($row=mysql_fetch_object($ergebnis1))
		{
			$news_id=$row->news_id;
			//get untereinträge zu Eintrag
			$abfrage2="SELECT * FROM news WHERE untereintrag_zu=$news_id";
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
		<hr>';
	}
}
elseif($aktion=="detail")
{
	if ((isset($_GET['newsid'])) AND (preg_match("/^\d*$/", $_GET['newsid'])) AND ($_GET['newsid']<>""))
	{
		$news_id=$_GET['newsid'];

		function subeintraege($newsid, &$ausgabe)
		{
			include('sessiontest.inc.php');
		    $abfrage = "SELECT * FROM news WHERE news_id=$newsid";
		    $ergebnis = mysql_query($abfrage);
			$row = mysql_fetch_object($ergebnis);
			if ($row->untereintrag_zu!==0)
			  {
			    $abfrage = "SELECT * FROM news WHERE untereintrag_zu=$newsid ORDER BY erstellungsdatum ASC";
				$ergebnis = mysql_query($abfrage);
				while($row = mysql_fetch_object($ergebnis))
				  {
				    $ausgabe .= '<div class="row-fluid">
				    				<div class="row-fluid">
					    				<div class="span2">
					    				</div>
										<div class="span1">
											<a href="./framework.php?id=profile&amp;aktion=zeigen&amp;user='.$row->autor_id.'" >
												<img src="'.getProfilePicture($row->autor_id, 50, 50).'" alt="" class="img-polaroid img-rounded" width="50" height="50">
											</a>
										</div>
										<div class="span8 thumbnail">
												'.$row->text.'
										</div>
									</div>
									<div class="row-fluid">
					    				<div class="span3">
					    				</div>
										<div class="span7">
											<p>
											  <i class="icon-user"></i> von <a href="./framework.php?id=profile&amp;aktion=zeigen&amp;user='.$row->autor_id.'">'.getFullUserName($row->autor_id).'</a>
											  | <i class="icon-calendar"></i> '.humanTime($row->erstellungsdatum).'';
												if ($s_user_id==$row->autor_id)
													$ausgabe .= '| <i class="icon-pencil"></i> <a href="./framework.php?id=news&amp;aktion=bearbeiten&amp;newsid='.$row->news_id.'"> editieren</a>';
											$ausgabe .= '
											</p>
										</div>
									</div>
								</div>
								<br>';
				    subeintraege($row->news_id 	, $ausgabe);
			      }
			   }
		}

		$abfrage = "SELECT * FROM news WHERE news_id=$news_id";
		$ergebnis=mysql_query($abfrage);
		$anzahl_news = mysql_num_rows($ergebnis);
		if($anzahl_news>0)
		{
			$row = mysql_fetch_object($ergebnis);
			?>
			<script>
				function openPopup() {
				 window.open("http://www.facebook.com/share.php?u=<?php echo $full_site_url ?>framework.php?id=news&aktion=detail&newsid=<?php echo $row->news_id; ?>", "popup_id", "scrollbars,resizable,width=500,height=400");
				}
			</script>
			<?php

			//shorten text
			$eintragstext = strip_tags(smart_wordwrap($row->text, 10));
			$inhalt = '
			<div class="row">
			  <div class="span8">
				<div class="row">
				  <div class="span8">
					<h4><strong><a href="#">'.$row->title.'</a></strong></h4>
				  </div>
				</div>
				<div class="row">
				  <div class="span1">
					<a href="./framework.php?id=profile&amp;aktion=zeigen&amp;user='.$row->autor_id.'" >
						<img src="'.getProfilePicture($row->autor_id, 100, 100).'" alt="" class="img-polaroid img-rounded">
					</a>
				  </div>
				  <div class="span5">
					<div class="well">
						'.$eintragstext.'
					</div>
					<div class="span5">
						<p>
						  <i class="icon-user"></i> von <a href="./framework.php?id=profile&amp;aktion=zeigen&amp;user='.$row->autor_id.'">'.getFullUserName($row->autor_id).'</a>
						  | <i class="icon-calendar"></i> '.humanTime($row->erstellungsdatum).'
						  | <i class="icon-share"></i> <a href="#" onclick="openPopup();"> Teilen </a>';
							if ($s_user_id==$row->autor_id)
								$inhalt .= '| <i class="icon-pencil"></i> <a href="./framework.php?id=news&amp;aktion=bearbeiten&amp;newsid='.$row->news_id.'"> editieren</a>';
						$inhalt .= '
						</p>
					</div>
				  </div>
				</div>
			  </div>
			</div>
			<hr>';
			subeintraege($news_id, $inhalt);
			$inhalt .= '<hr><h4>Neuen Untereintrag erstellen</h4>
			<form accept-charset="UTF-8" action="news.php" method="POST">
				<textarea maxlength="255" class="span6" id="new_message" name="text" placeholder="Was möchtest du dazu sagen?" rows="2"></textarea>
				<input type="hidden" name="bearbeiten" value="n"/>
				<input type="hidden" name="untereintrag_zu" value="'.$news_id.'"/>
				<button class="btn btn-primary" type="submit">Absenden</button>
			</form>';
			echo $inhalt;
		}
		else
		{
			echo showError404();
		}
	}
	else
	{
		echo showError404();
	}
}
else
{
	echo showError404();
}

?>

