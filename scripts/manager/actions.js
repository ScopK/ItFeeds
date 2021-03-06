var folders;
var tags;
var idxFolder;
var idxFeed;
var idxTag;
var first = false;

function loadFolders(){
	$("#user_list").fadeOut(200);
	loading_run();
	$("#folder_list").hide();
	$("#tag_list").hide();
	closeDialogs();
	folders = "";

	$.ajax({
		url: "./ajax/get_user_info.php",
		type: "GET",
		dataType : "json",
		success: function(result){
			folders = result.folders;
			tags = result.tags;
			load(folders);
			loadTags(tags);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
		},
		complete: function(){
			loading_stop();
		}
	});
}

function cleanAll(){
	loading_run();
	var cleanForm = $(this).closest("form").serialize();
	$.ajax({
		url: "./ajax/manager/clean_all.php",
		type: "POST",
		data: cleanForm,
		dataType : "json",
		success: function(result){
			showMessage("Cleaned "+result.postsDeleted+" posts",true);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
		},
		complete: function(){
			loading_stop();
		}
	});
	closeDialogs();
}

function cleanFeed(){
	var prevIdxFolder = idxFolder;
	var prevIdxFeed = idxFeed;
	loading_run();
	var cleanForm = $(this).closest("form").serialize();
	$.ajax({
		url: "./ajax/manager/clean_feed.php",
		type: "POST",
		data: cleanForm,
		dataType : "json",
		success: function(result){
			var folder = folders[prevIdxFolder];
			var feed = folder.feeds[prevIdxFeed];
			var cleaned = feed.count-result.feedCount;
			feed.unread = result.feedUnread;
			feed.count = result.feedCount;

			folder.unread = result.folderUnread;
			folder.count = result.folderCount;
			load(folders);
			showMessage("Cleaned "+cleaned+" posts",true);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
		},
		complete: function(){
			loading_stop();
		}
	});
	closeDialogs();
}

function editFeed(){
	var prevIdxFolder = idxFolder;
	var prevIdxFeed = idxFeed;
	loading_run();
	var editForm = $(this).closest("form").serialize();
	$.ajax({
		url: "./ajax/manager/edit_feed.php",
		type: "POST",
		data: editForm,
		dataType : "json",
		success: function(result){
			var feed = folders[prevIdxFolder].feeds[prevIdxFeed];
			feed['name'] = result['name'];
			feed['rss_link'] = result['rss_link'];
			feed['max_unread'] = result['max_unread'];
			feed['link'] = result['link'];
			feed['enabled'] = result['enabled'];
			feed['upd_time'] = result['upd_time'];
			folders[prevIdxFolder].feeds.sort(nameSort);

			load(folders);
			showMessage("Feed edited correctly",true);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
		},
		complete: function(){
			loading_stop();
		}
	});
	closeDialogs();
}

function editFolder(){
	var prevIdxFolder = idxFolder;
	loading_run();
	var editForm = $(this).closest("form").serialize();
	$.ajax({
		url: "./ajax/manager/edit_folder.php",
		type: "POST",
		data: editForm,
		dataType : "json",
		success: function(result){
			var folder = folders[prevIdxFolder];
			folder['name'] = result['name'];
			folder['hidden'] = result['hidden'];
			folders.sort(nameSort);

			load(folders);
			showMessage("Folder edited correctly",true);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
		},
		complete: function(){
			loading_stop();
		}
	});
	closeDialogs();
}

function editTag(){
	var prevIdxTag = idxTag;
	loading_run();
	var editForm = $(this).closest("form").serialize();

	$.ajax({
		url: "./ajax/manager/edit_tag.php",
		type: "POST",
		data: editForm,
		dataType : "json",
		success: function(result){
			var tag = tags[prevIdxTag];
			tag['name'] = result['name'];
			tag['hidden'] = result['hidden'];
			tag['public'] = result['public'];
			tags.sort(nameSort);

			loadTags(tags);
			showMessage("Tag edited correctly",true);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
		},
		complete: function(){
			loading_stop();
		}
	});
	closeDialogs();
}

function cleanFolder(){
	var prevIdxFolder = idxFolder;
	loading_run();
	var cleanForm = $(this).closest("form").serialize();
	$.ajax({
		url: "./ajax/manager/clean_folder.php",
		type: "POST",
		data: cleanForm,
		dataType : "json",
		success: function(result){
			var cleaned = folders[prevIdxFolder].count-result.count;
			folders[prevIdxFolder] = result;
			load(folders);
			showMessage("Cleaned "+cleaned+" posts",true);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
		},
		complete: function(){
			loading_stop();
		}
	});
	closeDialogs();
}

function addFeed(){
	var prevIdxFolder = idxFolder;
	loading_run();
	var addForm = $(this).closest("form").serialize();

	$.ajax({
		url: "./ajax/manager/new_feed.php",
		type: "POST",
		data: addForm,
		dataType : "json",
		success: function(result){
			folders[prevIdxFolder].feeds.push(result);
			folders[prevIdxFolder].feeds.sort(nameSort);
			load(folders);
			closeDialogs();
			showMessage("Feed added correctly",true);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
			if (request.status<=500)
				closeDialogs();
		},
		complete: function(){
			loading_stop();
		}
	});
}

function deleteFeed(){
	var prevIdxFolder = idxFolder;
	var prevIdxFeed = idxFeed;
	loading_run();
	var feddelFeed = $(this).closest("form").serialize();

	$.ajax({
		url: "./ajax/manager/delete_feed.php",
		type: "POST",
		data: feddelFeed,
		//dataType : "json",
		success: function(result){
			if (result == "oK"){
				folders[prevIdxFolder].feeds.splice(prevIdxFeed,1);
				load(folders);
				showMessage("Feed deleted correctly",true);
			} else
				showMessage(result);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
		},
		complete: function(){
			loading_stop();
		}
	});

	closeDialogs();
}

function deleteFolder(){
	var prevIdxFolder = idxFolder;
	loading_run();
	var foldelForm = $(this).closest("form").serialize();

	$.ajax({
		url: "./ajax/manager/delete_folder.php",
		type: "POST",
		data: foldelForm,
		//dataType : "json",
		success: function(result){
			if (result == "oK"){
				folders.splice(prevIdxFolder,1);
				load(folders);
				showMessage("Folder deleted correctly",true);
			} else
				showMessage(result);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
		},
		complete: function(){
			loading_stop();
		}
	});

	closeDialogs();

}

function deleteTag(){
	var prevIdxTag = idxTag;
	loading_run();
	var tagdelForm = $(this).closest("form").serialize();

	$.ajax({
		url: "./ajax/manager/delete_tag.php",
		type: "POST",
		data: tagdelForm,
		//dataType : "json",
		success: function(result){
			if (result == "oK"){
				tags.splice(prevIdxTag,1);
				loadTags(tags);
				showMessage("Tag deleted correctly",true);
			} else
				showMessage(result);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
		},
		complete: function(){
			loading_stop();
		}
	});

	closeDialogs();
}

function addFolder(){
	loading_run();
	var addForm = $(this).closest("form").serialize();

	$.ajax({
		url: "./ajax/manager/new_folder.php",
		type: "POST",
		data: addForm,
		dataType : "json",
		success: function(result){
			folders.push(result);
			folders.sort(nameSort);

			load(folders);
			closeDialogs();
			showMessage("Folder added correctly",true);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
			if (request.status<=500) 
				closeDialogs();
		},
		complete: function(){
			loading_stop();
		}
	});
}

function nameSort(a, b){
	var aName = a.name.toLowerCase();
	var bName = b.name.toLowerCase(); 
	return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
}