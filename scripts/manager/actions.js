var folders;
var tags;
var idxFolder;
var idxFeed;
var idxTag;

function loadFolders(user){
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
			console.log(tags);
			load(folders);
			loadTags(tags);
			loading_stop();
		},
		error: function(result){
			alert("Unknown error 0x001");
			loading_stop();
		}
	});
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

			feed.unread = result.feedUnread;
			feed.count = result.feedCount;

			folder.unread = result.folderUnread;
			folder.count = result.folderCount;
			load(folders);
			loading_stop();
		},
		error: function(result){
			alert("Unknown error 0x002");
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
			feed['link'] = result['link'];
			feed['enabled'] = result['enabled'];
			feed['upd_time'] = result['upd_time'];

			load(folders);
			loading_stop();
		},
		error: function(result){
			alert("Unknown error 0x003");
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

			load(folders);
			loading_stop();
		},
		error: function(result){
			alert("Unknown error 0x004");
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
			folders[prevIdxFolder] = result;
			console.log(folders[prevIdxFolder]);
			load(folders);
			loading_stop();
		},
		error: function(result){
			alert("Unknown error 0x005");
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

			load(folders);
			loading_stop();
			closeDialogs();
		},
		error: function (request, status, error){
			if (request.status>500)
				alert(error);
			else {
				alert("Unknown error 0x006");
				closeDialogs();
			}
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
				loading_stop();
			} else
				alert(result);
		},
		error: function (request, status, error){
			alert("Unknown error 0x007");
			loading_stop();
		}
	});


	closeDialogs();
}

function deleteFolder(){
	alert("NO, TOO DANGEROUS");
	/*
	var prevIdxFolder = idxFolder;

	var foldelForm = $(this).closest("form").serialize();
	alert(foldelForm);
	*/
	closeDialogs();

}

function unlockHidden(){
	var hidd = $(this).closest("form").serialize();
	loading_run();
	$.ajax({
		url: "./ajax/login_hidden.php",
		type: "POST",
		data: hidd,
		dataType : "json",
		success: function(result){
			$("#folder_list").hide();
			$("#tag_list").hide();

			folders = result.folders;
			tags = result.tags;
			console.log(tags);
			load(folders);
			loadTags(tags);
			loading_stop();
			$('#login_hidden').fadeOut(100);
			if (hidd == "hiddenPass=")
				$(".showHiddenButton").removeClass("set");
			else
				$(".showHiddenButton").addClass("set");

		},
		error: function (request, status, error){
			alert("Incorrect password or error");
			loading_stop();
		}
	});
}

function logoutButton(){
	loading_run();
	$.ajax({
		url: "./ajax/logout.php",
		type: "POST",
		success: function(result){
			window.location = "./login.php";
		},
		error: function (request, status, error){
			alert("Error Logout'ing");
			loading_stop();
		}
	});
}
