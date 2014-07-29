var folders;
var tags;
var posts;

var preselectPost = 0;	//toDeleteWhenFinished
var postIdxSelected = 0;
var postCount;
var pagesLoaded;
var nextPostId;

var totalPages;
var totalPosts;

var get;
readGetParameters();

$(document).ready(function(){
	initialize();
	//################# Blank space at the end of page
	$(window).resize(function(){
		$("#load_more_panel").css("height",$(window).height()-60);
	});
	$("#load_more_panel").css("height",$(window).height()-60);

	//################# Auto position post controls
	$(document).scroll(function() {
		var pos = $(document).scrollTop();
		$("#actions_panel").css("top",pos);
	});

	//################# Middle click on button will open new page
	$("button").mousedown(function(e){
		if (e.button == 1){
			window.open(window.location.pathname+window.location.search, '_blank', '');
			return true; // to allow the browser to know that we handled it.
		}
	});

	//################# Mouse navigator
	$('#mouse_nav').mousedown(function(event) {
	    switch (event.which) {
	        case 1:
	            nextPost();
	            break;
	        case 3:
	            prevPost();
	            return false;
	    }
	});

	//################# Hide message when mouse over
	$("#top_message").hover(hideMessage);
});

function initialize(){
	$("#page").hide();
	loading_run();
	$.ajax({
		url: "./ajax/get_user_info.php",
		type: "GET",
		dataType : "json",
		success: function(result){
			folders = result.folders;
			tags = result.tags;
			displayFolders();
			displayTags();

			reloadPosts();
		},
		error: function (request, status, error){
			showMessage("Couldn't get user info");
		},
		complete: function(){
			$("#page").show();
		}
	});
}

function randomColors(){
    var hueSel = Math.random();
    if (hueSel < 0.3)
        var hue = 'rgb(255,'+ (Math.floor(Math.random()*155)+100) +','+ (Math.floor(Math.random()*155)+100) +')';
    else if (hueSel <= 0.6)
        var hue = 'rgb('+ (Math.floor(Math.random()*155)+100) +',255,'+ (Math.floor(Math.random()*155)+100) +')';
    else
        var hue = 'rgb('+ (Math.floor(Math.random()*100)+155) +','+ (Math.floor(Math.random()*100)+155) +',255)';
    //$("body").css("background-color",hue);
    $.each(document.styleSheets[1].cssRules, function(){
        if (this.selectorText == ".feed.selected, .tag.selected, .folder.selected")
            this.style.backgroundColor = hue;
        else if (this.selectorText == ".post.unread .header")
            this.style.backgroundColor = hue;
    });
    //setTimeout(randomColors,2000);
}