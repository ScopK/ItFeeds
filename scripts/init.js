var folders;
var tags;
var posts;

var preselectPost = 0;	//toDeleteWhenFinished
var postIdxSelected = 0;
var firstPostIdx=0;
var postCount;
var pagesLoaded;

var totalPages;
var totalPosts;

var get;
readGetParameters();

$(document).ready(function(){
	$("#more-options-button").hide();
	$("#page").hide();
	initialize();
	//################# Blank space at the end of page
	$(window).resize(function(){
		$("#load_more_panel").css("height",$(window).height()-60);
	});
	$("#load_more_panel").css("height",$(window).height()-60);

	//################# Auto position post controls
	$(document).scroll(situatePostControls);

	//################# Mouse navigator
	$('#mouse_nav').mousedown(function(event) {
	    switch (event.which) {
	        case 1:
	            nextPost();
	            break;
	        case 3:
	            prevPost();
	            return false;
	    }
	});

	if (getCookie("fullscreen")=='1'){
		hideLateralMenu();
	}
	if ((compactedmode=getCookie("compactedmode"))!==""){
		$("#posts_mode").val(compactedmode);
	}
	if ((autoreadmode=getCookie("autoreadmode"))!==""){
		$("#autoread_mode").val(autoreadmode);
	}

//checkout
	var idleTime = 0;
	var reloadTime = 0;
    $(document).mousemove(function(e){ idleTime = 0; });
    $(document).click(function(e){ idleTime = 0; });
    $(document).keypress(function(e){ idleTime = 0; });

	setInterval(function(){
		idleTime++;
		reloadTime++;
		if (reloadTime >= 4){ //2 minutes
			initialize(postIdxSelected==0 && idleTime>=4);
			reloadTime=0;
			idleTime=0;
		}
	},30000);
});


function situatePostControls(){
	var pos = $(document).scrollTop();
	var controllers = $(".post").toArray();
	var idx = 0;
	$.each(controllers.reverse(),function(){
		var con = $(this).find(".controller");
		if ($(this).hasClass("minimized")){
			con.removeClass("fixed");
			con.css("top","0");
			return;
		}
		var top = this.offsetTop;
		var bot = top + this.offsetHeight - con.height();
		if (top < pos && pos < bot){
			con.addClass("fixed");
			con.css("top","0");
		} else if (pos >= bot){
			con.removeClass("fixed");
			con.css("top",(this.offsetHeight - con.height()-5)+"px");
		} else {
			con.removeClass("fixed");
			con.css("top","0");
		}
	});
}

function initialize(reload){
	if (reload==undefined) reload = true;
	loading_run();
	$.ajax({
		url: "./ajax/get_user_info.php",
		type: "GET",
		dataType : "json",
		success: function(result){
			folders = result.folders;
			tags = result.tags;
			displayFolders();
			displayTags();

			if (reload)
				reloadPosts();
		},
		error: function (request, status, error){
			showMessage("Couldn't get user info<br/>Error "+request.status+": "+request.responseText);
		},
		complete: function(){
			$("#page").show();
			$("#more-options-button").show();
			loading_stop();
		}
	});
}

function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+d.toUTCString();
	window["cookie_"+cname]=cvalue;
	document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
	if (window["cookie_"+cname]!=undefined){
		return window["cookie_"+cname];
	}
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1);
		if (c.indexOf(name) != -1){
			var cvalue = c.substring(name.length,c.length);
			window["cookie_"+cname]=cvalue;
			return cvalue;
		}
	}
	return "";
}

function deleteCookie(cname) {
	document.cookie = cname+"=;expires=Thu, 01 Jan 1970 00:00:01 GMT;";
	window["cookie_"+cname]=undefined;
}