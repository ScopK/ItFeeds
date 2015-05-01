(function() {
	var styl = document.createElement('style');
	var css = "";
	css += ".message_sys{\n";
	css += "	font-family:'Noto Sans','Lucida Sans Unicode', 'Lucida Grande', sans-serif;\n";
	css += "}\n";
	css += "#top_message{\n";
	css += "	z-index:30;\n";
	css += "	position:fixed;\n";
	css += "	top:-56px;\n";
	css += "	left:0;\n";
	css += "	right:0;\n";
	css += "	background-color: rgba(200,56,56,0.97);\n";
	css += "	border-bottom:1px solid #000;\n";
	css += "	transition: top 0.4s;\n";
	css += "	box-shadow: 0 0 6px -1px;\n";
	css += "}\n";
	css += "#top_message.good{\n";
	css += "	background-color: rgba(96,188,56,0.97);\n";
	css += "}\n";
	css += "#top_message p{\n";
	css += "	text-align: center;\n";
	css += "	color:white;\n";
	css += "	font-weight: bold;\n";
	css += "	font-size: 13.5px;\n";
	css += "	margin: 0.6em 0px;\n";
	css += "}\n";
	css += "#popup_message{\n";
	css += "	z-index:30;\n";
	css += "	position:fixed;\n";
	css += "	right:0px;\n";
	css += "	text-align:center;\n";
	css += "	top:100px;\n";
	css += "	background-color:#444;\n";
	css += "	color: black;\n";
	css += "	opacity:1; visibility:visible;\n";
	css += "	box-shadow: 0px 0px 6px -2px;\n";
	css += "	transition: all 0.3s, visibility 0s 0s;\n";
	css += "}\n";
	css += "#popup_message.hidden{\n";
	css += "	transition-delay: 0s, 0.3s;\n";
	css += "	right:-100px;\n";
	css += "	opacity:0; visibility:hidden;\n";
	css += "}\n";
	css += "#popup_message p{\n";
	css += "	font-variant: small-caps;\n";
	css += "	font-weight: bold;\n";
	css += "	cursor:default;\n";
	css += "	color: white;\n";
	css += "	font-size: 14px;\n";
	css += "	margin:0; padding: 10px 25px;\n";
	css += "}\n";

	styl.innerHTML = css;
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
	var hh = document.getElementById("top_message").offsetHeight;
	document.getElementById("top_message").style.top="-"+(hh+6)+"px";
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