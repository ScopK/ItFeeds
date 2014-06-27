var folders;
var tags;
var posts;

var preselectPost = 0;
var postIdxSelected = 0;
//var idxFolder;
//var idxFeed;
//var idxTag;

var totalPages;
var totalPosts;

var get;
readGetParameters();

$(document).ready(function(){
	initialize();
	//################# Blank space at the end of page
	$(window).resize(function(){
		$("#blankspace").css("height",$(window).height());
	});
	$("#blankspace").css("height",$(window).height());

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
		error: function(result){
			alert("Unknown error 0x001");
			$("#page").fadeIn();
		},
		complete: function(){
			$("#page").fadeIn();
		}
	});
}