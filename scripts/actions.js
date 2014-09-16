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
	if (args.length>0){
		var page = "./index.php?"+args;
		$("#managerLink").attr("href","./manager.php?"+args);
	} else {
		var page = "./index.php";
		$("#managerLink").attr("href","./manager.php");
	}
	window.history.pushState("", "", page);
}

function toogleViewFeeds(me){
	var hid = $(me).html();

	if (hid == '+'){
		$(me).closest(".folder").find(".folderfeeds").slideDown();
		$(me).html("-");
	} else {
		$(me).closest(".folder").find(".folderfeeds").slideUp();
		$(me).html("+");
	}
	stopPropagation();
}

function toggleFavs(me){
	if (!get.fav) {
		get.fav = "1";
		get.unread = "0";
		get.feed = undefined;
		get.folder = undefined;
		get.tag = undefined;
		$(".feed, .tag, .folder").removeClass("selected");
	} else 
		get.fav = undefined;
	updateUrl();
	reloadPosts();
}

function toggleUnread(me){
	if (!get.unread)
		get.unread = "0";
	else {
		get.unread = undefined;
		get.fav = undefined;
	}
	updateUrl();
	reloadPosts();
}

function toggleSort(me){
	if (!get.sortby)
		get.sortby = "0";
	else 
		get.sortby = undefined;
	updateUrl();
	reloadPosts();
}

function showAddTagsDialog(){
	if (postIdxSelected>0) {
		$('#add_tag').fadeIn(100);
		$('#add_tag p').removeClass("selected");
		$('#newtagField').val("");
		$('#newtagField').focus();
	}
}

function showSearchDialog(){
	$('#search_dialog').fadeIn(100);
	$('#searchField').val("");
	$('#searchField').focus();
}

function hideLateralMenu(){
    $("#show-lateral-button").html("&#10095;");
    $("#lateral_menu").addClass("hidden");
	$("#content").css("margin-left","0");
}

function showLateralMenu(){
	$("#show-lateral-button").html("&#10094;");
	$("#content").css("margin-left",$("#lateral_menu").outerWidth()+"px");
    $("#lateral_menu").removeClass("hidden");
}

function toggleLateralMenu(){
    if ($("#lateral_menu").hasClass("hidden"))
        showLateralMenu();
    else
        hideLateralMenu();
}

function addSelectedTag(element){
	$(element).toggleClass("selected");
}

function loadMore(){
	if (!$("#loadMoreLabel").hasClass('disabled')) {
		$("#loadMoreLabel").addClass('disabled');
		$("#loadMore").prop('disabled',true);
		ajaxMorePosts("");
	}
}


function openNewWindowTag(tagid){
	var res = location.search.replace(/&?(feed|folder|tag|unread)=[\w-]*/g, "");
	var url = window.location.pathname;
	if (res.length<2)
		url+="?tag="+tagid+"&unread=0";
	else
		url+=res+"&tag="+tagid+"&unread=0";
	window.open(url, '_blank', '');
}

function openNewWindowFeed(feedid){
	var res = location.search.replace(/&?(feed|folder|tag)=[\w-]*/g, "");
	var url = window.location.pathname;
	if (res.length<2)
		url+="?feed="+feedid;
	else
		url+=res+"&feed="+feedid;
	window.open(url, '_blank', '');
}

function openNewWindowFolder(folderid){
	var res = location.search.replace(/&?(feed|folder|tag)=[\w-]*/g, "");
	var url = window.location.pathname;
	if (res.length<2)
		url+="?folder="+folderid;
	else
		url+=res+"&folder="+folderid;
	window.open(url, '_blank', '');
}

//#################### CONTEXT MENU
function cmTag(e,context){
	var arr = [{
	/*	name: "Console.log(context)",
		function: function(){console.log(this)},
		context: context
	},{/**/
		name: "Open in new window",
		function: function(){
			openNewWindowTag($(this).attr("idtag"));
		},
		context: context
	}];
	if ($(context).hasClass("public"))
		arr.push({
			name: "Open with public tag viewer",
			function: function(){
				var id = $(this).attr("idtag");
				window.open('tag/'+id, '_blank', '');
			},
			context: context
		});
	setCMContent(arr);
	showCM(e.clientX,e.clientY)
	return false;
}


function cmFeed(e,context){
	var arr = [{
	/*	name: "Console.log(context)",
		function: function(){console.log(this)},
		context: context
	},{/**/
		name: "Open in new window",
		function: function(){
			openNewWindowFeed($(this).attr("idfeed"));
		},
		context: context
	}];

	setCMContent(arr);
	showCM(e.clientX,e.clientY);
	e.stopPropagation();
	return false;
}

function cmFolder(e,context){
	var arr = [{
	/*	name: "Console.log(context)",
		function: function(){console.log(this)},
		context: context
	},{/**/
		name: "Open in new window",
		function: function(){
			openNewWindowFolder($(this).attr("idfolder"));
		},
		context: context
	}];

	setCMContent(arr);
	showCM(e.clientX,e.clientY)
	return false;
}