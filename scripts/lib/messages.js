(function() {
	var styl = document.createElement('style');
	var css = "";
	css += ".message_sys{\n";
	css += "	font-family:'Noto Sans','Lucida Sans Unicode', 'Lucida Grande', sans-serif;\n";
	css += "}\n";
	/*
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
	/**/
	css += "#top_message.hidden,#popup_message.hidden{\n";
	css += "	transition-delay: 0s, 0.3s;\n";
	css += "	right:-100px;\n";
	css += "	opacity:0; visibility:hidden;\n";
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
	css += "	border:solid #444;\n";
	css += "	border-width:1px 0 1px 1px;\n";
	css += "	transition: all 0.3s, visibility 0s 0s;\n";
	css += "}\n";
	css += "#popup_message p{\n";
	css += "	font-variant: small-caps;\n";
	css += "	font-weight: bold;\n";
	css += "	cursor:default;\n";
	css += "	color: white;\n";
	css += "	font-size: 14px;\n";
	css += "	margin:0; padding: 10px 25px;\n";
	css += "}\n";

	css += "#popup_message.good{\n";
	css += "	border-color: #0C7B19;\n";
	css += "	background-color: #60BC38;\n";
	css += "}\n";

	css += "#popup_message.bad{\n";
	css += "	border-color: #7B0C0C;\n";
	css += "	background-color: #C83838;\n";
	css += "}\n";	

	styl.innerHTML = css;
	document.head.appendChild(styl);
})();

window.addEventListener("load", function() {
    //var di = document.createElement('div');
    //di.id = 'top_message';
    //di.className ="message_sys";
    //di.onmouseover = function(){hideMessage()};
	//di.appendChild(document.createElement('p'));
    //document.body.appendChild(di);

    var di = document.createElement('div');
    di.id = 'popup_message';
    di.className ="message_sys hidden";
    di.onmouseover = function(){hidePopMessage()};
	di.appendChild(document.createElement('p'));
    document.body.appendChild(di);
});

var hidepopmsgtimer;
function showPopMessage(msg,good){
	var classes;
	if (good==undefined) 	classes= "message_sys";
	else if (good)			classes= "message_sys good";
	else					classes= "message_sys bad";

	msg = (typeof msg !== 'undefined')? msg : "";

	var box = document.getElementById("popup_message");
	box.children[0].innerHTML=msg;
	box.style.display="";
	box.className = classes;

	clearTimeout(hidepopmsgtimer);
	hidepopmsgtimer = setTimeout(hidePopMessage,2000);
}

function hidePopMessage(){
	document.getElementById("popup_message").className += " hidden";
}

function showMessage(msg, good){
	good = good==undefined?false:good;
	showPopMessage(msg,good);
}
function hideMessage() {
	alert();
	hidePopMessage();
}