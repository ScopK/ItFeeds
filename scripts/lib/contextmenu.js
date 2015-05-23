(function() {
	var style = document.createElement('style');
	var css="#contextual-menu,.contextual-folder{";
	css  += "	position: fixed;";
	css  += "	visibility: hidden;";
	css  += "	background-color: #e7e7e7;";//#f0f0f0;";
	css  += "	border: 1px solid #999;";//#979797;";
	css  += "	width: 211px;";
	css  += "	padding: 3px;";//2px;";
	css  += "	box-shadow: 0px 0px 10px -5px black;";//3px 3px 2px rgba(0,0,0,0.5);";
	css  += "	z-index: 3;";
	css  += "}";
	css  += ".cm-item{";
	css  += "	border: 1px solid transparent;";
	css  += "	padding: 7px;";//1px 7px 2px;";
	css  += "	font-size: 13px;";//12px;";
	css  += "	font-family: \"Open Sans\",Tahoma,Geneva,sans-serif,segoe ui;";//segoe ui;";
	css  += "	cursor: default;";
	css  += "}";
	css  += ".cm-item.folder{";
	css  += "	background-image:url('data:image/png;base64,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');";
	css  += "	background-repeat: no-repeat;";
	css  += "	background-position: 198px center;";
	css  += "}";
	css  += ".cm-item:hover,.cm-item.active{";
	css  += "	background-color: #ccc;";//#d1e2f2;";
	css  += "	border-color: #999;";//#78aee5;";
	css  += "}";
	css  +=  "#contextual-menu hr,.contextual-folder hr{";
	css  += "	border: solid #ccc;";//#d7d7d7;";
	css  += "	border-width: 0 0 1px 0;";
	css  += "	margin: 2px 1px;";//1px 1px 2px;";
	css  += "}";
	style.innerHTML = css;
	document.head.appendChild(style);
})();

window.addEventListener("load", function() {
    var di = document.createElement('div');
    di.id = 'contextual-menu';
    document.body.appendChild(di);
});

function setCMContent(array){
	var cm = document.getElementById("contextual-menu");
	cm.innerHTML = "";
	addCMContent(cm,array);
}

var closeFolderTimer;
function addCMContent(root,array){
	array.forEach(function(entry) {
		if (entry.type=="separator"){
			var hr = document.createElement('hr');
			root.appendChild(hr);
			return;
		}

		if (entry.type=="folder"){
			var contextFolder = document.createElement('div');
			var di = document.createElement('div');

			var showContext = function(){
				clearTimeout(closeFolderTimer);
				clearTimeout(tout);
				var folders = root.getElementsByClassName("contextual-folder");
				for (var i=0;i<folders.length;i++)
					if (folders[i]!=contextFolder)
						folders[i].style.visibility ="hidden";

				showCMFolder(di.offsetLeft,di.offsetTop,contextFolder);
				return false;
			}

			di.innerHTML = entry.name;
			di.className = "cm-item folder";
			di.onclick = showContext;
			di.oncontextmenu = showContext;
			root.appendChild(di);

			contextFolder.style.position="absolute";
			contextFolder.style.left="209px";
			contextFolder.style.top=(di.offsetTop-3)+"px";
			contextFolder.style.visibility="hidden";
			contextFolder.className="contextual-folder";
			root.appendChild(contextFolder);

			var tout;
			di.onmouseenter = function(){
				clearTimeout(closeFolderTimer);
				tout=setTimeout(showContext,500);
			}
			di.onmouseleave=function(){
				clearTimeout(tout);
				tout=undefined;
			}
			contextFolder.onmouseenter = function(){
				di.className="cm-item folder active";
			}
			contextFolder.onmouseleave=function(){
				di.className="cm-item folder";
			}
			addCMContent(contextFolder,entry.options);

		} else {
			var fun = function(){
				hideCM();
				if('context' in entry)
					entry.function.call(entry.context);
				else
					entry.function();
				return false;
			};

			var di = document.createElement('div');
			di.className = "cm-item";
			di.innerHTML = entry.name;
			di.onclick = fun;
			di.oncontextmenu = fun;
			di.onmousedown = function(e){if (e.buttons>3) hideCM();};
			di.onmouseenter = function(){
				var folders = root.getElementsByClassName("contextual-folder");
				for (var i=0;i<folders.length;i++){
					if (folders[i].style.visibility != "hidden"){
						clearTimeout(tout);
						closeFolderTimer = setTimeout(function(){
							//var folders = root.getElementsByClassName("contextual-folder");
							for (var i=0;i<folders.length;i++)
								folders[i].style.visibility = "hidden";
						},500);
						break;
					}
				}
			}
			root.appendChild(di);
		}
	});
}

function showCM(x,y){
	// Adjust coords
	var cm = document.getElementById("contextual-menu");
	var ww = document.body.offsetWidth;
	var wh = window.innerHeight;
	var cmw = cm.offsetWidth;
	var cmh = cm.offsetHeight;

	//x-=cmw/2.5;
	x-=40;
	if (x<0) x=0;
	
	if ((x+cmw)>ww) x=ww-cmw;
	if ((y+cmh)>wh) y-=cmh;

	cm.style.top = y+"px";
	cm.style.left = x+"px";
	cm.style.visibility = "visible";
	
	/*
	var backup = window.onmousedown;
	window.onmousedown = function(event){
		if (event.target.classList[0] != "cm-item"){
			hideCM();
			window.onmousedown = backup;
		}
	};*/

	window.addEventListener("mousedown", function(event){
		if (event.target.classList[0] != "cm-item")
			hideCM();
	});
}

function showCMFolder(cmx,cmy,folder){
	var ww = document.body.offsetWidth;
	var wh = window.innerHeight;
	var x = folder.offsetLeft;
	var y = folder.offsetTop;
	var w = folder.offsetWidth;
	var h = folder.offsetHeight;

	if (cmx>x) x = 209;
	if ((cmy-10)>y)	y += h-28;

	var folderPos = folder.getBoundingClientRect();
	var absx=folderPos.x+1;
	var absy=folderPos.y+1;

	if ((absx+w)>ww) folder.style.left="-211px";
	else 				folder.style.left="209px";

	if ((absy+h)>wh) folder.style.top=(cmy-h+25)+"px";
	else 				folder.style.top=(cmy-3)+"px";
	folder.style.visibility = "visible";
}

function hideCM(){
	var cm = document.getElementById("contextual-menu");
	cm.style.visibility = "hidden";
	var folders = cm.getElementsByClassName("contextual-folder");
	for (var i=0;i<folders.length;i++)
		folders[i].style.visibility = "hidden";
}

