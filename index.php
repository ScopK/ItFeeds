<?php
    session_start();
    session_destroy();
    session_start();

    if (!isset($_SESSION['log_user'])){
        header('Location: ./login.php');
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="anims3.css">
        <script src="jsanims.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script>
            function loading_stop(){
                var status = $("#loading_icon").css("animation-play-state");
                if (status == "paused") loading_run();
                else                    loading_stop();
            }
        </script>
    </head>
    <body>
        <button onclick="stop()" value="STTOP">stop</button>
        <div id="loading_icon"></div>
    </body>
</html>
