var lateral = {
	refresh: {
		all: function(){
			lateral.refresh.folders();
			lateral.refresh.tags();
		},
		folders: function(){
			var idsExtended = [];
			$.each($("#folders .folder:not(.selected)"),function(){
				if ($(this).find(".folderfeeds")[0].style.display=="block")
					idsExtended.push(this.getAttribute("idfolder"));
			});
			var folderSelectedIsMinimized =$(".folder.selected .expander").html()=="+";
			var content;
			$("#feeds").html("");
			$("#folders").html("");
			$.each(folders,function(index){
				if (this.name == "null"){
					content = code.lateralFeed(this.feeds,index);
					$("#feeds").append(content);
				} else {
					content = code.lateralFolder(index,folderSelectedIsMinimized);
					$("#folders").append(content);
				}
			});

			idsExtended.forEach(function(id){
				lateral.toggle.viewFeeds(folders.findBy("id",id).element.querySelector(".expander"),0);
			});
			if (get.feed != undefined){
				$(".feed[idFeed='"+get.feed+"']").closest(".folderfeeds").show();
				$(".feed[idFeed='"+get.feed+"']").closest(".folder").find("button").html("-");
			}
			lateral.actions.addFoldersAndFeeds();
			

			if (!$('#quick_search').hasClass("hidden")){
				lateral.refresh.quickSearch();
			}
		},
		tags:function(){
			$("#tags").html("");
			var content = code.lateralTag(tags);
			$("#tags").append(content);

			var html = "";
			$.each(tags,function(index){
				html += '<p '+((this.hidden==1)?'class="hiddenTag"':'')+' onclick="$(this).toggleClass(\'selected\')" idtag="'+(this.id)+'">'+(this.name)+'</p>';
			});
			$("#add_tag .taglist").html(html);

			lateral.actions.addTags();
		},
		quickSearch: function(event){
			var span = document.getElementById("quick_results");
			var text = $("#quick_search_input").val().toLowerCase();
			
			if (event!=undefined && event.keyCode!=undefined){
				if (event.keyCode==27){ // ESC
					lateral.hide.quickSearch();
					$("#quick_search_input").blur();
					return;
				}
				if (event.keyCode==13){ // Enter
					$("#quick_search_input").blur();
					lateral.hide.quickSearch();
					if (span.children.length>0) {
						span.children[0].click();
					}
					return;
				}
			}

			$(span).html("");

			if (text=="") return;

			var feedFunction=function(){
				var id = this.getAttribute("feedid");
				var elem = document.querySelector(".feed[idfeed='"+id+"']");
				elem.click();
				lateral.hide.quickSearch();
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
				lateral.hide.quickSearch();
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
		},
		navigationOptions: function(){
			if (get.fav) $("#favsTButton").addClass("marked");
			else		 $("#favsTButton").removeClass("marked");

			if (get.unread) $("#unreadTButton").removeClass("marked");
			else		    $("#unreadTButton").addClass("marked");

			if (get.sortby) $("#sortTButton").html("Older");
			else		  	$("#sortTButton").html("Newer");

			if (get.search){
				$("#searchContent").text("Search query: "+decodeURIComponent(get.search));
				$("#searchContent").slideDown();
				$("#searchButton").addClass("marked");
			} else {
				$("#searchButton").removeClass("marked");
				$("#searchContent").slideUp();
			}
		},
		controlButtons: function(){
			if (selectedPost != undefined){
				if (selectedPost.unread) $("#mouse_bottom .markunread").addClass("colored");
				else $("#mouse_bottom .markunread").removeClass("colored");

				if (selectedPost.favorite) $("#mouse_bottom .markfav").addClass("colored");
				else $("#mouse_bottom .markfav").removeClass("colored");

				$("#mouse_bottom").show();
			} else {
				$("#mouse_bottom").hide();
			}
		},
		updateCounts:function(feed){
			var folder = feed.folder;
			if (folder.name != "null"){
				var folderelem = $(".folder[idfolder='"+folder.id+"']");
				var folderCount = folderelem.find(".folderTitle .count");
				folderCount.find(".num").html(folder.unread);
				if (folder.unread<=0) 	folderCount.addClass("hidden");
				else 					folderCount.removeClass("hidden");
				var feedCount = folderelem.find(".feed[idfeed='"+feed.id+"'] .feedTitle .count");
				feedCount.find(".num").html(feed.unread);
				if (feed.unread<=0) 	feedCount.addClass("hidden");
				else 					feedCount.removeClass("hidden");
			} else {
				var feedCount = $("#feeds").find(".feed[idfeed='"+feed.id+"'] .feedTitle .count");
				feedCount.find(".num").html(feed.unread);
				if (feed.unread<=0) 	feedCount.addClass("hidden");
				else 					feedCount.removeClass("hidden");
			}
		}
	},
	show: {
		settings: function(){
			$('#settings_panel').removeClass('hidden');
			lateral.hide.quickSearch();
		},
		quickSearch: function(){
			$('#quick_search').removeClass('hidden');
			$("#quick_search_input").val("");
			$("#quick_results").html("");
			var tryfocus = function(){
				$("#quick_search_input").focus();
				if (!$("#quick_search_input").is(":focus")){
					setTimeout(tryfocus, 10);
				}
			}
			setTimeout(tryfocus, 10);
			lateral.hide.settings();
		},
		all: function(){
			$("#show-lateral-button").html("&lsaquo;");
			$("#content").css("margin-left",$("#lateral_menu").outerWidth()+"px");
			$("#lateral_menu").removeClass("hidden");
			$("#more-options-button").removeClass("hidden");
			$("#show-video-button").css("top","100px");
			cookie.delete("fullscreen");
		}
	},
	hide: {
		settings: function(){
			$('#settings_panel').addClass('hidden');
		},
		quickSearch: function(){
			$('#quick_search').addClass('hidden');
		},
		all: function(){
			$("#show-lateral-button").html("&rsaquo;");
			$("#lateral_menu").addClass("hidden");
			$("#content").css("margin-left","0");
			$('#settings_panel').addClass('hidden');
			$("#more-options-button").addClass("hidden");
			$("#show-video-button").css("top","50px");
			cookie.set("fullscreen","1",1);
		}
	},
	toggle: {
		settings: function(on){
			on = on==undefined?$('#settings_panel').hasClass("hidden"):on;
			on? lateral.show.settings() : lateral.hide.settings();
		},
		quickSearch: function(on){
			on = on==undefined?$('#quick_search').hasClass("hidden"):on;
			on? lateral.show.quickSearch() : lateral.hide.quickSearch();
		},
		all: function(on){
			on = on==undefined?$("#lateral_menu").hasClass("hidden"):on;
			on? lateral.show.all() : lateral.hide.all();
		},
		viewFeeds: function(button,sec){
			var hid = $(button).html();

			if (hid == '+'){
				$(button).closest(".folder").find(".folderfeeds").slideDown(sec);
				$(button).html("-");
			} else {
				$(button).closest(".folder").find(".folderfeeds").slideUp(sec);
				$(button).html("+");
			}
		},
		filterFavorites: function(){
			if (!get.fav) {
				get.fav = "1";
				get.unread = "0";
				get.feed = undefined;
				get.folder = undefined;
				get.tag = undefined;
				$(".feed, .tag, .folder").removeClass("selected");
			} else 
				get.fav = undefined;
			utils.updateURL();
			call.loadPosts();
		},
		filterUnread: function(){
			if (!get.unread)
				get.unread = "0";
			else {
				get.unread = undefined;
				get.fav = undefined;
			}
			utils.updateURL();
			call.loadPosts();
		},
		filterOrder: function(){
			if (!get.sortby)
				get.sortby = "0";
			else 
				get.sortby = undefined;
			utils.updateURL();
			call.loadPosts();
		},
	},
	actions:{
		select: function(el,value,folder){
			if (value){
				$(el).addClass("selected");
				if (folder){
					folder = $(el).hasClass("folder")? $(el) : $(el).closest(".folder");
					var button = folder.find("button");
					button.closest(".folder").find(".folderfeeds").slideDown();
					button.html("-");
				}
			} else {
				$(el).removeClass("selected");
				if (folder){
					$(".folderfeeds").slideUp();
					$(".expander").html("+");
				}
			}
		},
		addFoldersAndFeeds: function (){
			// Action when click a feed:
			$(".feed").click(function(){
				get.page = undefined;
				get.folder = undefined;
				get.tag = undefined;
				$(".feed, .tag, .folder").removeClass("selected");

				var idf = $(this).attr("idFeed");
				if (idf == get.feed){
					get.feed = undefined;
					lateral.actions.select(this,false,true);
				} else {
					get.feed = idf;
					lateral.actions.select(this,true,true);
				}
				utils.updateURL();
				call.loadPosts();
			});

			// Action when clicking a folder:
			$(".folder").click(function(event){
				if ($(event.target).is(".folderHeader,.folderTitle,.folderTitle .count,.folderTitle .count .num,.folder")){
					get.page = undefined;
					get.feed = undefined;
					get.tag = undefined;

					$(".feed, .tag, .folder").removeClass("selected");

					var idf = $(this).attr("idFolder");
					if (idf == get.folder){
						get.folder = undefined;
						lateral.actions.select(this,false,true);
					} else {
						get.folder = idf;
						lateral.actions.select(this,true);
					}
					utils.updateURL();
					call.loadPosts();
				}
			});

			// Action when clicking the + or - icon next to the folder:
			$(".expander").click(function(){
				lateral.toggle.viewFeeds(this);
			});
		},
		addTags: function(){
			// Action when clicking a tag:
			$(".tag").click(function(){
				get.page = undefined;
				get.feed = undefined;
				get.folder = undefined;

				$(".feed, .tag, .folder").removeClass("selected");

				var idt = $(this).attr("idTag");
				if (idt == get.tag) {
					get.tag = undefined;
					lateral.actions.select(this,false);
				} else {
					get.tag = idt;
					get.fav = undefined;
					get.unread="0";
					lateral.actions.select(this,true);
				}
				utils.updateURL();
				call.loadPosts();
			});
		}
	}
};

$(document).ready(function(){
	$("#settingsButton").click(lateral.show.settings);
	$(".button_close_settings").click(lateral.hide.settings);
	$("#unlockButton").click(dialog.unlock.show);
	$("#logoutButton").click(utils.logout);
	$("#changePassBtn").click(dialog.passwordChange.show);
	$("#changeLPassBtn").click(dialog.lockChange.show);
	$("#loadMore,#loadMoreLabel").click(call.loadMorePosts);
	$("#searchButton").click(dialog.search.show);
	$("#searchContent").click(function(){
		get.search=undefined;
		call.loadPosts();
		lateral.refresh.navigationOptions();
		utils.updateURL();
	});

	$("#favsTButton").click(lateral.toggle.filterFavorites);
	$("#unreadTButton").click(lateral.toggle.filterUnread);
	$("#sortTButton").click(lateral.toggle.filterOrder);

	$(".button_close_quickSearch").click(lateral.hide.quickSearch);
	$("#quick_search_input").keyup(lateral.refresh.quickSearch);
});