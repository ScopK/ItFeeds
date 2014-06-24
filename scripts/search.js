function findFeedIndex(id){
	var res = [];
	var fo = 0;
	var found = false;
	$.each(folders,function(){
		var fe = 0;
		$.each(this.feeds,function(){
			if (this.id == id){
				res[0] = fo;
				res[1] = fe;
				found = true;
				return;
			}
			fe++;
		});
		if (found == true) return;
		fo++;
	});
	return res;
}

function findFolderIndex(id){
	var index = -1;
	var fo = 0;
	$.each(folders,function(){
		if (this.id == id){
			index = fo;
			return;
		}
		fo++;
	});
	return index;
}

function findTagIndex(id){
	var index = -1;
	var ta = 0;
	$.each(tags,function(){
		if (this.id == id){
			index = ta;
			return;
		}
		ta++;
	});
	return index;
}