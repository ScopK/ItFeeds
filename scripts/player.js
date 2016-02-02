var player = {
	blacklist: [],
	size: [1280,720],//[700,460]
	object: undefined,
	playing: undefined,
	maximized: false,
	minimized: false,
	busy: false,
	autonext: true,
	queue: [],
	queueIndex: 0,
	playlist: [],
	playlistIndex: 0,
	get: {},

	show: function(){
		dialog.effects.show();
		$("#video_viewer_dialog").fadeIn(100);
	},
	hide: function(){
		dialog.effects.hide();
		$("#video_viewer_dialog").hide();
	},

	close: function(){
		player.hide();
		player.toggleMax(false);
		if (player.object!=undefined){
			player.object.unloadVideo();
		}
		player.object = undefined;
		player.playing = undefined;
		player.minimized = false;
		player.size = [1280,720];

		$.each($("#video_td video,#video_td audio"),function(){this.pause();});
		$("#video_td").html("");
	},

	toggleMax: function(value){
		player.maximized = value==undefined? !player.maximized : value;
		if (player.maximized){
			$("body").css("overflow","hidden");
			$("#video_viewer").css("width","100%");
			$("#video_viewer").attr("maxi","1");
			$("#video_viewer").addClass("maximized");
			$("iframe#YoutubeScriptIframe,iframe#SoundCloudIframe,#HTML5VideoPlayer").css("min-height","100vh");
		} else {
			$("body").css("overflow","inherit");
			$("#video_viewer").css("width",player.size[0]+"px");
			$("#video_viewer").removeClass("maximized");
			$("iframe#YoutubeScriptIframe,iframe#SoundCloudIframe").css("min-height",player.size[1]+"px");
			$("#HTML5VideoPlayer").css("min-height","inherit");
		}
	},

	toggleMin: function(value){
		player.minimized = value==undefined? !player.minimized : value;
		if (player.minimized){
			$("body").css("overflow","inherit");
			$('#video_viewer_dialog').addClass("minimized");
			player.hide();
			$('#show-video-button').show();
		} else {
			if (player.maximized) $("body").css("overflow","hidden");
			$('#video_viewer_dialog').removeClass("minimized");
			player.show();
			$('#show-video-button').hide();
		}
	},

	start: function(post){
		post = post==undefined? selectedPost : post;
		if (post==undefined) return;
		if (player.busy) return;
		player.busy = true;

		call.videoFind(post.id,function(success,values){
			player.queueIndex = 0;
			player.queue = values;
			if (success){
				if (post.unread){
					$("#video_viewer_dialog").addClass("selected");
				} else {
					$("#video_viewer_dialog").removeClass("selected");
				}
				player.playing = post;
				player.get = {};
				player.playlist = [];
				for (var i in get) player.get[i] = get[i];
				for (var i=0;i<posts.length;i++) player.playlist.push(posts[i]);
				player.playlistIndex = posts.indexOf(post);
				var video = values[0];
				switch (video.type){
					case 'yt': player.object = new YoutubePlayer();		break;
					case 'sc': player.object = new SoundCloudPlayer();	break;
					case 'h5': player.object = new HTML5Player();		break;
				}
				player.object.loadVideo(video.src,true);
				player.list.update();
				$("#counter_videos").html((player.queueIndex+1)+"/"+player.queue.length);
				$("#video_viewer_dialog .title").html(post.title);
			} else {
				player.busy = false;
			}
		});
	},

	load: function(video){
		if (player.object.is(video.type)){
			player.object.swapVideo(video.src);
		} else {
			player.object.unloadVideo();
			switch (video.type){
				case 'yt': player.object = new YoutubePlayer();		break;
				case 'sc': player.object = new SoundCloudPlayer();	break;
				case 'h5': player.object = new HTML5Player();		break;
			}
			player.object.loadVideo(video.src,true);
		}
		player.list.update();
	},

	nextElement: function(){
		if (player.queueIndex < player.queue.length){
			player.queueIndex++;
			if (player.queueIndex==player.queue.length)
				player.queueIndex=0;

			var video = player.queue[player.queueIndex];
			player.load(video);

			$("#counter_videos").html((player.queueIndex+1)+"/"+player.queue.length);
		}
	},

	//// TODOOOOOOOOOOOOOOOOO
	next: function(findNext){
		if (player.playlistIndex < player.playlist.length-1){
			if (player.busy) return;
			player.busy = true;

			player.playlistIndex++;
			var post = player.playlist[player.playlistIndex];

			if (player.playlistIndex == player.playlist.length-1){
				player.loadMore();
			}

			if (player.playing == selectedPost){
				selectNextPost();
			}

			call.videoFind(post.id,function(success,values){
				player.queueIndex = 0;
				player.queue = values;
				if (success){
					if (post.unread){
						$("#video_viewer_dialog").addClass("selected");
					} else {
						$("#video_viewer_dialog").removeClass("selected");
					}
					player.playing = post;
					var video = values[0];
					player.load(video);
					$("#counter_videos").html((player.queueIndex+1)+"/"+player.queue.length);
					$("#video_viewer_dialog .title").html(post.title);
				} else {
					player.busy = false;
				}
			});
		}
	},
	prev: function(findPrev){
		if (player.playlistIndex > 0){
			if (player.busy) return;
			player.busy = true;

			player.playlistIndex--;
			var post = player.playlist[player.playlistIndex];

			if (player.playing == selectedPost){
				selectPrevPost();
			}

			call.videoFind(post.id,function(success,values){
				player.queueIndex = 0;
				player.queue = values;
				if (success){
					if (post.unread){
						$("#video_viewer_dialog").addClass("selected");
					} else {
						$("#video_viewer_dialog").removeClass("selected");
					}
					player.playing = post;
					var video = values[0];
					player.load(video);
					$("#counter_videos").html((player.queueIndex+1)+"/"+player.queue.length);
					$("#video_viewer_dialog .title").html(post.title);
				} else {
					player.busy = false;
				}
			});
		}
	},

	selectFromIdx: function(playlistIdx){
		if (player.busy) return;
		player.busy = true;
		
		var post = player.playlist[playlistIdx];

		call.videoFind(post.id,function(success,values){
			player.queueIndex = 0;
			player.queue = values;
			if (success){
				if (post.unread){
					$("#video_viewer_dialog").addClass("selected");
				} else {
					$("#video_viewer_dialog").removeClass("selected");
				}
				player.playlistIndex = playlistIdx;
				player.playing = post;
				var video = values[0];
				player.load(video);
				$("#counter_videos").html((player.queueIndex+1)+"/"+player.queue.length);
				$("#video_viewer_dialog .title").html(post.title);
			} else {
				player.busy = false;
			}
		});
	},


	resize: function(ww,hh){
		var tw = $(window).width();
		var th = $(window).height()-41;
		if (ww>tw) ww=tw;
		else if (ww<640) ww=640;
		if (hh>th) hh=th;
		else if (hh<360) hh=360;
		player.size = [ww,hh];
		var wpx = ww+"px";
		var hpx = hh+"px";
		$("#video_viewer").css("width",wpx);
		$("#SoundCloudIframe,#YoutubeScriptIframe").css("min-height",hpx);
		$("#HTML5VideoPlayer").css("height",hpx);
		//$("#SoundCloudIframe,#YoutubeScriptIframe").css("min-height",hpx);
		//$("#HTML5VideoPlayer").css("max-height",hpx);
	},

	loadMore: function(){
		if (objectEquals(player.get,get)){
			call.loadMorePosts();
		} else {
			call.loadMoreVideos();
		}
	},

	list: {
		update: function(){
			if (player.playing==undefined) return;
			$("#videolist").html("");
			for (var i=0;i<player.playlist.length;i++){
				var video = player.playlist[i];
				var classes = (i==player.playlistIndex)?"video listening":"video";
				if (video.unread) classes+=" unread";
				var pos = i;
				pos++;
				var div = document.createElement("div");
				div.className = classes;
				div.setAttribute("idx",i);
				div.onclick=function(){
					player.selectFromIdx(this.getAttribute("idx"));
				};
				div.innerHTML="<span class='idx'>"+pos+"</span><span class='tit'>"+video.title+"</span>";
				$("#videolist").append(div);
			}
			$("#videolist").append("<div id='moresongs' onclick='player.loadMore()'><p>+</p></div>");
		}
	}
}

// ###################################################### PLAYER OBJECT
// ####################################################################
// ####################################################################
// ####################################################### HTML5 PLAYER

function HTML5Player(){
	this.showVideo = false;

	var div = document.getElementById("video_td");
	div.innerHTML="";
	var vid = document.createElement("video");
	vid.id="HTML5VideoPlayer";
	vid.controls=vid.autoplay=true;
	vid.style.width=vid.style.height="100%";
	if (player.maximized){
		$("#video_viewer").css("width","100%");
		vid.style.height="100vh";//"calc(100vh - 41px)";
	} else {
		$("#video_viewer").css("width",player.size[0]+"px");
		vid.style.height=player.size[1]+"px";
	}
	vid.style.maxHeight="100vh";
	vid.style.display="table";
	vid.onended = function(){
		if (player.autonext){
			if ((player.queueIndex+1)==player.queue.length){
				player.next(true);
			} else {
				player.nextElement();
			}
		}
	}

	var pl = this;
	vid.addEventListener('loadedmetadata', function() {
		player.busy = false;
		if (pl.showVideo) player.toggleMin(false);
	}, false);
	div.appendChild(vid);
}

HTML5Player.prototype.loadVideo = function(url,show){
	this.showVideo = show;
	var vid = document.getElementById("HTML5VideoPlayer");
	var src = document.createElement("source");
	src.src = url;
	src.onerror = function(){
		player.busy = false;
		showMessage("Error loading video");
	}
	vid.appendChild(src);
}

HTML5Player.prototype.unloadVideo = function(code){
	var vid = document.getElementById("HTML5VideoPlayer");
	vid.pause();
	vid.src="";
	vid.load();
	$("#video_td video").remove();
}

HTML5Player.prototype.swapVideo = function(code){
	var vid = document.getElementById("HTML5VideoPlayer");
	$(vid).find("source").attr("src",code);
	vid.load();
	vid.play()
}

HTML5Player.prototype.is = function(code){
	return code=="h5";
}

// ####################################################### HTML5 PLAYER
// ####################################################################
// ####################################################################
// ##################################################### YOUTUBE PLAYER

var onYouTubeIframeAPIReady;
function YoutubePlayer(){
	this.YTplayer = undefined;

	if (player.maximized){
		$("#video_viewer").css("width","100%");
		$("#video_td").html("<div id='YoutubeScriptIframe' style='display:block;min-height:100vh'></div>");
	} else {
		$("#video_viewer").css("width",player.size[0]+"px");
		$("#video_td").html("<div id='YoutubeScriptIframe' style='display:block;min-height:"+player.size[1]+"px'></div>");
	}
}

YoutubePlayer.prototype.loadVideo = function(code,show){
	loading_run();
	var pl = this;
	onYouTubeIframeAPIReady = function(){
		pl.YTplayer = new YT.Player("YoutubeScriptIframe", {
			height: '100%',//460
			width: '100%',
			theme: "light",
			videoId: code,
			events: {
				'onReady': function(event){
					loading_stop();
					event.target.playVideo();
					if (show) player.toggleMin(false);
					player.busy = false;
				},
				'onStateChange': function(event) {
					if (event.data == YT.PlayerState.ENDED && player.autonext){
						if ((player.queueIndex+1)==player.queue.length){
							player.next(true);
						} else {
							player.nextElement();
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
}

YoutubePlayer.prototype.unloadVideo = function(code){
	this.YTplayer.stopVideo();
}

YoutubePlayer.prototype.swapVideo = function(code){
	this.YTplayer.loadVideoById(code);
	player.busy = false;
}

YoutubePlayer.prototype.is = function(code){
	return code=="yt";
}

// ##################################################### YOUTUBE PLAYER
// ####################################################################
// ####################################################################
// ################################################## SOUNDCLOUD PLAYER

function SoundCloudPlayer(){
	this.playerSC = undefined;
}

SoundCloudPlayer.prototype.loadVideo = function(code,show){
	if (player.maximized){
		$("#video_viewer").css("width","100%");
		var html = "<iframe id='SoundCloudIframe' style='display:block;min-height:100vh' src='https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/"+code+"&auto_play=true&download=true' allowfullscreen frameBorder='0' width='100%' height='100%'></iframe>";
	} else {
		$("#video_viewer").css("width",player.size[0]+"px");
		var html = "<iframe id='SoundCloudIframe' style='display:block;min-height:"+player.size[1]+"px' src='https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/"+code+"&auto_play=true&download=true' allowfullscreen frameBorder='0' width='100%' height='100%'></iframe>";
	}

	$("#video_td").html(html);
	this.isLastSong = false;
	var pl = this;

	this.playerSC = SC.Widget(document.getElementById("SoundCloudIframe"));
	this.playerSC.bind(SC.Widget.Events.PLAY,function(){
		pl.playerSC.getSounds(function(sounds){
			pl.playerSC.getCurrentSoundIndex(function(idx){
				pl.isLastSong=sounds.length==(idx+1);
			});
		});
	});
	this.playerSC.bind(SC.Widget.Events.FINISH,function(){
		if (player.autonext && pl.isLastSong){
			if ((player.vars.queueIndex+1)==player.queue.length){
				player.next(true);
			} else {
				player.nextElement();
			}
		}
	});
	if (show) player.toggleMin(false);
	player.busy = false;
}

SoundCloudPlayer.prototype.unloadVideo = function(code){}

SoundCloudPlayer.prototype.swapVideo = function(code){
	this.playerSC.load("https://api.soundcloud.com/"+code+"?auto_play=true&download=true");
	this.isLastSong = false;
	player.busy = false;
}

SoundCloudPlayer.prototype.is = function(code){
	return code=="sc";
}


// ########################### SOUNDCLOUD PLAYER
// #############################################
// ########################### RESIZER BEHAVIOUR

$(document).ready(function(){
	var back = $("#video_viewer").parent();
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

		player.resize(player.size[0]+dx,player.size[1]+dy);
		initPos=[x,y];
	}
});