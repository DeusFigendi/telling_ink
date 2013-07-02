<?php
require_once('db_connect.php');

	function person_get_all ($pid_array,$sorttype) {
		$sql_query = "SELECT * FROM `person` WHERE `pid` = '".join("' OR `pid` = '",$pid_array)."' ORDER BY `".$sorttype."` LIMIT 30";
		$sql_result = mysql_query($sql_query);
		
		$return_array = array();
		while ($row = mysql_fetch_array($sql_result)) {
			$return_array[] = $row;
		}
		return $return_array;
	}

	function person_get_id ($search_array,$search_type = 0) {
		// search type means this:
		// 0 = search-array contains [0] = first name; [1] = mid names; [2] = last name; [3] = nickname;
		// 1 = search-array contains [0] = first name; [1] = last name;
		// 2 = search-array is a nickname-string
		// 3 = search-array is a webpage-url as kinda unique identifier
		//
		// returns false on error, 0 on no match and the persons ID on success
		
		if ($search_type == 0) {
			$sql_query = "SELECT pid FROM `person` WHERE ( `name1` LIKE '".mysql_real_escape_string($search_array[0])."' AND `name3` LIKE '".mysql_real_escape_string($search_array[2])."' ) OR `nickname` LIKE '".mysql_real_escape_string($search_array[3])."' LIMIT 1";
		} else if ($search_type == 1) {
			$sql_query = "SELECT pid FROM `person` WHERE ( `name1` LIKE '".mysql_real_escape_string($artist_names[0])."' AND `name3` LIKE '".mysql_real_escape_string($artist_names[1])."' ) LIMIT 1";
		} else if ($search_type == 2) {
			$sql_query = "SELECT pid FROM `person` WHERE `nickname` LIKE '".mysql_real_escape_string($search_array)."' LIMIT 1";
		} else if ($search_type == 3) {
			$sql_query = "SELECT pid FROM `person` WHERE (`website1` = '".mysql_real_escape_string($search_array)."' OR `website2` = '".mysql_real_escape_string($search_array)."' OR `website3` = '".mysql_real_escape_string($search_array)."') LIMIT 1";
		} else {
			return false;
		}
		$sql_result = mysql_query($sql_query);
		if (mysql_num_rows($sql_result)) {
			$sql_row = mysql_fetch_object($sql_result);
			return (int)$sql_row -> pid;
		} else {
			return 0;
		}
	}
	
	function person_add ($name_array,$profile_array = null) {
		// The length of the array tells what it contains:
		// 1 element or string: Nickname
		// 2 elements: first and last name
		// 3 elements: first name, last name, nickname
		// 4 elements: first name, mid names, last name, nickname
		//
		// profile-array might contain...
		// * URLs
		// * integer
		// * filenames...
		// if urls or filenames fileextension seems to be an image use it as an image. Otherwise as a webpage
		// integer counts as role-ids
		
		if (!is_array($name_array)) {
			$name_array = array($name_array);
		}
		
		if (count($name_array) >= 4) {
			//nothing to do here
		} else if (count($name_array) == 3) {
			$name_array[3] = $name_array[2];
			$name_array[2] = $name_array[1];
			$name_array[1] = '';
		} else if (count($name_array) == 2) {
			$name_array[2] = $name_array[1];
			$name_array[1] = '';
			$name_array[3] = '';
		} else if (count($name_array) == 1) {
			$name_array[3] = $name_array[0];
			$name_array[2] = '';
			$name_array[1] = '';
			$name_array[0] = '';
		}
		
		$image = "";
		$websites = array();
		$role = 0;
		
		if ($profile_array) {
			foreach ($profile_array as $profile_field) {
				if (preg_match("/^\d+$/",$profile_field)) { $role = (int)$profile_field;
				} else if (preg_match("/(png|jpe?g|gif|bmp)$/",$profile_field)) { $image = $profile_field;
				} else { $websites[] = $profile_field; }
			}
		}
		
		while (count($websites) < 3) { $websites[] = ""; }
		
		$sql_query = "INSERT INTO `person` (`name1`, `name2`, `name3`, `nickname`, `role`, `image`, `website1`, `website2`, `website3`) VALUES ('".mysql_real_escape_string($name_array[0])."', '".mysql_real_escape_string($name_array[1])."', '".mysql_real_escape_string($name_array[2])."', '".mysql_real_escape_string($name_array[3])."', '".(int)$role."',  '".mysql_real_escape_string($image)."', '".mysql_real_escape_string($websites[0])."', '".mysql_real_escape_string($websites[1])."',   '".mysql_real_escape_string($websites[2])."');";
		
		mysql_query($sql_query);
		return mysql_insert_id();
	}
	
	function link_get_id($links_url,$add_it = false) {
		//searches for a links url and returns its ID.
		//if not found and $add_it is set true it adds the link and returns the ID
		
		$sql_query = "SELECT lid FROM `links` WHERE `uri` = '".mysql_real_escape_string(trim($links_url))."' LIMIT 1";

		$sql_result = mysql_query($sql_query);
				if (mysql_num_rows($sql_result)) {
					$sql_row = mysql_fetch_object($sql_result);
					return $sql_row -> lid;
				} else if ($add_it) {
					$sql_query = "INSERT INTO `links` (`uri`) VALUES ('".mysql_real_escape_string(trim($links_url))."');";

					mysql_query($sql_query);
					return mysql_insert_id();
				} else {
					return 0;
				}
	}
	
	function tag_get_id($tags_url,$add_it = false) {
		//searches for a tag and returns its ID.
		//if not found and $add_it is set true it adds the tag and returns the ID
		
		$sql_query = "SELECT tid FROM `tags` WHERE `text` LIKE '".mysql_real_escape_string($tags_url)."' LIMIT 1";

		$sql_result = mysql_query($sql_query);
		if (mysql_num_rows($sql_result)) {
			return mysql_fetch_object($sql_result) -> tid;
		} else if ($add_it) {
			$sql_query = "INSERT INTO `tags` (`text`) VALUES ('".mysql_real_escape_string($tags_url)."');";
			mysql_query($sql_query);
			return mysql_insert_id();
		} else {
			return 0;
		}
	}
	
	function episode_add ($ep_aid,$ep_title,$ep_album,$ep_persons,$ep_trackno,$ep_summary,$ep_links,$ep_tags,$ep_date,$ep_length,$ep_audiofiles,$ep_procfile,$ep_imagefile,$ep_license,$ep_ishidden,$ep_flattr,$ep_chap,$ep_wave) {
		//echo ("\n\nepisode_add()\n\n");
		//This function adds an episode to the database...
		/*
		 * $ep_aid			string	auphonic-ID		(uuid conform string)
		 * $ep_title		string	the episode or chapters title
		 * $ep_album		string	the album or books title
		 * $ep_persons		array	related persons	(array of pids, integer)
		 * $ep_trackno		integer	track or chapters number
		 * $ep_summary		string	summary or other kinda text for the episode
		 * $ep_links		array	related links	(array of lids, integer)
		 * $ep_tags			array	related tags	(array of tids, integer)
		 * $ep_date			integer	recording date	(timestamp)
		 * $ep_length		float	length of the soundfiles in seconds
		 * $ep_audiofiles	array	audiofiles...
		 * $ep_audiofiles[0]				array	the vorbis-audio
		 * $ep_audiofiles[0]['filename']	string	the vorbis' filename
		 * $ep_audiofiles[0]['size']		string	the vorbis' filesize
		 * $ep_audiofiles[1]				array	the mp3-audio (similar to vorbis)
		 * $ep_procfile		string	auphonics processfile	(filename)
		 * $ep_imagefile	string	episodes cover	(filename of an imagefile)
		 * $ep_license		integer	license
		 * $ep_license = 0		unknown license
		 * $ep_license = 1		cc-by
		 * $ep_ishidden		integer	this episode hidden?	(0 or 1 as boolean)
		 * $ep_flattr		integer	payment allowed?		(0 or 1 as boolean)
		 * $ep_wave			string	episodes waveform		(filename of an imagefile)
		 * $ep_chap			string	episodes chapters		(filename of an xml-file)
		 */
		 
		if (!preg_match("/^\w{22}$/",$ep_aid)) { return -1; } //auphonics uuIDs seems to be a 22 Byte long string of random chars and digits
		if (is_array($ep_persons)) {
			//ep_persons is array, check if it contains integer only...
			foreach ($ep_persons as $ep_persons_id) {
				if (!is_int($ep_persons_id)) { return -4; } //maybe check if a non-int matches a person...
			}
		} else {
			return -4; //ep_persons is no array...
		}
		if (!preg_match("/^\d+$/",$ep_trackno)) { return -5; } //tracknumbers are integer
		if (is_array($ep_links)) {
			//ep_links is array, check if it contains integer only...
			foreach ($ep_links as $ep_link_id) {
				if (!preg_match("/^\d+$/",$ep_link_id)) { return -7; } //maybe check if a non-int matches a link...
			}
		} else {
			return -7; //ep_links is no array...
		}
		if (is_array($ep_tags)) {
			//ep_tags is array, check if it contains integer only...
			foreach ($ep_tags as $ep_tag_id) {
				if (!preg_match("/^\d+$/",$ep_tag_id)) { return -8; } //maybe check if a non-int matches a tag...
			}
		} else {
			return -8; //ep_tags is no array...
		}
		
		if (!preg_match("/^\d+$/",$ep_date)) { return -9; } //timestamps are integer... maybe accept date-strings?
		
		
		if (!preg_match("/^\d+\.?\d*$/",$ep_length)) { return -10; } //lengths are floatingpoint
		
		//no longer used...
		/*
		if (is_array($ep_audiofiles)) {
			//ep_audiofiles is an array lets check if they are correct ones...
			foreach ($ep_audiofiles as $ep_audiofile) {
				if (is_array($ep_audiofile) && array_key_exists('filename',$ep_audiofile) && array_key_exists('size',$ep_audiofile) ) {
					//everything's fine
				} else {
					return -11; //sub array does not contain filename and size as keys or element is no array...
				}
			}
		} else {
			return -11; //ep_audiofiles is no array
		}
		*/
		
		//echo ("\n\n\n '".$ep_imagefile."'\n");
		if (!preg_match("/.*(png|jpe?g|gif|bmp)$/",$ep_imagefile)) { return -12; /*echo (" is no image\n\n\n\n");*/ } //string seems not to be an imagefile...
		
		if (!preg_match("/^\d+$/",$ep_license)) { return -13; } //license should be integer
		
		
		// Okay, as far as possible without touching the database the given
		// data is verified... it's in general okay to have empty arrays for
		// persons and tags and links and the like... in that case here will
		// no be inserted and that has to be done elsewhere.
		
		//$sql_query = "INSERT INTO `episodes` (`aid`, `title`, `album`, `track`, `summary`, `license`, `audiodate`, `length`, `audiofile0`, `audiosize0`, `audiofile1`, `audiosize1`, `procfile`, `image`) VALUES ('".mysql_real_escape_string($ep_aid)."', '".mysql_real_escape_string($ep_title)."', '".mysql_real_escape_string($ep_album)."', '".(int)$ep_trackno."', '".mysql_real_escape_string($ep_summary)."', '".(int)$ep_license."', '".date("Y-m-d h:i:s",$ep_date)."', '".(1 * $ep_length)."', '".mysql_real_escape_string($ep_audiofiles[0]['filename'])."', '".(int)$ep_audiofiles[0]['size']."', '".mysql_real_escape_string($ep_audiofiles[1]['filename'])."', '".(int)$ep_audiofiles[1]['size']."', '".mysql_real_escape_string($ep_procfile)."', '".mysql_real_escape_string($ep_imagefile)."');";
		$sql_query = "INSERT INTO `episodes` (`aid`, `title`, `album`, `track`, `summary`, `license`, `audiodate`, `length`, `audiofile0`, `audiosize0`, `audiofile1`, `audiosize1`, `procfile`, `image`, `hidden`, `payment`, `waveform`, `chapter` ) VALUES ('".mysql_real_escape_string($ep_aid)."', '".mysql_real_escape_string($ep_title)."', '".mysql_real_escape_string($ep_album)."', '".(int)$ep_trackno."', '".mysql_real_escape_string($ep_summary)."', '".(int)$ep_license."', '".date("Y-m-d H:i:s",$ep_date)."', '".(1 * $ep_length)."', '-', '0', '-', '0', '".mysql_real_escape_string($ep_procfile)."', '".mysql_real_escape_string($ep_imagefile)."', '".(int)$ep_ishidden."', '".(int)$ep_flattr."', '".mysql_real_escape_string($ep_wave)."', '".mysql_real_escape_string($ep_chap)."' );";
	
	
				/*
		 * $ep_aid			string	auphonic-ID		(uuid conform string)
		 * $ep_title		string	the episode or chapters title
		 * $ep_album		string	the album or books title
		 * $ep_persons		array	related persons	(array of pids, integer)
		 * $ep_trackno		integer	track or chapters number
		 * $ep_summary		string	summary or other kinda text for the episode
		 * $ep_links		array	related links	(array of lids, integer)
		 * $ep_tags			array	related tags	(array of tids, integer)
		 * $ep_date			integer	recording date	(timestamp)
		 * ep_length
		 * $ep_audiofiles	array	audiofiles...
		 * $ep_audiofiles[0]				array	the vorbis-audio
		 * $ep_audiofiles[0]['filename']	string	the vorbis' filename
		 * $ep_audiofiles[0]['size']		string	the vorbis' filesize
		 * $ep_audiofiles[1]				array	the mp3-audio (similar to vorbis)
		 * $ep_procfile		string	auphonics processfile	(filename)
		 * $ep_imagefile	string	episodes cover	(filename of an imagefile)
		 * $ep_license		integer	license
		 * $ep_license = 0		unknown license
		 * $ep_license = 1		cc-by
		 * $ep_ishidden		this episode hidden?
		 * 
		 * */
		 
		mysql_query($sql_query);
		$episode_id = mysql_insert_id();
		
		$sql_error = mysql_errno();
		if ($sql_error) { return (-1 * $sql_error); }
		
			
			
		foreach ($ep_persons as $artist_id) {
			$sql_query = "INSERT INTO `artistsXepisodes` (`arid`, `eid`) VALUES ('".$artist_id."', '".$episode_id."');";
			mysql_query($sql_query);
			
			$sql_error = mysql_errno();
			if ($sql_error) { return -4; }
		}


		foreach ($ep_links as $link_id) {
			$sql_query = "INSERT INTO `linksXepisodes` (`lid`, `eid`) VALUES ('".$link_id."', '".$episode_id."');";
			mysql_query($sql_query);
			
			$sql_error = mysql_errno();
			if ($sql_error) { return -7; }
		}

		foreach ($ep_tags as $tag_id) {
			$sql_query = "INSERT INTO `tagsXepisodes` (`tid`, `eid`) VALUES ('".$tag_id."', '".$episode_id."');";
			mysql_query($sql_query);
			
			$sql_error = mysql_errno();
			if ($sql_error) { return -8; }
		}
			
		return $episode_id;
			
	}


function audiofile_add($audio_array) {
	//check if all data is given...
	if (!array_key_exists('filename', $audio_array)) { return -10; }
	if (!array_key_exists('filesize', $audio_array)) { return -20; }
	if (!array_key_exists('format'  , $audio_array)) { return -30; }
	if (!array_key_exists('mimetype', $audio_array)) { return -40; }
	if (!array_key_exists('eid'     , $audio_array)) { return -50; }
	//might be more failure-returns like -21 for "filesize is no int" or
	//  -31 for "format is not known" or -41 for "mimetype is not audio"
	//             or -51 for "eid is no int" or -52 for "eid not found"
	$sql_query = "INSERT INTO `audiofiles` (`eid`, `filename`, `filesize`, `mimetype`, `format`) VALUES ('".(int)$audio_array['eid']."', '".mysql_real_escape_string($audio_array['filename'])."', '".(int)$audio_array['filesize']."', '".mysql_real_escape_string($audio_array['mimetype'])."', '".mysql_real_escape_string($audio_array['format'])."');";
	mysql_query($sql_query);
	return mysql_insert_id();				
}

function audiofiles_get ($eid,$format=false,$mime=false,$maxsize=false) {
	$sql_query = "SELECT * FROM `audiofiles` WHERE `eid` = '".(int)$eid."' ".($maxsize?"AND `filesize` <= '".(int)$maxsize."'":"")." ".($mime?"AND `mimetype` LIKE '".mysql_real_escape_string($mime)."'":"")." ".($format?"AND `format` LIKE '".mysql_real_escape_string($format)."'":"")." LIMIT 30";
	$sql_result = mysql_query($sql_query);
	$return_array = array();
	while ($row = mysql_fetch_array($sql_result)) {
		$return_array[] = $row;
	}
	return $return_array;
}

?>
