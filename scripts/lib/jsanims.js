var loading_stacks=0;

/** LOADING ANIMATIONS CONTROL **/
function loading_stop() {
	loading_stacks--;
	if (loading_stacks <= 0){
        loading_stacks=0;
	    $(".loading").css("animation-play-state","paused");
	    $("#loading_panel").fadeOut();
	}
}

function loading_run() {
    $(".loading").css("animation-play-state","running");
    $("#loading_panel").fadeIn(100);
    loading_stacks++;
}

function loading_toggle(){
    if (loading_check()) loading_stop();
    else                 loading_run();
}

function loading_check(){
    var status = $("#loadingBar1").css("animation-play-state");
    return status != "paused";
}