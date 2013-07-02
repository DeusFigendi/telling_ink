<?php
require_once('db_connect.php');

/*
if (isset ($_REQUEST['eid'])) {
	//use the episode number
} else {
	//no episode number chosen, check if book is given
	if (isset ($_REQUEST['book'])) {
		//find the last episode in the book
	} else {
		//no episode and no book given, check if there's an artist-search
		if (isset ($_REQUEST['artist'])) {
			//find the last episode of the artist
		} else {
			//okay, no episode-id no book no artist so maybe one or more tags?
			if (isset ($_REQUEST['tags'])) {
				//search for tags and find the latest episode that matches all tags
				
				//search for tag_ids...
				$sql_query = "SELECT * FROM `tags` WHERE ";
				foreach (split(",",$_REQUEST['tags']) as $single_tag) {
					$sql_query .= "`text` LIKE '".$single_tag."' OR ";
				}
				$sql_query .= "FALSE LIMIT 30";
			} else {
				 //* well... 
				 //* no episode given
				 //* no no book
				 //* no artist
				 //* no tags
				 //* 
				 //* maybe... a date?
				           
				
					if (isset ($_REQUEST['date'])) {
						//search for the episode next to the given date
						 preg_match("(\d{2,4})-?(\d?\d)-?(\d?\d)(T|\s)?(\d?\d):?(\d?\d):?(\d?\d)",$_REQUEST['date'],$time_array);
						 
						 $sql_query = "SELECT * FROM `episodes` WHERE `audiodate` >= '". date("Y-m-d h:i:s",mkdate($time_array[4],$time_array[5],$time_array[6],$time_array[1],$time_array[2],$time_array[0]))  ."' ORDER BY `audiodate` DESC LIMIT 1":
					} else {
						 //* no episode
						 //* no book
						 //* no artist
						 //* no tags
						 //* no date
						 //* 
						 //* hmm maybe erm...
						 //* well just the fucking last one!
						 
						 
						 $sql_query = "SELECT * FROM `episodes` WHERE `audiodate` <= NOW ORDER BY `audiodate` DESC LIMIT 1":
					}
			}
		}
	}
}
*/
if (isset ($_REQUEST['eid'])) {
	$sql_query = "SELECT * FROM `episodes` WHERE `eid` = '".(int)$_REQUEST['eid']."' LIMIT 1";
} else {
	$sql_query = "SELECT * FROM `episodes` WHERE TRUE ORDER BY `audiodate` DESC LIMIT 1";
}

$sql_result = mysql_query($sql_query);
$sql_row = mysql_fetch_array($sql_result);
