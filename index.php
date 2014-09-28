<?php
    session_start();
    if (!isset($_SESSION['log_user'])){
        $data = http_build_query($_REQUEST);
        header('Location: ./login.php?'.$data);
    } else {
        $log_user = $_SESSION['log_user'];
        $hidClass = (isset($_SESSION['hid_user']))?"set":"";
    }
?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title>Fydeph - <?=$log_user?></title>
        <link rel="shortcut icon" href="imgs/icon.png" />
        <link rel="stylesheet" type="text/css" href="style/anims.css">
        <link rel="stylesheet" type="text/css" href="style/style.css">
        <script src="scripts/lib/jquery.min.js"></script>
        <script src="scripts/lib/jsanims.js"></script>
        <script src="scripts/lib/contextmenu.js"></script>
        <script src="scripts/lib/messages.js"></script>
        <script src="scripts/updateandload.js"></script>
        <script src="scripts/actions.js"></script>
        <script src="scripts/search.js"></script>
        <script src="scripts/mode_control.js"></script>
        <script src="scripts/posts_control.js"></script>
        <script src="scripts/init.js"></script>
        <script>
        $(document).ready(function(){
            $(".background-modal").hide();
            $("#managerLink").attr("href","./manager.php"+location.search);
            loading_run();
            randomColors();
        });
        </script>
    </head>
    <body>
        <div id="page">
            <div id="lateral_menu">
                <div id="settings_panel" class="hidden">
                    <button class="button-panel" style="position:absolute;top:0;right:0;width:35px;margin:10px" onclick="$('#settings_panel').addClass('hidden')">X</button>
                    <div id="user-box" style="border:dotted #666;border-width:0 0 1px;padding-bottom:10px;margin-bottom:20px">
                        <p style="margin-left:6px"><b>Logged as <?=$log_user?></b></p>
                        <button class="button-panel" onclick="alert('WIP')">Change password</button>
                        <button class="button-panel" onclick="logout()">Logout</button>
                    </div>
                    <a class="button-panel" id="managerLink" style="display:block;margin:2px 0" href="./manager.php">Advanced Manager</a>
                    <div id="footer" style="position:absolute;bottom:10px;right:10px">
                        <p>Sc-pyK</p>
                    </div>
                </div>

                <div id="navopts_top" class="options_panel">
                    <!--<button id="settingsButton" class="button-panel gear-icon" style="width:35px" onclick="$('#settings_panel').toggleClass('hidden')">&nbsp;</button>-->
                    <button class="buttonuser-panel" id="settingsButton" onclick="$('#settings_panel').toggleClass('hidden')"><?=$log_user?></button>
                    <button class="button-panel" id="favsTButton" onclick="toggleFavs(this)">Favs</button>
                    <button class="button-panel" id="unreadTButton" onclick="toggleUnread(this)">Unread</button>
                    <button class="button-panel" id="sortTButton" onclick="toggleSort(this)"></button>
                </div>
                <div id="navigation_panel" style="overflow-y: auto;">
                    <div id="folders"></div>
                    <div id="feeds"></div>
                    <div id="tags"></div>
                </div>
                <div id="navopts_bottom" class="options_panel">
                    <span id="pages">
                        <span id="totalPages">10</span> (<span id="percentSeen">0</span>)
                    </span>
                    <button class="button-panel" id="loadMore" onclick="loadMore()">Load More</button>
					<button class="button-panel" id="searchButton" onclick="showSearchDialog(); return false;">Search</button>
                </div>
            </div>
            <div id="content">
                <div id="posts_panel"></div>
                <div id="load_more_panel" style="margin-top:60px"><p id="loadMoreLabel" onclick="loadMore()">LOAD MORE</p></div>
            </div>
        </div>

        <button id="show-lateral-button" class="mouse-button" style="font-size:20px" onclick="toggleLateralMenu()">&lsaquo;</button>
        <button id="mouse_nav" oncontextmenu="return false;"></button>
        <div id="mouse_bottom" style="display:none">
            <button class="markunread mouse-button colored" onclick="toogleUnreadPost(true)">U</button>
            <button class="markfav mouse-button" onclick="toogleFavPost(true)">F</button>
            <button class="mouse-button" onclick="showAddTagsDialog();">T</button>
        </div>

        <div id="loading_panel" style="pointer-events:none;">
            <div class="loading" id="smallBall"></div>
            <div class="loading" id="bigBall"></div>
        </div>
        <div id="add_tag" class="background-modal"><div style="display:table-cell;vertical-align:middle;"><div id="add_tag_content">
            <form action="" method="POST">
                <table><tr><th colspan="2">Add tag</th></tr>
                <tr><td class="taglist" colspan="2"></td></tr>
                <tr><td><input id="newtagField" type="text" name="newtagname" autocomplete="off" /></td></tr>
                <tr><td colspan="2" class="dialog_buttons">
                    <button class="addTag" onclick="addTag(); return false;">Add</button>
                    <button class="cancelAddTag" onclick="$('#add_tag').fadeOut(100);return false;">Cancel</button>
                </td></tr>
                </table>
            </form>
        </div></div></div>
		
        <div id="search_dialog" class="background-modal"><div style="display:table-cell;vertical-align:middle;"><div id="search_content">
            <form action="" method="POST">
                <table><tr><th colspan="2">Search</th></tr>
                <tr><td><input id="searchField" type="text" name="newtagname" autocomplete="off" /></td></tr>
                <tr><td colspan="2" class="dialog_buttons">
                    <button class="searchButton" onclick="searchAction(); return false;">Add</button>
                    <button class="cancelAddTag" onclick="$('#search_dialog').fadeOut(100);return false;">Cancel</button>
                </td></tr>
                </table>
            </form>
        </div></div></div>
		
    </body>
</html>