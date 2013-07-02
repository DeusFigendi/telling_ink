<?php
	$db_conntection['server']     = 'localhost';
	$db_conntection['user']       = 'database_user';
	$db_conntection['password']   = 'database password'; 
	$db_conntection['db_name']    = 'database_name';
	$db_conntection['hidden_key'] = "key_to_view_secret_episodes";
	$db_conntection['hidden']     = false;
	if (isset($_REQUEST['show_hidden'])) {
		if ($_REQUEST['show_hidden'] == $db_conntection['hidden_key']) {
			$db_conntection['hidden'] = true;
			try { setcookie('show_hidden',$_COOKIE['show_hidden'], time()+60*60); } catch(Exeption $e) { /* nothing to do here, it's okay setting the cookie failed... */ }
		}
	}
	if (isset($_COOKIE['show_hidden'])) {
		if ($_COOKIE['show_hidden'] == $db_conntection['hidden_key']) {
			$db_conntection['hidden'] = true;
			try { setcookie('show_hidden',$_COOKIE['show_hidden'], time()+60*60*36); } catch(Exeption $e) { /* nothing to do here, it's okay setting the cookie failed... */ }
		}
	}

	mysql_connect($db_conntection['server'],$db_conntection['user'],$db_conntection['password']);
	mysql_select_db($db_conntection['db_name']);

?>
