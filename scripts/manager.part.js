//###############################################
//###############################################
//###############################################
//###############################################
//###############################################
//###############################################
//###################################### SETTINGS

function showSettings_folder(idx){
	var f = folders[idx];
	$("#settings_dialog .tab:not(.folder-tab)").hide();
	$("#settings_dialog .folder-tab").show();
	$("#settings_dialog").fadeIn(100);

	$("#settings_dialog .folder-tab .title").html("Edit folder: "+f.name);
	$("#edit_name_folder").val(f.name);
	$("#edit_idx_folder").val(idx);
	$("#edit_hidden_folder").prop("checked",f.hidden==1);
}

function showSettings_feed(idxf,idx){
	var f = folders[idxf].feeds[idx];
	$("#settings_dialog .tab:not(.feed-tab)").hide();
	$("#settings_dialog .feed-tab").show();
	$("#settings_dialog").fadeIn(100);

	$("#settings_dialog .feed-tab .title").html("Edit Feed: "+f.name);
	$("#edit_name_feed").val(f.name);
	$("#edit_rss_feed").val(f.rss_link);
	$("#edit_link_feed").val(f.link);
	$("#edit_max_feed").val(f.max_unread);
	$("#edit_upd_feed").val(f.upd_time);
	$("#edit_idx_folderfeed").val(idxf);
	$("#edit_idx_feed").val(idx);
	$("#edit_enabled_feed").prop("checked",f.enabled==1);
}

function showSettings_tag(idx){
	var t = tags[idx];
	$("#settings_dialog .tab:not(.tag-tab)").hide();
	$("#settings_dialog .tag-tab").show();
	$("#settings_dialog").fadeIn(100);

	$("#settings_dialog .tag-tab .title").html("Edit Tag: "+t.name);
	$("#edit_name_tag").val(t.name);
	$("#edit_idx_tag").val(idx);
	$("#edit_hidden_tag").prop("checked",t.hidden==1);
	$("#edit_public_tag").prop("checked",t.public==1);
}

//###############################################

function editFolder(){
	var name = $("#edit_name_folder").val();
	var hidden = $("#edit_hidden_folder").prop("checked");
	var idx = $("#edit_idx_folder").val();
	var folder = folders[idx];
	var id = folder.id;
	loading_run();

	$.ajax({
		url: "./ajax/manager/edit_folder.php",
		type: "POST",
		data: "folderId="+id+"&fname="+encodeURIComponent(name)+(hidden?"&hid=on":""),
		dataType : "json",
		success: function(result){
			folder['name'] = result['name'];
			folder['hidden'] = result['hidden'];
			folders.sort(nameSort);

			displayFolders();

			showMessage("Folder edited correctly",true);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
		},
		complete: function(){
			loading_stop();
		}
	});
	$('#settings_dialog').fadeOut(100);
}

function editFeed(){
	var name = $("#edit_name_feed").val();
	var link = $("#edit_link_feed").val();
	var rss = $("#edit_rss_feed").val();
	var fidx = $("#edit_idx_folderfeed").val();
	var idx = $("#edit_idx_feed").val();
	var updTime = $("#edit_upd_feed").val();
	var maxUnre = $("#edit_max_feed").val();
	var enabled = $("#edit_enabled_feed").prop("checked");
	var feed = folders[fidx].feeds[idx];
	var id = feed.id;
	loading_run();

	$.ajax({
		url: "./ajax/manager/edit_feed.php",
		type: "POST",
		data: "feedId="+id+"&fname="+encodeURIComponent(name)+"&rlink="+encodeURIComponent(rss)+"&link="+encodeURIComponent(link)+"&uptime="+updTime+"&maxunr="+maxUnre+(enabled?"&ena=on":""),
		dataType : "json",
		success: function(result){
			feed['name'] = result['name'];
			feed['rss_link'] = result['rss_link'];
			feed['max_unread'] = result['max_unread'];
			feed['link'] = result['link'];
			feed['enabled'] = result['enabled'];
			feed['upd_time'] = result['upd_time'];
			folders[fidx].feeds.sort(nameSort);

			displayFolders();

			showMessage("Feed edited correctly",true);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
		},
		complete: function(){
			loading_stop();
		}
	});
	$('#settings_dialog').fadeOut(100);
}

function editTag(){
	var name = $("#edit_name_tag").val();
	var hidden = $("#edit_hidden_tag").prop("checked");
	var pub = $("#edit_public_tag").prop("checked");
	var idx = $("#edit_idx_tag").val();
	var tag = tags[idx];
	var id = tag.id;
	loading_run();

	$.ajax({
		url: "./ajax/manager/edit_tag.php",
		type: "POST",
		data: "tagId="+id+"&tagname="+encodeURIComponent(name)+(hidden?"&hidden=on":"")+(pub?"&public=on":""),
		dataType : "json",
		success: function(result){
			tag['name'] = result['name'];
			tag['hidden'] = result['hidden'];
			tag['public'] = result['public'];
			tags.sort(nameSort);

			displayTags();

			showMessage("Tag edited correctly",true);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
		},
		complete: function(){
			loading_stop();
		}
	});
	$('#settings_dialog').fadeOut(100);
}

//###############################################
//###############################################
//###############################################
//###############################################
//###############################################
//###############################################
//######################################### CLEAN

function showClean_folder(idx){
	var f = folders[idx];
	$("#cleaning_dialog .tab:not(.folder-tab)").hide();
	$("#cleaning_dialog .folder-tab").show();
	$("#cleaning_dialog").fadeIn(100);

	$("#clean_idx_folder").val(idx);
	$("#cleaning_dialog .folder-tab .title").html("Clean folder posts: "+f.name);
	$("#clean_folder_days").val("3");
	$("#clean_folder_unread").prop("checked",false);
}

function showClean_feed(idxf,idx){
	var f = folders[idxf].feeds[idx];
	$("#cleaning_dialog .tab:not(.feed-tab)").hide();
	$("#cleaning_dialog .feed-tab").show();
	$("#cleaning_dialog").fadeIn(100);

	$("#clean_idx_folderfeed").val(idxf);
	$("#clean_idx_feed").val(idx);
	$("#cleaning_dialog .feed-tab .title").html("Clean feed posts: "+f.name);
	$("#clean_feed_days").val("3");
	$("#clean_feed_unread").prop("checked",false);
}

function showClean_all(){
	$("#cleaning_dialog .tab:not(.all-tab)").hide();
	$("#cleaning_dialog .all-tab").show();
	$("#cleaning_dialog").fadeIn(100);

	$("#clean_all_days").val("3");
	$("#clean_all_unread").prop("checked",false);
}


//###############################################


function cleanFolder(){
	var days = $("#clean_folder_days").val();
	var unread = $("#clean_folder_unread").prop("checked");
	var idx = $("#clean_idx_folder").val();
	var folder = folders[idx];
	var id = folder.id;

	$.ajax({
		url: "./ajax/manager/clean_folder.php",
		type: "POST",
		data: "folderId="+id+"&days="+days+(unread?"&unread=on":""),
		dataType : "json",
		success: function(result){
			var cleaned = folder.count-result.count;
			folders[idx] = result;

			displayFolders();

			showMessage("Cleaned "+cleaned+" posts",true);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
		},
		complete: function(){
			loading_stop();
		}		
	});
	$('#cleaning_dialog').fadeOut(100);
}

function cleanAll(){
	var days = $("#clean_all_days").val();
	var unread = $("#clean_all_unread").prop("checked");

	$.ajax({
		url: "./ajax/manager/clean_all.php",
		type: "POST",
		data: "days="+days+(unread?"&unread=on":""),
		dataType : "json",
		success: function(result){
			initialize(false);

			showMessage("Cleaned "+result.postsDeleted+" posts",true);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
		},
		complete: function(){
			loading_stop();
		}
	});
	$('#cleaning_dialog').fadeOut(100);
}

function cleanFeed(){
	var days = $("#clean_feed_days").val();
	var unread = $("#clean_feed_unread").prop("checked");
	var fidx = $("#clean_idx_folderfeed").val();
	var idx = $("#clean_idx_feed").val();
	var feed = folders[fidx].feeds[idx];
	var id = feed.id;

	$.ajax({
		url: "./ajax/manager/clean_feed.php",
		type: "POST",
		data: "feedId="+id+"&days="+days+(unread?"&unread=on":""),
		dataType : "json",
		success: function(result){
			var folder = folders[fidx];
			var feed = folder.feeds[idx];

			var cleaned = feed.count-result.feedCount;
			feed.unread = result.feedUnread;
			feed.count = result.feedCount;
			folder.unread = result.folderUnread;
			folder.count = result.folderCount;

			displayFolders();

			showMessage("Cleaned "+cleaned+" posts",true);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
		},
		complete: function(){
			loading_stop();
		}
	});
	$('#cleaning_dialog').fadeOut(100);
}


//###############################################
//###############################################
//###############################################
//###############################################
//###############################################
//###############################################
//######################################## DELETE

function showDelete_folder(idx){
	var f = folders[idx];
	$("#delete_dialog .tab:not(.folder-tab)").hide();
	$("#delete_dialog .folder-tab").show();
	$("#delete_dialog").fadeIn(100);

	$("#delete_idx_folder").val(idx);
	$("#delete_folder_pass").val("");
	$("#delete_dialog .folder-tab .title").html("Delete folder: "+f.name);
	$("#delete_dialog .folder-tab .foldername").html(f.name);
}

function showDelete_feed(idxf,idx){
	var f = folders[idxf].feeds[idx];
	$("#delete_dialog .tab:not(.feed-tab)").hide();
	$("#delete_dialog .feed-tab").show();
	$("#delete_dialog").fadeIn(100);

	$("#delete_idx_folderfeed").val(idxf);
	$("#delete_idx_feed").val(idx);
	$("#delete_dialog .feed-tab .title").html("Delete feed: "+f.name);
	$("#delete_dialog .feed-tab .feedname").html(f.name);
}

function showDelete_tag(idx){
	var t = tags[idx];
	$("#delete_dialog .tab:not(.tag-tab)").hide();
	$("#delete_dialog .tag-tab").show();
	$("#delete_dialog").fadeIn(100);

	$("#delete_idx_tag").val(idx);
	$("#delete_dialog .tag-tab .title").html("Delete tag: "+t.name);
	$("#delete_dialog .tag-tab .tagname").html(t.name);
}


//###############################################


function deleteFeed(){
	var fidx = $("#delete_idx_folderfeed").val();
	var idx = $("#delete_idx_feed").val();
	var feed = folders[fidx].feeds[idx];
	var id = feed.id;
	loading_run();

	$.ajax({
		url: "./ajax/manager/delete_feed.php",
		type: "POST",
		data: "feedId="+id,
		//dataType : "json",
		success: function(result){
			if (result == "oK"){
				folders[fidx].feeds.splice(idx,1);
				displayFolders();
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
	$('#delete_dialog').fadeOut(100);
}

function deleteFolder(){
	var pass = $("#delete_folder_pass").val();
	var idx = $("#delete_idx_folder").val();
	var folder = folders[idx];
	var id = folder.id;
	loading_run();

	$.ajax({
		url: "./ajax/manager/delete_folder.php",
		type: "POST",
		data: "folderId="+id+"&pass="+encodeURIComponent(pass),
		//dataType : "json",
		success: function(result){
			if (result == "oK"){
				folders.splice(idx,1);
				displayFolders();
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
	$('#delete_dialog').fadeOut(100);
}

function deleteTagMan(){
	var idx = $("#delete_idx_tag").val();
	var tag = tags[idx];
	var id = tag.id;
	loading_run();

	$.ajax({
		url: "./ajax/manager/delete_tag.php",
		type: "POST",
		data: "tagId="+id,
		//dataType : "json",
		success: function(result){
			if (result == "oK"){
				tags.splice(idx,1);
				displayTags();
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
	$('#delete_dialog').fadeOut(100);
}

//###############################################
//###############################################
//###############################################
//###############################################
//###############################################
//###############################################
//######################################## CREATE

function showCreate_folder(){
	$("#create_dialog .tab:not(.folder-tab)").hide();
	$("#create_dialog .folder-tab").show();
	$("#create_dialog").fadeIn(100);

	$("#create_folder_name").val("");
}

function showCreate_feed(folder_idx){
	var f = folders[folder_idx];
	$("#create_dialog .tab:not(.feed-tab)").hide();
	$("#create_dialog .feed-tab").show();
	$("#create_dialog").fadeIn(100);

	if (f.name=="null")
		$("#create_dialog .feed-tab .title").html("Create new feed");
	else
		$("#create_dialog .feed-tab .title").html("Create new feed for '"+f.name+"'");
	$("#create_idx_folder").val(folder_idx);

	$("#create_feed_name").val("");
	$("#create_feed_rss").val("");
	$("#create_feed_link").val("");
}

//###############################################

function createFolder(){
	var foldername = $("#create_folder_name").val();

	loading_run();
	$.ajax({
		url: "./ajax/manager/new_folder.php",
		type: "POST",
		data: "foldername="+encodeURIComponent(foldername),
		dataType : "json",
		success: function(result){
			folders.push(result);
			folders.sort(nameSort);

			displayFolders();
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
	$("#create_dialog").hide();
}

function createFeed(){
	var fname = $("#create_feed_name").val();
	var rssLink = $("#create_feed_rss").val();
	var link = $("#create_feed_link").val();
	var idx = $("#create_idx_folder").val();
	var folder = folders[idx];
	var id = folder.id;

	loading_run();
	$.ajax({
		url: "./ajax/manager/new_feed.php",
		type: "POST",
		data: "folderId="+id+"&fname="+encodeURIComponent(fname)+"&rlink="+encodeURIComponent(rssLink)+"&link="+encodeURIComponent(link),
		dataType : "json",
		success: function(result){
			folders[idx].feeds.push(result);
			folders[idx].feeds.sort(nameSort);

			displayFolders();
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
	$("#create_dialog").hide();
}

//###############################################
//###############################################
//###############################################
//###############################################
//###############################################
//###############################################
//########################################## MOVE


function showMove_feed(idxf,idx){
	var f = folders[idxf].feeds[idx];
	//$("#move_dialog .tab:not(.feed-tab)").hide();
	//$("#move_dialog .feed-tab").show();
	$("#move_dialog").fadeIn(100);

	$("#move_dialog .feed-tab .title").html("Move Feed: "+f.name);
	$("#move_idx_folderfeed").val(idxf);
	$("#move_idx_feed").val(idx);
	
	var html = "";
	for (var i in folders){
		var name = folders[i].name;
		var selected = (i==idxf)? "selected":"";
		if (name=="null")
			html="<option value='"+i+"' "+selected+">- None -</option>"+html;
		else
			html+="<option value='"+i+"' "+selected+">"+name+"</option>";
	}
	$("#move_feed_to").html(html);
}

//###############################################

function moveFeed(){
	var fidx = $("#move_idx_folderfeed").val();
	var idx = $("#move_idx_feed").val();
	var newFolderIdx = $("#move_feed_to").val();
	var newFolder = folders[newFolderIdx];
	var feed = folders[fidx].feeds[idx];
	var id = feed.id;

	loading_run();
	$.ajax({
		url: "./ajax/manager/move_feed.php",
		type: "POST",
		data: "folderId="+newFolder.id+"&feedId="+feed.id,
		//dataType : "json",
		success: function(result){
			if (result=="oK"){
				folders[newFolderIdx].feeds.push(folders[fidx].feeds.splice(idx,1)[0]);
				folders[newFolderIdx].feeds.sort(nameSort);

				displayFolders();
				showMessage("Feed moved correctly",true);
			}
			else alert("ERROR MOVING FEED: "+result);
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
	$("#move_dialog").hide();
}