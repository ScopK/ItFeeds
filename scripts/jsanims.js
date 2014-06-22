/** LOADING ANIMATIONS CONTROL **/
function loading_stop() {
    $(".loading").css("animation-play-state","paused");
    $(".loading").hide();
}


function loading_run() {
    $(".loading").css("animation-play-state","running");
    $(".loading").show();
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
	$("#lateral_menu").css("margin-left",-lateral_width+"px");
	$("#content").css("margin-left","0");
	
}

function showLateralMenu(){
	$("#content").css("margin-left",lateral_width+"px");
	$("#lateral_menu").css("margin-left","0");
}