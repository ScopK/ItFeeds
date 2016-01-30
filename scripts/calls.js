var call = {
	loadingPosts: false,
	userContent: function(reload){
		if (reload==undefined) reload = true;
		loading_run();
		$.ajax({
			url: "./ajax/get_user_info.php",
			type: "GET",
			dataType : "json",
			success: function(result){
				tags = [];
				for (var i=0; i<result.tags.length; i++) {
					tags.push(new Tag(result.tags[i]));
				}
				folders = [];
				for (var i=0; i<result.folders.length; i++) {
					folders.push(new Folder(result.folders[i]));
				}

				lateral.refresh.folders();
				lateral.refresh.tags();

				if (reload)
					call.loadPosts();
			},
			error: function (request, status, error){
				showMessage("Couldn't get user info<br/>Error "+request.status+": "+request.responseText);
			},
			complete: function(){
				$("#page").show();
				$("#more-options-button").show();
				loading_stop();
			}
		});
	},
	loadPosts: function(){
		if (call.loadingPosts) return;
		call.loadingPosts=true;

		$("#loadMoreLabel").addClass('disabled');
		$("#loadMore").prop('disabled',true);

		lateral.refresh.navigationOptions();
		var params = "";

		if (get.feed != undefined)			params += "feed="+get.feed+"&";
		else if (get.folder != undefined)	params += "folder="+get.folder+"&";
		else if (get.tag != undefined)		params += "tag="+get.tag+"&";

		if (get.search != undefined)	params+="search="+get.search+"&";
		if (get.unread != undefined)	params+="unread="+get.unread+"&";
		if (get.fav != undefined)		params+="fav="+get.fav+"&";
		if (get.postspage != undefined)	params+="postspage="+get.postspage+"&";
		if (get.page != undefined)		params+="page="+get.page+"&";
		if (get.sortby != undefined)	params+="sortBy="+get.sortby+"&";

		loading_run();
		$.ajax({
			url: "./ajax/get_posts.php",
			type: "GET",
			data: params,
			dataType : "json",
			success: function(result){
				if (selectedPost != undefined)
					selectedPost.unselect();
				postCount = 0;
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
					var postObj = new Post(this);
					posts.push(postObj);
					
					$("#posts_panel").append(postObj.element);
					postObj.refreshTagList();

					var i = player.playlist.findIndexBy("id",postObj.id);
					if (i>=0){
						player.playlist[i] = postObj;
						if (player.playing != undefined && postObj.id == player.playing.id){
							player.playing = postObj;
						}
					}
				});
				addPostClickEvent();
				if (totalPages <= pagesLoaded){
					$("#loadMoreLabel").addClass('disabled');
					$("#loadMore").prop('disabled',true);
				} else {
					$("#loadMoreLabel").removeClass('disabled');
					$("#loadMore").prop('disabled',false);
				}
				$('html,body').animate({scrollTop: 0},0);
				lateral.refresh.controlButtons();
			},
			error: function (request, status, error){
				showMessage("Error getting posts<br/>"+error);
			},
			complete: function(){
				loading_stop();
				call.loadingPosts=false;
			}
		});
	},
	loadMorePosts: function(){
		if (call.loadingPosts) return;
		call.loadingPosts=true;

		$("#loadMoreLabel").addClass('disabled');
		$("#loadMore").prop('disabled',true);

		if (posts.length <= 0) return;
		var params = "nextid="+(posts[posts.length-1].id)+"&";

		if (get.feed != undefined)			params += "feed="+get.feed+"&";
		else if (get.folder != undefined)	params += "folder="+get.folder+"&";
		else if (get.tag != undefined)		params += "tag="+get.tag+"&";

		if (get.search != undefined)		params+="search="+get.search+"&";
		if (get.unread != undefined)		params+="unread="+get.unread+"&";
		if (get.fav != undefined)			params+="fav="+get.fav+"&";
		if (get.postspage != undefined)		params+="postspage="+get.postspage+"&";
		if (get.sortby != undefined)		params+="sortBy="+get.sortby+"&";

		loading_run();
		$.ajax({
			url: "./ajax/get_nextPosts.php",
			type: "GET",
			data: params,
			dataType : "json",
			success: function(result){
				$.each(result.posts,function(){
					var postObj = new Post(this);
					posts.push(postObj);

					$("#posts_panel").append(postObj.element);
					postObj.refreshTagList();

					var i = player.playlist.findIndexBy("id",postObj.id);
					if (i>=0){
						player.playlist[i] = postObj;
						if (player.playing != undefined && postObj.id == player.playing.id){
							player.playing = postObj;
						}
					}
				});

				pagesLoaded++;
				$("#percentSeen").html(pagesLoaded+"/"+totalPages);
				addPostClickEvent();

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
				call.loadingPosts=false;
				if (objectEquals(player.get,get) && player.playlist.length>0){
					for (var i=0;i<posts.length;i++){
						if (player.playlist.indexOf(posts[i])<0){
							player.playlist.push(posts[i]);
						}
					}
					player.list.update();
					/*var lastv = player.playlist[player.playlist.length-1];
					var i = posts.indexOf(lastv);
					if (i>=0){
						for (i++; i < posts.length; i++){
							player.playlist.push(posts[i]);
						}
						player.list.update();
					}*/
				}
			}
		});
	},
	loadMoreVideos: function(){
		if (call.loadingPosts) return;
		call.loadingPosts=true;

		var hadReachedLimit = $("#loadMore").prop('disabled');
		$("#loadMoreLabel").addClass('disabled');
		$("#loadMore").prop('disabled',true);

		if (player.playlist.length<=0) return;
		var content = player.get;

		var params = "nextid="+(player.playlist[player.playlist.length-1].id)+"&";
		if (content.feed != undefined)			params += "feed="+content.feed+"&";
		else if (content.folder != undefined)	params += "folder="+content.folder+"&";
		else if (content.tag != undefined)		params += "tag="+content.tag+"&";

		if (content.search != undefined)		params+="search="+content.search+"&";
		if (content.unread != undefined)		params+="unread="+content.unread+"&";
		if (content.fav != undefined)			params+="fav="+content.fav+"&";
		if (content.postspage != undefined)		params+="postspage="+content.postspage+"&";
		if (content.sortby != undefined)		params+="sortBy="+content.sortby+"&";

		loading_run();
		$.ajax({
			url: "./ajax/get_nextPosts.php",
			type: "GET",
			data: params,
			dataType : "json",
			success: function(result){
				$.each(result.posts,function(){
					var postObj = new Post(this);
					player.playlist.push(postObj);
				});
			},
			error: function (request, status, error){
				showMessage("Error getting posts<br/>"+error);
			},
			complete: function(){

				loading_stop();
				call.loadingPosts=false;
				if (!hadReachedLimit){
					$("#loadMoreLabel").removeClass('disabled');
					$("#loadMore").prop('disabled',false);
				}
				player.list.update();
			}
		});
	},
	addTag: function(post,tag,callback){
		loading_run();
		$.ajax({
			url: "./ajax/add_posttag.php",
			type: "POST",
			data: "postid="+post.id+"&tagname="+tag,
			dataType : "json",
			success: function(result){
				var newTags = [];
				$.each(result,function(){
					var tagObj = new Tag(this);
					newTags.push(tagObj);
				});
				addTags(newTags);

				if (callback) callback(true,newTags);
				showMessage("Tags added succesfully",true);
			},
			error: function (request, status, error){
				if (callback) callback(false);
				showMessage("An error ocurred adding tags<br/>"+error);
			},
			complete: function(){
				loading_stop();
			}
		});
	},
	deleteTag: function(post,idTag,callback){
		loading_run();
		$.ajax({
			url: "./ajax/delete_posttag.php",
			type: "POST",
			data: "postid="+post.id+"&tag="+idTag,
			success: function(result){
				removeTag(idTag);
				if (callback) callback(true);
				showMessage("Tag deleted succesfully",true);
			},
			error: function (request, status, error){
				if (callback) callback(false);
				showMessage("An error ocurred deleting tag<br/>"+error);
			},
			complete: function(){
				loading_stop();
			}
		});
	},
	videoFind: function(id,callback){
		loading_run();
		$.ajax({
			url: "./ajax/get_youtube_code.php",
			type: "GET",
			data: "postid="+id,
			dataType : "json",
			success: function(result){
				if (result.length>0){
					if (callback) callback(true,result);
				} else {
					showMessage("No videos were found");
					if (callback) callback(false,[]);
				}
			},
			error: function (request, status, error){
				loadingvid=false;
				if (request.responseText=="Code not found"){
					showMessage("No videos were found");
				} else {
					showMessage("JS Error "+request.status+": "+request.responseText);
				}
				if (callback) callback(false,[]);
			},
			complete: function(){
				loading_stop();
			}
		});
	},
	markPost: function(field, value, post, callback){
		switch(field){
			case consts.UNREAD: 	var fieldname = "unread"; break;
			case consts.FAVORITE: 	var fieldname = "fav"; break;
			default: return;
		}
		loading_run();
		$.ajax({
			url: "./ajax/mark_post.php",
			type: "POST",
			data: "postid="+post.id+"&"+fieldname+"="+(value?1:0),
			dataType : "json",
			success: function(result){
				if (callback){
					switch(field){
						case consts.UNREAD: 	callback(true,result.unread == 1); break;
						case consts.FAVORITE: 	callback(true,result.favorite == 1); break;
					}
				}


				if (field==consts.UNREAD){ // read/unread


				} else if (field==consts.FAVORITE) {	// fav/unfav 

				}
				lateral.refresh.controlButtons();
			},
			error: function (request, status, error){
				showMessage("An error ocurred marking post<br/>"+error);
			},
			complete: function(){
				loading_stop();
			}
		});
	}
}


$(document).ready(function(){
	call.userContent();

	//Polling
	var idleTime = 0;
	var reloadTime = 0;
	$(document).mousemove(function(e){ idleTime = 0; });
	$(document).click(function(e){ idleTime = 0; });
	$(document).keypress(function(e){ idleTime = 0; });

	setInterval(function(){
		idleTime++;
		reloadTime++;
		if (reloadTime >= 4){ //2 minutes
			call.userContent(selectedPost==undefined && idleTime>=4);
			reloadTime=0;
			idleTime=0;
		}
	},30000);
});


// Global VARS:
var folders;
var tags;
var posts;

var postIdxSelected = 0; //delete?
var firstPostIdx=0;
var postCount;
var pagesLoaded;

var totalPages;
var totalPosts;

var map = {feed:{},folder:{},tag:{}}

var get;
(function(){ // readGetParameters()
	get = {};
	location.search.replace('?', '').split('&').forEach(function (val) {
	    split = val.split("=", 2);
	    get[split[0]] = split[1];
	});
})();