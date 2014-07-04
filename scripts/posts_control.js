$(document).ready(function(){
    $(document).bind('keydown', function(e) {
    	if ($(".loading").css("animation-play-state") != "paused") return;
        if (e.ctrlKey || e.altKey || e.shiftKey) return;
        if ($("input").is(":focus")) return;
        switch (e.which) {
            case 83: //s
                toogleFavPost();
                break;
            case 77: //m  
                toogleUnreadPost();
                break;
            case 74: //j
                nextPost();
                break;
            case 75: //k
                prevPost();
                break;
            case 32: //space
            case 66: //b
            case 68: //d
            case 70: //f
            case 78: //n
            case 84: //t
            case 86: //v
            case 116: //f5
            case 123: //f12
            default: return;
                //alert(e.which+" - "+e.key);
        }
    });
});

function postsInit(){
	$(".post").click(function(){
		selectPost($(this).attr("idxpost"));/*
		$(".post").removeClass("selected");
		postIdxSelected = $(this).attr("idxpost");
		$(this).addClass("selected");*/
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
	} else {
		$('html,body').animate({scrollTop: 0},0); 
		disableControls();
	}
}



function nextPost(){
	if (postIdxSelected < ((get.postspage)?get.postspage:10)){
		var idx = postIdxSelected;
		selectPost(++idx);
		var newPost = $(".post[idxpost='"+postIdxSelected+"']");
		focusPost(newPost,100);
	} else {
		preselectPost = 1;
		nextPage();
	}
}


function prevPost(){
	if (postIdxSelected > 1){
		var idx = postIdxSelected;
		selectPost(--idx);
		var newPost = $(".post[idxpost='"+postIdxSelected+"']");
		focusPost(newPost,100);
	} else {
		preselectPost = (get.postspage)? -get.postspage:-10;
		prevPage();
	}
}

function focusPost(post,speed){
	$('html,body').animate({scrollTop: post.offset().top}, speed); 
}

function selectPost(idx){
	var pos = $(".post[idxpost='"+idx+"']").offset().top;
	$("#actions_panel").css("top",pos);

	if (postIdxSelected == idx) return;

	$(".post[idxpost='"+postIdxSelected+"']").removeClass("selected");
	postIdxSelected = idx;
	$(".post[idxpost='"+postIdxSelected+"']").addClass("selected");

	if (posts[idx-1].unread == 1)
		markPost(0, 0, idx);

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
}

function enableControls(){
	var post = posts[postIdxSelected-1];

	if (post.unread == 1)	$("#actions_panel button.setUnread").addClass("unread");
	else	$("#actions_panel button.setUnread").removeClass("unread");

	if (post.favorite == 1)	$("#actions_panel button.setFav").addClass("fav");
	else	$("#actions_panel button.setFav").removeClass("fav");

	$("#actions_panel").removeClass('disabled');
	$("#actions_panel button").prop('disabled',false);
}

function addTag(){
	var tag = encodeURIComponent($("#newtagField").val());
	if (tag.length > 0 && tag.indexOf('%20') < 0) {
		var post = posts[postIdxSelected-1];
		loading_run();
		$.ajax({
			url: "./ajax/add_posttag.php",
			type: "POST",
			data: "postid="+post.id+"&tagname="+tag,
			dataType : "json",
			success: function(result){
				var tagInfo = Array();
				tagInfo["id"] = result.id;
				tagInfo["name"] = result.name;
				var found = -1;
				$.each(tags, function(i){
					found = (this.name == tag)?i:-1;
					return (found == -1);
				});
				if (found == -1){
					tags.push(result);
					tags.sort(nameSort);
				} else {
					tags[found].count++;
				}
				post.tags.push(tagInfo);
				post.tags.sort(nameSort);
				updateControlTags();
				displayTags();
			},
			error: function (request, status, error){
				alert(error+" 0x001");
			},
			complete: function(){
				loading_stop();
			}
		});

		$("#newtagField").val("");
		$('#add_tag').fadeOut(100);
	}
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
		},
		error: function (request, status, error){
			alert(error+" 0x001");
		},
		complete: function(){
			loading_stop();
		}
	});
}


function toogleUnreadPost(){
	var val = (posts[postIdxSelected-1].unread == 1)?0:1;
	markPost(0,val,postIdxSelected);
}

function toogleFavPost(){
	var val = (posts[postIdxSelected-1].favorite == 1)?0:1;
	markPost(1,val,postIdxSelected);
}


//  First param: 0-Read/unread  1-Favorite
// Second param: 0-read/nofav   1-unread/favorite
//  Third param: post idx
function markPost(field, value, postidx){
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
				if (value==0) {
					$(".post[idxpost='"+postidx+"']").removeClass("unread");
					folders[idx[0]].unread--;
					folders[idx[0]].feeds[idx[1]].unread--;
				} else {
					$(".post[idxpost='"+postidx+"']").addClass("unread");
					folders[idx[0]].unread++;
					folders[idx[0]].feeds[idx[1]].unread++;
				}
				updateCounts(idx);
			} else {	// fav/unfav 
			}
			enableControls();

		},
		error: function (request, status, error){
			alert(error+" 0x001");
		},
		complete: function(){
			loading_stop();
		}
	});
}

function updateCounts(idx){
	if (folders[idx[0]].name != "null"){
		var folderelem = $(".folder[idxfolder='"+idx[0]+"']");
		folderelem.find(".folderTitle .count .num").html(folders[idx[0]].unread);
		folderelem.find(".feed[idxfeed='"+idx[1]+"'] .feedTitle .count .num").html(folders[idx[0]].feeds[idx[1]].unread);
	} else {
		$("#feeds").find(".feed[idxfeed='"+idx[1]+"'] .feedTitle .count .num").html(folders[idx[0]].feeds[idx[1]].unread);		
	}
}