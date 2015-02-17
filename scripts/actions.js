function readGetParameters(){
	get = [];
	location.search.replace('?', '').split('&').forEach(function (val) {
	    split = val.split("=", 2);
	    get[split[0]] = split[1];
	});
}

function updateUrl(){
	var args = "";
	var first=1;
	$.each(Object.keys(get),function(){
		if (get[this]==undefined) return true;
		if (first)
			first = undefined;
		else
			args+="&";
		args+=this+"="+get[this];
	});
	if (args.length>0){
		var page = "./index.php?"+args;
		$("#managerLink").attr("href","./manager.php?"+args);
	} else {
		var page = "./index.php";
		$("#managerLink").attr("href","./manager.php");
	}
	window.history.pushState("", "", page);
}

function logout(){
	loading_run();
	$.ajax({
		url: "./ajax/logout.php",
		type: "POST",
		success: function(result){
			window.location = "./login.php"+location.search;
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
			loading_stop();
		}
	});
}

function toogleViewFeeds(me,sec){
	var hid = $(me).html();

	if (hid == '+'){
		$(me).closest(".folder").find(".folderfeeds").slideDown(sec);
		$(me).html("-");
	} else {
		$(me).closest(".folder").find(".folderfeeds").slideUp(sec);
		$(me).html("+");
	}
	//stopPropagation();
}

function toggleFavs(me){
	if (!get.fav) {
		get.fav = "1";
		get.unread = "0";
		get.feed = undefined;
		get.folder = undefined;
		get.tag = undefined;
		$(".feed, .tag, .folder").removeClass("selected");
	} else 
		get.fav = undefined;
	updateUrl();
	reloadPosts();
}

function toggleUnread(me){
	if (!get.unread)
		get.unread = "0";
	else {
		get.unread = undefined;
		get.fav = undefined;
	}
	updateUrl();
	reloadPosts();
}

function toggleSort(me){
	if (!get.sortby)
		get.sortby = "0";
	else 
		get.sortby = undefined;
	updateUrl();
	reloadPosts();
}

var addTagTo;
function showAddTagsDialog(idx){
	idx = (typeof idx !== 'undefined')? idx : postIdxSelected;
	if (idx>0) {
		addTagTo = idx;
		$('#add_tag').fadeIn(100);
		$('#add_tag p').removeClass("selected");
		$('#newtagField').val("");
		$('#newtagField').focus();
	}
}

var videos;
var idxVideo;
function searchYoutubeVideo(findNext){
	findNext = (typeof findNext === 'undefined')? false : findNext;
	if (postIdxSelected>0){
		var postidx = postIdxSelected;
		var postid = posts[postIdxSelected-1]['id'];
		loading_run();
		$.ajax({
			url: "./ajax/get_youtube_code.php",
			type: "GET",
			data: "postid="+postid,
			dataType : "json",
			success: function(result){
				if (result.length>0){
					var post = posts[postidx-1];
					videos=result;
					idxVideo=0;
					$("#youtube_viewer_dialog").show();
					$("#youtube_viewer_dialog").attr("postid",postid);
					$("#youtube_viewer_dialog").attr("postidx",postidx);
					console.log(post);
					if (post.unread==1)
						$("#youtube_viewer_dialog").addClass("selected");
					else
						$("#youtube_viewer_dialog").removeClass("selected");
					if (result[0].indexOf("/")>=0)
						var html = "<iframe src='https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/"+result[0]+"?auto_play=true' allowfullscreen frameBorder='0' width='100%' height='460' style='display:block'></iframe>";
					else
						var html = "<iframe src='http://www.youtube.com/embed/"+result[0]+"?autoplay=1&theme=light' allowfullscreen frameBorder='0' width='100%' height='460' style='display:block'></iframe>";
					$("#youtube_td").html(html);
					$("#counter_videos").html("Next video ("+1+"/"+result.length+")");
				} else {
					showMessage("No videos were found");
				}
			},
			error: function (request, status, error){
				if (request.responseText=="Code not found"){
					showMessage("No videos were found");
					if (findNext && postidx < posts.length){
						nextPost();
						searchYoutubeVideo();
					}
				} else {
					showMessage("JS Error "+request.status+": "+request.responseText);
				}
			},
			complete: function(){
				loading_stop();
			}
		});
	}
}

function nextVideo(){
	if (videos.length>1){
		idxVideo++;
		if (idxVideo==videos.length)
			idxVideo=0;
		if (videos[idxVideo].indexOf("/")>=0)
			var html = "<iframe src='https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/"+videos[idxVideo]+"?auto_play=true' allowfullscreen frameBorder='0' width='100%' height='460' style='display:block'></iframe>";
		else
			var html = "<iframe src='http://www.youtube.com/embed/"+videos[idxVideo]+"?autoplay=1' allowfullscreen frameBorder='0' width='100%' height='460' style='display:block'></iframe>";
		$("#youtube_td").html(html);
		$("#counter_videos").html("Next video ("+(idxVideo+1)+"/"+videos.length+")");
	}
}
function nextPostVideo(findNext){
	findNext = (typeof findNext === 'undefined')? false : findNext;
	nextPost();
	searchYoutubeVideo(findNext);
}

function showSearchDialog(){
	$('#search_dialog').fadeIn(100);
	$('#searchField').val("");
	$('#searchField').focus();
}

function showPasswordChangeDialog(){
	$('#settings_panel').addClass('hidden');
	$('#pwchange_dialog').fadeIn(100);
	$("#oldPassField").val("").focus();
	$("#newPassField").val("");
	$("#newPass2Field").val("");
}

function showLockPasswordChangeDialog(){
	$('#settings_panel').addClass('hidden');
	$('#pwlchange_dialog').fadeIn(100);
	$("#oldLPassField").val("").focus();
	$("#newLPassField").val("");
	$("#newLPass2Field").val("");
}

function showUnlockDialog(){
	$('#settings_panel').addClass('hidden');
	if ($('#unlockButton').hasClass("highlight-color")){
		$("#lockPassField").val("");
		unlockAction();
		return;
	}
	$('#unlock_dialog').fadeIn(100);
	$('#lockPassField').val("").focus();
}

function changePasswordAction(){
	var newp = $("#newPassField").val();
	if (newp != $("#newPass2Field").val()){
		showMessage("Passwords doesn't match");
		return;
	}
	var oldp = $("#oldPassField").val();
	loading_run();
	$.ajax({
		url: "./ajax/change_password.php",
		type: "POST",
		data: "lock=0&old="+oldp+"&new="+newp,
		success: function(result){
			if (result=="ok"){
				$('#pwchange_dialog').fadeOut(100);
			} else {
				showMessage("An error ocurred. Try again.");
				$("#oldPassField").val("");
				$("#newPass2Field").val("");
			}
		},
		error: function (request, status, error){
			showMessage("JS Error "+request.status+": "+request.responseText);
		},
		complete: function(){
			loading_stop();
		}
	});
}

function changeLockPasswordAction(){
	var newp = $("#newLPassField").val();
	if (newp != $("#newLPass2Field").val()){
		showMessage("Passwords doesn't match");
		return;
	}
	var oldp = $("#oldLPassField").val();
	loading_run();
	$.ajax({
		url: "./ajax/change_password.php",
		type: "POST",
		data: "lock=1&old="+oldp+"&new="+newp,
		success: function(result){
			if (result=="ok"){
				$('#pwlchange_dialog').fadeOut(100);
			} else {
				showMessage("An error ocurred. Try again.");
				$("#oldLPassField").val("");
				$("#newLPass2Field").val("");
			}
		},
		error: function (request, status, error){
			showMessage("JS Error "+request.status+": "+request.responseText);
		},
		complete: function(){
			loading_stop();
		}
	});
}

function unlockAction(){
	var unlockp = $("#lockPassField").val();
	$('#unlock_dialog').fadeOut(100);
	loading_run();
	$.ajax({
		url: "./ajax/login_hidden.php",
		type: "POST",
		data: "hiddenPass="+unlockp,
		dataType : "json",
		success: function(result){
			folders = result.folders;
			tags = result.tags;
			displayFolders();
			displayTags();
			reloadPosts();
			if (result.unlocked=="true"){
				$('#unlockButton').addClass("highlight-color");
			} else {
				$('#unlockButton').removeClass("highlight-color");
			}
		},
		error: function (request, status, error){
			showMessage("JS Error "+request.status+": "+request.responseText);
		},
		complete: function(){
			loading_stop();
		}
	});
}

function hideLateralMenu(){
    $("#show-lateral-button").html("&rsaquo;");
    $("#lateral_menu").addClass("hidden");
	$("#content").css("margin-left","0");
	$('#settings_panel').addClass('hidden');
	setCookie("fullscreen","1",1);
}

function showLateralMenu(){
	$("#show-lateral-button").html("&lsaquo;");
	$("#content").css("margin-left",$("#lateral_menu").outerWidth()+"px");
    $("#lateral_menu").removeClass("hidden");
    deleteCookie("fullscreen");
}

function toggleLateralMenu(){
    if ($("#lateral_menu").hasClass("hidden"))
        showLateralMenu();
    else
        hideLateralMenu();
}

function addSelectedTag(element){
	$(element).toggleClass("selected");
}

function loadMore(){
	if (!$("#loadMoreLabel").hasClass('disabled')) {
		$("#loadMoreLabel").addClass('disabled');
		$("#loadMore").prop('disabled',true);
		ajaxMorePosts("");
	}
}

function openNewWindowTag(tagid){
	var res = location.search.replace(/&?(feed|folder|tag|unread)=[\w-]*/g, "");
	var url = window.location.pathname;
	if (res.length<2)
		url+="?tag="+tagid+"&unread=0";
	else
		url+=res+"&tag="+tagid+"&unread=0";
	window.open(url, '_blank', '');
}

function openNewWindowFeed(feedid){
	var res = location.search.replace(/&?(feed|folder|tag)=[\w-]*/g, "");
	var url = window.location.pathname;
	if (res.length<2)
		url+="?feed="+feedid;
	else
		url+=res+"&feed="+feedid;
	window.open(url, '_blank', '');
}

function openNewWindowFolder(folderid){
	var res = location.search.replace(/&?(feed|folder|tag)=[\w-]*/g, "");
	var url = window.location.pathname;
	if (res.length<2)
		url+="?folder="+folderid;
	else
		url+=res+"&folder="+folderid;
	window.open(url, '_blank', '');
}

//#################### CONTEXT MENU
function cmTag(e,context){
	var arr = [{
		name: "Open in new window",
		function: function(){
			openNewWindowTag($(this).attr("idtag"));
		},
		context: context
	},{
		type: "separator"
	}];

	if ($(context).hasClass("public"))
		arr.push({
			name: "Open with public tag viewer",
			function: function(){
				var id = $(this).attr("idtag");
				window.open('tag/'+id, '_blank', '');
			},
			context: context
		});
	arr.push({
		name: "Edit options",
		type: "folder",
		options: [
			{
				name: "Settings",
				function: function(){
					var idx = this.getAttribute("idxtag");
					showSettings_tag(idx);
				},
				context: context
			},{ type : "separator" },{
				name: "Delete",
				function: function(){
					var idx = this.getAttribute("idxtag");
					showDelete_tag(idx);
				},
				context: context
			}]
	});
	setCMContent(arr);
	showCM(e.clientX,e.clientY)
	return false;
}

function cmFeed(e,context){
	var arr = [{
		name: "Open in new window",
		function: function(){
			openNewWindowFeed($(this).attr("idfeed"));
		},
		context: context
	},{ type: "separator" },{
		name: "Open website",
		function: function(){
			var fo = $(this).attr("idxfolder");
			var fe = $(this).attr("idxfeed");
			window.open(folders[fo].feeds[fe].link, '_blank', '');
		},
		context: context
	},{
		name: "Open RSS link",
		function: function(){
			var fo = $(this).attr("idxfolder");
			var fe = $(this).attr("idxfeed");
			window.open(folders[fo].feeds[fe].rss_link, '_blank', '');
		},
		context: context
	},{
		type: "separator"
	},{
		name: "Edit options",
		type: "folder",
		options: [
			{
				name: "Settings",
				function: function(){
					var fidx = this.getAttribute("idxfolder");
					var idx = this.getAttribute("idxfeed");
					showSettings_feed(fidx,idx);
				},
				context: context
			},{
				name: "Clean",
				function: function(){
					var fidx = this.getAttribute("idxfolder");
					var idx = this.getAttribute("idxfeed");
					showClean_feed(fidx,idx);
				},
				context: context
			},{ type : "separator" },{
				name: "Delete",
				function: function(){
					var fidx = this.getAttribute("idxfolder");
					var idx = this.getAttribute("idxfeed");
					showDelete_feed(fidx,idx);
				},
				context: context
			}]
		}];

	setCMContent(arr);
	showCM(e.clientX,e.clientY);
	e.stopPropagation();
	return false;
}

function cmFolder(e,context){
	var arr = [{
		name: "Open in new window",
		function: function(){
			openNewWindowFolder($(this).attr("idfolder"));
		},
		context: context
	},{
		type: "separator"
	},{
		name: "Edit options",
		type: "folder",
		options: [
			{
				name: "Add Feed",
				function: function(){
					var idx = this.getAttribute("idxfolder");
					showCreate_feed(idx);
				},
				context: context
			},{
				name: "Settings",
				function: function(){
					var idx = this.getAttribute("idxfolder");
					showSettings_folder(idx);
				},
				context: context
			},{
				name: "Clean",
				function: function(){
					var idx = this.getAttribute("idxfolder");
					showClean_folder(idx);
				},
				context: context
			},{ type : "separator" },{
				name: "Delete",
				function: function(){
					var idx = this.getAttribute("idxfolder");
					showDelete_folder(idx);
				},
				context: context
			}]
		}];

	setCMContent(arr);
	showCM(e.clientX,e.clientY)
	return false;
}