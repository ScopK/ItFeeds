<?php
    session_start();

    if (!isset($_SESSION['log_user'])){
        header('Location: ./login.php');
    } else {
        $log_user = $_SESSION['log_user'];
        $hidClass = (isset($_SESSION['hid_user']))?"set":"";
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title>Fydeph - <?=$log_user?></title>
        <link rel="shortcut icon" href="imgs/icon.png" />
        <link rel="stylesheet" type="text/css" href="style/anims.css">
        <link rel="stylesheet" type="text/css" href="style/style.css">
        <script src="scripts/jquery.min.js"></script>
        <script src="scripts/jsanims.js"></script>
        <script src="scripts/actions.js"></script>
        <script src="scripts/search.js"></script>
        <script src="scripts/lateral_scripts.js"></script>
        <script src="scripts/main_scripts.js"></script>

        <script>
        $(document).ready(function(){
            loading_run();
        });

        </script>
    </head>
    <body>
        <div id="page">
            <div id="lateral_menu">
                <div id="options_panel">
                    <a href="./manager.php">Manager</a>
                    <button>Hidden</button>
                    <button>+</button>
                </div>
            </div>
            <div id="content">
                <div id="actions_panel">
                    more options here
                </div>
                <div id="posts_panel">
                    content
                </div>
            </div>
        </div>
        <div id="loading_panel">
            <div class="loading" id="smallBall"></div>
            <div class="loading" id="bigBall"></div>
        </div>
    </body>
</html>
