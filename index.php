<?php
    session_start();

    if (!isset($_SESSION['log_user'])){
        header('Location: ./login.php');
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Fydeph - <?=$_SESSION['log_user']?></title>
        <link rel="shortcut icon" href="imgs/icon.png" />
        <link rel="stylesheet" type="text/css" href="style/anims.css">
        <link rel="stylesheet" type="text/css" href="style/style.css">
        <script src="scripts/jquery.min.js"></script>
        <script src="scripts/jsanims.js"></script>
        <script src="scripts/actions.js"></script>
        <script src="scripts/lateral_scripts.js"></script>
        <script>
        $(document).ready(function(){
            loading_run();
        });
        </script>
    </head>
    <body>
        <div class="loading" id="smallBall"></div>
        <div class="loading" id="bigBall"></div>
        <div id="page">
            <div id="lateral_menu">
                <div id="options_panel">
                    <button>Manager</button>
                    <button>Hidden</button>
                    <button>+</button>
                </div>
            </div>
            <div id="content">
                <div id="actions_panel">
                    more options here
                </div>
                    content
            </div>
        </div>
    </body>
</html>
