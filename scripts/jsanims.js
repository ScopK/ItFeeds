function loading_stop()
{
    $("#loading_icon").css("animation-play-state","paused");
    $("#loading_icon").hide();
}


function loading_run()
{
    $("#loading_icon").css("animation-play-state","running");
    $("#loading_icon").show();
}

function loading_toggle(){
    var status = $("#loading_icon").css("animation-play-state");
    if (status == "paused") loading_run();
    else                    loading_stop();
}