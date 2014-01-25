<?php
//echo(".cn2");

/*
 * <p>Checking for Die Tribute von Panem - Toedliche Spiele - Die Tribute - Kapitel 5.ogg (vorbis)</p><p>Audiofile found</p><p>Checking for Die Tribute von Panem - Toedliche Spiele - Die Tribute - Kapitel 5.png (image)</p><p>Image found</p><p>Checking for Die Tribute von Panem - Toedliche Spiele - Die Tribute - Kapitel 5_low.mp3 (mp3)</p><p>Audiofile found</p><p>Checking for Die Tribute von Panem - Toedliche Spiele - Die Tribute - Kapitel 5.waveform.png (waveform)</p><p>waveformImage found</p><p>Checking for Die Tribute von Panem - Toedliche Spiele - Die Tribute - Kapitel 5.json (descr)</p><p>Processfile found</p><p>All files are there, start processing...</p>
 */


 error_reporting(E_ALL);
//echo(".index5");
ini_set ('display_errors', true);


require_once('db_connect.php');



require_once ('db_functions.php');

$rdc_debug = true;




// Get a list of json-files...
	$json_files = glob("./TEMP/*.json");
	
	foreach ($json_files as $json_filename) {
		$json_string = file_get_contents($json_filename);
		$json_object = json_decode($json_string);
		
		//okay, opened and decoded the file, lets check if all files are there wich should be...
		foreach ($json_object -> output_files as $output_file) {
			$this_cast_okay = true;
			if ($rdc_debug) { echo("<p>Checking for ".$output_file -> filename." (".$output_file -> format.")</p>"); }
			if (file_exists("./TEMP/".$output_file -> filename)) {
				if ($output_file -> format == "vorbis") { echo ("<p>Audiofile found</p>");
				} else if ($output_file -> format == "vorbis"  ) { if ($rdc_debug) { echo ("<p>Audiofile found</p>"); }
				} else if ($output_file -> format == "mp3"     ) { if ($rdc_debug) { echo ("<p>Audiofile found</p>"); }
				} else if ($output_file -> format == "mp3-vbr" ) { if ($rdc_debug) { echo ("<p>Audiofile found</p>"); }
				} else if ($output_file -> format == "mp4"     ) { if ($rdc_debug) { echo ("<p>Audiofile found</p>"); }
				} else if ($output_file -> format == "aac"     ) { if ($rdc_debug) { echo ("<p>Audiofile found</p>"); }
				} else if ($output_file -> format == "descr"   ) { if ($rdc_debug) { echo ("<p>Processfile found</p>"); }
				} else if ($output_file -> format == "psc"     ) { if ($rdc_debug) { echo ("<p>Chapterfile found</p>"); }
				} else if ($output_file -> format == "chaps"   ) { if ($rdc_debug) { echo ("<p>Chapterfile found</p>"); }
				} else if ($output_file -> format == "image"   ) { if ($rdc_debug) { echo ("<p>Image found</p>"); }
				} else if ($output_file -> format == "waveform") { if ($rdc_debug) { echo ("<p>waveformImage found</p>"); }
				} else                                           { if ($rdc_debug) { echo ("<p>Unknown file-type found</p>"); } }
			} else {
				if ($rdc_debug) { echo ("<p>File not found...</p>"); }
				$this_cast_okay = false;
			}
		}
		if ($json_object -> error_status != NULL) {
			$this_cast_okay = false;
		}
		if ($this_cast_okay) {
			$database_entry = Array();
			if ($rdc_debug) { echo("<p>All files are there, start processing...</p>"); }
			$database_entry['aid'] = $json_object -> uuid;
			$database_entry['title'] = $json_object -> metadata -> title;
			$database_entry['album'] = $json_object -> metadata -> album;
			$database_entry['artists'] = explode(",",$json_object -> metadata -> artist);
			$database_entry['track'] = (int)$json_object -> metadata -> track;
			$database_entry['summary'] = $json_object -> metadata -> summary;
			$database_entry['link'] = Array();
			$database_entry['link'][] = $json_object -> metadata -> url;
			$database_entry['tags'] = $json_object -> metadata -> tags;
			$database_entry['date'] = strtotime($json_object -> creation_time);
			$database_entry['length'] = $json_object -> length;
			$database_entry['chapfile'] = "";
			$database_entry['waveform'] = "flatline.png";
			
			
			$database_entry['audiofiles'] = array();
			$temp_array = array();
			
			$format2mime = array(
				'aac' => 'audio/aac',
				'alac' => 'audio/mp4',
				'chaps' => 'text/plain',
				'descr' => 'application/json',
				'flac' => 'audio/flac',
				'image' => 'image/FILEEXTENSION',
				'input' => '*/*',
				'mp3' => 'audio/mpeg',
				'mp4' => 'audio/mpeg',
				'mp3-vbr' => 'audio/mpeg',
				'opus' => 'audio/opus',
				'psc' => 'application/xml',
				'vorbis' => 'audio/ogg',
				'wav' => 'audio/wav',
				'waveform' => 'image/png'
			);
			
			foreach ($json_object -> output_files as $output_file) {
				$temp_array = array();
				//the old way:
				/*
				if ($output_file -> format == "vorbis") {
					$database_entry['audiofiles'][0]['filename'] = $output_file -> filename;
					$database_entry['audiofiles'][0]['size'] = $output_file -> size;
					rename ( "./TEMP/".$output_file -> filename , "./audio/".$output_file -> filename );
				} else if ($output_file -> format == "mp3"   ) { 
					$database_entry['audiofiles'][1]['filename'] = $output_file -> filename;
					$database_entry['audiofiles'][1]['size'] = $output_file -> size;
					rename ( "./TEMP/".$output_file -> filename , "./audio/".$output_file -> filename );
				} else if ($output_file -> format == "descr" ) { 
					$database_entry['procfile'] = $output_file -> filename;
					rename ( "./TEMP/".$output_file -> filename , "./json/".$output_file -> filename );
				} else if ($output_file -> format == "image" ) {
					$database_entry['image'] = $output_file -> filename;
					rename ( "./TEMP/".$output_file -> filename , "./images/".$output_file -> filename );
				}
				*/				
				
				//the new way: Just create an array...				
				$temp_array['filename'] = $output_file -> filename;
				$temp_array['filesize'] = $output_file -> size;
				$temp_array['format'] = $output_file -> format.$output_file -> suffix;
				$temp_array['mimetype'] = $format2mime[$output_file -> format];
				$temp_array['mimetype'] = str_replace ( 'FILEEXTENSION' , $output_file -> ending , $temp_array['mimetype'] );
				
				$mime_base = explode('/',$temp_array['mimetype'])[0];
				
				if ($mime_base == 'audio') {
					//if its audio just add to audio-files... will handle later
					$database_entry['audiofiles'][] = $temp_array;
				} elseif ($mime_base == 'text') {
					//textfiles are usually chapterfiles... these aren't used at the moment
					rename ( "./TEMP/".$output_file -> filename , "./chapters/".$output_file -> filename );
				} elseif ($mime_base == 'application') {
					//application should be the processfile but MIGHT be a chapterfile... let's handle this as a processfile.
					if($output_file -> format == 'descr') {
						$database_entry['procfile'] = $output_file -> filename;
						rename ( "./TEMP/".$output_file -> filename , "./json/".$output_file -> filename );
					} elseif($output_file -> format == 'psc') {
						//this is a simple_chapter
						$database_entry['chapfile'] = $output_file -> filename;
						rename ( "./TEMP/".$output_file -> filename , "./chapters/".$output_file -> filename );
					}
				} elseif ($mime_base == 'image') {
					//shoulf be the episodes cover...
					if($output_file -> format == 'waveform') {
						$database_entry['waveform'] = $output_file -> filename;
						$waveform_filename_source = "./TEMP/".$output_file -> filename;
						$waveform_filename_target = "./images/".$output_file -> filename;
						include('ut_generate_waveform.php');
						rename ( "./TEMP/".$output_file -> filename , "./images/".$output_file -> filename.".original" );
					} else {
						$database_entry['image'] = $output_file -> filename;
						rename ( "./TEMP/".$output_file -> filename , "./images/".$output_file -> filename );
					}
				} else {
					rename ( "./TEMP/".$output_file -> filename , "./unknown_files/".$output_file -> filename );					
				}
			}
			
			
			if (preg_match("/^C(reative )?C(ommons )?-?(Attribution|by)[0-9\. ]*(Germany|de)/",$json_object -> metadata -> license)) {
				$database_entry['license'] = 1;
			} elseif (preg_match("/^C(reative )?C(ommons )?-?(Attribution|by)-?(NonCommercial|nc)-?(ShareAlike|sa)[0-9\. ]*(Germany|de)/",$json_object -> metadata -> license)) {
				$database_entry['license'] = 7;
			} elseif (preg_match("/^C(reative )?C(ommons )?-?(Attribution|by)-?(NonCommercial|nc)-?(NoDerivs|nd)[0-9\. ]*(Unported|$)/",$json_object -> metadata -> license)) {
				$database_entry['license'] = 16;
			} elseif (preg_match("/^C(reative )?C(ommons )?-?(zero|Zero|0) ?[0-9\. ]*(Unported|$)/",$json_object -> metadata -> license)) {
				$database_entry['license'] = 24;
			} else {
				$database_entry['license'] = 0;
			}
			
			/*
			$artist_ids = array();
			foreach ($database_entry['artists'] as $artist_name) {
				$sql_query = "SELECT arid FROM `artists` WHERE `name` LIKE '".mysql_real_escape_string($artist_name)."' LIMIT 1";

				$sql_result = mysql_query($sql_query);
				if (mysql_num_rows()) {
					$artist_ids[] = $sql_result -> arid;
				} else {
					$sql_query = "INSERT INTO `artists` (`name`) VALUES ('".mysql_real_escape_string($artist_name)."');";

					mysql_query($sql_query);
					$artist_ids[] = mysql_insert_id();
				}
			}
			*/
			
			$person_ids = array();
			foreach ($database_entry['artists'] as $artist_name) {
				$artist_name = trim($artist_name);
				//split artists name into first- last- and all other names…
				$artist_names[0] = trim(explode(" ",$artist_name)[0]);
				$artist_names[1] = trim(join(" ",array_slice(explode(" ",$artist_name),1,-1)));
				/*
				echo "\n\nartists name: '".$artist_name."'\n";
				echo "exploded: '".implode(";",explode(" ",$artist_name))."'\n";
				echo "sliced: '".implode(";",array_slice(explode(" ",$artist_name),-1))."'\n";
				*/
				$artist_names[2] = trim(implode("",array_slice(explode(" ",$artist_name),-1)));
				$artist_names[3] = trim($artist_name);
				
				$persons_id = person_get_id($artist_names,0);
				//$sql_query = "SELECT pid FROM `person` WHERE ( `name1` LIKE '".mysql_real_escape_string($artist_names[0])."' AND `name3` LIKE '".mysql_real_escape_string($artist_names[3])."' ) OR `nickname` LIKE '".mysql_real_escape_string($artist_names[4])."' LIMIT 1";
				//$sql_result = mysql_query($sql_query);
				
				if ($persons_id) {
					$person_ids[] = $persons_id;
				} else {
					$person_ids[] = person_add($artist_names);
				}
				
			}
			
			
			$link_ids = array();
			foreach ($database_entry['link'] as $link_url) {
				$link_ids[] = link_get_id($link_url,true);
			}
			
			$database_entry['is_hidden'] = 0;
			$database_entry['flattr'] = 1;
			
			$tag_ids = array();
			foreach ($database_entry['tags'] as $tag_text) {
				$tag_ids[] = tag_get_id($tag_text,true);
				if ($tag_text == "hidden") { $database_entry['is_hidden'] = 1; }
				if ($tag_text == "nocommercial") { $database_entry['flattr'] = 0; }
				if ($tag_text == "no commercial") { $database_entry['flattr'] = 0; }
				if ($tag_text == "nc") { $database_entry['flattr'] = 0; }
				if ($tag_text == "ccbync") { $database_entry['flattr'] = 0; }
				if ($tag_text == "ccbyncnd") { $database_entry['flattr'] = 0; }
				if ($tag_text == "ccbyncsa") { $database_entry['flattr'] = 0; }
				if ($tag_text == "cc-by-nc") { $database_entry['flattr'] = 0; }
				if ($tag_text == "cc-by-nc-nd") { $database_entry['flattr'] = 0; }
				if ($tag_text == "cc-by-nc-sa") { $database_entry['flattr'] = 0; }
				if ($tag_text == "noflattr") { $database_entry['flattr'] = 0; }
				if ($tag_text == "no flattr") { $database_entry['flattr'] = 0; }
				if ($tag_text == "guest") { $database_entry['flattr'] = 0; }
				if ($tag_text == "gast") { $database_entry['flattr'] = 0; }
				if ($tag_text == "nopayment") { $database_entry['flattr'] = 0; }
				if ($tag_text == "no payment") { $database_entry['flattr'] = 0; }
			}
			
			$new_episodes_id = episode_add ($database_entry['aid'],$database_entry['title'],$database_entry['album'],$person_ids,(int)$database_entry['track'],$database_entry['summary'],$link_ids,$tag_ids,$database_entry['date'],$database_entry['length'],$database_entry['audiofiles'],$database_entry['procfile'],$database_entry['image'],$database_entry['license'],$database_entry['is_hidden'],$database_entry['flattr'],$database_entry['chapfile'],$database_entry['waveform']);
			
			if ($rdc_debug) { echo("\n\n\n new_episodes_id: $new_episodes_id \n\n"); }
			
			if ($new_episodes_id >= 0) {
				foreach ($database_entry['audiofiles'] as $audiofile_array) {
					$audiofile_array['eid'] = $new_episodes_id;
					if (audiofile_add($audiofile_array) >= 0) {
						rename ( "./TEMP/".$audiofile_array['filename'] , "./audio/".$audiofile_array['filename'] );
					}
				}
			}
			
			
						
		}
		//var_dump($database_entry);
	}

?>
