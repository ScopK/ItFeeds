function reloadPosts(){
	if (get.feed != undefined)			ajaxPosts("feed="+get.feed);
	else if (get.folder != undefined)	ajaxPosts("folder="+get.folder);
	else if (get.tag != undefined)		ajaxPosts("tag="+get.tag);
	else if (get.search != undefined)	ajaxPosts("search="+get.search);
	else								ajaxPosts("");
}

function setFeedsActions(){
	$(".feed").click(function(){
		get.page = undefined;
		get.folder = undefined;
		get.tag = undefined;
		get.search = undefined;

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
			get.search = undefined;

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
		get.search = undefined;

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
	if (query.length > 0) {
		ajaxPosts("search="+query);
		get.search = query;
	} else {
		ajaxPosts("");
		get.search = undefined;
	}
	get.feed = undefined;
	get.folder = undefined;
	get.tag = undefined;
	updateNavigationElements();
	updateUrl();
	$(".feed, .tag, .folder").removeClass("selected");
	$(".folderfeeds").slideUp();
	$(".expander").html("+");
}

function updateNavigationElements(){
	if (get.fav) $("#favsTButton").addClass("marked");
	else		 $("#favsTButton").removeClass("marked");

	if (get.unread) $("#unreadTButton").removeClass("marked");
	else		    $("#unreadTButton").addClass("marked");

	if (get.sortby) $("#sortTButton").html("Older");
	else		  	$("#sortTButton").html("Newer");

	if (get.sortby) $("#sortTButton").html("Older");
	else		  	$("#sortTButton").html("Newer");

	if (get.search) $("#searchButton").addClass("marked");
	else		    $("#searchButton").removeClass("marked");
}