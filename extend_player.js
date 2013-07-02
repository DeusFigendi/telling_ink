tellingink_baseurl = ".";

function seconds2timelabel(i) {
		var my_hours = Math.floor(i / 3600);
		var my_minutes = Math.floor((i % 3600) / 60);
		if (my_minutes < 10) { my_minutes = "0"+my_minutes; }
		var my_seconds = Math.round(i % 60);
		if (my_seconds < 10) { my_seconds = "0"+my_seconds; }
		
		return (my_hours?my_hours+":":"")+my_minutes+":"+my_seconds;
	
}

function extend_player() {
	var player_container = document.createElement("div");
	var player_play = document.createElement("img");
	var player_player = document.getElementsByClassName("tellingink_audioplayer")[0];
	if (document.getElementsByClassName("audioplayer_waveform").length > 0) {
		var player_waveform = document.getElementsByClassName("audioplayer_waveform")[0];
	} else {
		var player_waveform = document.createElement("img");
	}
	var player_seekbar = document.createElement("div");
	var player_volume = document.createElement("div");
	var player_timelabel = document.createElement("div");
	var player_timelabel2 = document.createElement("div");
	//player_container.style.width = "100%";	
	player_container.style.borderRadius = "2em";
	player_container.style.borderColor = "rgba(0,0,0,0.1)";
	player_container.style.borderStyle = "solid";
	player_container.style.height = "74px";
	player_container.style.position = "relative";
	player_container.style.width = "370px";
	player_container.style.clear = "both";
	
	player_play.id = "play_button";
	player_play.src = tellingink_baseurl+"/images/btn_play1.png";
	player_play.style.position = "absolute";
	player_play.style.top = "5px";
	player_play.style.left = "5px";
	player_play.style.cursor = "pointer";
	player_play.addEventListener("click",function() {
		if (document.getElementById("audio_element").paused) {
			document.getElementById("audio_element").play();
		} else {
			document.getElementById("audio_element").pause();
		}
	});
	
	//player_waveform.src = "http://www.talkingink.de/testwave.php";
	player_waveform.id = "player_waveform";	
	player_waveform.style.height = "30px";
	player_waveform.style.width = "227px";
	player_waveform.style.position = "absolute";
	player_waveform.style.top = "0px";
	player_waveform.style.left = "75px";
		
	player_seekbar.id = "player_seekbar";
	player_seekbar.style.backgroundImage = "url("+tellingink_baseurl+"/images/bg_seekbar.png)";
	player_seekbar.style.backgroundPosition = "center "+(Math.round(1+ 99 * player_player.currentTime / player_player.duration) * 20)+"px";	
	player_seekbar.style.height = "20px";
	player_seekbar.style.width = "256px";
	player_seekbar.style.position = "absolute";
	player_seekbar.style.top = "29px";
	player_seekbar.style.left = "65px";
	player_seekbar.style.cursor = "pointer";
	player_seekbar.addEventListener("click",function(e) {
		var x_pos = 0.5;
		if (e.layerX) { x_pos = e.layerX;
		} else if (e.x) { x_pos = e.x;
		} else if (e.offsetX) { x_pos = e.offsetX; }
		x_pos = x_pos-14;
		var x_percent = parseFloat(x_pos / 227);
		
		document.getElementById("audio_element").currentTime = x_percent * document.getElementById("audio_element").duration;
	});
	player_seekbar.addEventListener("mousemove",function(e) {
		if (e.buttons || (e.buttons === undefined && e.which)) {
			var x_pos = 0.5;
			if (e.layerX) { x_pos = e.layerX;
			} else if (e.x) { x_pos = e.x;
			} else if (e.offsetX) { x_pos = e.offsetX; }
			x_pos = x_pos-14;
			var x_percent = parseFloat(x_pos / 227);
			
			//this.backgroundPosition = "center "+(100.0 * Math.round(x_percent) * 20)+"px";
			document.getElementById("audio_element").currentTime = x_percent * document.getElementById("audio_element").duration;
		}
	});
	
	player_volume.id = "volume_control";
	player_volume.style.backgroundImage = "url("+tellingink_baseurl+"/images/bg_volume.png)";
	player_volume.style.backgroundPosition = Math.round(1000.0 - player_player.volume * 980.0)+"px center";
	player_volume.style.width = "20px";
	player_volume.style.height = "64px";
	player_volume.style.display = "inline-block";
	player_volume.style.position = "absolute";
	player_volume.style.top = "5px";
	player_volume.style.left = "325px";
	player_volume.style.cursor = "pointer";
	player_volume.addEventListener("click",function(e) {
		var y_pos = 0.5;
		if (e.layerY) { y_pos = e.layerY;
		} else if (e.y) { y_pos = e.y;
		} else if (e.offsetY) { y_pos = e.offsetY; }
		document.getElementById("audio_element").volume = 1 - y_pos / 64.0;
	});
	player_volume.addEventListener("mousemove",function(e) {
		if (e.buttons || (e.buttons === undefined && e.which)) {
			var y_pos = 0.5;
			if (e.layerY) { y_pos = e.layerY;
			} else if (e.y) { y_pos = e.y;
			} else if (e.offsetY) { y_pos = e.offsetY; }
			document.getElementById("audio_element").volume = 1 - e.layerY / 64.0;
		}
	});
	
	player_player.parentNode.insertBefore(player_container,player_player);
	
	player_timelabel.id = "player_timelabel";
	player_timelabel.style.display = "inline-block";
	player_timelabel.style.position = "absolute";
	player_timelabel.style.top = "5px";
	player_timelabel.style.right = "70px";
	player_timelabel.style.fontFamily = 'Conv_Denise_Handwriting';
	player_timelabel.style.fontWeight = 'bold';
	
	player_timelabel2.id = "player_timelabel2";
	player_timelabel2.style.display = "inline-block";
	player_timelabel2.style.position = "absolute";
	player_timelabel2.style.bottom = "5px";
	player_timelabel2.style.right = "70px";
	player_timelabel2.style.fontFamily = 'Conv_Denise_Handwriting';
	player_timelabel2.style.fontWeight = 'bold';
	player_timelabel2.appendChild(document.createTextNode(seconds2timelabel(player_player.duration)));
	
	player_container.appendChild(player_play);
	player_container.appendChild(player_waveform);
	player_container.appendChild(player_seekbar);
	player_container.appendChild(player_timelabel);
	player_container.appendChild(player_timelabel2);
	player_container.appendChild(player_volume);
	
	player_player.id = "audio_element";
	player_player.addEventListener("timeupdate",function() {
		document.getElementById("player_seekbar").style.backgroundPosition = "center "+(Math.round(1+ 99.0 * this.currentTime / this.duration) * 20)+"px";
		document.getElementById("player_timelabel").innerHTML = seconds2timelabel(this.currentTime);
	});
	player_player.addEventListener("play",function() {
		document.getElementById("play_button").src = tellingink_baseurl+"/images/btn_play2.gif";
	});
	player_player.addEventListener("pause",function() {
		document.getElementById("play_button").src = tellingink_baseurl+"/images/btn_play3.gif";
	});
	player_player.addEventListener("volumechange",function() {
		document.getElementById("volume_control").style.backgroundPosition = Math.round(1000.0-(this.volume * 980.0))+"px center";
	});
	player_player.addEventListener("loadeddata",function() {
		document.getElementById("player_timelabel2").innerHTML = seconds2timelabel(this.duration);
	});
	player_player.addEventListener("loadedmetadata",function() {
		document.getElementById("player_timelabel2").innerHTML = seconds2timelabel(this.duration);
	});
	player_player.addEventListener("loadstart",function() {
		//loading has started... a good point to get the waveform XD
		if (this.title) {
			if (this.title.match(/eid_(\d+)/)) {
				var this_eid = parseInt(RegExp.$1);
				
				var waveform_updater = new XMLHttpRequest();
				waveform_updater.open("get","api.php?get=waveform&eid="+this_eid,true);
				waveform_updater.addEventListener("readystatechange", function () {
					if (waveform_updater.readyState == 4) {
						if(waveform_updater.status == 200) {
							document.getElementById("player_waveform").src = "./images/"+waveform_updater.responseText;
						}
					}
				});
				waveform_updater.send(null);
			}
		}
	});
	player_player.addEventListener("durationchange",function() {
		document.getElementById("player_timelabel2").innerHTML = seconds2timelabel(this.duration);
	});
	
	player_player.style.display = "none";
	
	
}

if (!window.opera) {
	window.addEventListener("load",function() { extend_player(); });
	
} else {
	//this is opera, because of massive trouble setting volume, getting
	//volume-change-events, detecting mouseposition and the like. Just
	//let operas own player do it. It does its job pretty fine.
}
