<?php
    $isServer=true;
    require_once("func/initind.php");

    if (!isset($_SESSION['log_user'])){
        $data = http_build_query($_REQUEST);
        header('Location: ./login.php?'.$data);
    } else {
        require_once("func/functions.php");

        $log_user = $_SESSION['log_user'];
        //$hid_user = isset($_SESSION['hid_user']);
        $hid_user = isset($_SESSION['hid_user'])?checkUserHiddenPassword($log_user,$_SESSION['hid_user']):false;
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
            //loading_run();
            randomColors();
        });
        </script>
    </head>
    <body>
        <div id="page">
            <div id="lateral_menu">
                <div id="settings_panel" class="hidden">
                    <button class="button-panel" style="position:absolute;top:0;right:0;width:35px;margin:10px" onclick="$('#settings_panel').addClass('hidden')">X</button>

                    <p style="margin-left:6px"><b>Logged as <?=$log_user?></b></p>
                    <a class="button-panel" id="managerLink" href="./manager.php">Advanced Manager</a>
                    <button class="button-panel <?php echo ($hid_user)?"highlight-color":"";?> lock-icon" id="unlockButton" style="width:14.5%" onclick="showUnlockDialog();return false">&nbsp;</button>
                    <button class="button-panel" style="float:right" onclick="logout()">Logout</button>
                    <hr/>

                    <table style="width:100%;margin:10px 5px 5px;border-collapse:collapse"><tr>
                        <td>Posts mode</td>
                        <td><select id="posts_mode" style="width:90%" class="select-panel" onchange="change_postsmode(this.value,false)">
                            <option value="0" title="Previous posts are minimized">Normal Mode</option>
                            <option value="1" title="All unselected posts are minimized">Minimized Mode</option>
                            <option value="2" title="Nothing is minimized">Never minimize</option>
                        </select></td>
                        <td style="color:#666;margin-left:5px">Key 'G'</td>
                    </tr><tr>
                        <td>Auto mark read</td>
                        <td><select id="autoread_mode" style="width:90%" class="select-panel" onchange="change_autoreadmode(this.value,false)">
                            <option value="0" title="Mark read when selecting a post">On select post</option>
                            <option value="1" title="Mark as read while scrolling">On scroll</option>
                            <option value="2" title="Don't mark as read automatically">Never</option>
                        </select></td>
                        <td style="color:#666;margin-left:5px"><!--Key ''--></td>
                    </tr></table>
                    
                    <button class="button-panel" style="text-align:left;width:100%" onclick="showPasswordChangeDialog();return false">Change password</button>
                    <button class="button-panel" style="text-align:left;width:100%" onclick="showLockPasswordChangeDialog();return false">Change Lock Password</button>
                    <div id="footer" style="position:absolute;bottom:10px;right:10px">
                        <p>Sc-pyK</p>
                    </div>
                </div>

                <div id="navopts_top" class="options_panel">
                    <button class="button-panel highlight-color" id="settingsButton" onclick="$('#settings_panel').toggleClass('hidden')"><?=$log_user?></button>
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
            <button class="markunread mouse-button colored" onclick="toogleUnreadPost(true)"></button>
            <button class="markfav mouse-button" onclick="toogleFavPost(true)"></button>
            <button class="addtag mouse-button" onclick="showAddTagsDialog();"></button>
        </div>

        <div id="loading_panel" style="pointer-events:none;">
            <div class="loading" id="loadingBar1"></div>
            <div class="loading" id="loadingBar2"></div>
            <div class="loading" id="loadingBar3"></div>
        </div>

        <!-- DIALOGS -->
        <div id="add_tag" class="background-modal"><div style="display:table-cell;vertical-align:middle;"><div id="add_tag_content" class="dialog-dim">
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
		
        <div id="search_dialog" class="background-modal"><div style="display:table-cell;vertical-align:middle;"><div id="search_content" class="dialog-dim">
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

        <div id="pwchange_dialog" class="background-modal"><div style="display:table-cell;vertical-align:middle;"><div id="pwd_change_content" class="dialog-dim">
            <form action="" method="POST">
                <table class="slim"><tr><th colspan="2">Change account password</th>
                </tr><tr>
                    <td align="right" style="padding-right:10px">Old password</td>
                    <td align="left"><input id="oldPassField" type="password" name="old" autocomplete="off" /></td>
                </tr><tr>
                    <td align="right" style="padding-right:10px">New password</td>
                    <td align="left"><input id="newPassField" type="password" name="new" autocomplete="off" /></td>
                </tr><tr>
                    <td align="right" style="padding-right:10px">Repeat</td>
                    <td align="left"><input id="newPass2Field" type="password" autocomplete="off" /></td>
                </tr><tr>
                <td colspan="2" class="dialog_buttons">
                    <button class="searchButton" onclick="changePasswordAction(); return false;">Confirm</button>
                    <button class="cancelAddTag" onclick="$('#pwchange_dialog').fadeOut(100);return false;">Cancel</button>
                </td></tr>
                </table>
            </form>
        </div></div></div>

        <div id="pwlchange_dialog" class="background-modal"><div style="display:table-cell;vertical-align:middle;"><div id="pwdlock_change_content" class="dialog-dim">
            <form action="" method="POST">
                <table class="slim"><tr><th colspan="2">Change lock password</th>
                </tr><tr>
                    <td align="right" style="padding-right:10px">Old password</td>
                    <td align="left"><input id="oldLPassField" type="password" name="old" autocomplete="off" /></td>
                </tr><tr>
                    <td align="right" style="padding-right:10px">New password</td>
                    <td align="left"><input id="newLPassField" type="password" name="new" autocomplete="off" /></td>
                </tr><tr>
                    <td align="right" style="padding-right:10px">Repeat</td>
                    <td align="left"><input id="newLPass2Field" type="password" autocomplete="off" /></td>
                </tr><tr>
                <td colspan="2" class="dialog_buttons">
                    <button class="searchButton" onclick="changeLockPasswordAction(); return false;">Confirm</button>
                    <button class="cancelAddTag" onclick="$('#pwlchange_dialog').fadeOut(100);return false;">Cancel</button>
                </td></tr>
                </table>
            </form>
        </div></div></div>

        <div id="unlock_dialog" class="background-modal"><div style="display:table-cell;vertical-align:middle;"><div id="unlock_content" class="dialog-dim">
            <form action="" method="POST">
                <table class="slim"><tr><th colspan="2">Unlock content</th>
                </tr><tr>
                    <td align="right" style="padding-right:10px">Enter lock password</td>
                    <td align="left"><input id="lockPassField" type="password" name="hiddenPass" autocomplete="off" /></td>
                </tr><tr>
                <tr><td colspan="2" class="dialog_buttons">
                    <button class="searchButton" onclick="unlockAction(); return false;">Confirm</button>
                    <button class="cancelAddTag" onclick="$('#unlock_dialog').fadeOut(100);return false;">Cancel</button>
                </td></tr>
                </table>
            </form>
        </div></div></div>
		
    </body>
</html>