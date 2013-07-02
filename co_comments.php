<?php
	//echo(2);
	if(!isset($comment['ttext']) && isset($content['title'])) {
		$comment['ttext'] = $content['title'];
	}
	
	$comment['html'] = '';
	
	if(isset($comment['ttype'])) {
	if(isset($comment['target'])) {
	if(isset($comment['ttext'])) {
		if(isset($_POST['comment_submitted'])) {
			//a comment was submittet, lets validate its data and save it...
			$comment_is_spam = false;
			if (strlen($_POST['email'])>1)        { $comment_is_spam = true; }
			if (strlen($_POST['emoil'])>1)        { $comment_is_spam = true; }
			if (strlen($_POST['website'])>1)      { $comment_is_spam = true; }
			if (strlen($_POST['comment'])>1) 	  { $comment_is_spam = true; }
			if (isset($_POST['hidden']) && (int)$_POST['hidden']) { $comment_is_hidden = 1; } else { $comment_is_hidden = 0; }
			
			$sql_query = "INSERT INTO `comments` (`username`, `email`, `content`, `date`, `targettype`, `target`, `is_spam`, `is_hidden`) VALUES ('".mysql_real_escape_string($_POST['username'])."', '".mysql_real_escape_string($_POST['emowl'])."', '".mysql_real_escape_string($_POST['comon'])."', now(), '".mysql_real_escape_string($_POST['ttype'])."', '".mysql_real_escape_string($_POST['target'])."', '".($comment_is_spam?"1":"0")."', '".$comment_is_hidden."');";
			mysql_query($sql_query);
		}
		$comment['html'] .= '
		<h4>Kommentiere '.$comment['ttext'].'</h4>
		<form action="'.$_SERVER["SCRIPT_URI"]."?".urlencode($_SERVER["QUERY_STRING"]).'" method="post" id="comment_form">
			<input type="hidden" id="comment_ttype" name="ttype" value="'.$comment['ttype'].'" />
			<input type="hidden" id="comment_target" name="target" value="'.$comment['target'].'" />
			'.(isset($comment['hidden'])?'<input type="hidden" id="comment_hidden" name="hidden" value="'.$comment['hidden'].'" />':'').'
			<label for="comment_username">Name <span>(wird angezeigt)</span></label>
			<input type="text" id="comment_username" name="username" />
			<label for="comment_emowl">E-Mail<span> (wird gespeichert, optional)</span></label>
			<input type="text" id="comment_email" name="email" />
			<input type="text" id="comment_emoil" name="emoil" />
			<input type="text" id="comment_emowl" name="emowl" />
			<input type="text" id="comment_website" name="website" />
			<label for="comment_comon">Kommentar:</label>
			<textarea name="comment" id="comment_area"></textarea>
			<textarea name="comon" id="comment_comon"></textarea>
			<input type="submit" value="kommentieren" name="comment_submitted" />';
		$comment['html'] .= '
		</form>
		
		
		<h4>Kommentare zu '.$comment['ttext'].'</h4>';
		
		$sql_query = "SELECT username, content, date, is_spam FROM `comments` WHERE `targettype` = '".mysql_real_escape_string($comment['ttype'])."' AND `target` = '".mysql_real_escape_string($comment['target'])."' AND (`is_spam` < '1' OR (`date` > '".date("Y-m-d H:i:s",time()-60*60*24*5)."' AND `is_spam` > '0')) ORDER BY `date` LIMIT 300";




		$sql_result = mysql_query($sql_query);
		while ($row = mysql_fetch_object($sql_result)) {
			$comment['html'] .= '
		<div class="comment'.(($row->is_spam && strtotime($row->date) < time()-60)?" spamcomment":"").'">
			<div class="commenter">'.htmlspecialchars($row->username).'<br />'.$row->date.'</div>
			'.nl2br(htmlspecialchars($row->content)).'
		</div>';
		}
		
	}}}
		
?>
