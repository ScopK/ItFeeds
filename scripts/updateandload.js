function displayFolders() {

	var idsExtended = [];
	$.each($("#folders .folder:not(.selected)"),function(){
		if ($(this).find(".folderfeeds")[0].style.display=="block")
			idsExtended.push(this.getAttribute("idfolder"));
	});

	var html = '';
	var feedHtml = '';
	$.each(folders,function(index){
		if (this.name == "null"){
			feedHtml += getHTMLFeeds(this.feeds,index);
		} else {
			html += getHTMLFolder(this,index);
		}
	});
	$("#folders").html(html);
	$("#feeds").html(feedHtml);

	idsExtended.forEach(function(id){
		toogleViewFeeds($(".folder[idfolder='"+id+"'] .expander")[0],0);
	});

	if (get.feed != undefined){
		$(".feed[idFeed='"+get.feed+"']").closest(".folderfeeds").show();
		$(".feed[idFeed='"+get.feed+"']").closest(".folder").find("button").html("-");
	}
	setFeedsActions();
}

function displayTags() {
	var html = getHTMLTags(tags);
	$("#tags").html(html);
	html = getHTMLTagsSelector(tags);
	$("#add_tag .taglist").html(html);

	setTagsActions();
}
function videolistUpdate(){
	if (!playlist.on) return;
	var html="";
	for (var i in playlist.songs){
		var song = playlist.songs[i];
		var classes = (i==playlist.index)?"video listening":"video";
		var pos = i;
		pos++;
		html+="<div class='"+classes+"' idx='"+i+"' onclick='selectPostVideo("+i+")'><span class='idx'>"+pos+"</span><span class='tit'>"+song.title+"</span></div>";
	}
	$("#videolist").html(html);
}

function getHTMLFolder(folder,idx){
	var unread="";
	if (folder.unread > 0)
		unread = ' <span class="count">(<span class="num">'+folder.unread+'</span>)</span>';
	else
		unread = ' <span class="count hidden">(<span class="num">'+folder.unread+'</span>)</span>';

	var selected = (get.folder == folder.id);
	html = '<div class="folder'+((selected)?' selected':'')+'" idxFolder="'+idx+'" idFolder="'+folder.id+'" oncontextmenu="return cmFolder(event,this);">'+
				'<div class="folderHeader'+((folder.hidden==1)?" hidden":"")+'">'+
					'<button class="expander" onclick="toogleViewFeeds(this)">'+((selected)?'-':'+')+'</button>'+
					'<span class="folderTitle">'+(folder.name)+unread+'</span>'+
				'</div>'+
				'<div class="folderfeeds"'+((selected)?' style="display:block"':'')+'>';

	html += getHTMLFeeds(folder.feeds,idx);
	html += '</div></div>';
	return html;
}

function getHTMLFeeds(feed_list,indexFolder){
	var html="";
	$.each(feed_list,function(indexFeed){
		var unread="";
		if (this.unread > 0)
			unread = ' <span class="count">(<span class="num">'+this.unread+'</span>)</span>';
		else 
			unread = ' <span class="count hidden">(<span class="num">'+this.unread+'</span>)</span>';

		var selected = (get.feed == this.id);
		html += '<div class="feed'+((selected)?' selected':'')+((unread=="")?"":" unread")+'" idxFolder="'+indexFolder+'" idxFeed="'+indexFeed+'" idFeed="'+this.id+'" oncontextmenu="return cmFeed(event,this);">'+
					'<div class="feedTitle">'+(this.name)+unread+'</div>'+
				'</div>';
	});
	return html;
}

function getHTMLTags(tag_list){
	var html = "";
	$.each(tags,function(index){
		var count="";
		if (this.count > 0)
			count = ' ';

		var selected = (get.tag == this.id);

		html += '<div class="tag'+((selected)?' selected':'')+((this.hidden==1)?" hidden":"")+((this.public==1)?" public":"")+'" idxTag="'+index+'" idTag="'+this.id+'" oncontextmenu="return cmTag(event,this);">'+
					'<span class="name">'+(this.name)+'</span> <span class="count">('+this.count+')</span></div>';

	});
	return html;
}

function getHTMLTagsSelector(tag_list){
	var html = "";
	$.each(tags,function(index){
		html += '<p '+((this.hidden==1)?'class="hiddenTag"':'')+' onclick="addSelectedTag(this)">'+(this.name)+'</p>';
	});
	return html;
}


/* ##############################################################
#################################################################
#################################################################
#################################################################
#################################################################
#################################################################
#################################################################
###########################POSTS#################################
#################################################################*/

function ajaxPosts(args){
	var params = "";
	updateNavigationElements();
	if (get.search != undefined)	params+="search="+get.search+"&";
	if (get.unread != undefined)	params+="unread="+get.unread+"&";
	if (get.fav != undefined)		params+="fav="+get.fav+"&";
	if (get.postspage != undefined)	params+="postspage="+get.postspage+"&";
	if (get.page != undefined)		params+="page="+get.page+"&";
	if (get.sortby != undefined)	params+="sortBy="+get.sortby+"&";
	params += args;

	loading_run();
	$.ajax({
		url: "./ajax/get_posts.php",
		type: "GET",
		data: params,
		dataType : "json",
		success: function(result){
			postIdxSelected = 0;
			postCount = 1;
			pagesLoaded = 1;

			$('#posts_panel').html("");

			var page = (get.page)?get.page:1;
			var postspage = (get.postspage)?get.postspage:10;
			totalPages = Math.ceil((get.postspage)?result.total/get.postspage:result.total/10)-(page-1);
			totalPosts = result.total-((page-1)*postspage);
			$("#totalPages").html(totalPosts);
			$("#percentSeen").html(pagesLoaded+"/"+totalPages);

			posts = [];
			$.each(result.posts,function(){
				var jhtml = $("<span>"+this.description+"</span>");
				jhtml.find("script").remove();
				//jhtml.find("iframe").prop("sandbox",true);
				jhtml.find("video").prop("controls",true);
				jhtml.find("a").attr("target","_blank");
				while (jhtml.children().last().prop("tagName") == "BR") jhtml.children().last().remove();
				this.description = jhtml.html();

				posts.push(this);

				var html = getHTMLPost(this,postCount++);
				$("#posts_panel").append(html);
				updateControlTags(postCount-1);
			});
			postsInit(true);
			if (totalPages <= pagesLoaded){
				$("#loadMoreLabel").addClass('disabled');
				$("#loadMore").prop('disabled',true);
			} else {
				$("#loadMoreLabel").removeClass('disabled');
				$("#loadMore").prop('disabled',false);
			}
		},
		error: function (request, status, error){
			showMessage("Error getting posts<br/>"+error);
		},
		complete: function(){
			loading_stop();
		}
	});
}

function ajaxMorePosts(args){
	if (posts.length <= 0) return;
	var params = "nextid="+(posts[posts.length-1].id)+"&";
	updateNavigationElements();

	if (get.feed != undefined)			params += "feed="+get.feed+"&";
	else if (get.folder != undefined)	params += "folder="+get.folder+"&";
	else if (get.tag != undefined)		params += "tag="+get.tag+"&";

	if (get.search != undefined)		params+="search="+get.search+"&";
	if (get.unread != undefined)		params+="unread="+get.unread+"&";
	if (get.fav != undefined)			params+="fav="+get.fav+"&";
	if (get.postspage != undefined)		params+="postspage="+get.postspage+"&";
	if (get.sortby != undefined)		params+="sortBy="+get.sortby+"&";
	params += args;

	loading_run();
	$.ajax({
		url: "./ajax/get_nextPosts.php",
		type: "GET",
		data: params,
		dataType : "json",
		success: function(result){
			$.each(result.posts,function(){
				var jhtml = $("<span>"+this.description+"</span>");
				jhtml.find("script").remove();
				//jhtml.find("iframe").prop("sandbox",true);
				jhtml.find("video").prop("controls",true);
				jhtml.find("a").attr("target","_blank");
				while (jhtml.children().last().prop("tagName") == "BR") jhtml.children().last().remove();
				this.description = jhtml.html();

				posts.push(this);

				var html = getHTMLPost(this,postCount++);
				$("#posts_panel").append(html);
				updateControlTags(postCount-1);
			});

			pagesLoaded++;
			$("#percentSeen").html(pagesLoaded+"/"+totalPages);
			postsInit(false);
			if (totalPages > pagesLoaded){
				$("#loadMore").prop('disabled',false);
				$("#loadMoreLabel").removeClass('disabled');
			}

			var deleteSince = ($(".post").length) - ((get.postspage?get.postspage:10)*6);
			if (deleteSince > 0){
				var ps = $(".post:lt("+deleteSince+")");
				var height = 0;
				$.each(ps,function(){
					height+=this.scrollHeight
				});
				var body = $("html, body");
				body.animate({scrollTop: body.scrollTop() - height}, {duration: 0});
				for (var i; i<deleteSince;i++)
					posts[i] = undefined;
					//posts.shift();
				ps.remove();
				firstPostIdx += deleteSince;
			}
		},
		error: function (request, status, error){
			showMessage("Error getting posts<br/>"+error);
		},
		complete: function(){
			loading_stop();
		}
	});
}

function getHTMLPost(post,indexPost){
	var html="";

	var ixs = findFeedIndex(post.feedId);
	var subtitle="";

	if (ixs.length==2){
		var folder = folders[ixs[0]];
		var folderInfo = (folder.name == "null")?"":folder.name+" | ";
		var feed = folder.feeds[ixs[1]];
		subtitle = '<div class="subtitle">[ '+folderInfo+'<a target="_blank" href="'+feed.link+'">'+feed.name+'</a> ] <a class="date" target="_blank" href="/post/'+post.id+'">'+post.date+'</a></div>';
	}
	var controller = '<div class="controller"><div class="control_panel">'+
	    '<button class="setUnread" onclick="toogleUnreadPost(true,'+indexPost+')"></button>'+
	    '<button class="setFav" onclick="toogleFavPost(true,'+indexPost+')"></button>'+
	    '<button class="addTag" onclick="showAddTagsDialog('+indexPost+');return false;"></button>'+
	    '<button class="maxminimize" onclick="toggleMinimize('+indexPost+')"></button>'+
	    '<button class="moreOptions" onclick="searchYoutubeVideo('+(indexPost-1)+',true)"></button>'+
	    '</div><div class="tagList"></div></div>';

	var unreadl=(post.unread==1)? "unread":"";
	var favoritel=(post.favorite==1)? "favorite":"";
	var compactedl=(getCookie("compactedmode")==1)? "minimized":"";
	html ='<div class="post '+unreadl+' '+favoritel+' '+compactedl+'" idxpost="'+indexPost+'">';
	html += '<div class="header">'+
				'<div class="title"><a target="_blank" href="'+post.link+'">'+post.title+'</a></div>'+subtitle+
			'</div>';
	html += controller;
	html += '<div class="description">'+post.description+'</div>';
	html +='</div>';
	return html;
}

function updateControlTags(idx){
	idx = (typeof idx !== 'undefined')? idx : postIdxSelected;
	var tags = posts[idx-1].tags;

	//if (tags.length > 0){
		var html="";
		$.each(tags, function(){
			html += '<div class="tagname" idTag="'+this.id+'">'+this.name+'<button onclick="deleteTag(this);"></button></div>';
		});
		$(".post[idxpost='"+idx+"'] .tagList").html(html);
	//}
}