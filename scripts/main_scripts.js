var FEED=0;var FOLDER=1;var TAG=2;var ALL=3;


function loadPosts(){
	sel = [];
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
		loadFeed($(this).attr("idxfolder"), $(this).attr("idxfeed"));
	});

	$(".folderTitle").click(function(){
		loadFolder($(this).closest(".folder").attr("idxfolder"));
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
	button.closest(".folder").find(".folderfeeds").show();
	button.attr("hidd","0");
	button.html("-");

	ajaxPosts("folder="+id);
}

function loadAll(){
	$(".feed, .tag").removeClass("selected");
	ajaxPosts("");
}

function ajaxPosts(args){
	loading_run();
	$.ajax({
		url: "./ajax/get_posts.php",
		type: "GET",
		data: args,
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
