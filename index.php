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
        <title>itFeeds - <?=$log_user?></title>
        <link rel="shortcut icon" href="imgs/icon.png" />
        <link rel="stylesheet" type="text/css" href="style/anims.css">
    <?php if (isset($_GET['old'])) { ?>
        <link rel="stylesheet" type="text/css" href="style/style_old.css">
    <?php } else { ?>
        <link rel="stylesheet" type="text/css" href="style/style.css?color=<?= isset($_SESSION['log_color'])?$_SESSION['log_color']:'3386C6' ?>">
    <?php } ?>
        <script src="scripts/lib/jquery.min.js"></script>
        <script src="scripts/lib/jsanims.js"></script>
        <script src="scripts/lib/contextmenu.js"></script>
        <script src="scripts/lib/messages.js"></script>
        <script src="scripts/context_actions.js"></script>
        <script src="scripts/init_structure.js"></script>
        <script src="scripts/code_utils.js"></script>
        <script src="scripts/calls.js"></script>
        <script src="scripts/dialogs.js"></script>
        <script src="scripts/lateral.js"></script>
        <script src="scripts/player.js"></script>
        <script src="https://w.soundcloud.com/player/api.js"></script>

        <script>
        $(document).ready(function(){
            $(".background-modal").hide();
        });
        </script>
    </head>
    <body>
    <div id="main">
        <div id="page">
            <div id="lateral_menu">
                <div id="settings_panel" class="hidden">
                    <button class="button-panel button_close_settings" style="position:absolute;top:0;right:0;width:35px;margin:10px">X</button>

                    <p style="margin-left:6px"><b>Logged as <?=$log_user?></b></p>
                    <button class="button-panel <?= ($hid_user)?"highlight-color":"";?> lock-icon" id="unlockButton" style="padding-right:30px"><?= ($hid_user)?"Unlocked":"Unlock";?></button>
                    <button class="button-panel" style="float:right" id="logoutButton">Logout</button>
                    <hr/>

                    <table style="width:100%;margin:10px 5px 5px;border-collapse:collapse"><tr>
                        <td>Posts mode</td>
                        <td><select id="posts_mode" style="width:90%" class="select-panel" onchange="window.cookie.actions.postsmode(this.value,false)">
                            <option value="0" title="Previous posts are minimized">Normal Mode</option>
                            <option value="1" title="All unselected posts are minimized">Minimized Mode</option>
                            <option value="2" title="Nothing is minimized">Never minimize</option>
                        </select></td>
                        <td style="color:#888;margin-left:5px">Key 'G'</td>
                    </tr><tr>
                        <td>Auto mark read</td>
                        <td><select id="autoread_mode" style="width:90%" class="select-panel" onchange="window.cookie.actions.autoreadmode(this.value,false)">
                            <option value="0" title="Mark read when selecting a post">On select post</option>
                            <option value="1" title="Mark as read while scrolling">On scroll</option>
                            <option value="2" title="Don't mark as read automatically">Never</option>
                        </select></td>
                        <td style="color:#888;margin-left:5px">Key 'H'</td>
                    </tr></table>
                    
                    <button class="button-panel" style="text-align:left;width:100%" id="changePassBtn">Change password</button>
                    <button class="button-panel" style="text-align:left;width:100%" id="changeLPassBtn">Change Lock Password</button>
                    <div id="footer" style="position:absolute;bottom:10px;right:10px">
                        <p>ScopK</p>
                    </div>
                </div>

                <div id="quick_search" class="hidden">
                    <input type="text" placeholder="Search" id="quick_search_input"/>
                    <button class='button_close_quickSearch'>X</button>
                    <hr/>
                    <span id="quick_results"></span>
                </div>

                <div id="navopts_top" class="options_panel">
                    <button class="button-panel highlight-color" id="settingsButton"><?=$log_user?></button>
                    <button class="button-panel" id="favsTButton">Favs</button>
                    <button class="button-panel" id="unreadTButton">Unread</button>
                    <button class="button-panel" id="sortTButton"></button>
                </div>
                <div id="navigation_panel" style="overflow-y: auto;">
                    <div id="folders"></div>
                    <div id="feeds"></div>
                    <div id="tags"></div>
                </div>
                <div id="navopts_bottom" class="options_panel">
                    <button class="button-panel" title="Load More" id="loadMore">
                    <span id="pages">
                        Posts: <span id="totalPages">10</span> | Page: <span id="percentSeen">0</span>
                    </span></button>
                    <button class="button-panel" id="searchButton">Search</button>
                </div>
            </div>
            <!-- #################################################################################################### -->
            <div id="content">
                <div id="searchContent" style='display:none'></div>
                <div id="posts_panel"></div>
                <div id="load_more_panel" style="margin-top:60px"><p id="loadMoreLabel">LOAD MORE</p></div>
            </div>
        </div>

        <button id="show-lateral-button" class="mouse-button" onclick="lateral.toggle.all();">&lsaquo;</button>
        <button id="more-options-button" class="mouse-button" onclick="return cmMore(event,this);" oncontextmenu="return cmMore(event,this);">+</button>
        <button id="show-video-button" class="mouse-button" style="display:none;padding:0" onclick="player.toggleMin(false)">Video Viewer</button>
        <button id="mouse_nav" oncontextmenu="return false;"></button>
        <div id="mouse_bottom" style="display:none">
            <button class="markunread mouse-button colored" onclick="markUnread(null,null,true)"></button>
            <button class="markfav mouse-button" onclick="markFavorite(null,null,true)"></button>
            <button class="addtag mouse-button" onclick="dialog.addTags.show();"></button>
        </div>
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
                    <button class="confirm">Add</button>
                    <button class="cancel">Cancel</button>
                </td></tr>
                </table>
            </form>
        </div></div></div>
        
        <div id="search_dialog" class="background-modal"><div style="display:table-cell;vertical-align:middle;"><div id="search_content" class="dialog-dim">
            <form action="" method="POST">
                <table><tr><th colspan="2">Search</th></tr>
                <tr><td><input id="searchField" type="text" name="newtagname" autocomplete="off" /></td></tr>
                <tr><td colspan="2" class="dialog_buttons">
                    <button class="confirm">Add</button>
                    <button class="cancel">Cancel</button>
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
                    <button class="confirm">Confirm</button>
                    <button class="cancel">Cancel</button>
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
                    <button class="confirm">Confirm</button>
                    <button class="cancel">Cancel</button>
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
                    <button class="confirm">Confirm</button>
                    <button class="cancel">Cancel</button>
                </td></tr>
                </table>
            </form>
        </div></div></div>

        <div id="video_viewer_dialog" class="background-modal">
        <div class="left-bar">
            <div id="nextprevcontroller" oncontextmenu="return false;"></div><div id="videolist"></div>
        </div>
        <div id="videospace" style="display:table-cell;vertical-align:middle;"><div id="video_viewer" class="dialog-dim" style="width:700px;position:relative">
            <div id='video_controls'>
                <div style='left:0' onclick="player.prev()">&#10096;</div>
                <div style='right:0' onclick="player.next(true)">&#10097;</div>
                <div style='right:40px' onclick="player.nextElement()"><span id="counter_videos" style='font-size:9px;vertical-align:middle'></span>&#10093;</div>
            </div>
            <table class="slim"><tr class='row-header'><th style="position:relative;" colspan="3"><span class="title">Video viewer</span>
            <span id='video_window_controls'>
                <button id='video_unread_button' onclick="player.playing.setUnread(!player.playing.unread)">&nbsp;</button>
                <button onclick="player.toggleMin(true)">·</button>
                <button onclick="player.toggleMax()">-</button>
                <button class='close-button' onclick="player.close()">×</button>
            </span></th>
            </tr><tr><td id="video_td" style="margin:0;padding:0;background-color:black;" colspan="3"></td>
            </tr></table>
        </div></div></div>

        <div id="settings_dialog" class="background-modal"><div style="display:table-cell;vertical-align:middle;"><div class="dialog-dim" style="width:600px">
            <span class="folder-tab tab">
                <form action="" method="GET">
                    <input id="edit_idx_folder" type="hidden" name="idx" />
                    <table class="slim"><tr><th colspan="2" class="title"></th>
                    </tr><tr>
                        <td align="right" style="width:45%;padding-right:10px">Folder name</td>
                        <td align="left"><input id="edit_name_folder" type="text" name="fname" autocomplete="off" /></td>
                    </tr><tr>
                        <td align="right" style="width:45%;padding-right:10px"><label for="edit_hidden_folder">Hidden</label></td>
                        <td align="left"><input id="edit_hidden_folder" type="checkbox" name="hid" /></td>
                    </tr><tr>
                    <td colspan="2" class="dialog_buttons">
                        <button class="confirmButton" onclick="editFolder(); return false;">Confirm</button>
                        <button class="cancel">Cancel</button>
                    </td></tr>
                    </table>
                </form>
            </span>
            <span class="tag-tab tab">
                <form action="" method="GET">
                    <input id="edit_idx_tag" type="hidden" name="idx" />
                    <table class="slim"><tr><th colspan="2" class="title"></th>
                    </tr><tr>
                        <td align="right" style="width:40%;padding-right:10px">Tag name</td>
                        <td align="left"><input id="edit_name_tag" type="text" name="fname" autocomplete="off" /></td>
                    </tr><tr>
                        <td align="right" style="width:40%;padding-right:10px"><label for="edit_hidden_tag">Hidden</label></td>
                        <td align="left"><input id="edit_hidden_tag" type="checkbox" name="hid" /></td>
                    </tr><tr>
                        <td align="right" style="width:40%;padding-right:10px"><label for="edit_public_tag">Public</label></td>
                        <td align="left"><input id="edit_public_tag" type="checkbox" name="pub" /></td>
                    </tr><tr>
                    <td colspan="2" class="dialog_buttons">
                        <button class="confirmButton" onclick="editTag(); return false;">Confirm</button>
                        <button class="cancel">Cancel</button>
                    </td></tr>
                    </table>
                </form>
            </span>
            <span class="feed-tab tab">
                <form action="" method="GET">
                    <input id="edit_idx_folderfeed" type="hidden" name="fidx" />
                    <input id="edit_idx_feed" type="hidden" name="idx" />
                    <table class="slim"><tr><th colspan="3" class="title"></th>
                    </tr><tr>
                        <td align="right" style="width:30%;padding-right:10px">Feed name</td>
                        <td align="left" colspan="2"><input id="edit_name_feed" type="text" name="fname" autocomplete="off" /></td>
                    </tr><tr>
                        <td align="right" style="width:30%;padding-right:10px">Feed URL</td>
                        <td align="left"><input style="width:350px" id="edit_rss_feed" type="text" name="rss" autocomplete="off" /></td>
                        <td align="left" style="width:100px">&#8611;</td>
                    </tr><tr>
                        <td align="right" style="width:30%;padding-right:10px">Link</td>
                        <td align="left"><input style="width:350px" id="edit_link_feed" type="text" name="link" autocomplete="off" /></td>
                        <td align="left" style="width:100px">&#8611;</td>
                    </tr><tr>
                        <td align="right" style="width:30%;padding-right:10px">Update time</td>
                        <td align="left" colspan="2"><input id="edit_upd_feed" type="text" name="upd" autocomplete="off" style="width:40px"/></td>
                    </tr><tr>
                        <td align="right" style="width:30%;padding-right:10px">Max. unread</td>
                        <td align="left" colspan="2"><input id="edit_max_feed" type="text" name="max" autocomplete="off" style="width:40px"/></td>
                    </tr><tr>
                        <td align="right" style="width:30%;padding-right:10px">Filter</td>
                        <td align="left" colspan="2"><input id="edit_filter_feed" type="text" name="filter" autocomplete="off" /></td>
                    </tr><tr>
                        <td align="right" style="width:30%;padding-right:10px"><label for="edit_enabled_feed">Enabled</label></td>
                        <td align="left" colspan="2"><input id="edit_enabled_feed" type="checkbox" name="en" /></td>
                    </tr><tr>
                    <td colspan="3" class="dialog_buttons">
                        <button class="confirmButton" onclick="editFeed(); return false;">Confirm</button>
                        <button class="cancel">Cancel</button>
                    </td></tr>
                    </table>
                </form>
            </span>
        </div></div></div>

        <div id="cleaning_dialog" class="background-modal"><div style="display:table-cell;vertical-align:middle;"><div class="dialog-dim">
            <span class="folder-tab tab">
                <form action="" method="GET">
                    <input id="clean_idx_folder" type="hidden" name="idx" />
                    <table class="slim"><tr><th colspan="2" class="title"></th>
                    </tr><tr>
                        <td align="center" colspan="2" style="width:50%;padding-right:10px">Delete posts older than
                        <input id="clean_folder_days" style="width:30px; text-align:center" type="text" name="fname" autocomplete="off" /> days</td>
                    </tr><tr>
                        <td align="right"><input id="clean_folder_unread" type="checkbox" name="hid" /></td>
                        <td align="left" style="width:60%;padding-left:5px"><label for="clean_folder_unread">Include unread</label></td>
                    </tr><tr>
                    <td colspan="2" class="dialog_buttons">
                        <button class="confirmButton" onclick="cleanFolder(); return false;">Confirm</button>
                        <button class="cancel">Cancel</button>
                    </td></tr>
                    </table>
                </form>
            </span>
            <span class="feed-tab tab">
                <form action="" method="GET">
                    <input id="clean_idx_folderfeed" type="hidden" name="idx" />
                    <input id="clean_idx_feed" type="hidden" name="idx" />
                    <table class="slim"><tr><th colspan="2" class="title"></th>
                    </tr><tr>
                        <td align="center" colspan="2" style="width:50%;padding-right:10px">Delete posts older than
                        <input id="clean_feed_days" style="width:30px; text-align:center" type="text" name="fname" autocomplete="off" /> days</td>
                    </tr><tr>
                        <td align="right"><input id="clean_feed_unread" type="checkbox" name="hid" /></td>
                        <td align="left" style="width:60%;padding-left:5px"><label for="clean_feed_unread">Include unread</label></td>
                    </tr><tr>
                    <td colspan="2" class="dialog_buttons">
                        <button class="confirmButton" onclick="cleanFeed(); return false;">Confirm</button>
                        <button class="cancel">Cancel</button>
                    </td></tr>
                    </table>
                </form>
            </span>
            <span class="all-tab tab">
                <form action="" method="GET">
                    <table class="slim"><tr><th colspan="2" class="title">Clean posts</th>
                    </tr><tr>
                        <td align="center" colspan="2" style="width:50%;padding-right:10px">Delete posts older than
                        <input id="clean_all_days" style="width:30px; text-align:center" type="text" name="fname" autocomplete="off" /> days</td>
                    </tr><tr>
                        <td align="right"><input id="clean_all_unread" type="checkbox" name="hid" /></td>
                        <td align="left" style="width:60%;padding-left:5px"><label for="clean_all_unread">Include unread</label></td>
                    </tr><tr>
                    <td colspan="2" class="dialog_buttons">
                        <button class="confirmButton" onclick="cleanAll(); return false;">Confirm</button>
                        <button class="cancel">Cancel</button>
                    </td></tr>
                    </table>
                </form>
            </span>
        </div></div></div>

        <div id="delete_dialog" class="background-modal"><div style="display:table-cell;vertical-align:middle;"><div class="dialog-dim">
            <span class="folder-tab tab">
                <input id="delete_idx_folder" type="hidden" name="idx" />
                <table class="slim delete"><tr><th class="title"></th>
                </tr><tr>
                    <td align="center">You are about to delete folder '<span class="foldername"></span>'.<br/>Enter your password to confirm:</td>
                </tr><tr>
                    <td align="center" ><input id="delete_folder_pass" type="password" name="fname" autocomplete="off" /></td>
                </tr><tr>
                <td class="dialog_buttons">
                    <button class="confirmButton" onclick="deleteFolder(); return false;">Confirm</button>
                    <button class="cancel">Cancel</button>
                </td></tr>
                </table>
            </span>
            <span class="feed-tab tab">
                <input id="delete_idx_folderfeed" type="hidden" name="idx" />
                <input id="delete_idx_feed" type="hidden" name="idx" />
                <table class="slim delete"><tr><th class="title"></th>
                </tr><tr>
                    <td align="center">You are about to delete feed '<span class="feedname"></span>'.</td>
                </tr><tr>
                <td class="dialog_buttons">
                    <button class="confirmButton" onclick="deleteFeed(); return false;">Confirm</button>
                    <button class="cancel">Cancel</button>
                </td></tr>
                </table>
            </span>
            <span class="tag-tab tab">
                <input id="delete_idx_tag" type="hidden" name="idx" />
                <table class="slim delete"><tr><th class="title"></th>
                </tr><tr>
                    <td align="center">You are about to delete tag '<span class="tagname"></span>'.</td>
                </tr><tr>
                <td class="dialog_buttons">
                    <button class="confirmButton" onclick="deleteTagMan(); return false;">Confirm</button>
                    <button class="cancel">Cancel</button>
                </td></tr>
                </table>
            </span>
        </div></div></div>

        <div id="create_dialog" class="background-modal"><div style="display:table-cell;vertical-align:middle;"><div class="dialog-dim" style="width:500px">
            <span class="folder-tab tab">
                <form action="" method="GET">
                    <table class="slim"><tr><th colspan="2" class="title">Create new Folder</th>
                    </tr><tr>
                        <td align="right" style="width:40%;padding-right:10px">Folder name</td>
                        <td align="left"><input id="create_folder_name" type="text" name="link" autocomplete="off" /></td>
                    </tr><tr>
                    <td class="dialog_buttons" colspan="2">
                        <button class="confirmButton" onclick="createFolder(); return false;">Confirm</button>
                        <button class="cancel">Cancel</button>
                    </td></tr>
                    </table>
                </form>
            </span>
            <span class="feed-tab tab">
                <form action="" method="GET">
                    <input id="create_idx_folder" type="hidden" name="idx" />
                    <table class="slim"><tr><th colspan="2" class="title" ></th>
                    </tr><tr>
                        <td align="right" style="width:22%;padding-right:10px">Feed name</td>
                        <td align="left"><input id="create_feed_name" type="text" name="fname" autocomplete="off" /></td>
                    </tr><tr>
                        <td align="right" style="width:22%;padding-right:10px">Feed URL</td>
                        <td align="left"><input style="width:350px" id="create_feed_rss" type="text" name="rss" autocomplete="off" /></td>
                    </tr><tr>
                        <td align="right" style="width:22%;padding-right:10px">Link</td>
                        <td align="left"><input style="width:350px" id="create_feed_link" type="text" name="link" autocomplete="off" /></td>
                    </tr><tr>
                    <td colspan="2" class="dialog_buttons">
                        <button class="confirmButton" onclick="createFeed(); return false;">Confirm</button>
                        <button class="cancel">Cancel</button>
                    </td></tr>
                    </table>
                </form>
            </span>
        </div></div></div>

        <div id="move_dialog" class="background-modal"><div style="display:table-cell;vertical-align:middle;"><div class="dialog-dim" style="width:400px">
            <span class="feed-tab tab">
                <form action="" method="GET">
                    <input id="move_idx_folderfeed" type="hidden" name="idx" />
                    <input id="move_idx_feed" type="hidden" name="idx" />
                    <table class="slim"><tr><th colspan="2" class="title" ></th>
                    </tr><tr>
                        <td align="right" style="width:47%;padding-right:10px">Select folder</td>
                        <td align="left"><select name="move_feed_to" id="move_feed_to"></select></td>
                    </tr><tr>
                    <td colspan="2" class="dialog_buttons">
                        <button class="confirmButton" onclick="moveFeed(); return false;">Confirm</button>
                        <button class="cancel">Cancel</button>
                    </td></tr>
                    </table>
                </form>
            </span>
        </div></div></div>
        
    </body>
</html>