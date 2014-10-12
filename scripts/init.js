var folders;
var tags;
var posts;

var preselectPost = 0;	//toDeleteWhenFinished
var postIdxSelected = 0;
var postCount;
var pagesLoaded;

var totalPages;
var totalPosts;

var get;
readGetParameters();

$(document).ready(function(){
	initialize();
	//################# Blank space at the end of page
	$(window).resize(function(){
		$("#load_more_panel").css("height",$(window).height()-60);
	});
	$("#load_more_panel").css("height",$(window).height()-60);

	//################# Middle click on button will open new page
	$("button").mousedown(function(e){
		if (e.button == 1){
			window.open(window.location.pathname+window.location.search, '_blank', '');
			return true; // to allow the browser to know that we handled it.
		}
	});


	//################# Auto position post controls
	$(document).scroll(situatePostControls);

	//################# Mouse navigator
	$('#mouse_nav').mousedown(function(event) {
	    switch (event.which) {
	        case 1:
	            nextPost();
	            break;
	        case 3:
	            prevPost();
	            return false;
	    }
	});
});

function situatePostControls(){
	var pos = $(document).scrollTop();
	var controllers = $(".post").toArray();
	var idx = 0;
	$.each(controllers.reverse(),function(){
		var con = $(this).find(".controller");
		var top = this.offsetTop;
		var bot = top + this.offsetHeight - con.height();
		if (top < pos && pos < bot){
			con.addClass("fixed");
		} else {
			con.removeClass("fixed");
		}
	});
}

function initialize(){
	$("#page").hide();
	loading_run();
	$.ajax({
		url: "./ajax/get_user_info.php",
		type: "GET",
		dataType : "json",
		success: function(result){
			folders = result.folders;
			tags = result.tags;
			displayFolders();
			displayTags();

			reloadPosts();
		},
		error: function (request, status, error){
			showMessage("Couldn't get user info<br/>Error "+request.status+": "+request.responseText);
		},
		complete: function(){
			$("#page").show();
			loading_stop();
		}
	});
}

function randomColors(){
	var bright = "#C8F56F";
	var dark = "#9acc37";
	/*
    var hueSel = Math.random();
    if (hueSel < 0.3)
        var hue = 'rgb(255,'+ (Math.floor(Math.random()*155)+100) +','+ (Math.floor(Math.random()*155)+100) +')';
    else if (hueSel <= 0.6)
        var hue = 'rgb('+ (Math.floor(Math.random()*155)+100) +',255,'+ (Math.floor(Math.random()*155)+100) +')';
    else
        var hue = 'rgb('+ (Math.floor(Math.random()*155)+100) +','+ (Math.floor(Math.random()*155)+100) +',255)';
    //$("body").css("background-color",hue);
	bright = hue;
	dark = hue;
    /**/
    $.each(document.styleSheets[1].cssRules, function(){
        if (this.selectorText == ".feed.selected, .tag.selected, .folder.selected")
            this.style.backgroundColor = bright;
        else if (this.selectorText == ".post.unread .header")
            this.style.backgroundColor = bright;
        else if (this.selectorText == ".mouse-button.colored")
            this.style.backgroundColor = dark;

	    else if (this.selectorText == ".highlight-color"){
			this.style.backgroundColor = dark;
			this.style.borderColor = dark;
	    }
	    else if (this.selectorText == ".highlight-color:active"){
			this.style.backgroundColor = bright;
			this.style.borderColor = bright;
	    }
	    else if (this.selectorText == ".button-panel.marked:active"){
			this.style.backgroundColor = bright;
			this.style.borderColor = bright;
	    }
	    else if (this.selectorText == ".button-panel:active"){
			this.style.backgroundColor = bright;
			this.style.borderColor = bright;
	    }
    });


    //setTimeout(randomColors,2000);
    /**/
}