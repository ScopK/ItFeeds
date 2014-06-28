
function displayFolders() {
	var html = '';
	var feedHtml = '';
	$.each(folders,function(index){
		if (this.name == "null"){
			feedHtml += getHTMLFeeds(this.feeds,index);
		} else {
			html += getHTMLFolder(this,index);
		}
	});
	$("#folders").html(html);
	$("#feeds").html(feedHtml);

	if (get.feed != undefined){
		$(".feed[idFeed='"+get.feed+"']").closest(".folderfeeds").show();
		$(".feed[idFeed='"+get.feed+"']").closest(".folder").find("button").html("-");
	}

	setFeedsActions();
}

function displayTags() {
	var html = getHTMLTags(tags);
	$("#tags").html(html);

	setTagsActions();
}

function getHTMLFolder(folder,idx){
	var unread="";
	if (folder.unread > 0)
		unread = ' <span class="count">('+folder.unread+')</span>';

	var selected = (get.folder == folder.id);
	html = '<div class="folder'+((selected)?' selected':'')+'" idxFolder="'+idx+'" idFolder="'+folder.id+'">'+
				'<div class="folderHeader'+((folder.hidden==1)?" hidden":"")+'">'+
					'<button class="expander" onclick="toogleViewFeeds(this)">'+((selected)?'-':'+')+'</button>'+
					'<span class="folderTitle">'+(folder.name)+unread+'</span>'+
				'</div>'+
				'<div class="folderfeeds"'+((selected)?' style="display:block"':'')+'>';

	html += getHTMLFeeds(folder.feeds,idx);
	html += '</div></div>';
	return html;
}

function getHTMLFeeds(feed_list,indexFolder){
	var html="";
	$.each(feed_list,function(indexFeed){
		var unread="";
		if (this.unread > 0)
			unread = ' <span class="count">('+this.unread+')</span>';
		
		var selected = (get.feed == this.id);
		html += '<div class="feed'+((selected)?' selected':'')+((unread=="")?"":" unread")+'" idxFolder="'+indexFolder+'" idxFeed="'+indexFeed+'" idFeed="'+this.id+'">'+
					'<div class="feedTitle">'+(this.name)+unread+'</div>'+
				'</div>';
	});
	return html;
}

function getHTMLTags(tag_list){
	var html = "";
	$.each(tags,function(index){
		var count="";
		if (this.count > 0)
			count = ' ';

		var selected = (get.tag == this.id);

		html += '<div class="tag'+((selected)?' selected':'')+((this.hidden==1)?" hidden":"")+'" idxTag="'+index+'" idTag="'+this.id+'">'+
					'<span class="name">'+(this.name)+'</span> <span class="count">('+this.count+')</span></div>';

	});
	return html;
}



