function reloadPosts(){
	if (get.feed != undefined){
		loadFeed(get.feed);
	} else if (get.folder != undefined){
		loadFolder(get.folder);
	} else if (get.tag != undefined){
		loadTag(get.tag);
	} else {
		loadAll();
	}
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

			loadAll();
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

			loadFeed(idf);
		}
	});

	$(".folder").click(function(event){
		if ($(event.target).is(".folderHeader,.folderTitle,.folderTitle .count")){
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

				loadAll();
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

				loadFolder(idf);
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
			loadAll();
		} else {
			get.tag = idt;
			get.fav = undefined;
			get.unread="0";
			updateUrl();
			$(this).addClass("selected");
			loadTag(idt);
		}
	});
}

function loadFeed(id){
	ajaxPosts("feed="+id);
}

function loadFolder(id){
	ajaxPosts("folder="+id);
}

function loadTag(id){
	ajaxPosts("tag="+id);
}

function loadAll(){
	get.feed = undefined;
	get.folder = undefined;
	get.tag = undefined;

	ajaxPosts("");
}

function ajaxPosts(args){
	postIdxSelected = 0;
	var params = "";
	updateNavigationElements();
	if (get.postspage != undefined)	params+="postspage="+get.postspage+"&";
	if (get.page != undefined)	params+="page="+get.page+"&";

	if (useLast){
		params += "useLast=1";
		useLast = undefined;
	} else {
		if (get.unread!=undefined)	params+="unread="+get.unread+"&";
		if (get.fav != undefined)	params+="fav="+get.fav+"&";
		if (get.sortby != undefined)	params+="sortBy="+get.sortby+"&";
		params += args;
	}

	loading_run();
	$.ajax({
		url: "./ajax/get_posts.php",
		type: "GET",
		data: params,
		dataType : "json",
		success: function(result){
			$('#posts_panel').html("");
			totalPages = Math.ceil((get.postspage)?result.total/get.postspage:result.total/10);
			totalPosts = result.total;
			$("#totalPages").html("/"+totalPages+"("+totalPosts+")");

			$("#nextPage").prop('disabled',(((get.page)?get.page:1) >= totalPages));
			posts = result.posts;
			var index = 0;
			$.each(posts,function(){
				var ixs = findFeedIndex(this.feedId);
				var subtitle="";
				if (ixs.length==2){
					var folder = folders[ixs[0]];
					var folderInfo = (folder.name == "null")?"":folder.name+" | ";
					var feed = folder.feeds[ixs[1]];
					subtitle = '<div class="subtitle">[ '+folderInfo+'<a target="_blank" href="'+feed.link+'">'+feed.name+'</a> ] '+this.date+'</div>';
				}
				var unreadl=(this.unread==1)? "unread":"";
				html ='<div class="post '+unreadl+'" idxpost="'+(++index)+'">';
				html += '<div class="header">'+
							'<div class="title"><a target="_blank" href="'+this.link+'">'+this.title+'</a></div>'+subtitle+
						'</div>';
				html += '<div class="description">'+this.description+'</div>';
				html +='</div>';

				$("#posts_panel").append(html);
			});
			postsInit();
		},
		error: function (request, status, error){
			alert(error+" 0x001");
		},
		complete: function(){
			loading_stop();
		}
	});
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

	var page = (get.page)? (get.page):1;

	$("#pageNumber").html(page);
	$("#prevPage").prop('disabled',(page <= 1));

	$("#nextPage").prop('disabled',true);
}