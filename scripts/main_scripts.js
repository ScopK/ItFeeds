function setContentActions(){
	$(".feed").click(loadFeed);
}

function loadFeed(){
	loading_run();
	var folder = folders[$(this).attr("idxfolder")];
	var feed = folder.feeds[$(this).attr("idxfeed")];

	$.ajax({
		url: "./ajax/get_posts.php",
		type: "GET",
		data: "feed="+feed.id,
		dataType : "json",
		success: function(result){
			$('#posts_panel').html("");
			$.each(result,function(){
				html ='<div class="post">';
				html += '<div class="header">'+
							'<div class="title"><a target="_blank" href="'+decodeURIComponent(escape(this.link))+'">'+this.title+'</a></div>'+
							'<div class="subtitle">[ <a target="_blank" href="'+feed.link+'">'+feed.name+'</a> ] '+this.date+'</div>'+
						'</div>';
				html += '<div class="description">'+this.description+'</div>';
				html +='</div>';

				$("#posts_panel").append(html);
			});
		},
		error: function (request, status, error){
			alert(error+" 0x001");
		},
		complete: function(){
			loading_stop();
		}
	});
}









function updateUrl(){
	var page = "/Fydeph/index.php?";
	window.history.pushState("", "", page);
}
