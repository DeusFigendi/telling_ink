function play_pressed(playbutton) {
	//find elements to change...
	var audioplayerelement		= document.getElementsByTagName("audio")[0];
	var coverimageelement		= document.getElementById("episode_cover");
	var albumtitleelement		= document.getElementsByClassName("book_title")[0];
	var episodetitleelement		= document.getElementsByClassName("episode_title")[0];
	var summaryelement			= document.getElementsByClassName("episode_summary")[0];
	
	//get data...
	var title_content = urldecode(playbutton.title);
	var episode_data = jsonParse(title_content);
		
	//set data...
	coverimageelement.src = "./images/"+episode_data.image;
	albumtitleelement.firstChild.data   = episode_data.album;
	episodetitleelement.firstChild.data = episode_data.title;
	audioplayerelement.title            =  "eid_"+episode_data.eid+";row_"+episode_data.row_no;
	while (summaryelement.children.length > 0) {
		summaryelement.removeChild(summaryelement.firstChild);
	}
	while (audioplayerelement.children.length > 0) {
		audioplayerelement.removeChild(audioplayerelement.firstChild);
	}
	
	var source_element = null;
	
	for (var i = 0; i < episode_data.audiofiles.length; i++) {
		source_element = document.createElement("source");
		source_element.src = "./audio/"+episode_data.audiofiles[i];
		audioplayerelement.appendChild(source_element);
	}
		
	
	//play from beginn...
	audioplayerelement.currentTime = 0;
	audioplayerelement.load();
	audioplayerelement.play();
	
}

window.addEventListener("load", function() {

	var audioplayerelement = document.getElementsByTagName("audio")[0];

	audioplayerelement.addEventListener("ended",function() {
		//okay, the song ended... lets check if there is a row-no given...
		if (this.title.match(/row_(\d+)/)) {
			var this_row_no = parseInt(RegExp.$1);
			//check if there's another
			var next_row = this_row_no+1;
			if(next_row < document.getElementsByClassName("playbutton").length) {
				play_pressed(document.getElementsByClassName("playbutton")[next_row]);
			}
		}
	});
});
/*
 
 //OLD VERSION WORKS IN FIREFOX:

function play_pressed(playbutton) {
	//find elements to change...
	var audioplayerelement		= document.getElementsByTagName("audio")[0];
	var coverimageelement		= document.getElementById("episode_cover");
	var albumtitleelement		= document.getElementsByClassName("book_title")[0];
	var episodetitleelement		= document.getElementsByClassName("episode_title")[0];
	var summaryelement			= document.getElementsByClassName("episode_summary")[0];
	
	//get data...
	var title_content = urldecode(playbutton.title);
	var episode_data = jsonParse(title_content);
	
	//audioplayerelement.removeEventListener("ended",function(){play_pressed(document.getElementsByClassName("playbutton")[Math.round(episode_data.row_no)])});
	//audioplayerelement.removeEventListener("ended",function(){play_pressed(document.getElementsByClassName("playbutton")[Math.round(episode_data.row_no+1)])});
	
	//set data...
	coverimageelement.src = "./images/"+episode_data.image;
	albumtitleelement.firstChild.data   = episode_data.album;
	episodetitleelement.firstChild.data = episode_data.title;
	audioplayerelement.title            =  "eid_"+episode_data.eid;
	while (summaryelement.children.length > 0) {
		summaryelement.removeChild(summaryelement.firstChild);
	}
	while (audioplayerelement.children.length > 0) {
		audioplayerelement.removeChild(audioplayerelement.firstChild);
	}
	
	var source_element = null;
	
	for (var i = 0; i < episode_data.audiofiles.length; i++) {
		source_element = document.createElement("source");
		source_element.src = "./audio/"+episode_data.audiofiles[i];
		audioplayerelement.appendChild(source_element);
	}
	
	//audioplayerelement.getElementsByTagName("source")[0].src = "./audio/"+episode_data.audiofile0;
	//audioplayerelement.getElementsByTagName("source")[1].src = "./audio/"+episode_data.audiofile1;
	
	
	//play from beginn...
	audioplayerelement.currentTime = 0;
	audioplayerelement.load();
	audioplayerelement.play();
	
	//register to play the next chapter after playing...
	
	audioplayerelement.onended = null;
	if (document.getElementsByClassName("playbutton").length > Math.round(episode_data.row_no+1)) {
		//alert(Math.round(episode_data.row_no+1));
		//audioplayerelement.addEventListener("ended",function(){play_pressed(document.getElementsByClassName("playbutton")[Math.round(episode_data.row_no+1)])});
		summaryelement.appendChild(document.createTextNode("es geht noch weiter..."));
		audioplayerelement.onEnded = function(){ play_pressed(document.getElementsByClassName("playbutton")[Math.round(episode_data.row_no+1)])};
	} else {
		//audioplayerelement.addEventListener("ended",function(){this.pause();});
		summaryelement.appendChild(document.createTextNode("Letztes Kapitel"));
		audioplayerelement.onEnded = function(){this.pause();};
	}
}

*/
