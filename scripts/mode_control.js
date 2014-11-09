function reloadPosts(){
	if (get.feed != undefined)			ajaxPosts("feed="+get.feed);
	else if (get.folder != undefined)	ajaxPosts("folder="+get.folder);
	else if (get.tag != undefined)		ajaxPosts("tag="+get.tag);
	else								ajaxPosts("");
}

function setFeedsActions(){
	$(".feed").click(function(){
		get.page = undefined;
		get.folder = undefined;
		get.tag = undefined;

		$(".feed, .tag, .folder").not(this).removeClass("selected");

		var idf = $(this).attr("idFeed");
		if (idf == get.feed){
			get.feed = undefined;
			updateUrl();

			$(this).removeClass("selected");
			$(".folderfeeds").slideUp();
			$(".expander").html("+");

			ajaxPosts("");
		} else {
			get.feed = idf;
			updateUrl();

			var button = $(this).closest(".folder").find("button");
			var obuttons = $(".expander").not(button);
			obuttons.closest(".folder").find(".folderfeeds").slideUp();
			obuttons.html("+");
			button.closest(".folder").find(".folderfeeds").slideDown();
			button.html("-");

			$(this).addClass("selected");

			ajaxPosts("feed="+idf);
		}
	});

	$(".folder").click(function(event){
		if ($(event.target).is(".folderHeader,.folderTitle,.folderTitle .count,.folderTitle .count .num")){
			get.page = undefined;
			get.feed = undefined;
			get.tag = undefined;

			$(".feed, .tag, .folder").not(this).removeClass("selected");

			var idf = $(this).attr("idFolder");
			if (idf == get.folder){
				get.folder = undefined;
				updateUrl();

				$(this).removeClass("selected");
				$(".folderfeeds").slideUp();
				$(".expander").html("+");

				ajaxPosts("");
			} else {
				get.folder = idf;
				updateUrl();

				var button = $(this).find("button");
				var obuttons = $(".expander").not(button);
				obuttons.closest(".folder").find(".folderfeeds").slideUp();
				obuttons.html("+");
				button.closest(".folder").find(".folderfeeds").slideDown();
				button.html("-");
				$(this).addClass("selected");

				ajaxPosts("folder="+idf);
			}
		}
	});
}

function setTagsActions(){
	$(".tag").click(function(){
		get.page = undefined;
		get.feed = undefined;
		get.folder = undefined;

		$(".feed, .tag, .folder").not(this).removeClass("selected");
		$(".folderfeeds").slideUp();
		$(".expander").html("+");

		var idt = $(this).attr("idTag");
		if (idt == get.tag) {
			get.tag = undefined;
			updateUrl();
			$(this).removeClass("selected");
			ajaxPosts("");
		} else {
			get.tag = idt;
			get.fav = undefined;
			get.unread="0";
			updateUrl();
			$(this).addClass("selected");
			ajaxPosts("tag="+idt);
		}
	});
}

function searchAction(){
	$('#search_dialog').fadeOut(100);
	get.page = undefined;
	var query = encodeURIComponent($("#searchField").val());
	if (query.length > 0){
		$(".selected").removeClass("selected");
		get.feed = undefined;
		get.folder = undefined;
		get.tag = undefined;
		get.search = query;
	} else {
		get.search = undefined;
	}
	reloadPosts();
	updateNavigationElements();
	updateUrl();
}

function updateNavigationElements(){
	if (get.fav) $("#favsTButton").addClass("marked");
	else		 $("#favsTButton").removeClass("marked");

	if (get.unread) $("#unreadTButton").removeClass("marked");
	else		    $("#unreadTButton").addClass("marked");

	if (get.sortby) $("#sortTButton").html("Older");
	else		  	$("#sortTButton").html("Newer");

	if (get.search) $("#searchButton").addClass("marked");
	else		    $("#searchButton").removeClass("marked");
}

function change_postsmode(val,msg){
	if (msg==undefined) msg=true;
	if (val==undefined){
		var val = getCookie("compactedmode");
		val++;
		if (val==3) val=0;
	}
	val++;val--;
	switch(val){
		case 0:
			if (msg) showPopMessage("Normal Mode: Previous posts are minimized");
			if (postIdxSelected>0){
				$(".post[idxpost='"+postIdxSelected+"']").prevAll("div.post").addClass("minimized");
				$(".post[idxpost='"+(postIdxSelected-1)+"']").nextAll("div.post").removeClass("minimized");
			} else {
				$(".post").removeClass("minimized");
			}
			break;
		case 1: 
			if (msg) showPopMessage("Minimized Mode: All unselected posts are minimized"); 
	    	$(".post").not(".selected").addClass("minimized");
	    	$(".post.selected").removeClass("minimized");
			break;
		case 2:
			if (msg) showPopMessage("Never minimize"); 
	    	$(".post").removeClass("minimized");
			break;
	}
	setCookie("compactedmode",val,3);
	$("#posts_mode").val(val);
	if (postIdxSelected>0)
		focusPost($(".post[idxpost='"+postIdxSelected+"']"),100);
}

function change_autoreadmode(val,msg){
	if (msg==undefined) msg=true;
	if (val==undefined){
		var val = getCookie("autoreadmode");
		val++;
		if (val==3) val=0;
	}
	val++;val--;
	switch(val){
		case 0: // On select post
			if (msg) showPopMessage("Normal Mode: Previous posts are minimized");
			break;
		case 1: // On scroll
			if (msg) showPopMessage("Minimized Mode: All unselected posts are minimized"); 
			break;
		case 2: // Never
			if (msg) showPopMessage("Never minimize"); 
			break;
	}
	setCookie("autoreadmode",val,3);
	$("#autoread_mode").val(val);
}