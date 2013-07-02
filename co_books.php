<?php
	$content['episode'][0] = "";
	$content['episode'][1] = "";
	$content['title'] = 'Alle Bücher';
	
	$content['episode'][0] .= '
	<h2>Alle Bücher:</h2>
	
	<table class="handwritten">
		<tr>
			<th>Buch</th>
			<th>Kapitel</th>
			<th>Lizenzen</th>
		</tr>';
		
	
	$sql_query = "SELECT album, license FROM `episodes` ".($db_conntection['hidden']?" ":"WHERE `hidden` = '0'")." ;";
	$sql_result = mysql_query($sql_query);
	$book_shelf = array();
	while ($row = mysql_fetch_object($sql_result)) {
		if (!array_key_exists($row -> album,$book_shelf)) { $book_shelf[$row -> album] = array("title" => "title","episodes" => 0,"licenses" => array()); }
		$book_shelf[$row -> album]['title'] = $row -> album;
		$book_shelf[$row -> album]['episodes']++;
		$book_shelf[$row -> album]['licenses'][(int)$row -> license] = true;
	}
	foreach ($book_shelf as $book_data) {
		$content['episode'][0] .= '
		<tr>
			<td><a href="./?b='.urlencode($book_data['title']).'">'.$book_data['title'].'</a></td>
			<td>'.$book_data['episodes'].'</td>
			<td>';
		if (count($book_data['licenses']) > 1) {
			$content['episode'][0] .= '
				<ul>';
		}
		foreach ($book_data['licenses'] as $key => $value) {
			if ($key == 0) {
				//alle Rechte vorbehalten
			} else if ($key == 1) {
				$content['episode'][0] .= '
				'.(count($book_data['licenses']) > 1?"<li>":"").'<a href="http://creativecommons.org/licenses/by/3.0/de/" title="Creative Commons Namensnennung 3.0 Deutschland Lizenz."><img src="./images/lic_ccby.svg" alt="ceative commons by" />'.(count($book_data['licenses']) > 1?"</li>":"");
			}
		}
		if (count($book_data['licenses']) > 1) {
			$content['episode'][0] .= '
				</ul>';
		}
		$content['episode'][0] .= '
			</td>
		</tr>';
	}
		
	$content['episode'][0] .= '
	</table>';
	
?>
