(function() {
	var styl = document.createElement('style');
	styl.innerHTML= "#contextual-menu{\n";
	styl.innerHTML+="	position:fixed;\n";
	styl.innerHTML+="	visibility:hidden;\n";
	styl.innerHTML+="	background-color: #f0f0f0;\n";
	styl.innerHTML+="	border: 1px solid #979797;\n";
	styl.innerHTML+="	width: 211px;\n";
	styl.innerHTML+="	padding: 2px;\n";
	styl.innerHTML+="	box-shadow: 3px 3px 2px rgba(0,0,0,0.5);\n";
	styl.innerHTML+="	z-index:3;\n";
	styl.innerHTML+="}\n";
	styl.innerHTML+=".cm-item{\n";
	styl.innerHTML+="	border: 1px solid transparent;\n";
	styl.innerHTML+="	padding:2px 7px;\n";
	styl.innerHTML+="	font-size: 12px;\n";
	styl.innerHTML+="	font-family: segoe ui;\n";
	styl.innerHTML+="	cursor:default;\n";
	styl.innerHTML+="}\n";
	styl.innerHTML+=".cm-item:hover{\n";
	styl.innerHTML+="	background-color:#d1e2f2;\n";
	styl.innerHTML+="	border-color: #78aee5;\n";
	styl.innerHTML+="}\n";
	document.head.appendChild(styl);
})();



window.addEventListener("load", function() {
    var di = document.createElement('div');
    di.id = 'contextual-menu';
    document.body.appendChild(di);

});

function setCMContent(array){
	document.getElementById("contextual-menu").innerHTML = "";
	addCMContent(array);
}

function addCMContent(array){
	var cm = document.getElementById("contextual-menu");

	array.forEach(function(entry) {
		var di = document.createElement('div');
		di.className = "cm-item";
		di.onclick = function(){
			document.getElementById("contextual-menu").style.visibility = "hidden";
			if('context' in entry)
				entry.function.call(entry.context);
			else
				entry.function();
		};
		di.innerHTML = entry.name;
		cm.appendChild(di);
	});
}

function showCM(x,y){
	// Adjust coords
	var ww = document.body.offsetWidth;
	var wh = window.innerHeight;
	var cmw = document.getElementById("contextual-menu").offsetWidth;
	var cmh = document.getElementById("contextual-menu").offsetHeight;
	if ((x+cmw)>ww) x=ww-cmw;
	if ((y+cmh)>wh) y-=cmh;

	document.getElementById("contextual-menu").style.top = y+"px";
	document.getElementById("contextual-menu").style.left = x+"px";
	document.getElementById("contextual-menu").style.visibility = "visible";
	
	var backup = window.onmousedown;
	window.onmousedown = function(event){
		if (event.target.className != "cm-item")
			document.getElementById("contextual-menu").style.visibility = "hidden";
		window.onmousedown = backup;
	};
}