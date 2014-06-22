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
        <link rel="stylesheet" type="text/css" href="style/anims.css">
        <link rel="stylesheet" type="text/css" href="style/style.css">
        <script src="scripts/jsanims.js"></script>
        <script src="scripts/jquery.min.js"></script>
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
                lateral here iam
            </div>
            <div id="content">
                <div id="actions_panel">
                    options here
                </div>
                    content
            </div>
        </div>
    </body>
</html>
