/** LOADING ANIMATIONS CONTROL **/
function loading_stop() {
    $(".loading").css("animation-play-state","paused");
    $("#loading_panel").fadeOut();
}


function loading_run() {
    $(".loading").css("animation-play-state","running");
    $("#loading_panel").show();
}

function loading_toggle(){
    var status = $("#smallBall").css("animation-play-state");
    if (status == "paused") loading_run();
    else                    loading_stop();
}

/** ANIMATIONS WEBSITE **/
var lateral_width;
function hideLateralMenu(){
	lateral_width = $("#lateral_menu").outerWidth();
    var left = $("#lateral_menu").position().left;
	$("#lateral_menu").css("margin-left",-(left+lateral_width)+"px");
	$("#content").css("margin-left","0");
	
}

function showLateralMenu(){
	$("#content").css("margin-left",lateral_width+"px");
	$("#lateral_menu").css("margin-left","0");
}