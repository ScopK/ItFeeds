(function() {
	var styl = document.createElement('style');
	styl.innerHTML= ".message_sys{\n";
	styl.innerHTML+="	font-family:'Noto Sans','Lucida Sans Unicode', 'Lucida Grande', sans-serif;\n";
	styl.innerHTML+="}\n";
	styl.innerHTML+="#top_message{\n";
	styl.innerHTML+="	z-index:30;\n";
	styl.innerHTML+="	position:fixed;\n";
	styl.innerHTML+="	top:-50px;\n";
	styl.innerHTML+="	left:0;\n";
	styl.innerHTML+="	right:0;\n";
	styl.innerHTML+="	background-color:rgba(230,46,0,0.9);\n";
	styl.innerHTML+="	border-bottom:2px solid #000;\n";
	styl.innerHTML+="	transition: top 0.4s;\n";
	styl.innerHTML+="}\n";
	styl.innerHTML+="#top_message.good{\n";
	styl.innerHTML+="	background-color: rgba(145,212,0,0.9);\n";
	styl.innerHTML+="}\n";
	styl.innerHTML+="#top_message p{\n";
	styl.innerHTML+="	text-align: center;\n";
	styl.innerHTML+="	color:white;\n";
	styl.innerHTML+="	font-weight: bold;\n";
	styl.innerHTML+="	font-size: 14px;\n";
	styl.innerHTML+="	margin: 1em 0px;\n";
	styl.innerHTML+="}\n";
	styl.innerHTML+="#popup_message{\n";
	styl.innerHTML+="	z-index:30;\n";
	styl.innerHTML+="	position:fixed;\n";
	styl.innerHTML+="	right:50px;\n";
	styl.innerHTML+="	text-align:center;\n";
	styl.innerHTML+="	bottom:50px;\n";
	styl.innerHTML+="	background-color:rgba(0,146,230,0.9);\n";
	styl.innerHTML+="	border:2px solid #000;\n";
	styl.innerHTML+="	opacity:1; visibility:visible;\n";
	styl.innerHTML+="	transition: opacity 0.5s, visibility 0s 0s;\n";
	styl.innerHTML+="}\n";
	styl.innerHTML+="#popup_message.hidden{\n";
	styl.innerHTML+="	transition-delay: 0s, 0.5s;\n";
	styl.innerHTML+="	opacity:0; visibility:hidden;\n";
	styl.innerHTML+="}\n";
	styl.innerHTML+="#popup_message p{\n";
	styl.innerHTML+="	font-variant: small-caps;\n";
	styl.innerHTML+="	font-weight: bold;\n";
	styl.innerHTML+="	color: #FFF;\n";
	styl.innerHTML+="	font-size: 15px;\n";
	styl.innerHTML+="	margin:0; padding: 10px 25px;\n";
	styl.innerHTML+="}\n";

	document.head.appendChild(styl);
})();

window.addEventListener("load", function() {
    var di = document.createElement('div');
    di.id = 'top_message';
    di.className ="message_sys";
    di.onmouseover = function(){hideMessage()};
	di.appendChild(document.createElement('p'));
    document.body.appendChild(di);

    var di = document.createElement('div');
    di.id = 'popup_message';
    di.className ="message_sys hidden";
    di.onmouseover = function(){hidePopMessage()};
	di.appendChild(document.createElement('p'));
    document.body.appendChild(di);
});


var hidemsgtimer;
function showMessage(msg, good){
	msg = (typeof msg !== 'undefined')? msg : "";
	good = (typeof good !== 'undefined')? good : false;

	var box = document.getElementById("top_message");
	if (good)
		box.className = "message_sys good";
	else
		box.className = "message_sys";
	box.children[0].innerHTML=msg;
	box.style.top=0;
	
	clearTimeout(hidemsgtimer);
	hidemsgtimer = setTimeout(hideMessage,3000);
}
function hideMessage() {
	var hh = document.getElementById("top_message").offsetHeight+"px";
	document.getElementById("top_message").style.top="-"+hh;
}

var hidepopmsgtimer;
function showPopMessage(msg){
	msg = (typeof msg !== 'undefined')? msg : "";

	var box = document.getElementById("popup_message");
	box.children[0].innerHTML=msg;
	box.style.display="";
	box.className = "message_sys";

	clearTimeout(hidepopmsgtimer);
	hidepopmsgtimer = setTimeout(hidePopMessage,2000);
}

function hidePopMessage(){
	document.getElementById("popup_message").className = "message_sys hidden";
}