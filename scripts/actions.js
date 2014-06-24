var folders;
var tags;
var idxFolder;
var idxFeed;
var idxTag;

var get;
readGetParameters();

$(document).ready(function(){
	initialize();

	$(window).resize(function(){
		$("#page").css("min-height",$(window).height());
	});
	$("#page").css("min-height",$(window).height());

	$(document).scroll(function() {
		var pos = $(document).scrollTop();
		$("#actions_panel").css("top",pos);
	});
});

function initialize(){
	$("#page").fadeOut();
	$.ajax({
		url: "./ajax/get_user_info.php",
		type: "GET",
		dataType : "json",
		success: function(result){
			folders = result.folders;
			tags = result.tags;
			displayFoldersAndTags();

			loadPosts();
		},
		error: function(result){
			alert("Unknown error 0x001");
			$("#page").fadeIn();
		},
		complete: function(){
			$("#page").fadeIn();
			loading_stop();
		}
	});
}

function toogleViewFeeds(me){
	var hid = $(me).html();

	if (hid == '+'){
		$(me).closest(".folder").find(".folderfeeds").slideDown();
		$(me).attr("hidd","0");
		$(me).html("-");
	} else {
		$(me).closest(".folder").find(".folderfeeds").slideUp();
		$(me).attr("hidd","1");
		$(me).html("+");
	}
	stopPropagation();
}

function readGetParameters(){
	get = [];
	location.search.replace('?', '').split('&').forEach(function (val) {
	    split = val.split("=", 2);
	    get[split[0]] = split[1];
	});
}
function updateUrl(){
	var args = "";
	var first=1;
	$.each(Object.keys(get),function(){
		if (get[this]==undefined) return true;
		if (first)
			first = undefined;
		else
			args+="&";
		args+=this+"="+get[this];
	});
	var page = "/Fydeph/index.php?"+args;
	window.history.pushState("", "", page);
}
