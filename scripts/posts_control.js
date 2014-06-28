$(document).ready(function(){
    $(document).bind('keydown', function(e) {
        if (e.ctrlKey || e.altKey || e.shiftKey) return;
        if ($("input").is(":focus")) return;
        switch (e.which) {
            case 32: //space
                alert("space");
                break;
            case 70: //f
                alert("f");
                break;
            case 66: //b
                alert("b");
                break;
            case 68: //d
                alert("d");
                break;
            case 83: //s
                alert("s");
                break;
            case 77: //m  
            case 78: //n
                alert("m/n");
                break;
            case 74: //j
                nextPost();
                break;
            case 75: //k
                prevPost();
                break;
            case 84: //t
                alert("t");
                break;
            case 86: //v
                alert("v");
                break;
            case 116: //f5
            case 123: //f12
            	break;
            default:
                alert(e.which+" - "+e.key);
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
	enableControls();
	$(".post[idxpost='"+postIdxSelected+"']").removeClass("selected");
	postIdxSelected = idx;
	$(".post[idxpost='"+postIdxSelected+"']").addClass("selected");
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
	$("#actions_panel").removeClass('disabled');
	$("#actions_panel button").prop('disabled',false);
}

function addTag(){
	var tag = encodeURIComponent($("#newtagField").val());
	if (tag.length > 0) {
		//TODO: check if tag has spaces
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