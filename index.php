<?php

//echo(".index3");
 error_reporting(E_ALL);
//echo(".index5");
ini_set ('display_errors', true);


//echo(".index9");



srand();
$content = array();
$content['header'] = "";
include ('co_navi1.php');
include ('co_navi2.php');
//echo(".index11");
if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'books' || $_REQUEST['action'] == 'blogroll' || $_REQUEST['action'] == 'contact' || $_REQUEST['action'] == 'privacy')) {
//echo(".index13");
	include ('co_'.$_REQUEST['action'].'.php');
} else {
//echo(".index15");
	include ('co_episode.php');
}



//echo(".index20");




include ('co_foot.php');
include ('co_random.php');

echo ('<!DOCTYPE html>
<html ');
if (array_key_exists('html_attributes',$content)) {
	foreach ($content['html_attributes'] as $key => $value) {
		echo ($key.'="'.$value.'" ');
	}
}
echo ('>
	<head ');
if (array_key_exists('head_attributes',$content)) {
	foreach ($content['head_attributes'] as $key => $value) {
		echo ($key.'="'.$value.'" ');
	}
}
echo ('>
		<title>'.(array_key_exists('title',$content)?$content['title']:'Telling Ink').'</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />');












if (array_key_exists('meta',$content)) {
	foreach ($content['meta'] as $content_meta) {
		echo ('
		<meta ');
		foreach ($content_meta['attributes'] as $key => $value) {
			echo ($key.'="'.$value.'" ');
		}
		echo ('/>');
	}
}





//echo(".index60");




echo ('		
		<link href="./style.css" rel="stylesheet" type="text/css"  />
		<link href="./screen.css" rel="stylesheet" type="text/css" media="screen" />
		<link href="./mobile.css" rel="stylesheet" type="text/css" media="handheld" />');
if (array_key_exists('style',$content)) {
	foreach ($content['style'] as $content_style) {
		echo ('
		<link href="'.$content_style['uri'].'" rel="stylesheet" type="text/css" />');
	}
}
echo ('		
		<link rel="alternate" type="application/rss+xml" title="RSS Feed" href="./feed.php?format=ogg" />
		'.($db_conntection['hidden']?'<link rel="alternate" type="application/rss+xml" title="RSS Feed" href="./feed.php?format=vorbis&amp;'.$db_conntection['hidden_key'].'" />':'<!-- hidden episodes -->').'
		<script type="text/javascript" src="./ut_functions.js"></script>');









if (array_key_exists('script',$content)) {
	foreach ($content['script'] as $content_script) {
		echo ('
		<script type="text/javascript" src="'.$content_script['uri'].'" ></script>');
	}
}
echo ('	
	</head>
	<body>
		<!-- <header>'.$content['header'].'</header> -->
		<!-- <img src="./images/banner1.gif" id="head_banner" alt="Banner" /> -->
		<h1 id="head_banner"></h1>
		<nav id="top_nav">'.$content['nav'].'</nav>
		<nav id="books_nav">'.$content['books'].'</nav>
		<article id="maincontent">'.$content['episode'][0].'</article>
		<aside id="metadata">'.$content['episode'][1].'</aside>
		<footer>'.$content['foot'].'</footer>
	</body>	
</html>');

//echo(".index100");

?>
