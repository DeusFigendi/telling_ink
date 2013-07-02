<?php
	
	require_once('db_connect.php');
	
	function sql_query_get($my_sqlquery) {
		$return_string = "SELECT ".$my_sqlquery['field']." FROM `".$my_sqlquery['table']."` WHERE ";
		foreach ($my_sqlquery['where'] as $mykey => $myvalue) {
			$return_string .= " `".$mykey."` = '".$myvalue."' AND ";
		}
		$return_string .= " TRUE LIMIT 1;";
		$sql_result = mysql_query($return_string);
		$sql_object = mysql_fetch_array($sql_result);
		return $sql_object[$my_sqlquery['field']];
	}
	
	
	
	if (isset($_REQUEST['get'])) {
		$return_value = 'error';
		if ($_REQUEST['get'] == 'waveform') {
			$return_value = 'flatline.png';
			$sql_query['table'] = "episodes";
			$sql_query['field'] = "waveform";
			if(isset($_REQUEST['eid'])) {
				$sql_query['where']["eid"] = (int)$_REQUEST['eid'];
			}
		}
		
		if ((array_key_exists ('table' , $sql_query)) && (array_key_exists ('field' , $sql_query)) && (array_key_exists ('where' , $sql_query))) {
			$return_value_temp = sql_query_get($sql_query);
			if (strlen($return_value_temp) > 0) {
				$return_value = $return_value_temp;
			}
		}
		
		echo $return_value;
	} else {
		echo "Error: No valid query must be get";
	}

?>
