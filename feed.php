<?php
	error_reporting(E_ALL);
	ini_set ('display_errors', true);







	require_once('db_connect.php');	
	require_once('ut_functions.php');	
	require_once('db_functions.php');	
	

	
	if(!isset($_REQUEST['format'])) {
		//no format is set, so lets check beloved mimetypes :D
		//and maybe the user agent (kinda scoring?)
		
		##possible formats:
		$format_score = array('plain' => 0,'html' => 0.01, 'ogg' => 0.03, 'mp3' => 0.02);
		
		if (isset($_SERVER['HTTP_ACCEPT'])) {
			$accepted_mimes = explode(",",$_SERVER['HTTP_ACCEPT']);
		} else {
			$accepted_mimes = Array();
		}
		foreach ($accepted_mimes as $accept_line) {
			$accept_entry = explode(";",$accept_line);
			if (count($accept_entry) < 2) { $accept_entry[] = 'q=1'; }
			$accept_entry['mimetype'] = trim($accept_entry[0]);
			$accept_entry['q'] = explode("=",$accept_entry[1]);
			if (count($accept_entry['q']) < 2) { $accept_entry['q'] = array('q',0); }
			$accept_entry['q'] = (float)$accept_entry['q'][1];			
			if       ($accept_entry['mimetype'] == 'text/html'            ) { $format_score['html'] += $accept_entry['q'];
			} elseif ($accept_entry['mimetype'] == 'application/xhtml'    ) { $format_score['html'] += $accept_entry['q'];
			} elseif ($accept_entry['mimetype'] == 'application/xhtml+xml') { $format_score['html'] += $accept_entry['q'];
			} elseif ($accept_entry['mimetype'] == 'audio/ogg'            ) { $format_score['ogg']  += $accept_entry['q'];
			} elseif ($accept_entry['mimetype'] == 'audio/vorbis'         ) { $format_score['ogg']  += $accept_entry['q'];
			} elseif ($accept_entry['mimetype'] == 'audio/mpeg'           ) { $format_score['mp3']  += $accept_entry['q'];
			} elseif ($accept_entry['mimetype'] == 'text/plain'           ) { $format_score['plain']+= $accept_entry['q'];
			}
		}
		
		
		
		
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Mozilla")!==FALSE    ) { $format_score['html'] += 0.2; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Firefox")!==FALSE    ) { $format_score['mp3']  -= 0.2; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Firefox")!==FALSE    ) { $format_score['html'] += 0.5; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Firefox")!==FALSE    ) { $format_score['ogg']  += 0.2; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"AppleWebKit")!==FALSE) { $format_score['ogg']  -= 0.1; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"AppleWebKit")!==FALSE) { $format_score['mp3']  += 0.2; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"AppleWebKit")!==FALSE) { $format_score['html'] += 0.2; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Chromium")!==FALSE   ) { $format_score['html'] += 0.5; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Chromium")!==FALSE   ) { $format_score['ogg']  += 0.2; }
		
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Chrome")!==FALSE     ) { $format_score['html'] += 0.5; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Chrome")!==FALSE     ) { $format_score['ogg']  += 0.2; }
		
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Safari")!==FALSE     ) { $format_score['html'] += 0.5; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Safari")!==FALSE     ) { $format_score['mp3']  += 0.2; }
		
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Opera")!==FALSE      ) { $format_score['mp3']  -= 0.2; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Opera")!==FALSE      ) { $format_score['html'] += 0.5; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Opera")!==FALSE      ) { $format_score['ogg']  += 0.2; }
		
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Wget")!==FALSE       ) { $format_score['ogg']  += 0.5; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Wget")!==FALSE       ) { $format_score['mp3']  += 0.5; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Wget")!==FALSE       ) { $format_score['html'] -= 0.2; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Wget")!==FALSE       ) { $format_score['plain']+= 0.2; }
		
		if (strpos($_SERVER['HTTP_USER_AGENT'],"curl")!==FALSE       ) { $format_score['ogg']  += 0.5; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"curl")!==FALSE       ) { $format_score['mp3']  += 0.5; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"curl")!==FALSE       ) { $format_score['html'] -= 0.2; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"curl")!==FALSE       ) { $format_score['plain']+= 0.5; }
		
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Miro")!==FALSE       ) { $format_score['ogg']  += 0.5; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Miro")!==FALSE       ) { $format_score['mp3']  += 0.5; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Miro")!==FALSE       ) { $format_score['html'] -= 0.2; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Miro")!==FALSE       ) { $format_score['plain']-= 0.2; }		
		
		if (strpos($_SERVER['HTTP_USER_AGENT'],"VLC")!==FALSE        ) { $format_score['ogg']  += 0.5; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"VLC")!==FALSE        ) { $format_score['mp3']  += 0.5; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"VLC")!==FALSE        ) { $format_score['html'] -= 0.2; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"VLC")!==FALSE        ) { $format_score['plain']-= 0.2; }
		
		if (strpos($_SERVER['HTTP_USER_AGENT'],"FeedValidator")!==FALSE       ) { $format_score['ogg']  += 0.5; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"FeedValidator")!==FALSE       ) { $format_score['mp3']  += 0.5; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"FeedValidator")!==FALSE       ) { $format_score['html'] -= 0.2; }
		if (strpos($_SERVER['HTTP_USER_AGENT'],"FeedValidator")!==FALSE       ) { $format_score['plain']-= 0.2; }	
		
		
		
		
		/*
		$sql_query = "INSERT INTO `temp_log` (`content`) VALUES ('".mysql_real_escape_string($_SERVER['HTTP_ACCEPT'])."');";
		mysql_query($sql_query);
		$sql_query = "INSERT INTO `temp_log` (`content`) VALUES ('".mysql_real_escape_string($_SERVER['HTTP_USER_AGENT'])."');";
		mysql_query($sql_query);
		*/
		
		
		
		
		arsort($format_score);
		//var_dump($format_score);
		reset($format_score);
				
		$_REQUEST['format'] = key($format_score);
	}
	//echo "\n\n\n You'd get the feed in ".$_REQUEST['format'];
	
	//atm there is no usual mp3-file so mp3 means mp3_low
	if ($_REQUEST['format'] == "mp3") { $_REQUEST['format'] = "mp3_low"; }
	if ($_REQUEST['format'] == "low") { $_REQUEST['format'] = "mp3_low"; }
	
	if ($_REQUEST['format'] == "mp3_low" || $_REQUEST['format'] == "ogg" || $_REQUEST['format'] == "mp4" || $_REQUEST['format'] == "vorbis" || $_REQUEST['format'] == "aac") {
		//header("Content-Type: application/xml");
		header("Content-Type: application/rss+xml");
		$base_url = str_replace("feed.php","",$_SERVER["SCRIPT_URI"]);
		$format_string = $_REQUEST['format'];
		if ($_REQUEST['format'] == "ogg") { $format_string = "vorbis"; }
		if ($_REQUEST['format'] == "mp4") { $format_string = "aac"; }
		
		$sql_query = "SELECT eid,aid,title,album,track,summary,audiodate,payment,chapter FROM `episodes` ".($db_conntection['hidden']?" ":"WHERE `hidden` = '0'")." ORDER BY `audiodate` DESC LIMIT 30";
		$sql_result = mysql_query($sql_query);
		$episodes_array = array();
		
		echo ('<?xml version="1.0"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
   <channel>
      <title>Telling Ink</title>
      <atom:link rel="self"
                 href="'.$base_url.'feed.php?format='.$format_string.'"
                 type="application/atom+xml"
                 title="'.$_REQUEST['format'].' '.$format_string.' audio"/>
      <atom:link rel="alternate"
                 href="'.$base_url.'feed.php?format=vorbis"
                 type="application/atom+xml"
                 title="ogg/vorbis audio"/>
      <atom:link rel="alternate"
                 href="'.$base_url.'feed.php?format=mp3_low"
                 type="application/atom+xml"
                 title="mp3 audio (64k mono for low-bandwidth)"/>
      <atom:link rel="alternate"
                 href="'.$base_url.'feed.php?format=mp4"
                 type="application/atom+xml"
                 title="aac/mp4 audio"/>
      <link>'.$base_url.'</link>
      <!-- <description>Freie Texte vorgelesen</description> -->
      <description>Lausche der Tinte</description>
      <language>de-de</language>
      <pubDate>'.date(DATE_RSS).'</pubDate>
      <lastBuildDate>'.date(DATE_RSS).'</lastBuildDate>
      <docs>http://cyber.law.harvard.edu/rss/rss.html</docs>
      <generator>Generator has no name jet</generator>
      <managingEditor>deusfigendi@dnd-gate.de (Deus Figendi)</managingEditor>
      <webMaster>deusfigendi@dnd-gate.de (Deus Figendi)</webMaster>
      <image>
             <url>'.$base_url.'/images/banner4.gif</url>
             <title>Telling Ink</title>
             <link>'.$base_url.'</link>
      </image>');
		
		while ($item = mysql_fetch_object($sql_result)) {
			$this_episode = array();
			/*item-fields w/o any special namespace...
			 * title: Album_short+Track+Album+Title
			 * link: eid
			 * description: summary (+tags?)
			 * author: persons mail
			 * categoriy: Album?
			 * comments: eid
			 * enclosure: audiofile
			 * guid: auphonic-id
			 * pubDate: audiodate
			 */
			 $this_episode['title'] = strtoupper(abbr_text($item -> album,4)).fix_digits((int)$item -> track,3)." - ".htmlspecialchars($item -> album)." - ".htmlspecialchars($item -> title);
			 $this_episode['episodestitle'] = $item -> title;
			 $this_episode['booktitle'] = $item -> album;
			 $this_episode['link'] = $base_url."index.php"."?e=".(int)$item -> eid;
			 $this_episode['description'] = $item -> summary;
			 $this_episode['categoriy'] = preg_replace("/[^\w\s]/","", $item -> album);
			 $this_episode['comments'] = $this_episode['link'];
			 $this_episode['guid'] = $item -> aid;
			 $this_episode['pubDate'] = $item -> audiodate;
			 $this_episode['flattrble'] = (int)$item -> payment;
			 if (strlen($item -> chapter) > 1) {
				$this_episode['chapterfile'] = $item -> chapter;
			}
			 //still missing: author and enclosure
			 
			$sql_query = "SELECT arid FROM `artistsXepisodes` WHERE `eid` = '".(int)$item -> eid."' LIMIT 30";
			$pid_result = mysql_query($sql_query);
			$pidlist = array();
			while ($pid_row = mysql_fetch_object($pid_result)) { $pidlist[] = (int)$pid_row -> arid; }
			$sql_query = "SELECT uri FROM `socialmedia` WHERE (`target` = '".implode("' OR `target` = '",$pidlist)."') AND `type` = 'person' AND `service` = '2' LIMIT 30";
			$author_result = mysql_query($sql_query);
			if (mysql_num_rows($author_result)) {
				$author_row = mysql_fetch_object($author_result);
				$this_episode['author'] = $author_row -> uri;
			}
			$itemfile_array = audiofiles_get((int)$item -> eid,$format_string);
			
			if(!count($itemfile_array)) {
				$itemfile_array = audiofiles_get((int)$item -> eid);
			}
			$this_episode['enclosure'] = $itemfile_array[0];
			echo('

      <item>
         <title>'.htmlspecialchars($this_episode['title']).'</title>
         <link>'.$this_episode['link'].'</link>
         <atom:link rel="http://podlove.org/deep-link" href="'.$this_episode['link'].'#" />
         ');
         //echo (($this_episode['flattrble']?'<atom:link rel="payment" href="https://flattr.com/submit/auto?user_id=deusfigendi&amp;url='.rawurlencode($this_episode['link']).'&amp;title='.rawurlencode($this_episode['episodestitle']).'&amp;description='.rawurlencode("Lesung des Kapitels ".urldecode($this_episode['episodestitle'])." aus ".urldecode($this_episode['booktitle'])).'&amp;language=de_DE&amp;tags='.urldecode('audiobook,podcast,hörbuch,hoerbuch,freecontent,audio').'&amp;category=audio" type="text/html" />':'<!-- non-commercial no flattr -->'));
         //echo (($this_episode['flattrble']?'<atom:link rel="payment" href="https://flattr.com/submit/auto?user_id=deusfigendi&amp;url='.rawurlencode($this_episode['link']).'&amp;title='.rawurlencode($this_episode['episodestitle']).'&amp;description='.urlencode("Lesung des Kapitels ".urldecode($this_episode['episodestitle'])." aus ".urldecode($this_episode['booktitle'])).'&amp;language=de_DE&amp;tags='.urldecode('audiobook,podcast,hörbuch,hoerbuch,freecontent,audio').'&amp;category=audio" type="text/html" />':'<!-- non-commercial no flattr -->'));
         //echo (($this_episode['flattrble']?'<atom:link rel="payment" href="https://flattr.com/submit/auto?user_id=deusfigendi&amp;url='.rawurlencode($this_episode['link']).'&amp;title='.urlencode($this_episode['episodestitle']).'&amp;description='.rawurlencode("Lesung des Kapitels ".urldecode($this_episode['episodestitle'])." aus ".urldecode($this_episode['booktitle'])).'&amp;language=de_DE&amp;tags='.urldecode('audiobook,podcast,hörbuch,hoerbuch,freecontent,audio').'&amp;category=audio" type="text/html" />':'<!-- non-commercial no flattr -->'));
         //echo (($this_episode['flattrble']?'<atom:link rel="payment" href="https://flattr.com/submit/auto?user_id=deusfigendi&amp;url='.rawurlencode($this_episode['link']).'&amp;title='.urlencode($this_episode['episodestitle']).'&amp;description='.urlencode("Lesung des Kapitels ".urldecode($this_episode['episodestitle'])." aus ".urldecode($this_episode['booktitle'])).'&amp;language=de_DE&amp;tags='.urldecode('audiobook,podcast,hörbuch,hoerbuch,freecontent,audio').'&amp;category=audio" type="text/html" />':'<!-- non-commercial no flattr -->'));
         //echo (($this_episode['flattrble']?'<atom:link rel="payment" href="https://flattr.com/submit/auto?user_id=deusfigendi&amp;url='.urlencode($this_episode['link']).'&amp;title='.rawurlencode($this_episode['episodestitle']).'&amp;description='.rawurlencode("Lesung des Kapitels ".urldecode($this_episode['episodestitle'])." aus ".urldecode($this_episode['booktitle'])).'&amp;language=de_DE&amp;tags='.urldecode('audiobook,podcast,hörbuch,hoerbuch,freecontent,audio').'&amp;category=audio" type="text/html" />':'<!-- non-commercial no flattr -->'));
         //echo (($this_episode['flattrble']?'<atom:link rel="payment" href="https://flattr.com/submit/auto?user_id=deusfigendi&amp;url='.urlencode($this_episode['link']).'&amp;title='.rawurlencode($this_episode['episodestitle']).'&amp;description='.urlencode("Lesung des Kapitels ".urldecode($this_episode['episodestitle'])." aus ".urldecode($this_episode['booktitle'])).'&amp;language=de_DE&amp;tags='.urldecode('audiobook,podcast,hörbuch,hoerbuch,freecontent,audio').'&amp;category=audio" type="text/html" />':'<!-- non-commercial no flattr -->'));
         //echo (($this_episode['flattrble']?'<atom:link rel="payment" href="https://flattr.com/submit/auto?user_id=deusfigendi&amp;url='.urlencode($this_episode['link']).'&amp;title='.urlencode($this_episode['episodestitle']).'&amp;description='.rawurlencode("Lesung des Kapitels ".urldecode($this_episode['episodestitle'])." aus ".urldecode($this_episode['booktitle'])).'&amp;language=de_DE&amp;tags='.urldecode('audiobook,podcast,hörbuch,hoerbuch,freecontent,audio').'&amp;category=audio" type="text/html" />':'<!-- non-commercial no flattr -->'));
         //echo (($this_episode['flattrble']?'<atom:link rel="payment" href="https://flattr.com/submit/auto?user_id=deusfigendi&amp;url='.urlencode($this_episode['link']).'&amp;title='.urlencode($this_episode['episodestitle']).'&amp;description='.urlencode("Lesung des Kapitels ".urldecode($this_episode['episodestitle'])." aus ".urldecode($this_episode['booktitle'])).'&amp;language=de_DE&amp;tags='.urldecode('audiobook,podcast,hörbuch,hoerbuch,freecontent,audio').'&amp;category=audio" type="text/html" />':'<!-- non-commercial no flattr -->'));
         echo (($this_episode['flattrble']?'<atom:link rel="payment" href="https://flattr.com/submit/auto?user_id=deusfigendi&amp;url='.rawurlencode($this_episode['link']).'&amp;title='.rawurlencode($this_episode['episodestitle']).'&amp;description='.rawurlencode("Lesung des Kapitels ".urldecode($this_episode['episodestitle'])." aus ".urldecode($this_episode['booktitle'])).'&amp;language=de_DE&amp;tags='.rawurlencode('audiobook,podcast,hörbuch,hoerbuch,freecontent,audio').'&amp;category=audio" type="text/html" />':'<!-- non-commercial no flattr -->'));
         echo ('
         <description>'.htmlspecialchars($this_episode['description']).'</description>
         '.(array_key_exists('author',$this_episode)?'<author>'.$this_episode['author'].'</author>':'').'
         <category>'.$this_episode['categoriy'].'</category>
         <comments>'.$this_episode['link'].'</comments>
         <enclosure url="'.$base_url.'audio/'.rawurlencode($this_episode['enclosure']['filename']).'" length="'.$this_episode['enclosure']['filesize'].'" type="'.$this_episode['enclosure']['mimetype'].'" />
         <pubDate>'.date(DATE_RSS,strtotime($this_episode['pubDate'])).'</pubDate>
         <guid isPermaLink="false">'.$this_episode['guid'].'</guid>
         '.(array_key_exists('chapterfile',$this_episode)?'<atom:link rel="http://podlove.org/simple-chapters" href="'.$base_url.'chapters/'.rawurlencode($this_episode['chapterfile']).'" />':'<!-- no chapters -->').'
      </item>');
			
		}
		echo('

   </channel>
</rss>');
		

		
	} elseif ($_REQUEST['format'] == "html") {
		header('Content-Type: text/html; charset=utf-8');
		echo('<!DOCTYPE html>
	<html>
		<head>
			<title>RSS-Feeds, Übersicht</title>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		</head>
		<body>
			<h1>RSS-Feeds</h1>
			<h2>Übersicht über die Podcasts</h2>
			<p>Hier sind alle derzeitigen RSS-Feeds aufgelistet für die
			verschiedenen Formate und Techniken. Ist eine Datei mal nicht
			im Wunschformat verfügbar, wird sie dennoch auch im entsprechenden
			Feed ausgeliefert als ein anderes (verfügbares) Dateiformat.</p>
			<ul>
				<li><a href="./feed.php?format=html">Diese Übersicht</a></li>
				<li><a href="./feed.php?format=plain">Diese Übersicht im Textformat</a></li>
				<li><a href="./feed.php?format=vorbis">Ogg/Vorbis-Feed</a>'.($db_conntection['hidden']?'<ul><li><a href="./feed.php?format=vorbis&amp;'.$db_conntection['hidden_key'].'">(mit verborgenen Episoden)</a></li></ul>':'').'</li>
				<li><a href="./feed.php?format=mp3_low">MP3-Feed mit reduzierter Bitrate (64kbit/s, mono)</a>'.($db_conntection['hidden']?'<ul><li><a href="./feed.php?format=mp3_low&amp;'.$db_conntection['hidden_key'].'">(mit verborgenen Episoden)</a></li></ul>':'').'</li>
				<li><a href="./feed.php?format=mp4">MP4/AAC-Feed</a>'.($db_conntection['hidden']?'<ul><li><a href="./feed.php?format=mp4&amp;'.$db_conntection['hidden_key'].'">(mit verborgenen Episoden)</a></li></ul>':'').'</li>
			</ul>
			<p>Je nach Programm kannst du auch versuchen
			<a href="./feed.php">den allgemeinen Feed</a> zu abonnieren,
			dann rät der Server welches Format das geeignete für dich
			ist.</p>
		</body>
	</html>');
	} elseif ($_REQUEST['format'] == "plain") {
		header('Content-Type: text/plain; charset=utf-8');
		echo('
RSS-Feeds
Übersicht über die Podcasts

Hier sind alle derzeitigen RSS-Feeds aufgelistet für die verschiedenen
Formate und Techniken. Ist eine Datei mal nicht im Wunschformat verfüg-
bar, wird sie dennoch auch im entsprechenden Feed ausgeliefert als ein
anderes (verfügbares) Dateiformat.

* Diese Übersicht in HTML
  '.$_SERVER["SCRIPT_URI"].'?format=html
* Diese Übersicht
  '.$_SERVER["SCRIPT_URI"].'?format=plain
* Ogg/Vorbis-Feed
  '.$_SERVER["SCRIPT_URI"].'?format=vorbis
* MP3-Feed mit reduzierter Bitrate (64kbit/s, mono)
  '.$_SERVER["SCRIPT_URI"].'?format=mp3_low
* MP4/AAC-Feed
  '.$_SERVER["SCRIPT_URI"].'?format=mp4

Je nach Programm kannst du auch versuchen
den allgemeinen Feed unter
'.$_SERVER["SCRIPT_URI"].'
zu abonnieren, dann rät der Server welches
Format das geeignete für dich ist.');
	}
	
	include('co_random.php');
?>
