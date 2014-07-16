$(document).ready(function(){
	$(".user_field").click(function(){
		var userSelected = $(this).html();
		loadFolders(userSelected);
	});

	$("button.cancel").click(closeDialogs);
	$("button.editFeed").click(editFeed);
	$("button.cleanFeed").click(cleanFeed);
	$("button.editFolder").click(editFolder);
	$("button.cleanFolder").click(cleanFolder);
	$("button.addFeed").click(addFeed);
	$("button.deleteFeed").click(deleteFeed);
	$("button.deleteFolder").click(deleteFolder);
	$("button.unlockHidden").click(unlockHidden);
	$("button.logoutButton").click(logoutButton);

	$("button#folderConfig").click(showCleanFolder);
	$("button#folderDelete").click(showDeleteFolder);
	$("button#feedAdd").click(showAddFeed);
	$("button#feedConfig").click(showCleanFeed);
	$("button#feedDelete").click(showDeleteFeed);

	$("#content_folders #header").click(showAddFolder);
	$("button.addFolder").click(addFolder);

	$("input[name='days'],input[name='unread']").on("focus",function(){
	    $("button.cleanFolder, button.cleanFeed").prop('disabled',false);    
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
		var line = '<div class="tag" idTag="'+this.id+'" idxFolder="'+indexTag+'"><h3'+style+'>'+this.name+' <span class="counter">('+this.count+')</span></h3>';
		indexTag++;

		$("#tag_list").append(line);
	});
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
	$("#tools_folder").find("button").attr("folderidx",idxFolder);
	$("#tools_folder").addClass("active");
}

function showCleanFolder(){
	$("#tools_folder").removeClass("active");
	idxFolder = $(this).attr("folderidx");
	var folder = folders[idxFolder];

	$("#clean_folder h3").html(folder['name']);

	$("#clean_folder").find("input[name='folderId']").val(folder['id']);
	$("#clean_folder").find("input[name='fname']").val(folder['name']);
	$("#clean_folder").find("input[name='hid']").prop('checked',(folder['hidden'] == '1'));

	$("#clean_folder").find("input[name='days']").val(3);
	$("#clean_folder").find("input[name='unread']").prop('checked',false);

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

	$("#confdel_folder").find("input[name='folderId']").val(folder['id']);
	$("#confdel_folder").find("input[name='pass']").val("");

	$("#confdel_folder").addClass("active");
}

function showAddFeed(){
	$("#tools_folder").removeClass("active");
	idxFolder = $(this).attr("folderidx");
	var folder = folders[idxFolder];
	
	$("#confadd_feed h3").html(folder['name']);

	$("#confadd_feed").find("input[name='folderId']").val(folder['id']);
	$("#confadd_feed").find("input[name='fname']").val("");
	$("#confadd_feed").find("input[name='rlink']").val("");
	$("#confadd_feed").find("input[name='link']").val("");
	
	$("#confadd_feed").addClass("active");
}


function showFeedTools(){
	idxFolder = $(this).closest(".folder").attr("idxFolder");
	idxFeed = $(this).closest(".feed").attr("idxfeed");
	var feed = folders[idxFolder].feeds[idxFeed];

	$("#tools_feed h3").html(feed['name']+" - Options");
	$("#tools_feed").find("button").attr("folderidx",idxFolder);
	$("#tools_feed").find("button").attr("feedidx",idxFeed);
	$("#tools_feed").addClass("active");
}

function showCleanFeed(){
	$("#tools_feed").removeClass("active");
	idxFolder = $(this).attr("folderidx");
	idxFeed = $(this).attr("feedidx");
	var feed = folders[idxFolder].feeds[idxFeed];

	$("#clean_feed h3").html(feed['name']);

	$("#clean_feed").find("input[name='feedId']").val(feed['id']);
	$("#clean_feed").find("input[name='fname']").val(feed['name']);
	$("#clean_feed").find("input[name='rlink']").val(feed['rss_link']);
	$("#clean_feed").find("input[name='link']").val(feed['link']);
	$("#clean_feed").find("input[name='uptime']").val(feed['upd_time']);
	$("#clean_feed").find("input[name='ena']").prop('checked',(feed['enabled'] == '1'));

	$("#clean_feed").find("input[name='days']").val(3);
	$("#clean_feed").find("input[name='unread']").prop('checked',false);

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

	$("#confdel_feed").find("input[name='feedId']").val(feed['id']);

	$("#confdel_feed").addClass("active");
}

function showAddFolder(){
	$("#add_folder").find("input[name='foldername']").val("").focus();
	$("#add_folder").addClass("active");
}

function closeDialogs(){
	$(".inside_dialog").removeClass("active");
}

function showHiddenDialog(){
	$('#login_hidden').fadeIn(100);
	$('#pwdHiddenField').val("");
	$('#pwdHiddenField').focus();

}


/** LOADING ANIMATIONS CONTROL **/
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
}