<?php	

	$comment['ttype']	= 'meta';
	$comment['target']  = 'contact';
	$comment['ttext']   = 'diese Webseite verborgen';
	$comment['hidden']  = 1;
	$content['title']   = "Impressum";
	
		
	$content['episode'][0] = '
	<h1>Über diese Seite</h1>
		<h2>Was soll das denn hier?</h2>
		<p>Dies ist ein Hörbuch-Podcast</p>
		<p>Etwas länger: Schon seit Langem habe ich - von Zeit zu Zeit -
		kürzere Texte eingesprochen und aufgenommen, weil es mir Spaß
		macht. Nie aber habe ich sie wirklich veröffentlicht, sondern
		irgendwo hochgeladen und unter Freunden hier und dort gezeigt.</p>
		<p>Jetzt im Februar 2013 habe ich begonnen Alice\'s Abenteuer im
		Wunderland vorzulesen und auf Soundcloud zu publizieren, da es
		aber bereits eine ganze Reihe von Vorlese-Podcasts gibt dachte ich
		es wäre an der Zeit und auch angebracht eine Webseite und ein
		RSS-Feed für diese Audio-Produktionen zu erstellen.</p>
		<p>Das ist diese Webseite</p>
		<p>Hier lese ich freie Texte vor, das bedeutete Texte, bei denen
		ich keine Rechte explizit erwerben muss. Alice\' ist gemeinfrei,
		aber ich habe auch schon viele Gedichte unter CC-Lizenz oder GPL
		vorgelesen.</p>
		<p>Viel Spaß beim Stöbern :)</p>
		<h2>Rechtliches</h2>
		<p>Die allermeisten Inhalte dieses Webangebots unterliegen freien
		Lizenzen, das trifft insbesondere auf die Audio-Daten zu.</p>
		<p>Über dies werden auf dieser Webseite Werke anderer verwendet,
		denen ich Dank, Respekt und Erwähnung schuldig bin:</p>
		<ul>
			<li><strong>Das Logo</strong> oben Beispielsweise wurde von
			<a href="https://pod.geraspora.de/u/theradialactive">Dennis</a>
			erstellt, der mir liebenswerter Weise auch die Quellen der
			Grafik zur Verfügung stellte, woraufhin ich es noch einmal
			modifizierte.</li>
			<li><strong>Die Hintergrundgrafik</strong> habe ich ebenfalls
			modifiziert, sie stammt ursprünglich von
			<a href="http://www.bashcorpo.dk">bashcorpo</a> und ist lizensiert
			unter <a href="http://creativecommons.org/licenses/by/3.0/">
			Creative Commons Attribution 3.0 License</a>.
			</li>
			<li><strong>Die Schriftart</strong>, die ich für manche Links
			verwende heißt <a href="http://www.dafont.com/denise.font?l[]=10">
			Denise Handwriting</a> und wurde von <a href="http://budeni.com/">
			Denise Busch</a> erstellt.</li>
		</ul>';
	$content['episode'][1] = '
	<h2>Impressum</h2>
	<div id="hcard-Kai-Uwe-Kramer" class="vcard">
		<img style="float:left; margin-right:4px" src="http://deusf.ara.uberspace.de/audiobook/images/p_DeusFigendi.jpg" alt="photo of " class="photo"/>
		<span class="fn">Kai-Uwe Kramer</span>
		<a class="email" href="mailto:deusfigendi@dnd-gate.de">deusfigendi@dnd-gate.de</a>
		<div class="adr">
			<div class="street-address">Immenstraße 14</div>
			<span class="locality">Bad Salzuflen</span>
			, 
			<span class="postal-code">32108</span>

			<span class="country-name">D</span>

		</div>
		<a class="url" href="xmpp:deusfigendi@dnd-gate.de">Jabber</a>
		<p style="font-size:smaller;">This <a href="http://microformats.org/wiki/hcard">hCard</a> created with the <a href="http://microformats.org/code/hcard/creator">hCard creator</a>.</p>
	</div>';

//	<h3></h3>
//	Kai-Uwe Kramer
//	alias Deus Figendi
//	Immenstraße 14
//	32108 Bad Salzuflen
	
//	Vorläufig - bis dieses Angebot einen Namen und eine Domain hat - erreichbar unter deusfigendi@dnd-gate.de
	
//	Kontaktformular oder Telefonnummer

	$content['episode'][1] .= '
	
	<p>Inhaltlich verantwortlich ist Kai-Uwe Kramer alias Deus Figendi, Adresse wie oben.</p>
	<p>Wenn Ihnen rechtliche Probleme mit Inhalten in diesem Webangebot
	auffallen, wäre ich durchaus zutiefst verbunden, wenn Sie einfach
	unbürokratischen Kontakt zu mir aufnehmen, dann betrachte ich Ihr Anliegen
	und werde versuchen Ihren Befindlichkeiten Rechnung zu tragen
	(Notice and Takedown -Prinzip)</p>';
	
	
	include('co_comments.php');
	$content['episode'][0] .= $comment['html'];
		
?>
