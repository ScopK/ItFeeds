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
		    	$(".post").not(".selected").addClass("minimized");
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
	$(".post").click(function(){
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
		//$(".post[idxpost='"+postIdxSelected+"']").addClass("minimized");
		var idx = postIdxSelected;
		selectPost(++idx);
		$(".post[idxpost='"+postIdxSelected+"']").prevAll("div.post").addClass("minimized");
		var newPost = $(".post[idxpost='"+postIdxSelected+"']");
		focusPost(newPost,100);
	}
}

function prevPost(){
	if (postIdxSelected > 1){
		//$(".post[idxpost='"+postIdxSelected+"']").addClass("minimized");
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
	updateControlTags();
}

function updateControlTags(){
	var tags = posts[postIdxSelected-1].tags;
	if (tags.length > 0){
		var html="";
		$.each(tags, function(){
			html += '<div class="tagname" idTag="'+this.id+'">'+this.name+'<button onclick="deleteTag(this);"></button></div>';
		});
		$("#tagList").html(html);
		$("#tagList").show();
	} else {
		$("#tagList").hide();
	}
}

function disableControls(){
	$("#actions_panel button").prop('disabled',true);
	$("#actions_panel").addClass('disabled');

	$("#mouse_bottom").hide();
}

function enableControls(){
	var post = posts[postIdxSelected-1];

	if (post.unread == 1)	$("#actions_panel button.setUnread").addClass("unread");
	else	$("#actions_panel button.setUnread").removeClass("unread");

	if (post.favorite == 1)	$("#actions_panel button.setFav").addClass("fav");
	else	$("#actions_panel button.setFav").removeClass("fav");

	$("#actions_panel").removeClass('disabled');
	$("#actions_panel button").prop('disabled',false);

	//

	if (post.unread == 1)	$("#mouse_bottom .markunread").addClass("colored");
	else	$("#mouse_bottom .markunread").removeClass("colored");

	if (post.favorite == 1)	$("#mouse_bottom .markfav").addClass("colored");
	else	$("#mouse_bottom .markfav").removeClass("colored");

	$("#mouse_bottom").show();
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
		var post = posts[postIdxSelected-1];
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
						tags[found].count = this.count;//tags[found].count++;

					post.tags.push(tagInfo);
				});
				tags.sort(nameSort);
				post.tags.sort(nameSort);
				updateControlTags();
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
	var post = posts[postIdxSelected-1];
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
			updateControlTags();
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

function toogleUnreadPost(click){
	click = (typeof click !== 'undefined')? click : false;

	var val = (posts[postIdxSelected-1].unread == 1)?0:1;
	markPost(0,val,postIdxSelected,click);
}

function toogleFavPost(click){
	click = (typeof click !== 'undefined')? click : false;

	var val = (posts[postIdxSelected-1].favorite == 1)?0:1;
	markPost(1,val,postIdxSelected,click);
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