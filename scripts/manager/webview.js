$(document).ready(function(){
	$("button.cleanAll").click(cleanAll);
	$("button.cancel").click(closeDialogs);
	$("button.editFeed").click(editFeed);
	$("button.cleanFeed").click(cleanFeed);
	$("button.editFolder").click(editFolder);
	$("button.cleanFolder").click(cleanFolder);
	$("button.addFeed").click(addFeed);
	$("button.deleteFeed").click(deleteFeed);
	$("button.deleteFolder").click(deleteFolder);
	$("button.editTag").click(editTag);
	$("button.deleteTag").click(deleteTag);

	$("button#folderConfig").click(showCleanFolder);
	$("button#folderDelete").click(showDeleteFolder);
	$("button#feedAdd").click(showAddFeed);
	$("button#feedConfig").click(showCleanFeed);
	$("button#feedDelete").click(showDeleteFeed);
	$("button#tagConfig").click(showConfigTag);
	$("button#tagDelete").click(showDeleteTag);

	$("#content_folders #header").click(showAddFolder);
	$("button.addFolder").click(addFolder);

	$("input[name='days'],input[name='unread']").on("focus",function(){
	    $("button.cleanFolder, button.cleanFeed, button.cleanAll").prop('disabled',false);    
	});

	$(document).scroll(function() {
		var pos = $(document).scrollTop()+
		         ($(window).height()/2)-
		         ($(".inside_dialog").height());
		$(".inside_dialog").css("top",pos);
	});

	var pos = $(document).scrollTop()+
	         ($(window).height()/2)-
	         ($(".inside_dialog").height());
	$(".inside_dialog").css("top",pos);

	//################# Hide message when mouse over
	$("#top_message").hover(hideMessage);
});

function load(flds){
	$("#folder_list").html("");
	var index=0;
	$.each(flds, function() {
		var style="";
		if (this.hidden == 1)
			style = 'hidden ';

		if (nullFolder = (this.name=="null")){
			this.name = "- NO FOLDER -";
			style += 'nullFolder';
		} else if (nullFolder = (this.name=="- NO FOLDER -")){
			style += 'nullFolder';
		}
		style = (style.length > 0)? ' class="'+style+'"':"";

		var line = '<div class="folder" idFolder="'+this.id+'" idxFolder="'+index+'"><h3'+style+'>'+this.name+' <span class="counter">('+this.unread+'/'+this.count+')</span></h3>';
		index++;

		var indexFeed=0;

		$.each(this.feeds, function() {
			var style="";
			if (this.enabled == 0)
				var style = ' class="disabled"';

			line += '<div class="feed" idFeed="'+this.id+'" idxFeed="'+indexFeed+'"><p'+style+'>'+this.name+' <span class="counter">('+this.unread+'/'+this.count+')</span></p></div>';
			indexFeed++;
		});

		line += '</div>';
		if (nullFolder)
			$("#folder_list").prepend(line);
		else
			$("#folder_list").append(line);
	});

	if (first){
		first = false;
		location.search.replace('?', '').split('&').every(function (val) {
			split = val.split("=", 2);
			if (split[0]=="folder"){
				showFolderTools.call($(".folder[idFolder='"+split[1]+"'] h3")[0]);
				return false;
			}
			if (split[0]=="feed"){
				showFeedTools.call($(".feed[idFeed='"+split[1]+"'] p")[0]);
				return false;
			}
			return true;
		});
	}
	
	$(".folder h3").click(showFolderTools);
	$(".feed p").click(showFeedTools);
	$("#folder_list").show();
}

function loadTags(tags){
	$("#tag_list").html("");

	var indexTag=0;
	$.each(tags, function() {
		var style="";
		if (this.hidden == 1)
			var style = ' class="hidden"';
		var line = '<div class="tag" idTag="'+this.id+'" idxTag="'+indexTag+'"><h3'+style+'>'+this.name+' <span class="counter">('+this.count+')</span></h3>';
		indexTag++;

		$("#tag_list").append(line);
	});

	if (first){
		first = false;
		location.search.replace('?', '').split('&').every(function (val) {
			split = val.split("=", 2);
			if (split[0]=="tag"){
				gotoTags();
				showTagTools.call($(".tag[idTag='"+split[1]+"'] h3")[0]);
				return false;
			}
			return true;
		});
	}
	$(".tag").click(showTagTools);
	$("#tag_list").show();
}

function gotoTags(){
	$("#content_folders").addClass("disabled");
	$("#content_folders").delay(300).hide(0);
	setTimeout( function() {
		$("#content_tags").show(00);
		$("#content_tags").removeClass("disabled");
	}, 300);
}

function gotoFolders(){
	$("#content_tags").addClass("disabled");
	$("#content_tags").delay(300).hide(0);
	setTimeout( function() {
		$("#content_folders").show(00);
		$("#content_folders").removeClass("disabled");
	}, 300);
}

function showFolderTools(){
	idxFolder = $(this).closest(".folder").attr("idxFolder");
	var folder = folders[idxFolder];

	$("#tools_folder h3").html(folder['name']+" - Options");
	$("#tools_folder button").attr("folderidx",idxFolder);
	$("#tools_folder").addClass("active");
}

function showCleanAll(){
	$("#clean_all input[name='days']").val(3);
	$("#clean_all input[name='unread']").prop('checked',false);

	$("button.cleanAll").prop('disabled',true);

	$("#clean_all").addClass("active");
}

function showCleanFolder(){
	$("#tools_folder").removeClass("active");
	idxFolder = $(this).attr("folderidx");
	var folder = folders[idxFolder];

	$("#clean_folder h3").html(folder['name']);

	$("#clean_folder input[name='folderId']").val(folder['id']);
	$("#clean_folder input[name='fname']").val(folder['name']);
	$("#clean_folder input[name='hid']").prop('checked',(folder['hidden'] == '1'));

	$("#clean_folder input[name='days']").val(3);
	$("#clean_folder input[name='unread']").prop('checked',false);

	$("button.cleanFolder").prop('disabled',true);
	if (folder.name == "- NO FOLDER -")
		$("#clean_folder form").first().find("input, button").prop('disabled',true);
	else
		$("#clean_folder form").first().find("input, button").prop('disabled',false);

	$("#clean_folder").addClass("active");
}

function showDeleteFolder(){
	$("#tools_folder").removeClass("active");
	idxFolder = $(this).attr("folderidx");
	var folder = folders[idxFolder];

	$("#confdel_folder h3").html(folder['name']);

	$("#confdel_folder input[name='folderId']").val(folder['id']);
	$("#confdel_folder input[name='pass']").val("");

	$("#confdel_folder").addClass("active");
}

function showAddFeed(){
	$("#tools_folder").removeClass("active");
	idxFolder = $(this).attr("folderidx");
	var folder = folders[idxFolder];
	
	$("#confadd_feed h3").html(folder['name']);

	$("#confadd_feed input[name='folderId']").val(folder['id']);
	$("#confadd_feed input[name='fname']").val("");
	$("#confadd_feed input[name='rlink']").val("");
	$("#confadd_feed input[name='link']").val("");
	
	$("#confadd_feed").addClass("active");
}

function showFeedTools(){
	console.log(this);
	idxFolder = $(this).closest(".folder").attr("idxFolder");
	idxFeed = $(this).closest(".feed").attr("idxfeed");
	var feed = folders[idxFolder].feeds[idxFeed];

	$("#tools_feed h3").html(feed['name']+" - Options");
	$("#tools_feed button").attr("folderidx",idxFolder);
	$("#tools_feed button").attr("feedidx",idxFeed);
	$("#tools_feed").addClass("active");
}

function showCleanFeed(){
	$("#tools_feed").removeClass("active");
	idxFolder = $(this).attr("folderidx");
	idxFeed = $(this).attr("feedidx");
	var feed = folders[idxFolder].feeds[idxFeed];

	$("#clean_feed h3").html(feed['name']);

	$("#clean_feed input[name='feedId']").val(feed['id']);
	$("#clean_feed input[name='fname']").val(feed['name']);
	$("#clean_feed input[name='rlink']").val(feed['rss_link']);
	$("#clean_feed input[name='link']").val(feed['link']);
	$("#clean_feed input[name='uptime']").val(feed['upd_time']);
	$("#clean_feed input[name='ena']").prop('checked',(feed['enabled'] == '1'));

	$("#clean_feed input[name='days']").val(3);
	$("#clean_feed input[name='unread']").prop('checked',false);

	$("#clean_feed #goLink a").attr("href",feed['link']);
	$("#clean_feed #goRss a").attr("href",feed['rss_link']);

	$("button.cleanFeed").prop('disabled',true);

	$("#clean_feed").addClass("active");
}

function showDeleteFeed(){
	$("#tools_feed").removeClass("active");
	idxFolder = $(this).attr("folderidx");
	idxFeed = $(this).attr("feedidx");
	var feed = folders[idxFolder].feeds[idxFeed];

	$("#confdel_feed h3").html(feed['name']);

	$("#confdel_feed input[name='feedId']").val(feed['id']);

	$("#confdel_feed").addClass("active");
}

function showAddFolder(){
	$("#add_folder input[name='foldername']").val("").focus();
	$("#add_folder").addClass("active");
}

function showTagTools(){
	idxTag = $(this).closest(".tag").attr("idxTag");
	var tag = tags[idxTag];

	$("#tools_tag h3").html(tag['name']+" - Options");
	$("#tools_tag button").attr("tagidx",idxTag);
	$("#tools_tag").addClass("active");
}

function showConfigTag() {
	$("#tools_tag").removeClass("active");
	idxTag = $(this).attr("tagidx");
	var tag = tags[idxTag];

	$("#editTag h3").html(tag['name']);

	$("#editTag input[name='tagId']").val(tag['id']);
	$("#editTag input[name='tagname']").val(tag['name']);
	$("#editTag input[name='hidden']").prop('checked',(tag['hidden'] == '1'));
	$("#editTag input[name='public']").prop('checked',(tag['public'] == '1'));

	$("#editTag").addClass("active");
}

function showDeleteTag() {
	$("#tools_tag").removeClass("active");
	idxTag = $(this).attr("tagidx");
	var tag = tags[idxTag];

	$("#confdel_tag h3").html(tag['name']);
	$("#confdel_tag input[name='tagId']").val(tag['id']);

	$("#confdel_tag").addClass("active");
}

function closeDialogs(){
	$(".inside_dialog").removeClass("active");
}

/** LOADING ANIMATIONS CONTROL **//*
function loading_stop() {
    $(".loading").css("animation-play-state","paused");
    $("#loading_panel").fadeOut();
}


function loading_run() {
    $(".loading").css("animation-play-state","running");
    $("#loading_panel").show();
}

function loading_toggle(){
    var status = $("#smallBall").css("animation-play-state");
    if (status == "paused") loading_run();
    else                    loading_stop();
}*/