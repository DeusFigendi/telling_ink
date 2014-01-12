<?php

function get_flattrs() {
        $ch = curl_init();
 
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_URL, "https://api.flattr.com/rest/v2/users/deusfigendi/things");
         
        $content = curl_exec($ch);
        curl_close($ch);
        
        
        // Speicher das in den Cache
        return json_decode($content);
}

$flattr_object = get_flattrs();
foreach ($flattr_object as $flattrvalue) {
/*
 
	https?://www.talkingink.de/?(index\.php)?\??((b|e)=([^&]+))?
	
	http://www.talkingink.de/index.php?e=36
	http://www.talkingink.de/index.php?b=Alice%27s%20Abenteuer%20im%20Wunderland
	http://www.talkingink.de/index.php
	http://www.talkingink.de/
	http://www.talkingink.de
*/
	//$content['foot'] .= "\n<!--\n ".$flattrvalue->url." \n ";
	if (preg_match('@https?://w*\.?t(alk|ell)ingink.de/?(index\.php)?\??((b|e)=([^&]+))?@', $flattrvalue->url, $urlparts)) {
		//$content['foot'] .= "Dies ist ein Tellingink-Thing\n";
		//Dies ist ein Tellingink-Thing
		if (count($urlparts) <= 3) {
			//Mainpage/Project was flattred
			//$content['foot'] .= "Mainpage/Project was flattred\n";
		} elseif ($urlparts[4] == 'e') {
			//An episode was flattred
			//episodes number is $urlparts[5]
			//$content['foot'] .= "An episode was flattred\n";
			$sql_query = "UPDATE `deusf_audiobook`.`episodes` SET `flattrs` = '".(int)$flattrvalue->flattrs."' WHERE `episodes`.`eid` = ".(int)$urlparts[5]."; ";
			//$content['foot'] .= "\n $sql_query \n ";
			mysql_query($sql_query);
		} elseif ($urlparts[4] == 'b') {
			//A book was flattred
			//$content['foot'] .= "A book was flattred\n";
		} else {
			//Something else that is NOT a book, an episode or the project was flattred
			//$content['foot'] .= "Something else that is NOT a book, an episode or the project was flattred\n";
		}
	}
	//$content['foot'] .= "-->\n";
}

/*
 
array(11) {
  [0]=>
  object(stdClass)#4 (16) {
    ["type"]=>
    string(5) "thing"
    ["resource"]=>
    string(45) "https://api.flattr.com/rest/v2/things/1982853"
    ["link"]=>
    string(32) "https://flattr.com/thing/1982853"
    ["id"]=>
    int(1982853)
    ["url"]=>
    string(21) "http://talkingink.de/"
    ["flattrs"]=>
    int(1)
    ["flattrs_user_count"]=>
    string(1) "1"
    ["title"]=>
    string(14) "Die Nullnummer"
    ["description"]=>
    string(47) "Lesung des Kapitels Die Nullnummer aus Metabuch"
    ["tags"]=>
    array(6) {
      [0]=>
      string(9) "audiobook"
      [1]=>
      string(7) "podcast"
      [2]=>
      string(8) "hörbuch"
      [3]=>
      string(8) "hoerbuch"
      [4]=>
      string(11) "freecontent"
      [5]=>
      string(5) "audio"
    }
    ["language"]=>
    string(5) "de_DE"
    ["category"]=>
    string(5) "audio"
    ["created_at"]=>
    int(1379523480)
    ["owner"]=>
    object(stdClass)#3 (5) {
      ["type"]=>
      string(4) "user"
      ["resource"]=>
      string(48) "https://api.flattr.com/rest/v2/users/deusfigendi"
      ["link"]=>
      string(38) "https://flattr.com/profile/deusfigendi"
      ["id"]=>
      string(6) "MAx7gq"
      ["username"]=>
      string(11) "DeusFigendi"
    }
    ["hidden"]=>
    int(0)
    ["image"]=>
    string(62) "http://static1.flattr.net/thing/image/1/9/8/2/8/5/3/medium.png"
  }
  [1]=>
  object(stdClass)#5 (16) {
    ["type"]=>
    string(5) "thing"
    ["resource"]=>
    string(45) "https://api.flattr.com/rest/v2/things/1639259"
    ["link"]=>
    string(32) "https://flattr.com/thing/1639259"
    ["id"]=>
    int(1639259)
    ["url"]=>
    string(39) "http://www.talkingink.de/index.php?e=36"
    ["flattrs"]=>
    int(0)
    ["flattrs_user_count"]=>
    int(0)
    ["title"]=>
    string(14) "Die Nullnummer"
    ["description"]=>
    string(47) "Lesung des Kapitels Die Nullnummer aus Metabuch"
    ["tags"]=>
    array(6) {
      [0]=>
      string(9) "audiobook"
      [1]=>
      string(7) "podcast"
      [2]=>
      string(8) "hörbuch"
      [3]=>
      string(8) "hoerbuch"
      [4]=>
      string(11) "freecontent"
      [5]=>
      string(5) "audio"
    }
    ["language"]=>
    string(5) "de_DE"
    ["category"]=>
    string(5) "audio"
    ["created_at"]=>
    int(1373645513)
    ["owner"]=>
    object(stdClass)#6 (5) {
      ["type"]=>
      string(4) "user"
      ["resource"]=>
      string(48) "https://api.flattr.com/rest/v2/users/deusfigendi"
      ["link"]=>
      string(38) "https://flattr.com/profile/deusfigendi"
      ["id"]=>
      string(6) "MAx7gq"
      ["username"]=>
      string(11) "DeusFigendi"
    }
    ["hidden"]=>
    int(0)
    ["image"]=>
    string(62) "http://static4.flattr.net/thing/image/1/6/3/9/2/5/9/medium.png"
  }
  [2]=>
  object(stdClass)#7 (16) {
    ["type"]=>
    string(5) "thing"
    ["resource"]=>
    string(45) "https://api.flattr.com/rest/v2/things/1164931"
    ["link"]=>
    string(32) "https://flattr.com/thing/1164931"
    ["id"]=>
    int(1164931)
    ["url"]=>
    string(91) "http://deusf.ara.uberspace.de/audiobook/?action=episode&b=Alice%27s+Abenteuer+im+Wunderland"
    ["flattrs"]=>
    int(0)
    ["flattrs_user_count"]=>
    int(0)
    ["title"]=>
    string(31) "Alice's Abenteuer im Wunderland"
    ["description"]=>
    string(46) "Eine Lesung von Alice' Abenteuer im Wunderland"
    ["tags"]=>
    array(1) {
      [0]=>
      string(32) "alice audiobook hörbuch podcast"
    }
    ["language"]=>
    string(5) "de_DE"
    ["category"]=>
    string(5) "audio"
    ["created_at"]=>
    int(1362738720)
    ["owner"]=>
    object(stdClass)#8 (5) {
      ["type"]=>
      string(4) "user"
      ["resource"]=>
      string(48) "https://api.flattr.com/rest/v2/users/deusfigendi"
      ["link"]=>
      string(38) "https://flattr.com/profile/deusfigendi"
      ["id"]=>
      string(6) "MAx7gq"
      ["username"]=>
      string(11) "DeusFigendi"
    }
    ["hidden"]=>
    int(0)
    ["image"]=>
    string(62) "http://static4.flattr.net/thing/image/1/1/6/4/9/3/1/medium.png"
  }
  [3]=>
  object(stdClass)#9 (16) {
    ["type"]=>
    string(5) "thing"
    ["resource"]=>
    string(45) "https://api.flattr.com/rest/v2/things/1034644"
    ["link"]=>
    string(32) "https://flattr.com/thing/1034644"
    ["id"]=>
    int(1034644)
    ["url"]=>
    string(52) "http://deusf.ara.uberspace.de/wiki/doku.php?id=start"
    ["flattrs"]=>
    int(0)
    ["flattrs_user_count"]=>
    int(0)
    ["title"]=>
    string(35) "Diaspora für Benutzer - Hauptseite"
    ["description"]=>
    string(252) "**Diaspora für Benutzer** ist ein Wiki, welches das Diaspora-Netzwerk aus Sicht von Benutzern dokumentieren soll. Die Gefahren, die Technik in einfachen Worten, Tipps und Tricks sowie andere hilfreiche Inhalte.

Dies ist die Hauptseite des Projekts."
    ["tags"]=>
    array(4) {
      [0]=>
      string(8) "diaspora"
      [1]=>
      string(4) "wiki"
      [2]=>
      string(4) "doku"
      [3]=>
      string(13) "dokumentation"
    }
    ["language"]=>
    string(5) "de_DE"
    ["category"]=>
    string(4) "text"
    ["created_at"]=>
    int(1354031798)
    ["owner"]=>
    object(stdClass)#10 (5) {
      ["type"]=>
      string(4) "user"
      ["resource"]=>
      string(48) "https://api.flattr.com/rest/v2/users/deusfigendi"
      ["link"]=>
      string(38) "https://flattr.com/profile/deusfigendi"
      ["id"]=>
      string(6) "MAx7gq"
      ["username"]=>
      string(11) "DeusFigendi"
    }
    ["hidden"]=>
    int(0)
    ["image"]=>
    string(62) "http://static2.flattr.net/thing/image/1/0/3/4/6/4/4/medium.png"
  }
  [4]=>
  object(stdClass)#11 (16) {
    ["type"]=>
    string(5) "thing"
    ["resource"]=>
    string(45) "https://api.flattr.com/rest/v2/things/1032599"
    ["link"]=>
    string(32) "https://flattr.com/thing/1032599"
    ["id"]=>
    int(1032599)
    ["url"]=>
    string(60) "http://soundcloud.com/deus-figendi/hp-lovecraft-deus-figendi"
    ["flattrs"]=>
    int(0)
    ["flattrs_user_count"]=>
    int(0)
    ["title"]=>
    string(50) "HP Lovecraft & Deus Figendi & burning-mir - Sunset"
    ["description"]=>
    string(216) "Sunset by H.P. Lovecraft (public domain)
read by Deus Figendi
background-sound: ambient sounds 13.mp3 by burning-mir: http://www.freesound.org/people/burning-mir/sounds/117358/ (cc0)

This file is also cc0 and pd"
    ["tags"]=>
    array(2) {
      [0]=>
      string(10) "soundcloud"
      [1]=>
      string(5) "music"
    }
    ["language"]=>
    string(5) "en_GB"
    ["category"]=>
    string(5) "audio"
    ["created_at"]=>
    int(1353877534)
    ["owner"]=>
    object(stdClass)#12 (5) {
      ["type"]=>
      string(4) "user"
      ["resource"]=>
      string(48) "https://api.flattr.com/rest/v2/users/deusfigendi"
      ["link"]=>
      string(38) "https://flattr.com/profile/deusfigendi"
      ["id"]=>
      string(6) "MAx7gq"
      ["username"]=>
      string(11) "DeusFigendi"
    }
    ["hidden"]=>
    int(0)
    ["image"]=>
    string(62) "http://static2.flattr.net/thing/image/1/0/3/2/5/9/9/medium.png"
  }
  [5]=>
  object(stdClass)#13 (16) {
    ["type"]=>
    string(5) "thing"
    ["resource"]=>
    string(45) "https://api.flattr.com/rest/v2/things/1032598"
    ["link"]=>
    string(32) "https://flattr.com/thing/1032598"
    ["id"]=>
    int(1032598)
    ["url"]=>
    string(50) "http://soundcloud.com/deus-figendi/szene-01-sailin"
    ["flattrs"]=>
    int(0)
    ["flattrs_user_count"]=>
    int(0)
    ["title"]=>
    string(17) "Szene 01 - sailin"
    ["description"]=>
    string(506) "URL	Urheber	Lizenz	Titel	darin enthalten…
38017	rockdoctor	Cc-by	sea2.wav	
13793	Soarer	Cc-by	North Sea.wav	
15553	laurent	Cc-by	tie the boat.wav	
16796	pushtobreak	Cc-by-nc	Earth Wind Fire Water pack 1 » Wind1.aif	
17783	suonho	Cc-by	ELEMENTS_Pack2 » ELEMENTS_WATER_02_Phasin-bubbles.wav	
22508	gadzooks	Cc-by	carferry.wav	
24327	Charel Sytze	Cc-by	eighty containers 3.mp3	
44076	daveincamas	Cc-by	Big Metal Chain » BigChain.wav	
80079	Benboncan	Cc-by	Wind » Cotton Flapping.wav	
82271	ra_"
    ["tags"]=>
    array(2) {
      [0]=>
      string(10) "soundcloud"
      [1]=>
      string(5) "music"
    }
    ["language"]=>
    string(5) "en_GB"
    ["category"]=>
    string(5) "audio"
    ["created_at"]=>
    int(1353877534)
    ["owner"]=>
    object(stdClass)#14 (5) {
      ["type"]=>
      string(4) "user"
      ["resource"]=>
      string(48) "https://api.flattr.com/rest/v2/users/deusfigendi"
      ["link"]=>
      string(38) "https://flattr.com/profile/deusfigendi"
      ["id"]=>
      string(6) "MAx7gq"
      ["username"]=>
      string(11) "DeusFigendi"
    }
    ["hidden"]=>
    int(0)
    ["image"]=>
    string(62) "http://static4.flattr.net/thing/image/1/0/3/2/5/9/8/medium.png"
  }
  [6]=>
  object(stdClass)#15 (16) {
    ["type"]=>
    string(5) "thing"
    ["resource"]=>
    string(45) "https://api.flattr.com/rest/v2/things/1032597"
    ["link"]=>
    string(32) "https://flattr.com/thing/1032597"
    ["id"]=>
    int(1032597)
    ["url"]=>
    string(43) "http://soundcloud.com/deus-figendi/szene-02"
    ["flattrs"]=>
    int(0)
    ["flattrs_user_count"]=>
    int(0)
    ["title"]=>
    string(8) "Szene 02"
    ["description"]=>
    string(503) "[	Soundscapes » Pirate Ship at Bay.wav	by	CGEffex	](	http://www.freesound.org/people/roscoetoon/sounds/	93678	/) - [	Cc-by	](	http://creativecommons.org/licenses/by/3.0/	) 	
[	Storm » rbh thunder storm.wav	by	Rhumphries	](	http://www.freesound.org/people/roscoetoon/sounds/	2523	/) - [	Cc-by	](	http://creativecommons.org/licenses/by/3.0/	) 	
[	Storm » rbh thunder_03.wav	by	Rhumphries	](	http://www.freesound.org/people/roscoetoon/sounds/	2525	/) - [	Cc-by	](	http://creativecommons.org/licenses/b"
    ["tags"]=>
    array(2) {
      [0]=>
      string(10) "soundcloud"
      [1]=>
      string(5) "music"
    }
    ["language"]=>
    string(5) "en_GB"
    ["category"]=>
    string(5) "audio"
    ["created_at"]=>
    int(1353877533)
    ["owner"]=>
    object(stdClass)#16 (5) {
      ["type"]=>
      string(4) "user"
      ["resource"]=>
      string(48) "https://api.flattr.com/rest/v2/users/deusfigendi"
      ["link"]=>
      string(38) "https://flattr.com/profile/deusfigendi"
      ["id"]=>
      string(6) "MAx7gq"
      ["username"]=>
      string(11) "DeusFigendi"
    }
    ["hidden"]=>
    int(0)
    ["image"]=>
    string(62) "http://static3.flattr.net/thing/image/1/0/3/2/5/9/7/medium.png"
  }
  [7]=>
  object(stdClass)#17 (16) {
    ["type"]=>
    string(5) "thing"
    ["resource"]=>
    string(45) "https://api.flattr.com/rest/v2/things/1032596"
    ["link"]=>
    string(32) "https://flattr.com/thing/1032596"
    ["id"]=>
    int(1032596)
    ["url"]=>
    string(43) "http://soundcloud.com/deus-figendi/szene-03"
    ["flattrs"]=>
    int(0)
    ["flattrs_user_count"]=>
    int(0)
    ["title"]=>
    string(8) "Szene 03"
    ["description"]=>
    string(503) "[	Soundscapes » Pirate Ship at Bay.wav	by	CGEffex	](	http://www.freesound.org/people/roscoetoon/sounds/	93678	/) - [	Cc-by	](	http://creativecommons.org/licenses/by/3.0/	) 	
[	Storm » rbh thunder storm.wav	by	Rhumphries	](	http://www.freesound.org/people/roscoetoon/sounds/	2523	/) - [	Cc-by	](	http://creativecommons.org/licenses/by/3.0/	) 	
[	Storm » rbh thunder_03.wav	by	Rhumphries	](	http://www.freesound.org/people/roscoetoon/sounds/	2525	/) - [	Cc-by	](	http://creativecommons.org/licenses/b"
    ["tags"]=>
    array(2) {
      [0]=>
      string(10) "soundcloud"
      [1]=>
      string(5) "music"
    }
    ["language"]=>
    string(5) "en_GB"
    ["category"]=>
    string(5) "audio"
    ["created_at"]=>
    int(1353877533)
    ["owner"]=>
    object(stdClass)#18 (5) {
      ["type"]=>
      string(4) "user"
      ["resource"]=>
      string(48) "https://api.flattr.com/rest/v2/users/deusfigendi"
      ["link"]=>
      string(38) "https://flattr.com/profile/deusfigendi"
      ["id"]=>
      string(6) "MAx7gq"
      ["username"]=>
      string(11) "DeusFigendi"
    }
    ["hidden"]=>
    int(0)
    ["image"]=>
    string(62) "http://static2.flattr.net/thing/image/1/0/3/2/5/9/6/medium.png"
  }
  [8]=>
  object(stdClass)#19 (16) {
    ["type"]=>
    string(5) "thing"
    ["resource"]=>
    string(45) "https://api.flattr.com/rest/v2/things/1032595"
    ["link"]=>
    string(32) "https://flattr.com/thing/1032595"
    ["id"]=>
    int(1032595)
    ["url"]=>
    string(43) "http://soundcloud.com/deus-figendi/szene-04"
    ["flattrs"]=>
    int(0)
    ["flattrs_user_count"]=>
    int(0)
    ["title"]=>
    string(8) "Szene 04"
    ["description"]=>
    string(502) "[	Ambient Nature Soundscapes » oceanwavescrushing.wav	by	Luftrum	](	http://www.freesound.org/people/roscoetoon/sounds/	48412	/) - [	Cc-by	](	http://creativecommons.org/licenses/by/3.0/	) 
[	freesoundATcaixaforum » Zoo_ses2.wav	by	freesound	](	http://www.freesound.org/people/roscoetoon/sounds/	25272	/) - [	cc0	](	http://creativecommons.org/about/cc0	) 
[	Ambient-01_JungleHills.wav	by	larval1977	](	http://www.freesound.org/people/roscoetoon/sounds/	38487	/) - [	Cc-sampling+	](	http://creativecom"
    ["tags"]=>
    array(2) {
      [0]=>
      string(10) "soundcloud"
      [1]=>
      string(5) "music"
    }
    ["language"]=>
    string(5) "en_GB"
    ["category"]=>
    string(5) "audio"
    ["created_at"]=>
    int(1353877533)
    ["owner"]=>
    object(stdClass)#20 (5) {
      ["type"]=>
      string(4) "user"
      ["resource"]=>
      string(48) "https://api.flattr.com/rest/v2/users/deusfigendi"
      ["link"]=>
      string(38) "https://flattr.com/profile/deusfigendi"
      ["id"]=>
      string(6) "MAx7gq"
      ["username"]=>
      string(11) "DeusFigendi"
    }
    ["hidden"]=>
    int(0)
    ["image"]=>
    string(62) "http://static2.flattr.net/thing/image/1/0/3/2/5/9/5/medium.png"
  }
  [9]=>
  object(stdClass)#21 (16) {
    ["type"]=>
    string(5) "thing"
    ["resource"]=>
    string(45) "https://api.flattr.com/rest/v2/things/1032594"
    ["link"]=>
    string(32) "https://flattr.com/thing/1032594"
    ["id"]=>
    int(1032594)
    ["url"]=>
    string(43) "http://soundcloud.com/deus-figendi/szene-05"
    ["flattrs"]=>
    int(0)
    ["flattrs_user_count"]=>
    int(0)
    ["title"]=>
    string(8) "Szene 05"
    ["description"]=>
    string(502) "[	Ambient Nature Soundscapes » oceanwavescrushing.wav	by	Luftrum	](	http://www.freesound.org/people/roscoetoon/sounds/	48412	/) - [	Cc-by	](	http://creativecommons.org/licenses/by/3.0/	) 
[	freesoundATcaixaforum » Zoo_ses2.wav	by	freesound	](	http://www.freesound.org/people/roscoetoon/sounds/	25272	/) - [	cc0	](	http://creativecommons.org/about/cc0	) 
[	Ambient-01_JungleHills.wav	by	larval1977	](	http://www.freesound.org/people/roscoetoon/sounds/	38487	/) - [	Cc-sampling+	](	http://creativecom"
    ["tags"]=>
    array(2) {
      [0]=>
      string(10) "soundcloud"
      [1]=>
      string(5) "music"
    }
    ["language"]=>
    string(5) "en_GB"
    ["category"]=>
    string(5) "audio"
    ["created_at"]=>
    int(1353877533)
    ["owner"]=>
    object(stdClass)#22 (5) {
      ["type"]=>
      string(4) "user"
      ["resource"]=>
      string(48) "https://api.flattr.com/rest/v2/users/deusfigendi"
      ["link"]=>
      string(38) "https://flattr.com/profile/deusfigendi"
      ["id"]=>
      string(6) "MAx7gq"
      ["username"]=>
      string(11) "DeusFigendi"
    }
    ["hidden"]=>
    int(0)
    ["image"]=>
    string(62) "http://static2.flattr.net/thing/image/1/0/3/2/5/9/4/medium.png"
  }
  [10]=>
  object(stdClass)#23 (16) {
    ["type"]=>
    string(5) "thing"
    ["resource"]=>
    string(45) "https://api.flattr.com/rest/v2/things/1032593"
    ["link"]=>
    string(32) "https://flattr.com/thing/1032593"
    ["id"]=>
    int(1032593)
    ["url"]=>
    string(56) "http://soundcloud.com/deus-figendi/nathalies-liebesbrief"
    ["flattrs"]=>
    int(0)
    ["flattrs_user_count"]=>
    int(0)
    ["title"]=>
    string(21) "Nathalies Liebesbrief"
    ["description"]=>
    string(135) "Nathalie schrieb einen "Liebesbrief an Diaspora" https://pod.geraspora.de/posts/cec31055e1d9c33b dies ist eine Vertonung dieses Briefs."
    ["tags"]=>
    array(2) {
      [0]=>
      string(10) "soundcloud"
      [1]=>
      string(5) "music"
    }
    ["language"]=>
    string(5) "en_GB"
    ["category"]=>
    string(5) "audio"
    ["created_at"]=>
    int(1353877533)
    ["owner"]=>
    object(stdClass)#24 (5) {
      ["type"]=>
      string(4) "user"
      ["resource"]=>
      string(48) "https://api.flattr.com/rest/v2/users/deusfigendi"
      ["link"]=>
      string(38) "https://flattr.com/profile/deusfigendi"
      ["id"]=>
      string(6) "MAx7gq"
      ["username"]=>
      string(11) "DeusFigendi"
    }
    ["hidden"]=>
    int(0)
    ["image"]=>
    string(62) "http://static2.flattr.net/thing/image/1/0/3/2/5/9/3/medium.png"
  }
}
 
*/

//$content['nav'] = "<strong>#####</strong>".$content['nav'];

?>
