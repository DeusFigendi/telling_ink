<?php

function human_byte($byte) {
	if ($byte > 2000000000) { return (round($byte/(1024*1024*1024),2)."GiB"); }
	if ($byte > 2000000) { return (round($byte/(1024*1024),2)."MiB"); }
	if ($byte > 2000) { return (round($byte/(1024),2)."KiB"); }
	return (($byte)."Byte");
}


function abbr_text($s,$target_length) {
	$words = preg_split("/\s/",$s);
	$return_string1 = "";
	$return_string2 = "";
	$return_string3 = "";
	foreach ($words as $this_word) {
		if (preg_match ( "/\w/" , $this_word , $letters )) { $return_string1 .= $letters[0]; }
		if (preg_match ( "/[[:alpha:]]/" , $this_word , $letters )) { $return_string2 .= $letters[0]; }
		if (preg_match ( "/[[:upper:]]/" , $this_word , $letters )) { $return_string3 .= $letters[0]; }
	}
	if (strlen($return_string1) <= $target_length) { return $return_string1; }
	if (strlen($return_string2) <= $target_length) { return $return_string2; }
	if (strlen($return_string3) <= $target_length) { return $return_string3; }
	return substr($return_string3,0,$target_length);	
}

function fix_digits($i,$target_length) {
	$return_string = $i;
	while (strlen($return_string) < $target_length) { $return_string = "0".$return_string; }
	return $return_string;
}
