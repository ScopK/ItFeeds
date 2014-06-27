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
        <script src="scripts/posts_control.js"></script>

        <script>
        $(document).ready(function(){
            loading_run();
        });

        </script>
    </head>
    <body>
        <div id="page">
            <div id="lateral_menu">
                <div id="navopts_top" class="options_panel">
                    <button onclick="hideLateralMenu()">&#10094; </button>
                    <a href="./manager.php">Manager</a>
                    <button id="favsTButton" onclick="toggleFavs(this)">Favs</button>
                    <button id="unreadTButton" onclick="toggleUnread(this)">Unread</button>
                    <button id="sortTButton" onclick="toggleSort(this)"></button>
                </div>
                <div id="navigation_panel" style="overflow-y: auto;"></div>
                <div id="navopts_bottom" class="options_panel">
                    <button id="prevPage" onclick="prevPage()" disabled>Prev</button>
                    <span id="pages">
                        <span id="pageNumber">1</span><span id="totalPages">/1(10)</span>
                    </span>
                    <button id="nextPage" onclick="nextPage()">Next</button>
                </div>
            </div>
            <div id="content">
                <div id="actions_panel">
                    <div id="control_panel">
                    <button disabled>Fav</button>
                    <button disabled>Unread</button>
                    <button disabled onclick="$('#add_tag').fadeIn(100);return false;">Tags</button>
                    </div>
                    <div id="tagList"></div>
                </div>
                <div id="posts_panel">
                    content
                </div>
                <div id="blankspace" style="height: 100px; width: 100px;"></div>
            </div>
        </div>
        <div id="loading_panel">
            <div class="loading" id="smallBall"></div>
            <div class="loading" id="bigBall"></div>
        </div>

        <div id="add_tag"><div id="add_tag_content">
            <form action="" method="POST">
                <table><tr><th colspan="2">Add tag</th></tr>
                <tr><td><input id="newtagField" type="text" name="newtagname" autocomplete="off" /></td></tr>
                <tr><td colspan="2" class="dialog_buttons">
                    <button class="addTag" onclick="addTag(); return false;">Add</button>
                    <button class="cancelAddTag" onclick="$('#add_tag').fadeOut(100);return false;">Cancel</button>
                </td></tr>
                </table>
            </form>
        </div></div>
    </body>
</html>
