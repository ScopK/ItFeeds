var loading_stacks=0;

/** LOADING ANIMATIONS CONTROL **/
function loading_stop() {
	loading_stacks--;
	if (loading_stacks == 0){
	    $(".loading").css("animation-play-state","paused");
	    $("#loading_panel").fadeOut();
	}
}


function loading_run() {
    $(".loading").css("animation-play-state","running");
    $("#loading_panel").show();
    loading_stacks++;
}

function loading_toggle(){
    var status = $("#smallBall").css("animation-play-state");
    if (status == "paused") loading_run();
    else                    loading_stop();
}