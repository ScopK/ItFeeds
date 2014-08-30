var postIdxSelected = 1;
var pagesLoaded = 0;
var postspage = 10;
var posts;
var total;

var allowed = true;
$(document).ready(function(){
	$(document).keydown(function(e) { 
		if (e.ctrlKey || e.altKey || e.shiftKey) return;
		switch (e.which) {
			case 74: //j
				nextPost();
				break;
			case 75: //k
				prevPost();
				break;
			case 32: // space
			case 40: // down
				if (!allowed) return false;
				var body = $("html, body");
				body.animate({scrollTop: body.scrollTop() + 200}, {duration: 210, easing: 'linear', queue: false});
				allowed = false;
				setTimeout(function() {allowed = true;}, 190);
				return false;
			case 38: // up
				if (!allowed) return false;
				var body = $("html, body");
				body.animate({scrollTop: body.scrollTop() - 200}, {duration: 210, easing: 'linear', queue: false});
				allowed = false;
				setTimeout(function() {allowed = true;}, 190);
				return false;
		}
	});

	$(document).scroll(function(e) { 
		var pos = $(document).scrollTop();
		var c = 1;
		$.each($(".post"),function(){
			if (pos < (this.offsetTop+this.offsetHeight)){
				console.log(c);
				selectPost(c);
				return false;
			}
			c++;
		});
	});

	initialize();
});

var loading = false;
function initialize(){
	if (loading) return;
	else loading=true;
	$.ajax({
		url: "./ajax/get_posts.php",
		type: "GET",
		data: "public=1&tag="+idtag,
		dataType : "json",
		success: function(result){
			posts = result.posts;
			total = result.total;
			postspage = posts.length;
			pagesLoaded = 1;

			$.each(posts,function(){
				if (this.title == "" || this.title == "&nbsp;") this.title = "-- NO TITLE --";
				var html = '<div class="post">';
					html+= '<div class="title-bg"><div class="space-used">';
						html+='<h1><a target="_blank" href="'+this.link+'">'+this.title+'</a></h1>';
						html+='<h2><a target="_blank" href="/post/'+this.id+'">'+this.date+'</a></h2>';
					html+='</div></div>';
					html+='<div class="space-used"><div class="description">'+this.description+'</div></div>';
				html+='</div>';
				$("#tag-content").append(html);
			});
			selectPost(postIdxSelected);
			$("#tag-content").fadeIn();
		},
		error: function (request, status, error){
			$("#error-message").fadeIn();
		},
		complete: function(){
			loading = false;
		}
	});
}

function loadMore(){
	if (loading) return;
	else loading=true;
	$.ajax({
		url: "./ajax/get_nextPosts.php",
		type: "GET",
		data: "public=1&tag="+idtag+"&nextid="+(posts[posts.length-1].id),
		dataType : "json",
		success: function(result){
			$.each(result.posts,function(){
				posts.push(this);
				var html = '<div class="post">';
					html+= '<div class="title-bg"><div class="space-used">';
						html+='<h1><a target="_blank" href="'+this.link+'">'+this.title+'</a></h1>';
						html+='<h2>'+this.date+'</h2>';
					html+='</div></div>';
					html+='<div class="space-used"><div class="description">'+this.description+'</div></div>';
				html+='</div>';
				$("#tag-content").append(html);
			});
			pagesLoaded++;
		},
		error: function (request, status, error){
			$("#error-message").fadeIn();
		},
		complete: function(){
			loading = false;
		}
	});
}

function nextPost(){
	if (postIdxSelected < posts.length){
		selectPost(++postIdxSelected);
		var newPost = $(".post:eq("+(postIdxSelected-1)+")");
		focusPost(newPost,100);
	}
}

function prevPost(){
	if (postIdxSelected > 1){
		selectPost(--postIdxSelected);
		var newPost = $(".post:eq("+(postIdxSelected-1)+")");
		focusPost(newPost,100);
	}
}

function focusPost(post,speed){
	$('html,body').animate({scrollTop: Math.round(post.offset().top)}, speed); 
}

function selectPost(idx){
	$(".post.selected").removeClass("selected");
	postIdxSelected = idx;
	$(".post:eq("+(postIdxSelected-1)+")").addClass("selected");

	if (total > posts.length && postIdxSelected == pagesLoaded*postspage){
		loadMore();
	}
}