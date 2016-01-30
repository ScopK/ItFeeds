Array.prototype.findBy = function(field,value) {
	for (var i=0; i<this.length; i++){
		var v = this[i];
		if (v[field] == value) return v;
	}
	return -1;
};
Array.prototype.findIndexBy = function(field,value) {
	for (var i=0; i<this.length; i++){
		var v = this[i];
		if (v[field] == value) return i;
	}
	return -1;
};
function objectEquals(t1,t2) {
	if (typeof t1 != typeof t2) return false;
	var c1 = 0;
	var c2 = 0;
	for (var i in t1){
		if (t2[i] != t1[i]){
			return false;
		}
		if (t1[i]!=undefined) c1++;
	}
	for (var i in t2) if (t2[i]!=undefined) c2++;
	return c1==c2;
}

// ###################################################################
// ###################################################################
// ######################################################## POST CLASS

var selectedPost = undefined;
function Post(json){
	this.id = json.id;
	this.idn = json.idx;
	this.feed = findFeed(json.feedId);
	this.feedId = json.feedId;
	this.tags = json.tags; // TODO?
	this.title = json.title;
	this.link = json.link;
	this.date = json.date;

	this.unread = json.unread == 1;
	this.favorite = json.favorite == 1;
	this.selected = false;
	this.lockautomark = false;
	this.loaded = true;

	var jhtml = $("<span>"+json.description+"</span>");
	jhtml.find("script").remove();
	//jhtml.find("iframe").prop("sandbox",true);
	jhtml.find("video").prop("controls",true);
	jhtml.find("a").attr("target","_blank");
	while (jhtml.children().last().prop("tagName") == "BR") jhtml.children().last().remove();
	this.description = jhtml.html();

	this.element = code.post(this);
}


Post.prototype.refreshTagList = function() {
	var html="";
	for (var i=0; i<this.tags.length; i++){
		var tag = this.tags[i];
		html += '<div class="tagname" idTag="'+tag.id+'">'+tag.name+'<button></button></div>';
	}
	var jhtml = $(html);
	var post = this;
	jhtml.find("button").click(function(){
		var tagId = this.parentElement.getAttribute("idTag");
		post.deleteTag(tagId);
	});
	$(this.element).find(".tagList").html(jhtml);
};

Post.prototype.addTag = function(tagName) {
	var post = this;
	call.addTag(this,tagName,function(success,data){
		if (success){
			for (var i=0; i<data.length; i++) {
				post.tags.push(data[i]);
			}
			post.refreshTagList();
		}
	});
};

Post.prototype.deleteTag = function(tagId) {
	var post = this;
	call.deleteTag(this,tagId,function(success,data){
		post.tags = $.grep(post.tags,function(el,i){
			return (el.id != tagId);	// delete
		});
		post.refreshTagList();
	});
};

Post.prototype.setUnread = function(unread,showMessage) {
	if (unread == this.unread) return;
	showMessage = showMessage==undefined?false:showMessage;

	this.lockautomark = true;
	var post = this;
	call.markPost(consts.UNREAD, unread, this, function(success,value){
		if (success){
			post.unread = value;

			if (value){
				if (post == player.playing){
					$("#video_viewer_dialog").addClass("selected");
					player.list.update();
				}
				$(post.element).addClass("unread");
				if (showMessage)
					showPopMessage("Post marked as unread");
				if (post.feed != undefined){
					post.feed.unread++;
					post.feed.folder.unread++;
					lateral.refresh.updateCounts(post.feed);
				}

			} else {
				if (post == player.playing){
					$("#video_viewer_dialog").removeClass("selected");
					player.list.update();
				}
				$(post.element).removeClass("unread");
				if (showMessage) 
					showPopMessage("Post marked as read");
				if (post.feed != undefined){
					post.feed.unread--;
					post.feed.folder.unread--;
					lateral.refresh.updateCounts(post.feed);
				}
			}
		}
	});
};

Post.prototype.setFavorite = function(favorite,showMessage) {
	if (favorite == this.favorite) return;
	showMessage = showMessage==undefined?false:showMessage;

	var post = this;
	call.markPost(consts.FAVORITE, favorite, this, function(success,value){
		if (success){
			post.favorite = value;

			if (value){
				$(post.element).addClass("favorite");
				if (showMessage) showPopMessage("Post added to favorites");
			} else{
				$(post.element).removeClass("favorite");
				if (showMessage) showPopMessage("Post deleted from favorites");
			}
		}
	});
};

Post.prototype.unselect = function() {
	if (selectedPost == this){
		selectedPost = undefined;
	}
	this.selected = false;
	$(this.element).removeClass("selected");
	return this;
};

Post.prototype.select = function() {
	if (selectedPost!=undefined){
		selectedPost.unselect();
	}
	this.selected = true;
	selectedPost = this;
	$(this.element).addClass("selected").removeClass("minimized");


	if (this.unread && !this.lockautomark && cookie.get("autoreadmode") < 2)
		this.setUnread(false);

	var postspage = (get.postspage)?get.postspage:10;
	if (posts.indexOf(this)+1 == pagesLoaded*postspage)
		call.loadMorePosts();

	lateral.refresh.controlButtons();

	return this;
};


Post.prototype.focus = function(speed) {
	speed = speed==undefined?100:speed;

	var mode = cookie.get("compactedmode");
	if (mode==2){
		var pos_top = this.element.offsetTop;
	} else {
		var p = $(this.element);

		var offset = (get.search)?32:3;
		var pos_top = offset + p.prevAll("div.post.minimized").length * (p.find(".header").height()+2);
		/*var notMinP = p.prevAll("div.post:not(.minimized)");
		for (var i=0; i<notMinP.length;i++){
			pos_top += notMinP[i].offsetHeight;
		}*/
	}
	$('html,body').animate({scrollTop: Math.floor(pos_top)}, speed);
	return this;
}


// ######################################################## POST CLASS
// ###################################################################
// ###################################################################
// #################################################### POST FUNCTIONS


function addPostClickEvent(){
	$(".post").click(function(ev){
		if (ev.target.tagName=="BUTTON") return;
		posts[$(this).attr("idxpost")].select();
	});
}

function markUnread(idx,value,showMessage){
	var post = idx==undefined? selectedPost : posts[idx];
	if (post==undefined) return;
	value = value==undefined? !post.unread : value;
	post.setUnread(value,showMessage);
}

function markFavorite(idx,value,showMessage){
	var post = idx==undefined? selectedPost : posts[idx];
	if (post==undefined) return;
	value = value==undefined? !post.favorite : value;
	post.setFavorite(value,showMessage);
}

function selectNextPost(){
	var n = posts.length;
	if (n>0){
		if (selectedPost == undefined){
			posts[0].select().focus();
		} else {
			var idx = posts.indexOf(selectedPost);
			idx++;
			if (idx < n){
				if (cookie.get("compactedmode")!=2){
					$(posts[idx].element).prevAll("div.post").addClass("minimized");
				}

				posts[idx].select().focus();
			}
		}
	}
}

function selectPrevPost(){
	var n = posts.length;
	if (n>0){
		if (selectedPost != undefined){
			var idx = posts.indexOf(selectedPost);
			idx--;
			if (idx >= 0 && posts[idx].loaded){
				if (cookie.get("compactedmode")==1)
					$(selectedPost.element).addClass("minimized");

				posts[idx].select().focus();
			}
		}
	}
}

function minimizeButtonAction(idx,value){
	var post = idx==undefined? selectedPost : posts[idx];
	var p = $(post.element);
	value = value==undefined? !p.hasClass("minimized") : value;

	var desc = p.find(".description");
	if (value){
		desc.css("height",desc.height());
		setTimeout(function(){
			p.addClass("minimized");
		},0);
		situatePostControls();
	} else {
		p.removeClass("minimized");
		setTimeout(function(){
			desc.css("height","");
		},100);
	}
	if ($(window).scrollTop()>p.offset().top)
		post.focus();
}

// #################################################### POST FUNCTIONS
// ###################################################################
// ###################################################################
// #################################################### FEED FUNCTIONS

function Feed(json,folder){
	this.id = json.id;
	this.folderId = json.folderId;
	this.folder = folder;
	this.name = json.name;

	this.unread = json.unread;
	this.count = json.count;
	this.deleted = json.deleted;

	this.link = json.link;
	this.rss_link = json.rss_link;

	this.max_unread = json.max_unread;
	this.enabled = json.enabled;
	this.filter = json.filter;
	this.upd_time = json.upd_time;
}

function findFeed(feedId){
	for (var i=0; i<folders.length; i++) {
		var f = folders[i];
		for (var j=0; j<f.feeds.length; j++) {
			var ff = f.feeds[j];
			if (ff.id==feedId){
				return ff;
			}
		}
	}
	return undefined;
}

// #################################################### FEED FUNCTIONS
// ###################################################################
// ###################################################################
// ################################################## FOLDER FUNCTIONS

function Folder(json){
	this.id = json.id;
	this.name = json.name;
	this.hidden = json.hidden == 1;
	this.unread = json.unread;
	this.count = json.count;

	this.feeds = [];
	for (var i=0; i<json.feeds.length; i++) {
		this.feeds.push(new Feed(json.feeds[i],this));
	}
}


// ################################################## FOLDER FUNCTIONS
// ###################################################################
// ###################################################################
// ##################################################### TAG FUNCTIONS

function Tag(json){
	this.id = json.id;
	this.name = json.name;
	this.hidden = json.hidden == 1;
	this.public = json.public == 1;
	this.count = json.count;
}

function addTags(newTags){
	for (var i=0;i<newTags.length;i++){
		var t = newTags[i];
		var found = undefined;
		for (var j=0; j<tags.length; j++){
			var v = tags[j];
			if (v.id == t.id){
				found = v;
				break;
			}
		}
		if (found==undefined){
			tags.push(t);
		} else {
			found.count = t.count;
		}
	}
	tags.sort(utils.nameSort);
	lateral.refresh.tags();
}

function removeTag(idTag){
	tags = $.grep(tags,function(el,i){
		if (el.id == idTag){
			el.count--;
			return (el.count > 0); // delete if 0
		}
		return true; //keep
	});
	lateral.refresh.tags();
}

// ##################################################### TAG FUNCTIONS
// ###################################################################
// ###################################################################
// #################################################### INITIALIZATION

$(document).ready(function(){
	$("#more-options-button").hide();
	$("#page").hide();

	//################# Blank space at the end of page
	$(window).resize(function(){
		$("#load_more_panel").css("height",$(window).height()-60);
	});
	$("#load_more_panel").css("height",$(window).height()-60);

	//################# Auto position post controls
	$(document).scroll(situatePostControls);

	//################# Mouse navigator
	$('#mouse_nav').mousedown(function(event) {
		switch (event.which) {
			case 1:
				selectNextPost();
				break;
			case 3:
				selectPrevPost();
				return false;
		}
	});

	$('#nextprevcontroller').mousedown(function(event) {
		switch (event.which) {
			case 1:
				player.next();
				break;
			case 3:
				player.prev();
				return false;
		}
	});

	if (cookie.get("fullscreen")=='1'){
		lateral.hide.all();
	}
	if ((compactedmode=cookie.get("compactedmode"))!==""){
		$("#posts_mode").val(compactedmode);
	}
	if ((autoreadmode=cookie.get("autoreadmode"))!==""){
		$("#autoread_mode").val(autoreadmode);
	}

    var allowed = true;
	$(document).keydown(function(e) { 
		if (e.ctrlKey || e.altKey || e.shiftKey) return;
    	if (e.which == 27 && $(".background-modal:not(#video_viewer_dialog)").is(":visible")){ $('.background-modal:not(#video_viewer_dialog)').fadeOut(100); dialog.effects.hide(); }
		if ($("input").is(":focus:visible")) return;
		if (!allowed) return false;
		allowed = false;
		//if ($(".loading").css("animation-play-state") != "paused") return;
		switch (e.which) {
			case 32: //space
			case 40: //down-arrow
				var body = $("html, body");
				body.animate({scrollTop: body.scrollTop() + 200}, {duration: 210, easing: 'linear', queue: false});
				setTimeout(function() {allowed = true;}, 200);
				$("button:focus").blur();
				return false;
			case 38: //up-arrow
				var body = $("html, body");
				body.animate({scrollTop: body.scrollTop() - 200}, {duration: 210, easing: 'linear', queue: false});
				setTimeout(function() {allowed = true;}, 200);
				$("button:focus").blur();
				return false;
			case 81: //q
				dialog.search.show();
				return false;
		    case 83: //s
		        markFavorite(null,null,true);
		        break;
		    case 78: //n
		    case 77: //m  
				markUnread(null,null,true);
		        break;
		    case 74: //j
		        selectNextPost();
		        break;
		    case 75: //k
		        selectPrevPost();
		        break;
		    case 70: //f
		    	lateral.toggle.all();
		    	break;
		    case 89: //y
		    	if (selectedPost != undefined){
					resetPlaylist();
					searchYoutubeVideo(selectedPost,true);
				}
		    	break;
		    case 71: //g
		    	cookie.actions.postsmode();
		    	return false;
		    case 72: //h
		    	cookie.actions.autoreadmode();
		    	return false;
		    case 76: //l
				call.loadMorePosts();
		    	break;
		    case 84: //t
			    dialog.addTags.show();
			    return false;
		    case 86: //v
		    	if (selectedPost != undefined){
		    		allowed = true;
		    		window.open(selectedPost.link, '_blank', '');
		    	}
		    	break;
		    case 66: //b
		    	if (selectedPost != undefined){
		    		allowed = true;
		    		window.open('post/'+selectedPost.id, '_blank', '');
		    	}
		    	break;
		    case 116: //f5
		    	$("#content .post").remove();
		    	$("#page").hide();
				$("#more-options-button").hide();
		    	call.userContent();
		    	return false;
		    case 190: //. (dot)
		    	lateral.toggle.quickSearch();
		    	break;
		    case 68: //d
		    case 123: //f12
		    default:
		        //showMessage("Key pressed:<br/>"+e.which+" - "+e.key,true);
		        return;
		}
	});

	$(document).keyup(function(e) { 
		allowed = true;
	});

});


function situatePostControls(){
	var pos = $(document).scrollTop();
	var controllers = $(".post").toArray();
	var idx = 0;
	$.each(controllers.reverse(),function(){
		var con = $(this).find(".controller");
		if ($(this).hasClass("minimized")){
			con.removeClass("fixed");
			con.css("top","0");
			return;
		}
		var top = this.offsetTop;
		var bot = top + this.offsetHeight - con.height();
		if (top < pos && pos < bot){
			con.addClass("fixed");
			con.css("top","0");

			//Auto-Mark Read
			var idx = $(this).attr("idxpost");
			var p = posts[idx];
			if (cookie.get("autoreadmode")==1){
				p.select();
				if (p.unread == 1 && (typeof p.lockautomark == "undefined" || !p.lockautomark) && !p.autoscrolled){
					p.autoscrolled=true;
					p.setUnread(false);
				}
			}
		} else if (pos >= bot){
			con.removeClass("fixed");
			con.css("top",(this.offsetHeight - con.height()-5)+"px");
		} else {
			con.removeClass("fixed");
			con.css("top","0");
		}
	});
}