<?php

//echo(".e5");



	require_once('db_functions.php');
	require_once('ut_functions.php');

	$content['episode'][0] = '<p>Neuste Episode</p><p>Kommentare</p>';
	$content['episode'][1] = '<p>Metadaten</p>';
	$episode0_after = "";
	if (isset($_REQUEST['b'])) {
		$episode_query = "SELECT * FROM `episodes` WHERE `album` LIKE '".mysql_real_escape_string(urldecode($_REQUEST['b']))."' ".($db_conntection['hidden']?" ":"AND `hidden` = '0'")." ORDER BY `audiodate` DESC LIMIT 1";
		$episode0_before = "";
		$episode0_after = "";
		
		$comment['ttype'] = "b";
		$comment['target'] = $_REQUEST['b'];
		$comment['ttext'] = $_REQUEST['b'];
		
		//create a table of content...
		
		$sql_query = "SELECT eid , title, album , track , audiofile0 , audiofile1 , image , license , length , audiodate, payment FROM `episodes` WHERE `album` LIKE '".mysql_real_escape_string(urldecode($_REQUEST['b']))."' ".($db_conntection['hidden']?" ":"AND `hidden` = '0'")." ORDER BY `track` LIMIT 1000";
		
		$sql_result = mysql_query($sql_query);

		$content['script'][] = array("uri" => "./episode_switch1.js");
		
		
		$row_count = 0;
		$episode_ids = array();
		$min_pubdate = time();
		$max_pubdate = 0;
		$licenses = array();
		$episode_array = array();
		$episodes_object = array();
		$flattrble = 1;
		while ($row = mysql_fetch_object($sql_result)) {
			$episodes_object = array();
			
			$episodes_object['album'] = $row -> album;
			$episodes_object['title'] = $row -> title;
			$episodes_object['image'] = $row -> image;
			$episodes_object['audiofiles'] = array();
			$episodes_object['row_no'] = $row_count;
			
			$episodes_object['eid'] = $row -> eid;
			$episodes_object['track'] = $row -> track;
			$episodes_object['length'] = $row -> length;
			
			$episode_array[] = $episodes_object;
			
			
			$episode_ids[] = (int)$row -> eid;
			if ($min_pubdate > strtotime($row -> audiodate)) { $min_pubdate = strtotime($row -> audiodate); }
			if ($max_pubdate < strtotime($row -> audiodate)) { $max_pubdate = strtotime($row -> audiodate); }
			$licenses[(int)$row -> license] = true;
			
			if (!(int)($row -> payment)) { $flattrble = 0; }
			
			$row_count++;
//echo(50);				
					
		}
		
		
		foreach ($episode_array as $e_key => $episodes_object) {
			$episode_array[$e_key]['audiofiles'] = audiofiles_get($episodes_object['eid']);
		}
		
		$content['script'][]['uri'] = "./extend_toc_of_book.js";
		
		
		
		$episode0_after .= "<table class=\"of_content handwritten\">";
		foreach ($episode_array as $episodes_object) {
		
			$episode01_after = '
					<tr ';
			$episode02_after = '
						<td><span class="playbutton" onclick="play_pressed(this);" title="'.urlencode('{"eid":"'.$episodes_object['eid'].'","album":"'.$episodes_object['album'].'","title":"'.$episodes_object['title'].'","image":"'.$episodes_object['image'].'",');
			$episode01_after .= '
					data-eid=         "'.(int)$episodes_object['eid'].'"
					data-albumtitle=  "'.urlencode($episodes_object['album']).'"
					data-episodetitle="'.urlencode($episodes_object['title']).'"
					data-imageurl=    "'.$episodes_object['image'].'"';
			
			$episode02_after .= urlencode('"audiofiles":[');

			
			$first_run = true;
				
			
			foreach ($episodes_object['audiofiles'] as $audio_filearray) {
				
				if ($first_run) { $first_run = false; } else { $episode02_after .=','; }
				$episode02_after .= urlencode('"'.$audio_filearray['filename'].'"');
				$episode01_after .= '
					data-'.$audio_filearray['format'].'_filename="'.$audio_filearray['filename'].'" 
					data-'.$audio_filearray['format'].'_filesize="'.$audio_filearray['filesize'].'" ';
			}
			
			$episode02_after .= urlencode(']');
			$episode02_after .= urlencode(',"row_no":'.(int)$episodes_object['row_no'].'}').'">▷</span></td>';
			$episode01_after .= '
					data-rowno="'.(int)$episodes_object['row_no'].'" 
					data-length="'.(int)$episodes_object['length'].'" 
					data-trackno="'.(int)$episodes_object['track'].'" ';
			
			$human_readable_time = $episodes_object['length']."s";
			if ($episodes_object['length'] > 100  ) { $human_readable_time = round($episodes_object['length']/60).'min'; }
			if ($episodes_object['length'] > 10000) { $human_readable_time = round($episodes_object['length']/3600).'h'; }
			
						
			$episode02_after .= '
						<td><a href="./index.php?e='.(int)$episodes_object['eid'].'">'.$episodes_object['track'].' '.$episodes_object['title'].'</a> <span class="tabletimeindex">('.$human_readable_time.')</span></td>';	
			$random_audiofile = rand(1,count($episodes_object['audiofiles']))-1;
			$random_audiofile = $episodes_object['audiofiles'][$random_audiofile];
			
			$episode02_after .= '
						<td class="downloadlink"><a href="./audio/'.rawurlencode($random_audiofile['filename']).'">⤋ ('.human_byte($random_audiofile['filesize']).')</a></td>';
					
			$episode01_after .= ' >';
			$episode0_after .= $episode01_after.$episode02_after.'
					</tr>';
		}
		
		
		
		$episode0_after .= "</table>";
		
		$content['title'] = urldecode($_REQUEST['b']);
		
		$content['head_attributes']['prefix'] = 'tellingink: http://talkingink.de/ns#';
		$content['html_attributes']['prefix'] = 'og: http://ogp.me/ns#';
		$metaelement['property'] = 'og:title';
		$metaelement['content'] = urldecode($_REQUEST['b']);
		$content['meta'][]['attributes'] = $metaelement;
		$metaelement['property'] = 'og:type';
		$metaelement['content'] = 'tellingink:audiobook';
		$content['meta'][]['attributes'] = $metaelement;
		$metaelement['property'] = 'og:url';
		$metaelement['content'] = $_SERVER["SCRIPT_URI"]."?b=".$episodes_object['album'];
		$content['meta'][]['attributes'] = $metaelement;
		$metaelement['property'] = 'og:image';
		$metaelement['content'] = preg_replace ( "/\/index\.php.*/" , "" , $_SERVER["SCRIPT_URI"] ) . $episodes_object['image'];
		$content['meta'][]['attributes'] = $metaelement;
		$metaelement['property'] = 'og:locale';
		$metaelement['content'] = 'de_DE';
		$content['meta'][]['attributes'] = $metaelement;
		$metaelement['property'] = 'og:site_name';
		$metaelement['content'] = 'telling ink';
		$content['meta'][]['attributes'] = $metaelement;
		
		$content['episode'][1] = '<h3>Metadaten</h3><p>Social-Kram</p><ul><li>Liste</li><li>beteiligter</li><li>Personen</li></ul><p>Tags</p><p>Lizenz</p><p>Aufnahmedatum</p>';
		
		
		$content['episode'][1] = '
			<h3>Über '.urldecode($_REQUEST['b']).'...</h3>';
		
		
		
		if($flattrble) {
		
			$content['episode'][1] .= '
			<div>';
		/*
			$content['episode'][1] .= '
				<a href="https://flattr.com/submit/auto?user_id=deusfigendi&url='.rawurlencode($_SERVER["SCRIPT_URI"].$_SERVER["QUERY_STRING"]).'&title='.rawurlencode(urldecode($_REQUEST['b'])).'&description='.rawurlencode("Lesung des Buchs ".urldecode($_REQUEST['b']).". Ein Hörbuch").'&language=de_DE&tags=audiobook,podcast,hörbuch,hoerbuch,freecontent,audio&category=audio">';
		
			$content['episode'][1] .= '
				<a href="https://flattr.com/submit/auto?user_id=deusfigendi&amp;url='.rawurlencode($_SERVER["SCRIPT_URI"]."?".$_SERVER["QUERY_STRING"]).'&amp;title='.rawurlencode(urldecode($_REQUEST['b'])).'&amp;description='.rawurlencode("Lesung des Buchs ".urldecode($_REQUEST['b']).". Ein Hörbuch").'&amp;language=de_DE&amp;tags=audiobook,podcast,hörbuch,hoerbuch,freecontent,audio&amp;category=audio">';
		*/
			$content['episode'][1] .= '
				<a href="https://flattr.com/submit/auto?user_id=deusfigendi&amp;url='.rawurlencode($_SERVER["SCRIPT_URI"]."?b=".rawurlencode($episodes_object['album'])).'&amp;title='.rawurlencode(urldecode($_REQUEST['b'])).'&amp;description='.rawurlencode("Lesung des Buchs ".urldecode($_REQUEST['b']).". Ein Hörbuch").'&amp;language=de_DE&amp;tags=audiobook,podcast,hörbuch,hoerbuch,freecontent,audio&amp;category=audio">';

//<atom:link rel="payment" href="
//https://flattr.com/submit/auto?url=https%3A%2F%2Fdevelopers.flattr.net%2F&amp;user_id=flattr" type="text/html" />				
//https://flattr.com/submit/auto?user_id=deusfigendi&url=http%3A%2F%2Fwww.talkingink.de%2F%3Faction%3Depisode%26b%3DMetabuch&title=Metabuch&description=Lesung%20des%20Buchs%20Metabuch.%20Ein%20H%C3%B6rbuch&language=de_DE&tags=audiobook,podcast,h%C3%B6rbuch,hoerbuch,freecontent,audio&category=audio
//https://flattr.com/submit/auto?user_id=deusfigendi&url=http%3A%2F%2Fwww.talkingink.de%2F%3F                   b%3DMetabuch&title=Metabuch&description=Lesung%20des%20Buchs%20Metabuch.%20Ein%20H%C3%B6rbuch&language=de_DE&tags=audiobook,podcast,h%C3%B6rbuch,hoerbuch,freecontent,audio&category=audio
//https://flattr.com/submit/auto?user_id=deusfigendi&url=http%3A%2F%2Ftalkingink.de%2F%3Fb%3DAlice%27s%20Abenteuer%20im%20Wunderland&title=Alice%27s%20Abenteuer%20im%20Wunderland&description=Lesung%20des%20Buchs%20Alice%27s%20Abenteuer%20im%20Wunderland.%20Ein%20H%C3%B6rbuch&language=de_DE&tags=audiobook,podcast,h%C3%B6rbuch,hoerbuch,freecontent,audio&category=audio
//https://flattr.com/submit/auto?user_id=deusfigendi&url=http%3A%2F%2Ftalkingink.de%2Findex.php%3Fe%3D24&title=Kapitel%201%20-%20Hinunter%20in%20den%20Kaninchenbau.&description=Lesung%20des%20Kapitels%20Kapitel%201%20-%20Hinunter%20in%20den%20Kaninchenbau.%20aus%20Alices%20Abenteuer%20im%20Wunderland&language=de_DE&tags=audiobook,podcast,h%C3%B6rbuch,hoerbuch,freecontent,audio&category=audio
//https://flattr.com/submit/auto?user_id=deusfigendi&url=http%3A%2F%2Ftalkingink.de%2F%3Fb%3DAlices+Abenteuer+im+Wunderland&title=Alices%20Abenteuer%20im%20Wunderland&description=Lesung%20des%20Buchs%20Alices%20Abenteuer%20im%20Wunderland.%20Ein%20H%C3%B6rbuch&language=de_DE&tags=audiobook,podcast,h%C3%B6rbuch,hoerbuch,freecontent,audio&category=audio


//mine:        https://flattr.com/submit/auto?user_id=deusfigendi&url=http%3A%2F%2Ftalkingink.de%2F%3Fb%3DAlice%27s%20Abenteuer%20im%20Wunderland&title=Alice%27s%20Abenteuer%20im%20Wunderland&description=Lesung%20des%20Buchs%20Alice%27s%20Abenteuer%20im%20Wunderland.%20Ein%20H%C3%B6rbuch&language=de_DE&tags=audiobook,podcast,h%C3%B6rbuch,hoerbuch,freecontent,audio&category=audio
//flatrs(+):   https://flattr.com/submit/auto?user_id=DeusFigendi&url=http%3A%2F%2Ftalkingink.de%2F%3Fb%3DAlice%2527s%2BAbenteuer%2Bim%2BWunderland
//flatrs(%20): https://flattr.com/submit/auto?user_id=DeusFigendi&url=http%3A%2F%2Ftalkingink.de%2F%3Fb%3DAlice%2527s%2520Abenteuer%2520im%2520Wunderland

//Alice%27s%20Abenteuer%20im%20Wunderland
//Alice%27s%20Abenteuer%20im%20Wunderland


//old: https://flattr.com/submit/auto?user_id=deusfigendi&url=http%3A%2F%2Ftalkingink.de%2F%3Fb%3DAlice%27s%20Abenteuer%20im%20Wunderland&title=Alice%27s%20Abenteuer%20im%20Wunderland&description=Lesung%20des%20Buchs%20Alice%27s%20Abenteuer%20im%20Wunderland.%20Ein%20H%C3%B6rbuch&language=de_DE&tags=audiobook,podcast,h%C3%B6rbuch,hoerbuch,freecontent,audio&category=audio
//new: https://flattr.com/submit/auto?user_id=deusfigendi&url=http%3A%2F%2Ftalkingink.de%2F%3Fb%3DAlice%2527s%2520Abenteuer%2520im%2520Wunderland&title=Alice%27s%20Abenteuer%20im%20Wunderland&description=Lesung%20des%20Buchs%20Alice%27s%20Abenteuer%20im%20Wunderland.%20Ein%20H%C3%B6rbuch&language=de_DE&tags=audiobook,podcast,h%C3%B6rbuch,hoerbuch,freecontent,audio&category=audio



/*
 
 
 https://flattr.com/submit/auto?
  * user_id=deusfigendi&
  * url=http%3A%2F%2Ftalkingink.de%2F%3Faction%3Depisode%26b%3DAlice%2527s%2BAbenteuer%2Bim%2BWunderland&
  * title=Alice%27s%20Abenteuer%20im%20Wunderland&
  * description=Lesung%20des%20Buchs%20Alice%27s%20Abenteuer%20im%20Wunderland.%20Ein%20H%C3%B6rbuch&
  * language=de_DE&
  * tags=audiobook,podcast,h%C3%B6rbuch,hoerbuch,freecontent,audio&
  * scategory=audio
  
  
 */

		
			$content['episode'][1] .= '
				Dieses Buch flattrn
				<br />
				<img src="./images/btn_flattr.png" alt="Flattr '.urldecode($_REQUEST['b']).'" />';
			$content['episode'][1] .= '
				</a>
				<p>
					BitCoins spenden unter
					<br />
					<span style="font-size:small; letter-spacing:-0.3em;" >12KQoNxeBbd3nQrEemGwuoGRWww6JeSLdc</span>
				</p>
			</div>';
			
		} //end of "flattrble?"
		
		$person_ids = array("of","person ids","related","to","the","book");
		
		$sql_query = "SELECT arid FROM `artistsXepisodes` WHERE `eid` = '".join("' OR `eid` = '",$episode_ids)."' LIMIT 200";
		$sql_result = mysql_query($sql_query);
		while ($row = mysql_fetch_object($sql_result)) {
			$person_ids[] = (int)$row->arid;
		}
		
		$person_array = person_get_all($person_ids,"role");
		
		$social_media = array();
		
		$sql_query = "SELECT uri , service , target FROM `socialmedia` WHERE `type` = 'person' AND (`target` = '".join("' OR `target` = '",$person_ids)."') LIMIT 30";
		
		$sql_result = mysql_query($sql_query);
		while ($row = mysql_fetch_object($sql_result)) {
			$social_media[(int)$row->target][] = array("service" => $row->service,"id" => $row->uri);
		}
		
		
		
		

//echo(100);
		
		
		
		
		
		
		$last_role = -1;
		$role_list = array(0,1,2,3,4,5,6,7,8,9);
		
		
		$sql_query = "SELECT * FROM `roles` LIMIT 30";
		$sql_result = mysql_query($sql_query);
		while ($row = mysql_fetch_object($sql_result)) {
			$role_list[(int)$row -> rid] = $row -> name;
		}
		
		
		$content['episode'][1] .= '
			<h4>Beteiligte Personen ('.count($person_array).')</h4>
			<ul class="person_list">';
		
		foreach ($person_array as $person_object) {
			if ($last_role != $person_object['role']) {
				$last_role = $person_object['role'];
				$content['episode'][1] .= '
				<li class="list_subheader">'.$role_list[(int)$person_object['role']].'</li>';
			}
			
			$content['episode'][1] .= '
				<li>
					<a href="./?p='.(int)$person_object['pid'].'">';
			if (strlen($person_object['image']) > 1) {
				if (strpos($person_object['image'],"http") === 0) {
					$content['episode'][1] .= '<img src="'.$person_object['image'].'" class="list_avatar" alt="" /> ';
				} else {
					$content['episode'][1] .= '<img src="./images/'.$person_object['image'].'" class="list_avatar" alt="" /> ';
				}
			}
			if (strlen($person_object['nickname']) > 1) {
				$display_name = $person_object['nickname'];
			} else {
				$display_name = $person_object['name1'].' '.$person_object['name2'].' '.$person_object['name3'];
			}
			$content['episode'][1] .= $display_name;
			$content['episode'][1] .= '</a> <div class="hidden_elements">';
			

			if (strlen($person_object['website1']) > 1) {
				$content['episode'][1] .= '<br /><a href="'.$person_object['website1'].'">'.$display_name.'s Webseite</a>';
			} 
			if (strlen($person_object['website2']) > 1) {
				$content['episode'][1] .= '<br /><a href="'.$person_object['website2'].'">'.$display_name.'s private Webseite</a>';
			}  
			if (strlen($person_object['website3']) > 1) {
				$content['episode'][1] .= '<br /><a href="'.$person_object['website3'].'">Über '.$display_name.'</a>';
			}
			
			
			if (array_key_exists((int)$person_object['pid'],$social_media)) {
				$content['episode'][1] .= '<br />';
				foreach($social_media[(int)$person_object['pid']] as $value) {
					if ($value['service'] == 1) {
						//XMPP
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_XMPP.svg" alt="XMPP"/><a href="xmpp:'.$value['id'].'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="xmpp:'.$value['id'].'"><img class="social_icon_big" src="./images/ic_XMPP.svg" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 2) {
						//Email
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_email.svg" alt="Email"/><a href="mailto:'.$value['id'].'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="mailto:'.$value['id'].'"><img class="social_icon_big" src="./images/ic_email.svg" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 3) {
						//oStatus
						preg_match ( "/([^@]+)\@(.*)$/" , $value['id'], $social_url_parts);						
						$social_url = "http://".$social_url_parts[2]."/".$social_url_parts[1];
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_ostatus.png" alt="oStatus"/><a href="'.$social_url.'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="'.$social_url.'"><img class="social_icon_big" src="./images/ic_ostatus.png" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 4) {
						//phone
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_phone.png" alt="Telefon"/><a href="tel:'.preg_replace("/(\+|\d)+/","".$value['id']).'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="tel:'.preg_replace("/(\+|\d)+/","".$value['id']).'"><img class="social_icon_big" src="./images/ic_phone.png" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 5) {
						//sip
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_email.svg" alt="Email"/><a href="SIP:'.$value['id'].'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="SIP:'.$value['id'].'"><img class="social_icon_big" src="./images/ic_email.svg" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 6) {
						//Twitter
						preg_match ( "/\@?(.*)$/" , $value['id'], $social_url_parts);						
						$social_url = "http://twitter.com/".$social_url_parts[1];
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_twitter.svg" alt="Twitter"/><a href="'.$social_url.'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="'.$social_url.'"><img class="social_icon_big" src="./images/ic_twitter.svg" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 7) {
						//Facebook
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_facebook.svg" alt="Facebook"/><a href="http://www.facebook.com/search.php?q='.urlencode($value['id']).'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="http://www.facebook.com/search.php?q='.urlencode($value['id']).'"><img class="social_icon_big" src="./images/ic_facebook.svg" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 8) {
						//diaspora
						preg_match ( "/([^@]+)\@(.*)$/" , $value['id'], $social_url_parts);
						$social_url = "http://".$social_url_parts[2]."/u/".$social_url_parts[1];
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_diaspora.png" alt="Diaspora"/><a href="'.$social_url.'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="'.$social_url.'"><img class="social_icon_big" src="./images/ic_diaspora.png" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 8) {
						//tent???
						//$content['episode'][1] .= '<br />'.$value['id'];
						$content['episode'][1] .= $value['id'];
					} elseif ($value['service'] == 9) {
						//app.net
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_appnet.png" alt="App.net"/>'.$value['id'];
						$content['episode'][1] .= '<img class="social_icon_big" src="./images/ic_appnet.png" alt="'.$value['id'].'"/>';
					} else {
						//$content['episode'][1] .= '<br />'.$value['id'];
						$content['episode'][1] .= $value['id'];
					}
				}
				
			}
			

//echo(200);			
			
			$content['episode'][1] .= '</div>
				</li>';
		}
		$content['episode'][1] .= '
			</ul>';
			
		
		$content['episode'][1] .= '
			<div class="tag_container">';
		$sql_query = "SELECT tid FROM `tagsXepisodes` WHERE `eid` = '".join("' OR `eid` = '",$episode_ids)."' LIMIT 300";
		$sql_result = mysql_query($sql_query);
		$sql_query = "SELECT tid , text FROM `tags` WHERE ";
		while ($row = mysql_fetch_object($sql_result)) {
			$sql_query .= "`tid` = '".$row -> tid."' OR ";
		}
		$sql_query .= "FALSE LIMIT 30";
		
		$sql_result = mysql_query($sql_query);
		while ($row = mysql_fetch_object($sql_result)) {
			$content['episode'][1] .= '
				<a class="tag_link" href="./index.php?tag='.$row -> tid.'">#'.$row -> text.'</a> ';
		}
		
			
		$content['episode'][1] .= '
			</div>';
			
			
		$content['episode'][1] .= '
			<h4>Dieses Buch wurde aufgenommen im Zeitrahmen vom</h4>
			<time datetime="'.date("c\">j. n. Y",$min_pubdate).'</time> bis zum <time datetime="'.date("c\">j. n. Y",$max_pubdate).'</time>.
			<h4>Lizenzen</h4>(Unterschiedliche Episoden können unterschiedlich lizenziert sein...)';
			
		foreach ($licenses as $lic_id => $value) {
			if ($lic_id == 0) {
				$content['episode'][1] .= '
			<br />Alle Rechte vorbehalten';				
			} else if ($lic_id == 1) {
				$content['episode'][1] .= '
			<br /><a href="http://creativecommons.org/licenses/by/3.0/de/" title="Creative Commons Namensnennung 3.0 Deutschland Lizenz."><img src="./images/lic_ccby.svg" alt="creative commons by" /></a>';
			} else if ($lic_id == 7) {
				$content['episode'][1] .= '
			<br /><a href="http://creativecommons.org/licenses/by-nc-sa/3.0/de/" title="Creative Commons Namensnennung-NichtKommerziell-Weitergabe unter gleichen Bedingungen 3.0 Deutschland Lizenz."><img src="./images/lic_ccbyncsa.svg" alt="creative commons by" /></a>';
			} else if ($lic_id == 16) {
				$content['episode'][1] .= '
			<br /><a href="http://creativecommons.org/licenses/by-nc-nd/3.0/" title="Creative Commons Namensnennung-NichtKommerziell-KeineBearbeitung 3.0 Unported"><img src="./images/lic_ccbyncnd.svg" alt="creative commons by-nc-nd" /></a>';
			} else if ($lic_id == 24) {
				$content['episode'][1] .= '
			<br /><a href="http://creativecommons.org/publicdomain/zero/1.0/" title="Creative Commons zero 1.0"><img src="./images/lic_cc0.svg" alt="creative commons zero" /></a>';
			}
		}
			
		
			
	} elseif (isset($_REQUEST['tag'])) {
		//by tag
		//get episodes with this tag...
		if (!is_numeric($_REQUEST['tag'])) {
			//tag is no number... find the tid first...
			$_REQUEST['tag'] = 1;
		}
		
		$episode_list = array();
		$sql_query = "SELECT eid FROM `tagsXepisodes` WHERE `tid` = '".(int)$_REQUEST['tag']."' LIMIT 300";
		
		$sql_result = mysql_query($sql_query);
		
		while ($row = mysql_fetch_object($sql_result)) {
			$episode_list[(int)$row -> eid] = true;
		}
		
		
		
		$episode_query = "SELECT * FROM `episodes` WHERE (";
		$sql_query     = "SELECT * FROM `episodes` WHERE (";
		
		foreach ($episode_list as $this_eid => $value) {
			$episode_query .= "`eid` = '".(int)$this_eid."' OR ";
			$sql_query     .= "`eid` = '".(int)$this_eid."' OR ";
		}
		
		$episode_query .= "FALSE )".($db_conntection['hidden']?" ":"AND `hidden` = '0'")." ORDER BY `audiodate` DESC LIMIT 1";
		$sql_query     .= "FALSE )".($db_conntection['hidden']?" ":"AND `hidden` = '0'")." ORDER BY `album` DESC LIMIT 100";
		
		$sql_result = mysql_query($sql_query);
		
		$last_book = 'foobar';
		
		$episode0_after = '
			<h2>Weitere Episoden mit diesem Tag...</h2>
			<ul class="table_like_list">';
		

//echo(300);
		
		
		
		while ($row = mysql_fetch_object($sql_result)) {
			$episode0_after .= '
				<li>
					'.($last_book != $row -> album?('<a href="./?b='.urlencode($row->album).'">'.$row->album.'</a>'):'<span></span>').'
					<a href="./?e='.(int)$row->eid.'">'.$row->title.'</a>';
			if ((int)$row->license == 1) {
				$episode0_after .= '
					<a href="http://creativecommons.org/licenses/by/3.0/de/" title="Creative Commons Namensnennung 3.0 Deutschland Lizenz."><img src="./images/lic_ccby.svg" alt="ceative commons by" /></a>';				
			} elseif ((int)$row->license == 16	) {
				$episode0_after .= '
					<a href="http://creativecommons.org/licenses/by-nc-nd/3.0/" title="Creative Commons Namensnennung-NichtKommerziell-KeineBearbeitung 3.0 Unported"><img src="./images/lic_ccbyncnd.svg" alt="ceative commons by-nc-nd" /></a>';				
			} elseif ((int)$row->license == 24	) {
				$episode0_after .= '
					<a href="http://creativecommons.org/publicdomain/zero/1.0/" title="Creative Commons Zero 1.0"><img src="./images/lic_cc0.svg" alt="creative commons zero" /></a>';				
			}
			$episode0_after .= '
				</li>';
			$last_book = $row -> album;
		}
		$episode0_after .= '		
			</ul>';
			
		
		$content['episode'][1] = '
			<h4>Weitere Tags...</h4>
			<div class="tag_container">';
			
		$sql_query = "SELECT tid FROM `tagsXepisodes` WHERE `eid` = '".join("' OR `eid` = '",$episode_list)."' LIMIT 300";
		$sql_result = mysql_query($sql_query);
		
		$tid_list = array();
		while ($row = mysql_fetch_object($sql_result)) {
			if (array_key_exists((int)$row -> tid,$tid_list)) {
				$tid_list[(int)$row -> tid]++;
			} else {
				$tid_list[(int)$row -> tid]=1;
			}
		}
		
		$sql_query = "SELECT * FROM `tags` WHERE ";
		foreach ($tid_list as $this_tid => $tid_freq) {
			$sql_query .= "`tid` = '".$this_tid."' OR ";
		}
		$sql_query .= "FALSE LIMIT 100";
		$sql_result = mysql_query($sql_query);
		while ($row = mysql_fetch_object($sql_result)) {
			$content['episode'][1] .= '<a class="tag_link" href="./index.php?tag='.$row -> tid.'">#'.$row -> text.'</a> ';
		}
		
		
		$content['episode'][1] .= '	
			</div>';
		
		
		$person_ids = array();
		$sql_query = "SELECT arid FROM `artistsXepisodes` WHERE `eid` = '".join("' OR `eid` = '",$episode_list)."' LIMIT 200";
		$sql_result = mysql_query($sql_query);
		while ($row = mysql_fetch_object($sql_result)) {
			$person_ids[] = (int)$row->arid;
		}
		
		
		$person_array = person_get_all($person_ids,"role");
		
		$social_media = array();
		
		$sql_query = "SELECT uri , service , target FROM `socialmedia` WHERE `type` = 'person' AND (`target` = '".join("' OR `target` = '",$person_ids)."') LIMIT 30";
		
		$sql_result = mysql_query($sql_query);
		while ($row = mysql_fetch_object($sql_result)) {
			$social_media[(int)$row->target][] = array("service" => $row->service,"id" => $row->uri);
		}
		
		
		$last_role = -1;
		$role_list = array(0,1,2,3,4,5,6,7,8,9);
		
		
		$sql_query = "SELECT * FROM `roles` LIMIT 30";
		$sql_result = mysql_query($sql_query);
		while ($row = mysql_fetch_object($sql_result)) {
			$role_list[(int)$row -> rid] = $row -> name;
		}
		
		
		$content['episode'][1] .= '		
			<h4>Beteiligte Personen ('.count($person_array).')</h4>
			<ul class="person_list">';
		
		foreach ($person_array as $person_object) {
			if ($last_role != $person_object['role']) {
				$last_role = $person_object['role'];
				$content['episode'][1] .= '
				<li class="list_subheader">'.$role_list[(int)$person_object['role']].'</li>';
			}
			
			$content['episode'][1] .= '
				<li>
					<a href="./?p='.(int)$person_object['pid'].'">';
			if (strlen($person_object['image']) > 1) {
				
				if (strpos($person_object['image'],"http") === 0) {
					$content['episode'][1] .= '<img src="'.$person_object['image'].'" class="list_avatar" alt="" /> ';
				} else {
					$content['episode'][1] .= '<img src="./images/'.$person_object['image'].'" class="list_avatar" alt="" /> ';
				}
				
			}
			if (strlen($person_object['nickname']) > 1) {
				$display_name = $person_object['nickname'];
			} else {
				$display_name = $person_object['name1'].' '.$person_object['name2'].' '.$person_object['name3'];
			}
			$content['episode'][1] .= $display_name;
			$content['episode'][1] .= '</a> <div class="hidden_elements">';
			

			if (strlen($person_object['website1']) > 1) {
				$content['episode'][1] .= '<br /><a href="'.$person_object['website1'].'">'.$display_name.'s Webseite</a>';
			} 
			if (strlen($person_object['website2']) > 1) {
				$content['episode'][1] .= '<br /><a href="'.$person_object['website2'].'">'.$display_name.'s private Webseite</a>';
			}  
			if (strlen($person_object['website3']) > 1) {
				$content['episode'][1] .= '<br /><a href="'.$person_object['website3'].'">Über '.$display_name.'</a>';
			}
		
		

//echo(400);
			
			
			if (array_key_exists((int)$person_object['pid'],$social_media)) {
				$content['episode'][1] .= '<br />';
				foreach($social_media[(int)$person_object['pid']] as $value) {
					if ($value['service'] == 1) {
						//XMPP
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_XMPP.svg" alt="XMPP"/><a href="xmpp:'.$value['id'].'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="xmpp:'.$value['id'].'"><img class="social_icon_big" src="./images/ic_XMPP.svg" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 2) {
						//Email
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_email.svg" alt="Email"/><a href="mailto:'.$value['id'].'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="mailto:'.$value['id'].'"><img class="social_icon_big" src="./images/ic_email.svg" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 3) {
						//oStatus
						preg_match ( "/([^@]+)\@(.*)$/" , $value['id'], $social_url_parts);						
						$social_url = "http://".$social_url_parts[2]."/".$social_url_parts[1];
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_ostatus.png" alt="oStatus"/><a href="'.$social_url.'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="'.$social_url.'"><img class="social_icon_big" src="./images/ic_ostatus.png" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 4) {
						//phone
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_phone.png" alt="Telefon"/><a href="tel:'.preg_replace("/(\+|\d)+/","".$value['id']).'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="tel:'.preg_replace("/(\+|\d)+/","".$value['id']).'"><img class="social_icon_big" src="./images/ic_phone.png" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 5) {
						//sip
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_email.svg" alt="Email"/><a href="SIP:'.$value['id'].'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="SIP:'.$value['id'].'"><img class="social_icon_big" src="./images/ic_email.svg" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 6) {
						//Twitter
						preg_match ( "/\@?(.*)$/" , $value['id'], $social_url_parts);						
						$social_url = "http://twitter.com/".$social_url_parts[1];
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_twitter.svg" alt="Twitter"/><a href="'.$social_url.'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="'.$social_url.'"><img class="social_icon_big" src="./images/ic_twitter.svg" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 7) {
						//Facebook
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_facebook.svg" alt="Facebook"/><a href="http://www.facebook.com/search.php?q='.urlencode($value['id']).'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="http://www.facebook.com/search.php?q='.urlencode($value['id']).'"><img class="social_icon_big" src="./images/ic_facebook.svg" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 8) {
						//diaspora
						preg_match ( "/([^@]+)\@(.*)$/" , $value['id'], $social_url_parts);
						$social_url = "http://".$social_url_parts[2]."/u/".$social_url_parts[1];
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_diaspora.png" alt="Diaspora"/><a href="'.$social_url.'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="'.$social_url.'"><img class="social_icon_big" src="./images/ic_diaspora.png" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 8) {
						//tent???
						//$content['episode'][1] .= '<br />'.$value['id'];
						$content['episode'][1] .= $value['id'];
					} elseif ($value['service'] == 9) {
						//app.net
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_appnet.png" alt="App.net"/>'.$value['id'];
						$content['episode'][1] .= '<img class="social_icon_big" src="./images/ic_appnet.png" alt="'.$value['id'].'"/>';
					} else {
						//$content['episode'][1] .= '<br />'.$value['id'];
						$content['episode'][1] .= $value['id'];
					}
				}
				
			}
			
			
			
			$content['episode'][1] .= '</div>
				</li>';
		}
		$content['episode'][1] .= '
			</ul>';
			
		
			
	} elseif (isset($_REQUEST['p'])) {
		//by person
		
		
		$comment['ttype'] = "p";
		$comment['target'] = $_REQUEST['p'];
		
		
		$episode_list = array();
		$sql_query = "SELECT eid FROM `artistsXepisodes` WHERE `arid` = '".(int)$_REQUEST['p']."' LIMIT 300";
		
		$sql_result = mysql_query($sql_query);
		
		while ($row = mysql_fetch_object($sql_result)) {
			$episode_list[(int)$row -> eid] = true;
		}
		
		$sql_query = "SELECT * FROM `person` WHERE `pid` = '".(int)$_REQUEST['p']."' LIMIT 1";
		$sql_result = mysql_query($sql_query);
		$persons_object = mysql_fetch_object($sql_result);
		
		
		$episode_query = "SELECT * FROM `episodes` WHERE (";
		$sql_query     = "SELECT * FROM `episodes` WHERE (";
		
		foreach ($episode_list as $this_eid => $value) {
			$episode_query .= "`eid` = '".(int)$this_eid."' OR ";
			$sql_query     .= "`eid` = '".(int)$this_eid."' OR ";
		}
		
		

//echo(".500");



		
		
		$episode_query .= "FALSE) ".($db_conntection['hidden']?" ":"AND `hidden` = '0'")." ORDER BY `audiodate` DESC LIMIT 1";
		$sql_query     .= "FALSE) ".($db_conntection['hidden']?" ":"AND `hidden` = '0'")." ORDER BY `album` DESC LIMIT 100";
		
		$sql_result547868 = mysql_query($sql_query);
		
		$last_book = 'foobar';
		
		
		$role_list = array(0,1,2,3,4,5,6,7,8,9);
		
		
		$sql_query = "SELECT * FROM `roles` LIMIT 30";
		$sql_result = mysql_query($sql_query);
		while ($row = mysql_fetch_object($sql_result)) {
			$role_list[(int)$row -> rid] = $row -> name;
		}
		
		$social_media = array();
		
		$sql_query = "SELECT uri , service , target FROM `socialmedia` WHERE `type` = 'person' AND `target` = '".(int)$_REQUEST['p']."' LIMIT 30";
		
		$sql_result = mysql_query($sql_query);
		while ($row = mysql_fetch_object($sql_result)) {
			$social_media[(int)$row->target][] = array("service" => $row->service,"id" => $row->uri);
		}
		
		
		
		$episode0_after .= '<br style="break:both;" />';
		
		
		if (strlen($persons_object->image) > 1) {
				if (strpos($persons_object->image,"http") === 0) {
					$episode0_after .= '<img src="'.$persons_object->image.'" class="avatar_big" alt="" /> ';
				} else {
					$episode0_after .= '<img src="./images/'.$persons_object->image.'" class="avatar_big" alt="" /> ';
				}
		}
		if (strlen($persons_object->nickname) > 1) {
			$display_name = $persons_object->nickname;
		} else {
			$display_name = $persons_object->name1.' '.$persons_object->name2.' '.$persons_object->name3;
		}
		
		
		$comment['ttext'] = $display_name;
		
		
		$episode0_after .= '
			<h2>'.$display_name.' ('.$role_list[$persons_object->role].')</h2>';
			
			

		if (strlen($persons_object->website1) || strlen($persons_object->website2) || strlen($persons_object->website3) > 1) {
			$episode0_after .= '
			<h3>Webseiten</h3>
			<ul>
				';
			if (strlen($persons_object->website1) > 1) {
				$episode0_after .= '<li><a href="'.$persons_object->website1.'">Offiziell</a></li>';
			} 
			if (strlen($persons_object->website2) > 1) {
				$episode0_after .= '<li><a href="'.$persons_object->website2.'">Privat</a></li>';
			}  
			if (strlen($persons_object->website3) > 1) {
				$episode0_after .= '<li><a href="'.$persons_object->website3.'">Andere über '.$display_name.'</a></li>';
			}
			$episode0_after .= '
			</ul>';
		}
		
		
			if (array_key_exists((int)$persons_object->pid,$social_media)) {
				$episode0_after .= '
			<h3>Außerdem ist '.$display_name.' auch hier noch im Netz zu finden...</h3>
			<ul class="table_like_list">
				';
				foreach($social_media[(int)$persons_object->pid] as $value) {
					if ($value['service'] == 1) {
						//XMPP
						$episode0_after .= '<li><img class="social_icon_big" src="./images/ic_XMPP.svg" alt="XMPP"/><span>Jabber</span><a href="xmpp:'.$value['id'].'">'.$value['id'].'</a></li>';
					} elseif ($value['service'] == 2) {
						//Email
						$episode0_after .= '<li><img class="social_icon_big" src="./images/ic_email.svg" alt="Email"/><span>E-Mail</span><a href="mailto:'.$value['id'].'">'.$value['id'].'</a></li>';
					} elseif ($value['service'] == 3) {
						//oStatus
						preg_match ( "/([^@]+)\@(.*)$/" , $value['id'], $social_url_parts);						
						$social_url = "http://".$social_url_parts[2]."/".$social_url_parts[1];
						$episode0_after .= '<li><img class="social_icon_big" src="./images/ic_ostatus.png" alt="oStatus"/><span>Status.NET</span><a href="'.$social_url.'">'.$value['id'].'</a></li>';
					} elseif ($value['service'] == 4) {
						//phone
						$episode0_after .= '<li><img class="social_icon_big" src="./images/ic_phone.png" alt="Telefon"/><span>Telefon</span><a href="tel:'.preg_replace("/(\+|\d)+/","".$value['id']).'">'.$value['id'].'</a></li>';
					} elseif ($value['service'] == 5) {
						//sip
						$episode0_after .= '<li><img class="social_icon_big" src="./images/ic_email.svg" alt="Email"/><span>SIP</span><a href="SIP:'.$value['id'].'">'.$value['id'].'</a></li>';
					} elseif ($value['service'] == 6) {
						//Twitter
						preg_match ( "/\@?(.*)$/" , $value['id'], $social_url_parts);						
						$social_url = "http://twitter.com/".$social_url_parts[1];
						$episode0_after .= '<li><img class="social_icon_big" src="./images/ic_twitter.svg" alt="Twitter"/><span>Twitter</span><a href="'.$social_url.'">'.$value['id'].'</a></li>';
					} elseif ($value['service'] == 7) {
						//Facebook
						$episode0_after .= '<li><img class="social_icon_big" src="./images/ic_facebook.svg" alt="Facebook"/><span>Facebook</span><a href="http://www.facebook.com/search.php?q='.urlencode($value['id']).'">'.$value['id'].'</a></li>';
					} elseif ($value['service'] == 8) {
						//diaspora
						preg_match ( "/([^@]+)\@(.*)$/" , $value['id'], $social_url_parts);
						$social_url = "http://".$social_url_parts[2]."/u/".$social_url_parts[1];
						$episode0_after .= '<li><img class="social_icon_big" src="./images/ic_diaspora.png" alt="Diaspora"/><span>Diaspora*</span><a href="'.$social_url.'">'.$value['id'].'</a></li>';
					} elseif ($value['service'] == 8) {
						//tent???
						$episode0_after .= '<li><span></span><span>Tent</span><span>'.$value['id'].'</span></li>';
					} elseif ($value['service'] == 9) {
						//app.net
						$episode0_after .= '<li><img class="social_icon_big" src="./images/ic_appnet.png" alt="App.net"/><span>APP.NET</span><span>'.$value['id'].'</span></li>';
					} else {
						$episode0_after .= '<li><span></span><span></span><span>'.$value['id'].'</span></li>';
					}
				}
				$episode0_after .= '
			</ul>';
			}
			
			
		
//echo(".640");			
		
		
		$episode0_after .= '
			<h3>'.$display_name.' war an folgenden Episoden irgendwie beteiligt...</h3>
			<ul class="table_like_list">';
		
		while ($row = mysql_fetch_object($sql_result547868)) {
			$episode0_after .= '
				<li>
					'.($last_book != $row -> album?('<a href="./?b='.urlencode($row->album).'">'.$row->album.'</a>'):'<span></span>').'
					<a href="./?e='.(int)$row->eid.'">'.$row->title.'</a>';
			if ((int)$row->license == 1) {
				$episode0_after .= '
					<a href="http://creativecommons.org/licenses/by/3.0/de/" title="Creative Commons Namensnennung 3.0 Deutschland Lizenz."><img src="./images/lic_ccby.svg" alt="ceative commons by" /></a>';				
			} elseif ((int)$row->license == 16	) {
				$episode0_after .= '
					<a href="http://creativecommons.org/licenses/by-nc-nd/3.0/" title="Creative Commons Namensnennung-NichtKommerziell-KeineBearbeitung 3.0 Unported"><img src="./images/lic_ccbyncnd.svg" alt="ceative commons by-nc-nd" /></a>';				
			}
			$episode0_after .= '
				</li>';
			$last_book = $row -> album;
		}
		$episode0_after .= '		
			</ul>';
			
		
		$content['episode'][1] = '
			<h4>Weitere Tags...</h4>
			<div class="tag_container">';
			
		$sql_query = "SELECT tid FROM `tagsXepisodes` WHERE `eid` = '".join("' OR `eid` = '",$episode_list)."' LIMIT 300";
		$sql_result = mysql_query($sql_query);
		
		$tid_list = array();
		while ($row = mysql_fetch_object($sql_result)) {
			if (array_key_exists((int)$row -> tid,$tid_list)) {
				$tid_list[(int)$row -> tid]++;
			} else {
				$tid_list[(int)$row -> tid]=1;
			}
		}
		
		$sql_query = "SELECT * FROM `tags` WHERE ";
		foreach ($tid_list as $this_tid => $tid_freq) {
			$sql_query .= "`tid` = '".$this_tid."' OR ";
		}
		$sql_query .= "FALSE LIMIT 100";
		$sql_result = mysql_query($sql_query);
		while ($row = mysql_fetch_object($sql_result)) {
			$content['episode'][1] .= '<a class="tag_link" href="./index.php?tag='.$row -> tid.'">#'.$row -> text.'</a> ';
		}
		
		
		$content['episode'][1] .= '	
			</div>';
		
		
		$person_ids = array();
		$sql_query = "SELECT arid FROM `artistsXepisodes` WHERE `eid` = '".join("' OR `eid` = '",$episode_list)."' LIMIT 200";
		$sql_result = mysql_query($sql_query);
		while ($row = mysql_fetch_object($sql_result)) {
			$person_ids[] = (int)$row->arid;
		}
		
		
		$person_array = person_get_all($person_ids,"role");
		
		
		$last_role = -1;
		
		
//echo(".710");
		
		
		
		$content['episode'][1] .= '		
			<h4>Beteiligte Personen ('.count($person_array).')</h4>
			<ul class="person_list">';
		
		foreach ($person_array as $person_object) {
			if ($last_role != $person_object['role']) {
				$last_role = $person_object['role'];
				$content['episode'][1] .= '
				<li class="list_subheader">'.$role_list[(int)$person_object['role']].'</li>';
			}
			
			$content['episode'][1] .= '
				<li>
					<a href="./?p='.(int)$person_object['pid'].'">';
			if (strlen($person_object['image']) > 1) {
				if (strpos($person_object['image'],"http") === 0) {
					$content['episode'][1] .= '<img src="'.$person_object['image'].'" class="list_avatar" alt="" /> ';
				} else {
					$content['episode'][1] .= '<img src="./images/'.$person_object['image'].'" class="list_avatar" alt="" /> ';
				}
			}
			if (strlen($person_object['nickname']) > 1) {
				$display_name = $person_object['nickname'];
			} else {
				$display_name = $person_object['name1'].' '.$person_object['name2'].' '.$person_object['name3'];
			}
			$content['episode'][1] .= $display_name;
			$content['episode'][1] .= '</a> <div class="hidden_elements">';
			

			if (strlen($person_object['website1']) > 1) {
				$content['episode'][1] .= '<br /><a href="'.$person_object['website1'].'">'.$display_name.'s Webseite</a>';
			} 
			if (strlen($person_object['website2']) > 1) {
				$content['episode'][1] .= '<br /><a href="'.$person_object['website2'].'">'.$display_name.'s private Webseite</a>';
			}  
			if (strlen($person_object['website3']) > 1) {
				$content['episode'][1] .= '<br /><a href="'.$person_object['website3'].'">Über '.$display_name.'</a>';
			}
			
			
			if (array_key_exists((int)$person_object['pid'],$social_media)) {
				$content['episode'][1] .= '<br />';
				foreach($social_media[(int)$person_object['pid']] as $value) {
					if ($value['service'] == 1) {
						//XMPP
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_XMPP.svg" alt="XMPP"/><a href="xmpp:'.$value['id'].'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="xmpp:'.$value['id'].'"><img class="social_icon_big" src="./images/ic_XMPP.svg" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 2) {
						//Email
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_email.svg" alt="Email"/><a href="mailto:'.$value['id'].'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="mailto:'.$value['id'].'"><img class="social_icon_big" src="./images/ic_email.svg" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 3) {
						//oStatus
						preg_match ( "/([^@]+)\@(.*)$/" , $value['id'], $social_url_parts);						
						$social_url = "http://".$social_url_parts[2]."/".$social_url_parts[1];
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_ostatus.png" alt="oStatus"/><a href="'.$social_url.'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="'.$social_url.'"><img class="social_icon_big" src="./images/ic_ostatus.png" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 4) {
						//phone
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_phone.png" alt="Telefon"/><a href="tel:'.preg_replace("/(\+|\d)+/","".$value['id']).'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="tel:'.preg_replace("/(\+|\d)+/","".$value['id']).'"><img class="social_icon_big" src="./images/ic_phone.png" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 5) {
						//sip
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_email.svg" alt="Email"/><a href="SIP:'.$value['id'].'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="SIP:'.$value['id'].'"><img class="social_icon_big" src="./images/ic_email.svg" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 6) {
						//Twitter
						preg_match ( "/\@?(.*)$/" , $value['id'], $social_url_parts);						
						$social_url = "http://twitter.com/".$social_url_parts[1];
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_twitter.svg" alt="Twitter"/><a href="'.$social_url.'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="'.$social_url.'"><img class="social_icon_big" src="./images/ic_twitter.svg" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 7) {
						//Facebook
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_facebook.svg" alt="Facebook"/><a href="http://www.facebook.com/search.php?q='.urlencode($value['id']).'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="http://www.facebook.com/search.php?q='.urlencode($value['id']).'"><img class="social_icon_big" src="./images/ic_facebook.svg" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 8) {
						//diaspora
						preg_match ( "/([^@]+)\@(.*)$/" , $value['id'], $social_url_parts);
						$social_url = "http://".$social_url_parts[2]."/u/".$social_url_parts[1];
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_diaspora.png" alt="Diaspora"/><a href="'.$social_url.'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="'.$social_url.'"><img class="social_icon_big" src="./images/ic_diaspora.png" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 8) {
						//tent???
						//$content['episode'][1] .= '<br />'.$value['id'];
						$content['episode'][1] .= $value['id'];
					} elseif ($value['service'] == 9) {
						//app.net
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_appnet.png" alt="App.net"/>'.$value['id'];
						$content['episode'][1] .= '<img class="social_icon_big" src="./images/ic_appnet.png" alt="'.$value['id'].'"/>';
					} else {
						//$content['episode'][1] .= '<br />'.$value['id'];
						$content['episode'][1] .= $value['id'];
					}
				}
				
			}
			
			
			
			$content['episode'][1] .= '</div>
				</li>';
		}
		$content['episode'][1] .= '
			</ul>';
			
		
		
		
		
		
//echo(".e808");
		
		
		
		
		
		
	} elseif (isset($_REQUEST['l'])) {
		//by license
	} else {
		//in any other case including "by episode"...
		if (isset($_REQUEST['e'])) {
			$episode_query = "SELECT * FROM `episodes` WHERE `eid` LIKE '".(int)$_REQUEST['e']."' ".($db_conntection['hidden']?" ":"AND `hidden` = '0'")."  ORDER BY `audiodate` DESC LIMIT 1";
		} else {
			$episode_query = "SELECT * FROM `episodes`  ".($db_conntection['hidden']?" ":"WHERE `hidden` = '0'")." ORDER BY `audiodate` DESC LIMIT 1";
		}
		
		
		
		$sql_result = mysql_query($episode_query);
		$episode_object = mysql_fetch_object($sql_result);
		
		$comment['ttype'] = "e";
		$comment['target'] = $episode_object -> eid;
		$comment['ttext'] = $episode_object -> title;
		
		$content['title'] = urldecode($episode_object -> album .' - '. $episode_object -> title);
		
		$content['episode'][1] = '<h3>Metadaten</h3><p>Social-Kram</p><ul><li>Liste</li><li>beteiligter</li><li>Personen</li></ul><p>Tags</p><p>Lizenz</p><p>Aufnahmedatum</p>';
		
		
		$content['episode'][1] = '
			<h3>Über <a href="./?b='.urlencode($episode_object -> album).'" title="Buch öffnen">'.$episode_object -> album.'</a> - '.$episode_object -> title.'...</h3>';
		
		if ((int)$episode_object -> payment) {
			$content['episode'][1] .= '
			<div>';
			$content['episode'][1] .= '
				<a href="https://flattr.com/submit/auto?user_id=deusfigendi&amp;url='.rawurlencode($_SERVER["SCRIPT_URI"]."?e=".(int)$episode_object -> eid).'&amp;title='.rawurlencode($episode_object -> title).'&amp;description='.rawurlencode("Lesung des Kapitels ".urldecode($episode_object -> title)." aus ".urldecode($episode_object -> album)).'&amp;language=de_DE&amp;tags=audiobook,podcast,hörbuch,hoerbuch,freecontent,audio&amp;category=audio">';
		
			$content['episode'][1] .= '
				Diese Episode flattrn
				<br />
				<img src="./images/btn_flattr.png" alt="Flattr '.urldecode($episode_object -> title).'" /><span class="flattrcount">'.$episode_object -> flattrs.'</span>';
			$content['episode'][1] .= '
				</a>
				<p>
					BitCoins spenden unter
					<br />
					<span style="font-size:small; letter-spacing:-0.3em;" >12KQoNxeBbd3nQrEemGwuoGRWww6JeSLdc</span>
				</p>
			</div>';
			
			if (!isset($_REQUEST['e'])) {
				$content['episode'][1] .= '
			<div>';
				$content['episode'][1] .= '
				<a href="https://flattr.com/submit/auto?user_id=deusfigendi&amp;url='.rawurlencode(str_replace("index.php","",$_SERVER["SCRIPT_URI"])).'&amp;title='.rawurlencode($episode_object -> title).'&amp;description='.rawurlencode("Telling Ink").'&amp;language=de_DE&amp;tags=audiobook,podcast,hörbuch,hoerbuch,freecontent,audio&amp;category=audio">';
		
				$content['episode'][1] .= '
				Telling Ink flattrn
				<br />
				<img src="./images/btn_flattr.png" alt="Flattr telling ink" />';
				$content['episode'][1] .= '
				</a>
				<p>
					BitCoins spenden unter
					<br />
					<span style="font-size:small; letter-spacing:-0.3em;" >12KQoNxeBbd3nQrEemGwuoGRWww6JeSLdc</span>
				</p>
			</div>';
			}
		} 
		
		$person_ids = array("of","person ids","related","to","the","episode");
		
		$sql_query = "SELECT arid FROM `artistsXepisodes` WHERE `eid` = '".(int)$episode_object -> eid."' LIMIT 200";
		$sql_result = mysql_query($sql_query);
		while ($row = mysql_fetch_object($sql_result)) {
			$person_ids[] = (int)$row->arid;
		}
		
		
		
		$person_array = person_get_all($person_ids,"role");
		
		$social_media = array();
		
		$sql_query = "SELECT uri , service , target FROM `socialmedia` WHERE `type` = 'person' AND (`target` = '".join("' OR `target` = '",$person_ids)."') LIMIT 30";
		
		$sql_result = mysql_query($sql_query);
		while ($row = mysql_fetch_object($sql_result)) {
			$social_media[(int)$row->target][] = array("service" => $row->service,"id" => $row->uri);
		}
		
		
		$last_role = -1;
		$role_list = array(0,1,2,3,4,5,6,7,8,9);
		
		
		$sql_query = "SELECT * FROM `roles` LIMIT 30";
		$sql_result = mysql_query($sql_query);
		while ($row = mysql_fetch_object($sql_result)) {
			$role_list[(int)$row -> rid] = $row -> name;
		}
		
		
		$content['episode'][1] .= '
			<h4>Beteiligte Personen ('.count($person_array).')</h4>
			<ul class="person_list">';
	
	

//echo(".e900");




	
		foreach ($person_array as $person_object) {
			if ($last_role != $person_object['role']) {
				$last_role = $person_object['role'];
				$content['episode'][1] .= '
				<li class="list_subheader">'.$role_list[(int)$person_object['role']].'</li>';
			}
			$content['episode'][1] .= '
				<li>
					<a href="./?p='.(int)$person_object['pid'].'">';
			if (strlen($person_object['image']) > 1) {
				if (strpos($person_object['image'],"http") === 0) {
					$content['episode'][1] .= '<img src="'.$person_object['image'].'" class="list_avatar" alt="" /> ';
				} else {
					$content['episode'][1] .= '<img src="./images/'.$person_object['image'].'" class="list_avatar" alt="" /> ';
				}
			}
			if (strlen($person_object['nickname']) > 1) {
				$display_name = $person_object['nickname'];
			} else {
				$display_name = $person_object['name1'].' '.$person_object['name2'].' '.$person_object['name3'];
			}
			$content['episode'][1] .= $display_name;
			
			$content['episode'][1] .= '</a> <div class="hidden_elements">';
			

			if (strlen($person_object['website1']) > 1) {
				$content['episode'][1] .= '<br /><a href="'.$person_object['website1'].'">'.$display_name.'s Webseite</a>';
			} 
			if (strlen($person_object['website2']) > 1) {
				$content['episode'][1] .= '<br /><a href="'.$person_object['website2'].'">'.$display_name.'s private Webseite</a>';
			}  
			if (strlen($person_object['website3']) > 1) {
				$content['episode'][1] .= '<br /><a href="'.$person_object['website3'].'">Über '.$display_name.'</a>';
			}
			
			
	
	
	
	
	
			
			if (array_key_exists((int)$person_object['pid'],$social_media)) {
				$content['episode'][1] .= '<br />';
				foreach($social_media[(int)$person_object['pid']] as $value) {
					if ($value['service'] == 1) {
						//XMPP
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_XMPP.svg" alt="XMPP"/><a href="xmpp:'.$value['id'].'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="xmpp:'.$value['id'].'"><img class="social_icon_big" src="./images/ic_XMPP.svg" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 2) {
						//Email
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_email.svg" alt="Email"/><a href="mailto:'.$value['id'].'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="mailto:'.$value['id'].'"><img class="social_icon_big" src="./images/ic_email.svg" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 3) {
						//oStatus
						preg_match ( "/([^@]+)\@(.*)$/" , $value['id'], $social_url_parts);						
						$social_url = "http://".$social_url_parts[2]."/".$social_url_parts[1];
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_ostatus.png" alt="oStatus"/><a href="'.$social_url.'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="'.$social_url.'"><img class="social_icon_big" src="./images/ic_ostatus.png" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 4) {
						//phone
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_phone.png" alt="Telefon"/><a href="tel:'.preg_replace("/(\+|\d)+/","".$value['id']).'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="tel:'.preg_replace("/(\+|\d)+/","".$value['id']).'"><img class="social_icon_big" src="./images/ic_phone.png" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 5) {
						//sip
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_email.svg" alt="Email"/><a href="SIP:'.$value['id'].'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="SIP:'.$value['id'].'"><img class="social_icon_big" src="./images/ic_email.svg" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 6) {
						//Twitter
						preg_match ( "/\@?(.*)$/" , $value['id'], $social_url_parts);						
						$social_url = "http://twitter.com/".$social_url_parts[1];
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_twitter.svg" alt="Twitter"/><a href="'.$social_url.'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="'.$social_url.'"><img class="social_icon_big" src="./images/ic_twitter.svg" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 7) {
						//Facebook
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_facebook.svg" alt="Facebook"/><a href="http://www.facebook.com/search.php?q='.urlencode($value['id']).'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="http://www.facebook.com/search.php?q='.urlencode($value['id']).'"><img class="social_icon_big" src="./images/ic_facebook.svg" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 8) {
						//diaspora
						preg_match ( "/([^@]+)\@(.*)$/" , $value['id'], $social_url_parts);
						$social_url = "http://".$social_url_parts[2]."/u/".$social_url_parts[1];
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_diaspora.png" alt="Diaspora"/><a href="'.$social_url.'">'.$value['id'].'</a>';
						$content['episode'][1] .= '<a href="'.$social_url.'"><img class="social_icon_big" src="./images/ic_diaspora.png" alt="'.$value['id'].'"/></a>';
					} elseif ($value['service'] == 8) {
						//tent???
						//$content['episode'][1] .= '<br />'.$value['id'];
						$content['episode'][1] .= $value['id'];
					} elseif ($value['service'] == 9) {
						//app.net
						//$content['episode'][1] .= '<br /><img class="social_icon" src="./images/ic_appnet.png" alt="App.net"/>'.$value['id'];
						$content['episode'][1] .= '<img class="social_icon_big" src="./images/ic_appnet.png" alt="'.$value['id'].'"/>';
					} else {
						//$content['episode'][1] .= '<br />'.$value['id'];
						$content['episode'][1] .= $value['id'];
					}
				}
				
			}
			
			
			
			$content['episode'][1] .= '</div>
				</li>';
		}
		$content['episode'][1] .= '
			</ul>';
			
		
		
		$content['episode'][1] .= '
			<div class="tag_container">';
		$sql_query = "SELECT tid FROM `tagsXepisodes` WHERE `eid` = '".(int)$episode_object -> eid."' LIMIT 300";
		$sql_result = mysql_query($sql_query);
		$sql_query = "SELECT tid , text FROM `tags` WHERE ";
		while ($row = mysql_fetch_object($sql_result)) {
			$sql_query .= "`tid` = '".$row -> tid."' OR ";
		}
		$sql_query .= "FALSE LIMIT 30";
		
		$sql_result = mysql_query($sql_query);
		while ($row = mysql_fetch_object($sql_result)) {
			$content['episode'][1] .= '
				<a class="tag_link" href="./index.php?tag='.$row -> tid.'">#'.$row -> text.'</a> ';
		}


//echo(".e1010");

		
			
		$content['episode'][1] .= '
			</div>';


			
		
		$content['episode'][1] .= '
			<h4>Dieses Kapitel wurde aufgenommen am</h4>
			<time datetime="'.date('c">j. n. Y',strtotime($episode_object -> audiodate)).'</time>.
			<h4>Lizenz</h4>Diese Episode steht unter';

		if ((int) $episode_object -> license == 0) {
			$content['episode'][1] .= '
			<br />Alle Rechte vorbehalten';				
		} elseif ((int) $episode_object -> license  == 1) {
			$content['episode'][1] .= '
			<br /><a href="http://creativecommons.org/licenses/by/3.0/de/" title="Creative Commons Namensnennung 3.0 Deutschland Lizenz."><img src="./images/lic_ccby.svg" alt="ceative commons by" /></a>';
		} elseif ((int) $episode_object -> license == 16	) {
			$content['episode'][1] .= '
			<br /><a href="http://creativecommons.org/licenses/by-nc-nd/3.0/" title="Creative Commons Namensnennung-NichtKommerziell-KeineBearbeitung 3.0 Unported"><img src="./images/lic_ccbyncnd.svg" alt="ceative commons by-nc-nd" /></a>';				
		} elseif ((int) $episode_object -> license == 24	) {
			$content['episode'][1] .= '
			<br /><a href="http://creativecommons.org/publicdomain/zero/1.0/" title="Creative Commons Zero"><img src="./images/lic_cc0.svg" alt="ceative commons 0" /></a>';				
		}
		
		
		

	}



//echo(".e1042");


	$sql_result = mysql_query($episode_query);
	$sql_row = mysql_fetch_object($sql_result);
	
	$episodes_audiofiles = audiofiles_get($sql_row -> eid);
	
	$content['episode'][0] = '
			<img id="episode_cover" src="./images/'.rawurlencode($sql_row -> image).'" alt="Cover" />';
	
	$content['episode'][0] .= '
			<h1 class="book_title">'.$sql_row -> album.'</h1>';
	$content['episode'][0] .= '
			<h2 class="episode_title">'.$sql_row -> title.'</h2>';
	$content['episode'][0] .= '
			<p class="episode_summary">'.nl2br($sql_row -> summary).'</p>';
	
	$content['episode'][0] .= '
			<img src="./images/'.rawurlencode($sql_row -> waveform).'" alt="grafische Wellendarstellung (waveform) der Episode" class="audioplayer_waveform" />
			<audio controls="controls" id="tellingink_audioplayer_'.rand(100,999).'" class="tellingink_audioplayer">';
			
	foreach ($episodes_audiofiles as $audiofile_object) {
		$content['episode'][0] .= '
				<source src="./audio/'.rawurlencode($audiofile_object['filename']).'" type="'.$audiofile_object['mimetype'].'" />';
	}
	$content['episode'][0] .= '
			</audio>';
	$content['episode'][0] .= '
	
			'.$episode0_after;
			
			
//echo(".e1070");
			

	include('co_comments.php');
	
	if (isset($comment['html'])) {
		$content['episode'][0] .= $comment['html'];
	}
	$content['script'][]['uri'] = "./extend_player.js";
	$content['script'][]['uri'] = "./deep_link.js";



?>
