var folders;
var tags;
var idxFolder;
var idxFeed;
var idxTag;
$(document).ready(function(){
	getFoldersTags();

	$(window).resize(function(){
		$("#page").css("min-height",$(window).height());
	});
	$("#page").css("min-height",$(window).height());
});

function getFoldersTags(){
	$("#page").fadeOut();
	$.ajax({
		url: "./scripts/manager/ajax/get_user_info.php",
		type: "GET",
		dataType : "json",
		success: function(result){
			folders = result.folders;
			tags = result.tags;
			displayFoldersTags();
			loading_stop();
			$("#page").fadeIn();
		},
		error: function(result){
			alert("Unknown error 0x001");
			loading_stop();
			$("#page").fadeIn();
		}
	});
}

function toogleViewFeeds(me){
	var hid = $(me).html();

	if (hid == '+'){
		$(me).closest(".folder").find(".folderfeeds").slideDown();
		$(me).attr("hidd","0");
		$(me).html("-");
	} else {
		$(me).closest(".folder").find(".folderfeeds").slideUp();
		$(me).attr("hidd","1");
		$(me).html("+");
	}	
}

