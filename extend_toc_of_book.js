function switch_downloadformats2(formatstring) {
	
	var target_format = formatstring;
	var toc_element = document.getElementsByClassName('of_content')[0];
	
	var tr_element = null;
	var download_element = null;
	
	for (var i = 0; i < toc_element.getElementsByTagName("tr").length; i++) {
		tr_element = toc_element.getElementsByTagName("tr")[i];
		if (tr_element.getAttribute('data-'+target_format+'_filename') != null) {
			download_element = tr_element.getElementsByClassName("downloadlink")[0];
			download_element.firstChild.firstChild.data = '⤋ ('+(Math.round(parseInt(tr_element.getAttribute('data-'+target_format+'_filesize'))/1024/10.24)/100)+'MiB)';
			download_element.firstChild.href = './audio/'+tr_element.getAttribute('data-'+target_format+'_filename');
		}
	}
}

function switch_downloadformats(formatbutton) {
	var target_format=formatbutton.firstChild.data;	
	
	var cookie_expire = new Date();
	var cookie_expire1 = cookie_expire.getTime() + (300 * 24 * 60 * 60 * 1000);
	//cookie_expire1 = cookie_expire.getTime() + (60 * 1000);
	cookie_expire.setTime(cookie_expire1);
	//document.cookie="foo=bar; expires="+cookie_expire.toGMTString();
	document.cookie="favformat="+target_format+"; expires="+cookie_expire.toGMTString();
	//document.cookie="unfug=Unfug; expires="+cookie_expire.toGMTString();
	
	switch_downloadformats2(target_format);
	

}

function extend_booktoc() {
	//first find position where to place the switcher...
	var toc_element = document.getElementsByClassName('of_content')[0];
	
	var audioformatlist = new Array('vorbis','opus','mp3','aac');
	
	var audioformat_ul = document.createElement("ul");
	audioformat_ul.classList.add('handwritten');
	audioformat_ul.id="format_switch";
	var audioformat_li = null;
	var audioformat_a = null;
	
	for (var i=0; i < audioformatlist.length; i++) {
		audioformat_li = document.createElement('li');
		audioformat_a = document.createElement('a');
		audioformat_a.appendChild(document.createTextNode(audioformatlist[i]));
		audioformat_a.addEventListener('click',function() { switch_downloadformats(this); });
		audioformat_li.appendChild(audioformat_a);
		audioformat_ul.appendChild(audioformat_li);
	}
	
	//toc_element.style.backgroundColor= '#0ff';
	//toc_element.parentNode.style.backgroundColor= '#f0f';
	toc_element.parentNode.insertBefore(audioformat_ul,toc_element);
	
	//check cookie...
	var cookiecontent = document.cookie;
	if (cookiecontent.match(/favformat=(\w+);/)) {
		cookiecontent=cookiecontent.match(/favformat=(\w+);/)[1];
		switch_downloadformats2(cookiecontent);
	}
}



window.addEventListener("load",function() { extend_booktoc(); });
