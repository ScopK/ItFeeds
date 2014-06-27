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
		$("#blankspace").css("height",$(window).height());
	});
	$("#blankspace").css("height",$(window).height());


	$(document).scroll(function() {
		var pos = $(document).scrollTop();
		$("#actions_panel").css("top",pos);
	});

	$("button").mousedown(function(e){
		if (e.button == 1){
			window.open(window.location.pathname+window.location.search, '_blank', '');
			return true; // to allow the browser to know that we handled it.
		}
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

			reloadPosts();
		},
		error: function(result){
			alert("Unknown error 0x001");
			$("#page").fadeIn();
		},
		complete: function(){
			$("#page").fadeIn();
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
	if (args.length>0)
		var page = "/Fydeph/index.php?"+args;
	else
		var page = "/Fydeph/index.php";
	window.history.pushState("", "", page);
}


function toggleFavs(me){
	if (!get.fav) { get.fav = "1";
		get.unread = "0";
	} else			get.fav = undefined;

	reloadPosts();
}


function toggleUnread(me){
	if (!get.unread) get.unread = "0";
	else { 			 get.unread = undefined;
				get.fav = undefined;
	}

	reloadPosts();
}

function toggleSort(me){
	if (!get.sortby) get.sortby = "0";
	else 			 get.sortby = undefined;
	reloadPosts();
}

function prevPage(){
	if (get.page) var page = get.page;
	else var page = 1;
	if (page > 1){
		page--;
		if (page == 1) get.page=undefined;
		else get.page=page;
		reloadPosts();
	}
}

function nextPage(){
	if (get.page) var page = get.page;
	else var page = 1;
	if (page < totalPages){
		page++;
		get.page=page;
		reloadPosts();
	}
}
