<?php
	require_once('db_connect.php');
	$sql_query = "DELETE FROM `comments` WHERE (`is_spam` = '1' COLLATE utf8_bin) LIMIT 12;";
	mysql_query($sql_query);
?>
