function string_to_seconds(s) {
	var returnvalue = 0;
	if (s.match(/^\s*(\d\d?):(\d\d):(\d\d)\s*$/)) {
		returnvalue= (parseInt(RegExp.$1)*3600 + parseInt(RegExp.$2)*60 + parseInt(RegExp.$3));
	} else if (s.match(/^\s*(\d\d):(\d\d)\s*$/)) {
		returnvalue= (parseInt(RegExp.$1)*60 + parseInt(RegExp.$2));
	} else {
		returnvalue= parseInt(s);
	}
	if (isNaN(returnvalue)) { returnvalue = 0; }
	return returnvalue;
}

function check_deeplink() {
	if (window.location.hash) {
	if (window.location.hash.length > 1) {
	//#t=12456,15:30
	if (window.location.hash.match(/(^|#|&)t=(\d\d?:\d\d:\d\d|\d\d:\d\d|\d+)?(,(\d\d?:\d\d:\d\d|\d\d:\d\d|\d+))?($|&)/)) {
		if (document.getElementsByClassName("tellingink_audioplayer").length > 0) {
			var player_element = document.getElementsByClassName("tellingink_audioplayer")[0];
			var starttime = RegExp.$2;
			var stoptime = RegExp.$4;
			starttime = string_to_seconds(starttime);
			stoptime = string_to_seconds(stoptime);
			
			
			if(starttime>stoptime) {
				stoptime = player_element.duration;
			}
			
			
			player_element.currentTime = starttime;
			player_element.play();
			
			global_stoptime = stoptime;
			player_element.addEventListener("timeupdate",function(){
				if (this.currentTime >= global_stoptime) {
					this.pause();
					global_stoptime = this.duration;
				}
			});
		}
	}
	}
	}
}





window.addEventListener("load",function() { check_deeplink(); });
