
function displayFolders() {
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

function getHTMLFolder(folder,idx){
	var unread="";
	if (folder.unread > 0)
		unread = ' <span class="count">(<span class="num">'+folder.unread+'</span>)</span>';
	else
		unread = ' <span class="count hidden">(<span class="num">'+folder.unread+'</span>)</span>';

	var selected = (get.folder == folder.id);
	html = '<div class="folder'+((selected)?' selected':'')+'" idxFolder="'+idx+'" idFolder="'+folder.id+'">'+
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
		html += '<div class="feed'+((selected)?' selected':'')+((unread=="")?"":" unread")+'" idxFolder="'+indexFolder+'" idxFeed="'+indexFeed+'" idFeed="'+this.id+'">'+
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

		html += '<div class="tag'+((selected)?' selected':'')+((this.hidden==1)?" hidden":"")+'" idxTag="'+index+'" idTag="'+this.id+'">'+
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
			nextPostId = result.nextid;
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

			posts = result.posts;
			$.each(posts,function(){
				var ixs = findFeedIndex(this.feedId);
				var html = getHTMLPost(this,postCount++);
				$("#posts_panel").append(html);
			});
			postsInit(true);
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
	if (!nextPostId) return;
	var params = "nextid="+nextPostId+"&";
	updateNavigationElements();

	if (get.feed != undefined)			params += "feed="+get.feed+"&";
	else if (get.folder != undefined)	params += "folder="+get.folder+"&";
	else if (get.tag != undefined)		params += "tag="+get.tag+"&";

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
			nextPostId = result.nextid;
			$.each(result.posts,function(){
				posts.push(this);
				var html = getHTMLPost(this,postCount++);
				$("#posts_panel").append(html);
			});
			pagesLoaded++;
			$("#percentSeen").html(pagesLoaded+"/"+totalPages);
			postsInit(false);
			$("#loadMore").prop('disabled',false);
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
		subtitle = '<div class="subtitle">[ '+folderInfo+'<a target="_blank" href="'+feed.link+'">'+feed.name+'</a> ] '+post.date+'</div>';
	}
	var unreadl=(post.unread==1)? "unread":"";
	html ='<div class="post '+unreadl+'" idxpost="'+indexPost+'">';
	html += '<div class="header">'+
				'<div class="title"><a target="_blank" href="'+post.link+'">'+post.title+'</a></div>'+subtitle+
			'</div>';
	html += '<div class="description">'+post.description+'</div>';
	html +='</div>';
	return html;
}