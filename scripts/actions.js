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



