var FEED=0;var FOLDER=1;var TAG=2;var ALL=3;


function executeParams(){
	if (get.fav) $("#favsTButton").addClass("marked");
	else		 $("#favsTButton").removeClass("marked");

	if (get.unread) $("#unreadTButton").removeClass("marked");
	else		    $("#unreadTButton").addClass("marked");

	if (get.sortby) $("#sortTButton").html("Older");
	else		  	$("#sortTButton").html("Newer");


	if (get.feed != undefined){
		var ixs = findFeedIndex(get.feed);
		loadFeed(ixs[0],ixs[1]);
	} else if (get.folder != undefined){
		loadFolder(findFolderIndex(get.folder));
	} else if (get.tag != undefined){
		loadTag(findTagIndex(get.tag));
	} else {
		loadAll();
	}
}

function setContentActions(){
	$(".feed").click(function(){
		var index1 = $(this).attr("idxfolder");
		var index2 = $(this).attr("idxfeed");
		if (folders[index1].feeds[index2].id == get.feed){
			loadAll();
		} else {
			$(".folderfeeds").not($(this).closest(".folderfeeds")).slideUp();
			$(".expander").attr("hidd","1");
			$(".expander").html("+");
			loadFeed(index1, index2);
		}
	});

	$(".folder").click(function(event){
		if ($(event.target).is(".folderHeader,.folderTitle")){
			var index = $(this).attr("idxfolder");
			if (folders[index].id == get.folder){
				loadAll();
			} else {
				var button = $(".folder").not(this).find("button");
				button.closest(".folder").find(".folderfeeds").slideUp();
				button.attr("hidd","1");
				button.html("+");

				loadFolder(index);
			}
		}
	});

	$(".tag").click(function(){
		var index = $(this).attr("idxtag");

		if (tags[index].id == get.tag) {
			loadAll();
		} else {
			$(".folderfeeds").slideUp();
			$(".expander").attr("hidd","1");
			$(".expander").html("+");
			loadTag(index);
		}
	});
}

function loadFeed(indexFo, indexFe){
	var id = folders[indexFo].feeds[indexFe].id;
	get.feed = id;
	get.folder = undefined;
	get.tag = undefined;
	updateUrl();

	$(".feed, .tag, .folder").removeClass("selected");
	var object = $(".feed[idxFolder='"+indexFo+"'][idxFeed='"+indexFe+"']");
	object.addClass("selected");
	var button = object.closest(".folder").find("button");
	button.closest(".folder").find(".folderfeeds").show();
	button.attr("hidd","0");
	button.html("-");

	ajaxPosts("feed="+id);
}

function loadFolder(indexFo){
	var id = folders[indexFo].id;
	get.feed = undefined;
	get.folder = id;
	get.tag = undefined;
	updateUrl();

	$(".feed, .tag, .folder").removeClass("selected");
	var object = $(".folder[idxFolder='"+indexFo+"']");
	object.addClass("selected");
	var button = object.closest(".folder").find("button");
	button.closest(".folder").find(".folderfeeds").slideDown();
	button.attr("hidd","0");
	button.html("-");

	ajaxPosts("folder="+id);
}

function loadTag(indexTa){
	var id = tags[indexTa].id;
	get.feed = undefined;
	get.folder = undefined;
	get.tag = id;
	updateUrl();

	$(".feed, .tag, .folder").removeClass("selected");
	var object = $(".tag[idxtag='"+indexTa+"']");
	object.addClass("selected");

	ajaxPosts("tag="+id);
}

function loadAll(){
	get.feed = undefined;
	get.folder = undefined;
	get.tag = undefined;
	updateUrl();
	$(".feed, .tag, .folder").removeClass("selected");
	ajaxPosts("");
}

function ajaxPosts(args){
	var params = "";
	if (get.unread!=undefined)	params+="unread="+get.unread+"&";
	if (get.fav != undefined)	params+="fav="+get.fav+"&";
	if (get.postspage != undefined)	params+="postspage="+get.postspage+"&";
	if (get.page != undefined)	params+="page="+get.page+"&";
	if (get.sortby != undefined)	params+="sortBy="+get.sortby+"&";
	params += args;

	loading_run();
	$.ajax({
		url: "./ajax/get_posts.php",
		type: "GET",
		data: params,
		dataType : "json",
		success: function(result){
			$('#posts_panel').html("");
			$.each(result,function(){
				var ixs = findFeedIndex(this.feedId);
				var subtitle="";
				if (ixs.length==2){
					var feed = folders[ixs[0]].feeds[ixs[1]];
					subtitle = '<div class="subtitle">[ <a target="_blank" href="'+feed.link+'">'+feed.name+'</a> ] '+this.date+'</div>';
				}
				html ='<div class="post">';
				html += '<div class="header">'+
							'<div class="title"><a target="_blank" href="'+this.link+'">'+this.title+'</a></div>'+subtitle+
						'</div>';
				html += '<div class="description">'+this.description+'</div>';
				html +='</div>';

				$("#posts_panel").append(html);
			});
		},
		error: function (request, status, error){
			alert(error+" 0x001");
		},
		complete: function(){
			loading_stop();
		}
	});
}
