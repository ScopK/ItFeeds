function readGetParameters(){
	get = {};
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
	var page = "./index.php"+(args.length>0?("?"+args):"");
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
		openModal();
	}
}

var videos;
var idxVideo;
var videoDimensions = [1280,720];//[700,460];
function videoResize(ww,hh){
	var tw = $(window).width();
	var th = $(window).height()-41;
	if (ww>tw) ww=tw;
	else if (ww<200) ww=200;
	if (hh>th) hh=th;
	else if (hh<200) hh=200;
	videoDimensions = [ww,hh];
	var wpx = ww+"px";
	var hpx = hh+"px";
	$("#youtube_viewer").css("width",wpx);
	$("#SoundCloudIframe,#YoutubeScriptIframe").css("min-height",hpx);
	$("#HTML5VideoPlayer").css("height",hpx);
	//$("#SoundCloudIframe,#YoutubeScriptIframe").css("min-height",hpx);
	//$("#HTML5VideoPlayer").css("max-height",hpx);
}
$(document).ready(function(){
	var back = $("#youtube_viewer").parent();
	var initPos = [];
	var multiplies = [];
	back.mousedown(function(ev){
		if (back.is(ev.target)){
			var tw = $(window).width()/2;
			var th = $(window).height()/2;
			var x = ev.clientX;
			var y = ev.clientY;
			var mw = x<tw?-2:2;
			var mh = y<th?-2:2;
			multiplies=[mw,mh];
			initPos=[x,y];
			back.bind("mousemove", dragListener);
		}
	});

	back.mouseup(function(ev){
		back.unbind("mousemove", dragListener);
	});

	var dragListener = function(ev){
		if (ev.buttons!=1){
			back.unbind("mousemove", dragListener);
			return;
		}
		var x = ev.clientX;
		var y = ev.clientY;
		var dx = (x-initPos[0])*multiplies[0];
		var dy = (y-initPos[1])*multiplies[1];

		videoResize(videoDimensions[0]+dx,videoDimensions[1]+dy);
		initPos=[x,y];
	}
});
var playlist={};
var unloadVideo = function(){};
function resetPlaylist(){
	playlist={};
}

var loadingvid=false;
function searchYoutubeVideo(idx,overwrite,findNext){
	findNext = (typeof findNext === 'undefined')? false : findNext;

	if (idx<0 && (overwrite || !playlist.on)) return;

	if (!playlist.on || overwrite){
		restorePlayer();
		playlist.on=true;
		playlist.command={};
		playlist.songs=[];
		var postid = posts[idx].id;
		for(var i in get){
			playlist.command[i] = get[i];
		}
		var count=0;
		addToPlaylist(postid);
		var open_dialog = true;

	} else {
		var open_dialog = false;
	}

	var postidx = playlist.index;
	var postid = playlist.songs[playlist.index].id;
	loading_run();
	if (!togglePlayer(true))
		$("#youtube_viewer").css("width",videoDimensions[0]+"px");

	$.ajax({
		url: "./ajax/get_youtube_code.php",
		type: "GET",
		data: "postid="+postid,
		dataType : "json",
		success: function(result){
			var song = playlist.songs[postidx];
			if (result.length>0){
				videos=result;
				blacklist=[];
				for(var i=0;i<videos.length;i++){
					if ($.inArray(videos[i].src,blacklist)>=0){
						videos.splice(i,1);
						i--;
					}
				}
				idxVideo=0;
				loading_run();
				var fadeIn = function(){
					if (open_dialog){
						$("#youtube_viewer_dialog").fadeIn(100);
						maxPlayer();
					}
					loading_stop();
					loadingvid=false;
				};
				$("#youtube_viewer_dialog").attr("postid",postid);
				//$("#youtube_viewer_dialog").attr("postidx",postidx+1);
				$("#youtube_viewer_dialog .title").html(song.title);
				if (song.unread==1)
					$("#youtube_viewer_dialog").addClass("selected");
				else
					$("#youtube_viewer_dialog").removeClass("selected");
				switch(videos[0].type){
					case "yt":
						youtubePlayer(videos[0].src,fadeIn);break;
					case "sc":
						soundcloudPlayer(videos[0].src,fadeIn);break;
					case "h5":
						html5Player(videos[0].src,fadeIn);break;
				}
				$("#counter_videos").html(1+"/"+videos.length);
			} else {
				showMessage("No videos were found");
				loadingvid=false;
			}
		},
		error: function (request, status, error){
			loadingvid=false;
			if (request.responseText=="Code not found"){
				showMessage("No videos were found");
				if (findNext && postidx < posts.length){
					nextPostVideo(true);
				}
			} else {
				console.log(request);
				showMessage("JS Error "+request.status+": "+request.responseText);
			}
		},
		complete: function(){
			loading_stop();
		}
	});
}

function nextVideo(){
	if (videos.length>1){
		idxVideo++;
		if (idxVideo==videos.length)
			idxVideo=0;
		switch(videos[idxVideo].type){
			case "yt":
				youtubePlayer(videos[idxVideo].src);break;
			case "sc":
				soundcloudPlayer(videos[idxVideo].src);break;
			case "h5":
				html5Player(videos[idxVideo].src);break;
		}
		$("#counter_videos").html((idxVideo+1)+"/"+videos.length);
	}
}
function nextPostVideo(findNext){
	if (loadingvid) return;
	findNext = (typeof findNext === 'undefined')? false : findNext;
	if (postIdxSelected>0 && playlist.songs[playlist.index].id == posts[postIdxSelected-1].id){
		nextPost(false);
	}
	if (playlist.index<playlist.songs.length-1){
		playlist.index++;
		$("#videolist .video.listening").removeClass("listening");
		$("#videolist .video[idx='"+playlist.index+"']").addClass("listening");
		loadingvid=true;
		searchYoutubeVideo(-1,false,findNext);
		if (playlist.songs.length == playlist.index+1){
			loadMoreSongs();
		}
	}
}
function prevPostVideo(findPrev){
	if (loadingvid) return;
	findPrev = (typeof findPrev === 'undefined')? false : findPrev;
	if (postIdxSelected>0 && playlist.songs[playlist.index].id == posts[postIdxSelected-1].id){
		prevPost(false);
	}
	if (playlist.index>0){
		playlist.index--;
		$("#videolist .video.listening").removeClass("listening");
		$("#videolist .video[idx='"+playlist.index+"']").addClass("listening");
		loadingvid=true;
		searchYoutubeVideo(-1,false,findPrev);
	}
}
function selectPostVideo(idx){
	if (loadingvid) return;
	if (idx>=0 && idx<playlist.songs.length){
		playlist.index=idx;
		$("#videolist .video.listening").removeClass("listening");
		$("#videolist .video[idx='"+idx+"']").addClass("listening");
		loadingvid=true;
		searchYoutubeVideo(-1,false);
	}
}
function replicateSong(id,as,val){
	if (playlist.on){
		var idx = findSongIndex(id);
		if (idx>=0){
			playlist.songs[idx][as] = val+"";
			if (val==0) $("#videolist .video[idx='"+idx+"']").removeClass("unread");
			else $("#videolist .video[idx='"+idx+"']").addClass("unread");

			if (playlist.index==idx){
				if (as=="unread"){
					if (val==0)	$("#youtube_viewer_dialog").removeClass("selected");
					else $("#youtube_viewer_dialog").addClass("selected");
				}
			}
		}
	}
}
function loadMoreSongs(){
	if (isGet(playlist.command) && playlist.songs.length == posts.length){
		loadMore();
	} else {
		ajaxMoreSongs();
	}
}
function addToPlaylist(preselectid){
	preselectid = preselectid==undefined?-1:preselectid;
	if (playlist.on && isGet(playlist.command) && playlist.songs.length < posts.length){
		if (playlist.songs.length==0){
			var idx=0;
		} else {
			var lastId = playlist.songs[playlist.songs.length-1].id;
			var idx = findPostIndex(lastId)+1;
		}
		var count=idx;
		for (var i = idx; i<posts.length; i++){
			var p = posts[i];
			if (p.id == preselectid){
				playlist.index=count;
			}
			playlist.songs.push({
				id: p.id,
				favorite: p.favorite,
				unread: p.unread,
				title: p.title
			});
			count++;
		}
		videolistUpdate();
	}
};

var autonextvideo=true;
function html5Player(url,callback){
	unloadVideo();
	var div = document.getElementById("youtube_td");
	div.innerHTML="";
	var vid = document.createElement("video");
	vid.id="HTML5VideoPlayer";
	vid.controls=vid.autoplay=true;
	vid.style.width=vid.style.height="100%";
	vid.style.height=togglePlayer(true)?"100vh":videoDimensions[1]+"px";//"calc(100vh - 41px)";
	vid.style.maxHeight="100vh";
	vid.style.display="table";

	var src = document.createElement("source");
	src.src = url;
	vid.appendChild(src);
	div.appendChild(vid);
	if (typeof callback=="function"){
		vid.addEventListener('loadedmetadata', function() {
			callback();
		}, false);
	}
	vid.onended = function(){
		if (autonextvideo){
			if ((idxVideo+1)==videos.length){
				nextPostVideo(true);
			} else {
				nextVideo();
			}
		}
	};

	src.onerror = function(){
		loadingvid = false;
		loading_stop();
		showMessage("Error loading video");
	};

	unloadVideo = function(){
		$("#youtube_td video")[0].pause();
		$("#youtube_td video").remove();
	}
}
function soundcloudPlayer(code,callback){
	unloadVideo();

	var html = "<iframe id='SoundCloudIframe' style='display:block;min-height:"+(togglePlayer(true)?"100vh":videoDimensions[1]+"px")+"' src='https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/"+code+"?auto_play=true' allowfullscreen frameBorder='0' width='100%' height='100%'></iframe>";
	$("#youtube_td").html(html);
	if (typeof callback=="function") callback();
	var player=SC.Widget(document.getElementById("SoundCloudIframe"));
	var isLastSong=false;
	player.bind(SC.Widget.Events.PLAY,function(){
		player.getSounds(function(sounds){
			player.getCurrentSoundIndex(function(idx){
				isLastSong=sounds.length==(idx+1);
			});
		});
	});
	player.bind(SC.Widget.Events.FINISH,function(){
		if (autonextvideo && isLastSong){
			if ((idxVideo+1)==videos.length){
				nextPostVideo(true);
			} else {
				nextVideo();
			}
		}
	});
	unloadVideo = function(){}
}
var onYouTubeIframeAPIReady;
function youtubePlayer(code,callback){
	//var html = "<iframe src='http://www.youtube.com/embed/"+code+"?autoplay=1&theme=light' allowfullscreen frameBorder='0' width='100%' height='460' style='display:block'></iframe>";
	//$("#youtube_td").html(html);
	//if (typeof callback=="function") callback();
	unloadVideo();
	var id = "YoutubeScriptIframe";

	$("#youtube_td").html("<div id='"+id+"' style='display:block;min-height:"+(togglePlayer(true)?"100vh":videoDimensions[1]+"px")+"'></div>");

	if ($('#youtube_viewer_dialog').css("display")=="none"){
		$('#youtube_viewer_dialog').addClass("minimized");
		$('#youtube_viewer_dialog').css("display","");
	}
	var player;
	onYouTubeIframeAPIReady = function(){
		player = new YT.Player(id, {
			height: '100%',//460
			width: '100%',
			theme: "light",
			videoId: code,
			events: {
				'onReady': function(event){
					event.target.playVideo();
					if (typeof callback=="function") callback();
				},
				'onStateChange': function(event) {
					if (event.data == YT.PlayerState.ENDED && autonextvideo){
						if ((idxVideo+1)==videos.length){
							nextPostVideo(true);
						} else {
							nextVideo();
						}
					}
				}
			}
		});
	}
	if ($("#YoutubeScript").length==0){
		var tag = document.createElement('script');
		tag.src = "https://www.youtube.com/iframe_api";
		tag.id = "YoutubeScript";
		var firstScriptTag = document.getElementsByTagName('script')[0];
		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
	} else {
		onYouTubeIframeAPIReady();
	}
	unloadVideo = function(){
		player.stopVideo();
	}
}
function minPlayer(){
	$('#youtube_viewer_dialog').addClass("minimized");
	closeModal();
	$('#show-video-button').show();
}
function maxPlayer(){
	$('#youtube_viewer_dialog').removeClass("minimized");
	openModal();
	$('#show-video-button').hide();
}
function maximizePlayer(){
	$("#youtube_viewer").css("width","100%");
	$("#youtube_viewer").attr("maxi","1");
	$("iframe#YoutubeScriptIframe,iframe#SoundCloudIframe,#HTML5VideoPlayer").css("min-height","100vh");
}
function restorePlayer(){
	$("#youtube_viewer").css("width",videoDimensions[0]+"px");
	$("#youtube_viewer").attr("maxi","0");
	$("iframe#YoutubeScriptIframe,iframe#SoundCloudIframe").css("min-height",videoDimensions[1]+"px");
	$("#HTML5VideoPlayer").css("min-height","inherit");
}
function togglePlayer(returnval){
	returnval = returnval==undefined?false:returnval;
	if (returnval)
		return $("#youtube_viewer").attr("maxi")==1;
	if ($("#youtube_viewer").attr("maxi")==1)
		restorePlayer();
	else
		maximizePlayer();
}

function showSearchDialog(){
	$('#search_dialog').fadeIn(100);
	$('#searchField').val(get.search==undefined?"":decodeURIComponent(get.search));
	$('#searchField').focus();
	$('#searchField').select();
	openModal();
}

function showPasswordChangeDialog(){
	$('#settings_panel').addClass('hidden');
	$('#pwchange_dialog').fadeIn(100);
	$("#oldPassField").val("").focus();
	$("#newPassField").val("");
	$("#newPass2Field").val("");
	openModal();
}

function showLockPasswordChangeDialog(){
	$('#settings_panel').addClass('hidden');
	$('#pwlchange_dialog').fadeIn(100);
	$("#oldLPassField").val("").focus();
	$("#newLPassField").val("");
	$("#newLPass2Field").val("");
	openModal();
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
	openModal();
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
				closeModal();
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
				closeModal();
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
	closeModal();
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
				$('#unlockButton').html("Unlocked");
			} else {
				$('#unlockButton').removeClass("highlight-color");
				$('#unlockButton').html("Unlock");
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
	$("#more-options-button").addClass("hidden");
	$("#show-video-button").css("top","50px");
	setCookie("fullscreen","1",1);
}

function showLateralMenu(){
	$("#show-lateral-button").html("&lsaquo;");
	$("#content").css("margin-left",$("#lateral_menu").outerWidth()+"px");
    $("#lateral_menu").removeClass("hidden");
    $("#more-options-button").removeClass("hidden");
	$("#show-video-button").css("top","100px");
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
				name: "Move",
				function: function(){
					var fidx = this.getAttribute("idxfolder");
					var idx = this.getAttribute("idxfeed");
					showMove_feed(fidx,idx);
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

function cmMore(e,context){
	var arr = [
		{
			name: "Add Folder",
			function: function(){
				showCreate_folder();
			},
			context: context
		},{
			name: "Add Feed",
			function: function(){
				var idx = 0;
				folders.some(function(el){
					if (el.name == "null")
						return true;
					idx++;
				});
				showCreate_feed(idx);
			},
			context: context
		},{
			name: "Clean",
			function: function(){
				showClean_all();
			},
			context: context
		}];

	setCMContent(arr);
	showCM(e.clientX,e.clientY)
	return false;
}

function openModal(){
	blur(true);
}

function closeModal(){
	blur(false);
}

function blur(val){
	/*var style = document.getElementById("main").style;
	if (typeof val=="undefined"){
		val = style.filter=="";
	}
	style.filter = val?"blur(3px)":"";/**/
}

function toggleQuickSearch(on){
	on = on==undefined?$('#quick_search').hasClass("hidden"):on;

	if (on){
		$('#quick_search').removeClass('hidden');
		$("#quick_search_input").val("");
		$("#quick_results").html("");
		setTimeout(function() {
			$("#quick_search_input").focus();
		}, 200);
	} else {
		$('#quick_search').addClass('hidden');
	}
}

function updateQuickResults(text,ev){
	var span = $("#quick_results")[0];
	if (ev!=undefined && ev.keyCode!=undefined){
		if (ev.keyCode==27){ // ESC
			toggleQuickSearch(false);
			$("#quick_search_input").blur();
			return;
		}
		if (ev.keyCode==13){ // Enter
			$("#quick_search_input").blur();
			toggleQuickSearch(false);
			if (span.children.length>0) {
				span.children[0].click();
			}
			return;
		}
	}

	text = text==undefined?$("#quick_search_input").val():text;
	text = text.toLowerCase();
	$("#quick_results").html("");

	if (text=="") return;

	var feedFunction=function(){
		var id = this.getAttribute("feedid");
		var elem = document.querySelector(".feed[idfeed='"+id+"']");
		elem.click();
		toggleQuickSearch(false);
	}
	var feedRightFunction=function(ev){
		var id = this.getAttribute("feedid");
		var elem = document.querySelector(".feed[idfeed='"+id+"']");
		elem.oncontextmenu(ev);
		return false;
	}

	var folderFunction=function(){
		var id = this.getAttribute("folderid");
		var elem = document.querySelector(".folder[idfolder='"+id+"']");
		elem.click();
		toggleQuickSearch(false);
	}
	var folderRightFunction=function(ev){
		var id = this.getAttribute("folderid");
		var elem = document.querySelector(".folder[idfolder='"+id+"']");
		elem.oncontextmenu(ev);
		return false;
	}

	var appends = [];
	for (var i=0; i<folders.length; i++) {
		var folder = folders[i];
		var foldername = folder.name.toLowerCase();
		
		if (foldername!="null" && foldername.indexOf(text)>=0){
			var p = document.createElement("p");
			var elem = document.querySelector(".folder[idfolder='"+folder.id+"']");
			p.className=elem.className;
			p.setAttribute("folderid",folder.id);
			p.onclick=folderFunction;
			p.oncontextmenu=folderRightFunction;
			//p.innerHTML=folder.name;
			p.innerHTML=elem.querySelector(".folderTitle").innerHTML;
			appends.push(p)
		}

		for (var j=0; j<folder.feeds.length; j++) {
			var feed = folder.feeds[j];
			var feedname = feed.name.toLowerCase();//+feed.link.toLowerCase()+feed.rss_link.toLowerCase();

			if (feedname.indexOf(text)>=0){
				var p = document.createElement("p");
				var elem = document.querySelector(".feed[idfeed='"+feed.id+"']");
				p.className=elem.className;
				//p.innerHTML=feed.name;
				p.innerHTML=elem.querySelector(".feedTitle").innerHTML;
				p.setAttribute("feedid",feed.id);
				p.onclick=feedFunction;
				p.oncontextmenu=feedRightFunction;
				appends.push(p)
			}

		}
	}
	appends.sort(function(a1,a2){
		var a1l = a1.firstChild.length;
		var a2l = a2.firstChild.length;
		if (a1l > a2l)	return 1;
		else
		if (a1l < a2l)	return -1;
		return 0;
	})
	for (var i=0; i<appends.length; i++) {
		span.appendChild(appends[i]);
	}
}

function isGet(arr){
	var counta = 0;
	var countg = 0;
	for (var i in arr){
		if (get[i] != arr[i]){
			return false;
		}
		if (arr[i]!=undefined) counta++;
	}
	for (var i in get) if (get[i]!=undefined) countg++;
	return countg==counta;
}
