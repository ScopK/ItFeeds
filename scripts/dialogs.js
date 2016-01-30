var dialog = {
	effects: {
		show: function(){
			//dialog.effects.blur(true);
		},
		hide: function(){
			//dialog.effects.blur(false);
		},
		blur: function(show){
			var style = document.getElementById("main").style;
			style.filter = show?"blur(3px)":"";
		}
	},
	search: {
		show: function(){
			$('#search_dialog').fadeIn(100);
			$('#searchField').val(get.search==undefined?"":decodeURIComponent(get.search));
			$('#searchField').focus();
			$('#searchField').select();
			dialog.effects.show();
		},
		submit: function(){
			$('#search_dialog').fadeOut(100);
			dialog.effects.hide();
			get.page = undefined;
			var query = encodeURIComponent($("#searchField").val());
			if (query.length > 0){
				//$(".selected").removeClass("selected");
				//get.feed = undefined;
				//get.folder = undefined;
				//get.tag = undefined;
				get.search = query;
			} else {
				get.search = undefined;
			}
			lateral.refresh.navigationOptions();
			utils.updateURL();
			call.loadPosts();
			return false;
		}
	},
	passwordChange: {
		show: function(){
			$('#settings_panel').addClass('hidden');
			$('#pwchange_dialog').fadeIn(100);
			$("#oldPassField").focus();
			$('#pwchange_dialog form')[0].reset();
			dialog.effects.show();
		},
		submit: function(){
			var newp = $("#newPassField").val();
			if (newp != $("#newPass2Field").val()){
				showMessage("Passwords doesn't match");
				return;
			}
			var oldp = $("#oldPassField").val();
			loading_run();
			$.ajax({
				url: "./ajax/change_password.php",
				type: "POST",
				data: "lock=0&old="+oldp+"&new="+newp,
				success: function(result){
					if (result=="ok"){
						$('#pwchange_dialog').fadeOut(100);
						dialog.effects.hide();
					} else {
						showMessage("An error ocurred. Try again.");
						$("#oldPassField").val("");
						$("#newPass2Field").val("");
					}
				},
				error: function (request, status, error){
					showMessage("JS Error "+request.status+": "+request.responseText);
				},
				complete: function(){
					loading_stop();
				}
			});
			return false;
		}
	},
	lockChange: {
		show: function(){
			$('#settings_panel').addClass('hidden');
			$('#pwlchange_dialog').fadeIn(100);
			$("#oldLPassField").focus();
			$('#pwlchange_dialog form')[0].reset();
			dialog.effects.show();
		},
		submit: function(){
			var newp = $("#newLPassField").val();
			if (newp != $("#newLPass2Field").val()){
				showMessage("Passwords doesn't match");
				return;
			}
			var oldp = $("#oldLPassField").val();
			loading_run();
			$.ajax({
				url: "./ajax/change_password.php",
				type: "POST",
				data: "lock=1&old="+oldp+"&new="+newp,
				success: function(result){
					if (result=="ok"){
						$('#pwlchange_dialog').fadeOut(100);
						dialog.effects.hide();
					} else {
						showMessage("An error ocurred. Try again.");
						$("#oldLPassField").val("");
						$("#newLPass2Field").val("");
					}
				},
				error: function (request, status, error){
					showMessage("JS Error "+request.status+": "+request.responseText);
				},
				complete: function(){
					loading_stop();
				}
			});
			return false;
		}
	},
	unlock: {
		show: function(){
			$('#settings_panel').addClass('hidden');
			if ($('#unlockButton').hasClass("highlight-color")){
				$("#lockPassField").val("");
				dialog.unlock.submit();
				return;
			}
			$('#unlock_dialog').fadeIn(100);
			$('#lockPassField').val("").focus();
			dialog.effects.show();
		},
		submit: function(){
			var unlockp = $("#lockPassField").val();
			$('#unlock_dialog').fadeOut(100);
			dialog.effects.hide();
			loading_run();
			$.ajax({
				url: "./ajax/login_hidden.php",
				type: "POST",
				data: "hiddenPass="+unlockp,
				dataType : "json",
				success: function(result){
					tags = [];
					for (var i=0; i<result.tags.length; i++) {
						tags.push(new Tag(result.tags[i]));
					}
					folders = [];
					for (var i=0; i<result.folders.length; i++) {
						folders.push(new Folder(result.folders[i]));
					}
					lateral.refresh.folders();
					lateral.refresh.tags();
					call.loadPosts();
					if (result.unlocked=="true"){
						$('#unlockButton').addClass("highlight-color");
						$('#unlockButton').html("Unlocked");
					} else {
						$('#unlockButton').removeClass("highlight-color");
						$('#unlockButton').html("Unlock");
					}
				},
				error: function (request, status, error){
					showMessage("JS Error "+request.status+": "+request.responseText);
				},
				complete: function(){
					loading_stop();
				}
			});
			return false;
		}
	},
	addTags: {
		post: -1,
		show: function(idx){
			var post = idx==undefined? selectedPost : posts[idx];
			if (post!=undefined) {
				dialog.addTags.post = post;
				$('#add_tag').fadeIn(100);
				$('#add_tag p').removeClass("selected");
				$('#newtagField').val("");
				$('#newtagField').focus();
				dialog.effects.show();
			}
		},
		submit: function(){
			var tag = "";
			$("#add_tag .taglist p.selected").each(function(){
				tag += " "+this.innerHTML;
			});
			if ($("#newtagField").val())
				tag += " "+$("#newtagField").val();
			tag = encodeURIComponent(tag.substring(1));
			if (tag.length > 0) {
				var p = dialog.addTags.post;
				p.addTag(tag);
				$("#newtagField").val("");
				$('#add_tag').fadeOut(100);
				dialog.effects.hide();
			} else {
				showMessage("Select or write a tag");
			}
			return false;
		}
	}
}

$(document).ready(function(){
	$("#add_tag_content .confirm").click(dialog.addTags.submit);
	$("#search_content .confirm").click(dialog.search.submit);
	$("#pwd_change_content .confirm").click(dialog.passwordChange.submit);
	$("#pwdlock_change_content .confirm").click(dialog.lockChange.submit);
	$("#unlock_content .confirm").click(dialog.unlock.submit);

	$(".dialog-dim .cancel").click(function(){
		$(this).closest(".background-modal").fadeOut(100);
		dialog.effects.hide();
		return false;
	});
});