function showSettings_folder(idx){
	var f = folders[idx];
	$("#settings_dialog .tab:not(.folder-tab)").hide();
	$("#settings_dialog .folder-tab").show();
	$("#settings_dialog").show();

	$("#settings_dialog .folder-tab .title").html("Edit folder: "+f.name);
	$("#edit_name_folder").val(f.name);
	$("#edit_idx_folder").val(idx);
	$("#edit_hidden_folder").prop("checked",f.hidden==1);
}

function showSettings_feed(idxf,idx){
	var f = folders[idxf].feeds[idx];
	$("#settings_dialog .tab:not(.feed-tab)").hide();
	$("#settings_dialog .feed-tab").show();
	$("#settings_dialog").show();

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
	$("#settings_dialog").show();

	$("#settings_dialog .tag-tab .title").html("Edit Tag: "+t.name);
	$("#edit_name_tag").val(t.name);
	$("#edit_idx_tag").val(idx);
	$("#edit_hidden_tag").prop("checked",t.hidden==1);
	$("#edit_public_tag").prop("checked",t.public==1);
}

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
			$('#settings_dialog').fadeOut(100);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
		},
		complete: function(){
			loading_stop();
		}
	});
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
			$('#settings_dialog').fadeOut(100);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
		},
		complete: function(){
			loading_stop();
		}
	});
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
			$('#settings_dialog').fadeOut(100);
		},
		error: function (request, status, error){
			showMessage("Error "+request.status+": "+request.statusText);
		},
		complete: function(){
			loading_stop();
		}
	});
}