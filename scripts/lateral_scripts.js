function displayFoldersTags(){
	//$("#lateral_menu").html("");

	$('#options_panel').nextAll().remove();

	var html = '<div id="folders">';
	var nullHtml = '';
	var index=0;
	$.each(folders,function(){
		if (this.name == "null"){
			nullHtml += '<div id="feeds">';
			nullHtml += getHTMLFeeds(this.feeds,index);
			nullHtml += '</div>';
		} else {
			html += getHTMLFolder(this,index);
		}
		index++;
	});
	$("#lateral_menu").append(html);
	$("#lateral_menu").append(nullHtml);


	html = '<div id="tags">';
	html += getHTMLTags(tags);
	html += '</div>';
	$("#lateral_menu").append(html);
}

function getHTMLFolder(folder,idx){
	var unread="";
	if (folder.unread > 0)
		unread = ' <span class="count">('+folder.unread+')</span>';

	html = '<div class="folder" idxFolder="'+idx+'">'+
				'<div class="folderHeader">'+
					'<button class="expander" onclick="toogleViewFeeds(this)">+</button>'+
					'<span class="folderTitle">'+(folder.name)+unread+'</span>'+
				'</div>'+
				'<div class="folderfeeds">';
	html += getHTMLFeeds(folder.feeds,idx);
	html += '</div></div>';
	return html;
}

function getHTMLFeeds(feed_list,indexFolder){
	var indexFeed=0;
	var html="";
	$.each(feed_list,function(){
		var unread="";
		if (this.unread > 0)
			unread = ' <span class="count">('+this.unread+')</span>';
		
		html += '<div class="feed'+((unread=="")?"":" unread")+'" idxFolder="'+indexFolder+'" idxFeed="'+indexFeed+'">'+
					'<div class="feedTitle">'+(this.name)+unread+'</div>'+
				'</div>';
		indexFeed++;
	});
	return html;
}

function getHTMLTags(tag_list){
	var html = "";
	var index = 0;
	$.each(tags,function(){
		var count="";
		if (this.count > 0)
			count = ' <span class="count">('+this.count+')</span>';
		html += '<div class="tag" idxTag="'+index+'">'+(this.name)+count+'</div>';
		index++;

	});
	return html;
}