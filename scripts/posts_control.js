$(document).ready(function(){
    var allowed = true;
	$(document).keydown(function(e) { 
		if (e.ctrlKey || e.altKey || e.shiftKey) return;
    	if (e.which == 27 && $(".background-modal").is(":visible")) $('.background-modal').fadeOut(100);
		if ($("input").is(":focus:visible")) return;
		if (!allowed) return false;
		allowed = false;
		if (e.which == 32 || e.which == 40){	// space
	        var body = $("html, body");
	        body.animate({scrollTop: body.scrollTop() + 200}, {duration: 210, easing: 'linear', queue: false});
	        setTimeout(function() {allowed = true;}, 200);
	        $("button:focus").blur();
	        return false;
		} else if (e.which == 38) {
	        var body = $("html, body");
	        body.animate({scrollTop: body.scrollTop() - 200}, {duration: 210, easing: 'linear', queue: false});
	        setTimeout(function() {allowed = true;}, 200);
	        $("button:focus").blur();
	        return false;
		}
		if ($(".loading").css("animation-play-state") != "paused") return;
		switch (e.which) {
			case 81: //q
				showSearchDialog();
				return false;
		    case 83: //s
		        toogleFavPost(true);
		        break;
		    case 78: //n
		    case 77: //m  
		        toogleUnreadPost(true);
		        break;
		    case 74: //j
		        nextPost();
		        break;
		    case 75: //k
		        prevPost();
		        break;
		    case 70: //f
		    	toggleLateralMenu();
		    	break;
		    case 71: //g
		    	var compactedmode = getCookie("compactedmode");
		    	compactedmode++;
		    	switch(compactedmode){
		    		case 1: 
		    			showPopMessage("Compacted Mode"); 
				    	$(".post").not(".selected").addClass("minimized");
		    			break;
		    		case 2: 
		    			//showPopMessage("Extra Mode"); break;
		    		case 3: 
		    			showPopMessage("Normal Mode"); compactedmode=0;
		    			$(".post").removeClass("minimized");
		    			break;
		    	}
				setCookie("compactedmode",compactedmode,3);
		    	if (postIdxSelected>0)
					focusPost($(".post[idxpost='"+postIdxSelected+"']"),100);
		    	return false;
		    case 76: //l
				loadMore();
		    	break;
		    case 84: //t
			    showAddTagsDialog();
			    return false;
		    case 86: //v
		    	if (postIdxSelected>0){
		    		var post = posts[postIdxSelected-1];
		    		allowed = true;
		    		window.open(post.link, '_blank', '');
		    	}
		    	break;
		    case 66: //b
		    	if (postIdxSelected>0){
		    		var post = posts[postIdxSelected-1];
		    		allowed = true;
		    		window.open('post/'+post.id, '_blank', '');
		    	}
		    	break;
		    case 116: //f5
		    	$("#page").hide();
		    	initialize();
		    	//randomColors();
		    	return false;
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


function postsInit(scrollTop){
	$(".post").click(function(ev){
		if (ev.target.tagName=="BUTTON") return;
		selectPost($(this).attr("idxpost"));
	});

	if (preselectPost > 0){
		selectPost(preselectPost);
		preselectPost=0;
		var newPost = $(".post[idxpost='"+postIdxSelected+"']");
		focusPost(newPost,0);

	} else if (preselectPost < 0){
		selectPost(-preselectPost);
		preselectPost=0;
		var newPost = $(".post[idxpost='"+postIdxSelected+"']");
		focusPost(newPost,0);
	} else if (scrollTop) {
		$('html,body').animate({scrollTop: 0},0); 
		disableControls();
	}
}

function nextPost(){
	if (postIdxSelected < posts.length){
		var idx = postIdxSelected;
		selectPost(++idx);
		$(".post[idxpost='"+postIdxSelected+"']").prevAll("div.post").addClass("minimized");
		var newPost = $(".post[idxpost='"+postIdxSelected+"']");
		focusPost(newPost,100);
	}
}

function prevPost(){
	if (postIdxSelected > 1){
		var idx = postIdxSelected;
		selectPost(--idx);
		var newPost = $(".post[idxpost='"+postIdxSelected+"']");
		focusPost(newPost,100);
	}
}

function focusPost(post,speed){
	$('html,body').animate({scrollTop: Math.round(post.offset().top)}, speed); 
}

function selectPost(idx){
	if (postIdxSelected == idx) return;

	$(".post[idxpost='"+postIdxSelected+"']").removeClass("selected");
	postIdxSelected = idx;
	$(".post[idxpost='"+postIdxSelected+"']").addClass("selected");
	$(".post[idxpost='"+postIdxSelected+"']").removeClass("minimized");

	if (posts[idx-1].unread == 1)
		markPost(0, 0, idx);

	var postspage = (get.postspage)?get.postspage:10;
	if (postIdxSelected == pagesLoaded*postspage)
		loadMore();

	enableControls();
}

function disableControls(){
	$("#mouse_bottom").hide();
}

function enableControls(){
	if (postIdxSelected>0){
		var post = posts[postIdxSelected-1];

		if (post.unread == 1)	$("#mouse_bottom .markunread").addClass("colored");
		else	$("#mouse_bottom .markunread").removeClass("colored");

		if (post.favorite == 1)	$("#mouse_bottom .markfav").addClass("colored");
		else	$("#mouse_bottom .markfav").removeClass("colored");

		$("#mouse_bottom").show();
	}
}

function addTag(){
	var tag = "";
	$("#add_tag .taglist p.selected").each(function(){
		tag += " "+this.innerHTML;
	});
	if ($("#newtagField").val())
		tag += " "+$("#newtagField").val();
	tag = encodeURIComponent(tag.substring(1));
	if (tag.length > 0) {
		var idxpost = addTagTo;
		var post = posts[idxpost-1];
		loading_run();
		$.ajax({
			url: "./ajax/add_posttag.php",
			type: "POST",
			data: "postid="+post.id+"&tagname="+tag,
			dataType : "json",
			success: function(result){
				$.each(result,function(){
					var tagname = this.name;
					var tagInfo = Array();
					tagInfo["id"] = this.id;
					tagInfo["name"] = tagname;
					var found = -1;
					$.each(tags, function(i){
						found = (this.name == tagname)?i:-1;
						return (found == -1);
					});

					if (found == -1)
						tags.push(this);
					else
						tags[found].count = this.count;

					post.tags.push(tagInfo);
				});
				tags.sort(nameSort);
				post.tags.sort(nameSort);
				updateControlTags(idxpost);
				displayTags();
				showMessage("Tags added succesfully",true);
			},
			error: function (request, status, error){
				showMessage("An error ocurred adding tags<br/>"+error);
			},
			complete: function(){
				loading_stop();
			}
		});

		$("#newtagField").val("");
		$('#add_tag').fadeOut(100);
	} else
		showMessage("Select or write a tag");
}

function deleteTag(me){
	var idTag = $(me).parent().attr("idTag");
	var idxPost = $(me).closest(".post").attr("idxpost");
	var post = posts[idxPost-1];
	loading_run();
	$.ajax({
		url: "./ajax/delete_posttag.php",
		type: "POST",
		data: "postid="+post.id+"&tag="+idTag,
		success: function(result){
			post.tags = $.grep(post.tags,function(el,i){
			    return (el.id != idTag);	// delete
			});
			tags = $.grep(tags,function(el,i){
			    if (el.id == idTag){
			    	el.count--;
			    	return (el.count > 0); // delete if 0
			    }
			    return true; //keep
			});
			updateControlTags(idxPost);
			displayTags();
			showMessage("Tag deleted succesfully",true);
		},
		error: function (request, status, error){
			showMessage("An error ocurred deleting tag<br/>"+error);
		},
		complete: function(){
			loading_stop();
		}
	});
}

function toggleMinimize(idx,value){
	idx = (typeof idx !== 'undefined')? idx : postIdxSelected;
	var post = $(".post[idxpost='"+idx+"']");
	value = (typeof value !== 'undefined')? value : !post.hasClass("minimized");

	if (value){
		post.addClass("minimized");
		situatePostControls();
	} else
		post.removeClass("minimized");
}

function toogleUnreadPost(click,idx){
	click = (typeof click !== 'undefined')? click : false;
	idx = (typeof idx !== 'undefined')? idx : postIdxSelected;

	var val = (posts[idx-1].unread == 1)?0:1;
	markPost(0,val,idx,click);
}

function toogleFavPost(click,idx){
	click = (typeof click !== 'undefined')? click : false;
	idx = (typeof idx !== 'undefined')? idx : postIdxSelected;

	var val = (posts[idx-1].favorite == 1)?0:1;
	markPost(1,val,idx,click);
}

//  First param: 0-Read/unread  1-Favorite
// Second param: 0-read/nofav   1-unread/favorite
//  Third param: post idx
function markPost(field, value, postidx, click){
	click = (typeof click !== 'undefined')? click : false;

	var fieldname = (field==0)? "unread":"fav";
	loading_run();
	$.ajax({
		url: "./ajax/mark_post.php",
		type: "POST",
		data: "postid="+posts[postidx-1].id+"&"+fieldname+"="+value,
		dataType : "json",
		success: function(result){
			posts[postidx-1] = result;
			var post = posts[postidx-1];
			if (field==0){ // read/unread
				var idx = findFeedIndex(post.feedId);
				if (idx.length>0){
					var folder = folders[idx[0]];
					if (value==0) {
						$(".post[idxpost='"+postidx+"']").removeClass("unread");
						folder.unread--;
						folder.feeds[idx[1]].unread--;
					} else {
						$(".post[idxpost='"+postidx+"']").addClass("unread");
						folder.unread++;
						folder.feeds[idx[1]].unread++;
					}
					updateCounts(idx);
				} else {
					if (value==0)
						$(".post[idxpost='"+postidx+"']").removeClass("unread");
					else
						$(".post[idxpost='"+postidx+"']").addClass("unread");
				}
				if (click){
					if (value==0)
						showPopMessage("Post marked as read");
					else
						showPopMessage("Post marked as unread");
				}

			} else {	// fav/unfav 
				if (value==0)
					$(".post[idxpost='"+postidx+"']").removeClass("favorite");
				else
					$(".post[idxpost='"+postidx+"']").addClass("favorite");
				if (click){
					if (value==0)
						showPopMessage("Post deleted from favorites");
					else
						showPopMessage("Post added to favorites");
				}
			}
			enableControls();
		},
		error: function (request, status, error){
			showMessage("An error ocurred marking post<br/>"+error);
		},
		complete: function(){
			loading_stop();
		}
	});
}

function updateCounts(idx){
	var folder = folders[idx[0]];
	var feed = folder.feeds[idx[1]];
	if (folder.name != "null"){
		var folderelem = $(".folder[idxfolder='"+idx[0]+"']");
		var folderCount = folderelem.find(".folderTitle .count");
		folderCount.find(".num").html(folder.unread);
		if (folder.unread==0) 	folderCount.addClass("hidden");
		else 					folderCount.removeClass("hidden");
		var feedCount = folderelem.find(".feed[idxfeed='"+idx[1]+"'] .feedTitle .count");
		feedCount.find(".num").html(feed.unread);
		if (feed.unread==0) 	feedCount.addClass("hidden");
		else 					feedCount.removeClass("hidden");
	} else {
		var feedCount = $("#feeds").find(".feed[idxfeed='"+idx[1]+"'] .feedTitle .count");
		feedCount.find(".num").html(feed.unread);
		if (feed.unread==0) 	feedCount.addClass("hidden");
		else 					feedCount.removeClass("hidden");
	}
}