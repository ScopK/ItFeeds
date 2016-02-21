var code = {
	lateralFolder: function(idx,selectedMinimized){
		var folder = folders[idx];
		var selected = (get.folder == folder.id);

		var content = document.createElement("div");
		content.className='folder'+((selected)?' selected':'');
		content.setAttribute("idxFolder",idx);
		content.setAttribute("idFolder",folder.id);
		content.setAttribute("oncontextmenu","return cmFolder(event,this);");

		if (folder.unread > 0)
			unread = ' <span class="count">(<span class="num">'+folder.unread+'</span>)</span>';
		else
			unread = ' <span class="count hidden">(<span class="num">'+folder.unread+'</span>)</span>';

		html = '<div class="folderHeader'+((folder.hidden==1)?" hidden":"")+'">'+
					'<button class="expander"">'+((selected && !selectedMinimized)?'-':'+')+'</button>'+
					'<span class="folderTitle">'+(folder.name)+unread+'</span>'+
				'</div>';
		content.innerHTML=html;

		var feeds = document.createElement("div");
		feeds.className = "folderfeeds";
		if (selected && !selectedMinimized) feeds.style.display="block";
		content.appendChild(feeds);

		var feedsCont = code.lateralFeed(folder.feeds,idx);
		for (var i = 0; i < feedsCont.length; i++) {
			feeds.appendChild(feedsCont[i]);
		};

		folder.element=content;
		return content;
	},
	lateralFeed: function(feed_list,indexFolder){
		var html="";
		var contents=[];
		$.each(feed_list,function(indexFeed){
			var unread="";
			if (this.unread > 0)
				unread = ' <span class="count">(<span class="num">'+this.unread+'</span>)</span>';
			else 
				unread = ' <span class="count hidden">(<span class="num">'+this.unread+'</span>)</span>';

			var selected = (get.feed == this.id);
			var classes = ((selected)?' selected':'')+((this.unread > 0)?" unread":"")+((this.enabled==1)?"":" disabled")+((this.filter=="")?"":" filtered");

			var content = document.createElement("div");
			content.className='feed'+classes;
			content.setAttribute("idxFolder",indexFolder);
			content.setAttribute("idxFeed",indexFeed);
			content.setAttribute("idFeed",this.id);
			content.setAttribute("oncontextmenu","return cmFeed(event,this)");
			content.innerHTML='<div class="feedTitle">'+(this.name)+unread+'</div>';
			this.element = content;
			contents.push(content);
		});
		return contents;
	},
	lateralTag: function(tag_list){
		var html = "";
		var contents = [];
		$.each(tags,function(index){
			var count="";
			if (this.count > 0)
				count = ' ';

			var selected = (get.tag == this.id);

			var content = document.createElement("div");
			content.className = 'tag'+((selected)?' selected':'')+((this.hidden==1)?" hidden":"")+((this.public==1)?" public":"");
			content.setAttribute("idxTag",index);
			content.setAttribute("idTag",this.id);
			content.setAttribute("oncontextmenu","return cmTag(event,this);");
			content.innerHTML= '<span class="name">'+(this.name)+'</span> <span class="count">('+this.count+')</span>';
			this.element = content;
			contents.push(content);
		});

		return contents;
	},
	post: function(post){
		var indexPost = postCount++;
		var html="";

		var feed = post.feed;
		var subtitle="";

		if (feed != undefined){
			var folder = feed.folder;
			var folderInfo = (folder.name == "null")?"":folder.name+" | ";
			subtitle = '<div class="subtitle">[ '+folderInfo+'<a target="_blank" href="'+feed.link+'">'+feed.name+'</a> ] <a class="date" target="_blank" href="/post/'+post.id+'">'+post.date+'</a></div>';
		}

		var controller = '<div class="controller"><div class="control_panel">'+
			'<button class="setUnread" onclick="markUnread('+indexPost+',null,true)"></button>'+
			'<button class="setFav" onclick="markFavorite('+indexPost+',null,true)"></button>'+
			'<button class="addTag" onclick="dialog.addTags.show('+indexPost+');return false;"></button>'+
			'<button class="maxminimize" onclick="minimizeButtonAction('+indexPost+')"></button>'+
			'<button class="moreOptions" onclick="cmPostOptions(event,this,'+indexPost+');"></button>'+
			'</div><div class="tagList"></div></div>';

		var unreadl=(post.unread)? "unread":"";
		var favoritel=(post.favorite)? "favorite":"";
		var compactedl=(cookie.get("compactedmode")==1)? "minimized":"";

		html = '<div class="header">'+
					'<div class="title"><a target="_blank" href="'+post.link+'">'+post.title+'</a></div>'+subtitle+
				'</div>';
		html += controller;
		html += '<div class="description">'+post.description+'</div>';

		var content = document.createElement("div");
		content.className = 'post '+unreadl+' '+favoritel+' '+compactedl;
		content.setAttribute("idxpost",indexPost);
		content.innerHTML=html;

		return content;
	}
}

var cookie = {
	set: function(cname, cvalue, exdays){
		var d = new Date();
		d.setTime(d.getTime() + (exdays*24*60*60*1000));
		var expires = "expires="+d.toUTCString();
		window["cookie_"+cname]=cvalue;
		document.cookie = cname + "=" + cvalue + "; " + expires;
	},

	get: function(cname){
		if (window["cookie_"+cname]!=undefined){
			return window["cookie_"+cname];
		}
		var name = cname + "=";
		var ca = document.cookie.split(';');
		for(var i=0; i<ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1);
			if (c.indexOf(name) != -1){
				var cvalue = c.substring(name.length,c.length);
				window["cookie_"+cname]=cvalue;
				return cvalue;
			}
		}
		return "";
	},

	delete: function(cname){
		document.cookie = cname+"=;expires=Thu, 01 Jan 1970 00:00:01 GMT;";
		window["cookie_"+cname]=undefined;
	},

	actions: {
		postsmode: function(val,msg){
			if (msg==undefined) msg=true;
			if (val==undefined){
				var val = cookie.get("compactedmode");
				val++;
				if (val==3) val=0;
			}
			val++;val--;
			switch(val){
				case 0:
					if (msg) showPopMessage("Posts Mode "+(val+1)+". Normal Mode");
					if (selectedPost != undefined){
						$(selectedPost.element).prevAll("div.post").addClass("minimized");
						$(selectedPost.element).nextAll("div.post").add(selectedPost.element).removeClass("minimized");
					} else {
						$(".post").removeClass("minimized");
					}
					break;
				case 1: 
					if (msg) showPopMessage("Posts Mode "+(val+1)+". Minimized Mode"); 
					$(".post").not(".selected").addClass("minimized");
					$(".post.selected").removeClass("minimized");
					break;
				case 2:
					if (msg) showPopMessage("Posts Mode "+(val+1)+". Never minimize"); 
					$(".post").removeClass("minimized");
					break;
			}
			cookie.set("compactedmode",val,3);
			$("#posts_mode").val(val);
			if (selectedPost != undefined){
				if (val==2){
					setTimeout(function(){
						selectedPost.focus(0);
					},200);
				} else {
					selectedPost.focus(0);
				}
			}
		},
		autoreadmode: function(val,msg){
			if (msg==undefined) msg=true;
			if (val==undefined){
				var val = cookie.get("autoreadmode");
				val++;
				if (val==3) val=0;
			}
			val++;val--;
			switch(val){
				case 0: // On select post
					if (msg) showPopMessage("Auto-Read Mode "+(val+1)+". On select post");
					break;
				case 1: // On scroll
					if (msg) showPopMessage("Auto-Read Mode "+(val+1)+". On scroll"); 
					break;
				case 2: // Never
					if (msg) showPopMessage("Auto-Read Mode "+(val+1)+". Never"); 
					break;
			}
			cookie.set("autoreadmode",val,3);
			$("#autoread_mode").val(val);
		}
	}
}

var consts = {
	UNREAD: 0,
	FAVORITE: 1
}

var utils = {
	nameSort: function(a,b){
		var aName = a.name.toLowerCase();
		var bName = b.name.toLowerCase(); 
		return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
	},
	logout: function(){
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
	},
	updateURL: function(){
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
}