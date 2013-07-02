//Overlay
function unlock_secret_overlay() {
	var secret_container = document.createElement("div");
	secret_container.style.position = "fixed";
	secret_container.style.width = "80%";
	//secret_container.style.height = "80%";
	secret_container.style.backgroundColor = "rgba(0,0,0,0.6)";
	secret_container.style.color = "#fff";
	secret_container.style.top = "5%";
	secret_container.style.left = "5%";
	secret_container.style.padding = "5%";
	secret_container.id = "secret_container";
	
	var secret_headline = document.createElement("h2");
	secret_headline.appendChild(document.createTextNode("Geheime Episoden"));
	
	var secret_description1 = document.createElement("p");
	secret_description1.appendChild(document.createTextNode("Es wäre unter Umständen, vielleicht möglich, dass auf dieser Webseite Aufnahmen lagern, die ich - aus rechtlichen Gründen - nicht öffentlich machen kann. Diese Episoden sind auf Basis des Rechts auf Privatkopie mit einem Passwort gesichert. Dieses Passwort gebe ich engen Freunden. Wenn ihr mich nicht kennt oder vielmehr ich euch nicht kenne, bitte fragt gar nicht erst nach diesem Kennwort, ich werde es nicht leichtfertig herausgeben."));
	var secret_description2 = document.createElement("p");
	secret_description2.appendChild(document.createTextNode("Wenn du das Passwort also nicht kennst und ich dich nicht kenne, so klicke bitte einfach auf Abbrechen, andernfalls gib es eben ein und speichere es."));
	
	var secret_input = document.createElement("input");
	secret_input.type = "password";
	secret_input.id = "hidden_password";
	
	var secret_save =  document.createElement("input");
	secret_save.type = "button";
	secret_save.value = "Speichern";
	secret_save.addEventListener("click",function() { 
		//document.cookie = "show_hidden="+document.getElementById("hidden_password").value+";expires="+Date(new Date().getTime() + 1000 * 60 * 60 * 24 * 55).toGMTString();
		document.cookie = "show_hidden="+document.getElementById("hidden_password").value+";expires="+(new Date(new Date().getTime() + 1000 * 60 * 60).toGMTString());
		document.getElementsByTagName("body")[0].removeChild(document.getElementById("secret_container"));
	});
	
	var secret_close =  document.createElement("input");
	secret_close.type = "button";
	secret_close.value = "Abbrechen";
	secret_close.addEventListener("click",function() { 
		document.getElementsByTagName("body")[0].removeChild(document.getElementById("secret_container"));
	});
	
	secret_container.appendChild(secret_headline);
	secret_container.appendChild(secret_description1);
	secret_container.appendChild(secret_description2);
	secret_container.appendChild(secret_input);
	secret_container.appendChild(secret_save);
	secret_container.appendChild(secret_close);
	document.getElementsByTagName("body")[0].appendChild(secret_container);
}

function add_secret_button() {
	var my_button = document.createElement("a");
	my_button.href="#";
	my_button.appendChild(document.createTextNode("Geheimnisse"));
	my_button.addEventListener("click",function() { unlock_secret_overlay();  });
	
	document.getElementsByTagName("footer")[0].appendChild(my_button);
}

window.setTimeout(add_secret_button,1000);
