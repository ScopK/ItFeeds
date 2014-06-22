var folders;
var tags;
var idxFolder;
var idxFeed;
var idxTag;
$(document).ready(function(){
	getFoldersTags();
});

function getFoldersTags(){
	$("#page").fadeOut();
	$.ajax({
		url: "./manager/ajax/get_user_info.php",
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

function displayFoldersTags(){
	$("#lateral_menu").html("");
	var html = '<div id="folders">';
	var nullHtml = "";
	var index=0;
	$.each(folders,function(){
		if (this.name == "null"){
			$.each(this.feeds,function(){
				var unread=""
				if (this.unread > 0)
					unread = ' <span class="count">('+this.unread+')</span>'

				nullHtml += '<div class="feed" idxFeed="'+index+'"><div class="feedTitle">'+(this.name)+unread+'</div></div>'
			});
			return true;	// continue;
		}

		var unread=""
		if (this.unread > 0)
			unread = ' <span class="count">('+this.unread+')</span>'
		html += '<div class="folder" idxFolder="'+index+'"><div class="folderTitle">'+(this.name)+unread+'</div>'

		$.each(this.feeds,function(){
			var unread=""
			if (this.unread > 0)
				unread = ' <span class="count">('+this.unread+')</span>'
			html += '<div class="feed" idxFeed="'+index+'"><div class="feedTitle">'+(this.name)+unread+'</div></div>'
		});


		html += '</div>'
	});
	$("#lateral_menu").html(html);
	$("#lateral_menu").append(nullHtml);
}