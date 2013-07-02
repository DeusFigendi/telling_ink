<?php
	//You are editing navi1 this contains main navigation like "contact" or "links" or something...
	
	
	$content['nav'] = '
			<ul class="handwritten">
				<li><a href="./index.php">Seite 1</a></li>
				<li><a href="./index.php?action=books">Bücher</a></li>
				<li><a href="./index.php?action=blogroll">Andere Vorlesepodcasts</a></li>
				<li><a href="./index.php?action=contact">Über dieses Projekt</a></li>
				<li><a href="./feed.php">RSS-Feed</a></li>
			</ul>
		';
	if (!array_key_exists('foot',$content)) { $content['foot'] = ''; }
	$content['foot'] .= '
			<a href="./?action=privacy">Datenschutzerklärung</a>
			<a href="./?action=contact">Impressum &amp; Rechtliches</a>
			';
?>
