<?php
require_once('db_connect.php');
	$content['books'] = '
			<ul class="handwritten">';
	$sql_query = "SELECT album FROM `episodes` ".($db_conntection['hidden']?" ":"WHERE `hidden` = '0'")." ;";
	$sql_result = mysql_query($sql_query);
	$book_shelf = array();
	while ($row = mysql_fetch_object($sql_result)) {
		if (!array_key_exists($row -> album,$book_shelf)) { $book_shelf[$row -> album] = 0; }
		$book_shelf[$row -> album]++;
	}
	foreach ($book_shelf as $book_title => $book_episodes) {
		$bookstyle = array();
		$bookstyle[0] = md5($book_title);
		$bookstyle[1] = hexdec(substr($bookstyle[0] , 0 , 2 )) / 255.0;
		$bookstyle[2] = hexdec(substr($bookstyle[0] , 2 , 2 )) / 255.0;
		$bookstyle[3] = hexdec(substr($bookstyle[0] , 4 , 2 )) / 255.0;
		$bookstyle[4] = hexdec(substr($bookstyle[0] , 6 , 2 )) / 255.0;
		/*
		$content['books'] .= '
				<li style="margin-left:'.($bookstyle[1]*2).'em; min-height:'.($book_episodes * 1.3).'em; max-width:'.(floor($bookstyle[2]*30)+120).'px; background-position:'.(floor($bookstyle[3]*5)*200).'px top;"><a href=".?action=episode&amp;b='.urlencode($book_title).'">'.html_entity_decode($book_title).'</a></li>';
		*/
		$content['books'] .= '
				<li><a href=".?action=episode&amp;b='.urlencode($book_title).'">'.html_entity_decode($book_title).'</a></li>';
	}
				/*
	$content['books'] .= '
				<li style="margin-left:'.(rand(0,10)*0.2).'em; max-width:'.(rand(0,30)+120).'px; background-position:'.(rand(0,4)*200).'px top;"><a href="./">Die Wittwe</a></li>
				<li style="margin-left:'.(rand(0,10)*0.2).'em; max-width:'.(rand(0,30)+120).'px; background-position:'.(rand(0,4)*200).'px top;"><a href="./">Die wilde Miß vom Ohio</a></li>
				<!--
				<li style="margin-left:'.(rand(0,10)*0.2).'em; max-width:'.(rand(0,30)+120).'px; background-position:'.(rand(0,4)*200).'px top;"><a href="./">Lumpenmüllers Lieschen</a></li>
				<li style="margin-left:'.(rand(0,10)*0.2).'em; max-width:'.(rand(0,30)+120).'px; background-position:'.(rand(0,4)*200).'px top;"><a href="./">Der Geisterseher II</a></li>
				<li style="margin-left:'.(rand(0,10)*0.2).'em; max-width:'.(rand(0,30)+120).'px; background-position:'.(rand(0,4)*200).'px top;"><a href="./">Apotheker B.</a></li>
				-->';
				*/
	/*
	for ($i = 0; $i < 10; $i++) {		
		switch ($i) {
			case 0:
				$book_title = 'ligula eget dolor. Aenean';
				break;
			case 1:
				$book_title = 'felis eu pede mollis';
				break;
			case 2:
				$book_title = 'dapibus';
				break;
			case 3:
				$book_title = 'ultricies nisi. Nam eget dui. Etiam rhoncus';
				break;
			case 4:
				$book_title = 'Donec vitae sapien ut libero';
				break;
			case 5:
				$book_title = 'Sed fringilla mauris sit amet';
				break;
		    default:
				$book_title = $i;
		}
		$bookstyle = array();
		$bookstyle[0] = md5($book_title);
		$bookstyle[1] = hexdec(substr($bookstyle[0] , 0 , 2 )) / 255.0;
		$bookstyle[2] = hexdec(substr($bookstyle[0] , 2 , 2 )) / 255.0;
		$bookstyle[3] = hexdec(substr($bookstyle[0] , 4 , 2 )) / 255.0;
		$bookstyle[4] = hexdec(substr($bookstyle[0] , 6 , 2 )) / 255.0;
		$content['books'] .= '
				<li style="margin-left:'.($bookstyle[1]*2).'em; min-height:'.($bookstyle[4] * 5 * 1.3).'em; max-width:'.(floor($bookstyle[2]*30)+120).'px; background-position:'.(floor($bookstyle[3]*5)*200).'px top;"><a href=".?action=episode&amp;b='.urlencode($book_title).'">'.html_entity_decode($book_title).'</a></li>';
	}
	*/
	$content['books'] .= '
			</ul>';

?>
