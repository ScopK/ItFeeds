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
	$("#page").fadeOut();
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
			$("#page").fadeIn();
		}
	});
}