<?php
    session_start();

    if (!isset($_SESSION['log_user'])){
        header('Location: ./login.php');
    } else {
        $log_user = $_SESSION['log_user'];
        $hidClass = (isset($_SESSION['hid_user']))?"set":"";
    }

?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title>Fydeph Manager</title>
        <link rel="shortcut icon" href="imgs/icon.png" />

        <link rel="stylesheet" type="text/css" href="style/manager.css">
        <link rel="stylesheet" type="text/css" href="style/anims.css">
        
        <script src="scripts/jquery.min.js"></script>
        <script src="scripts/manager/actions.js"></script>
        <script src="scripts/manager/webview.js"></script>
        <script>
        $(document).ready(function(){
            loadFolders("<?=$log_user?>");
        });
        </script>
    </head>
    <body>
        <div id="loading_panel">
            <div class="loading" id="smallBall"></div>
            <div class="loading" id="bigBall"></div>
        </div>

        <div id="content_folders">
            <div id="extrabuttons">
                <a href="./">Main page</a>
                <button onclick="gotoTags()">Tags</button>
                <button class="showHiddenButton <?=$hidClass?>" onclick="showHiddenDialog();">Set hidden</button>
                <button class="logoutButton" onclick="return false">Logout</button>
            </div>
            <div id="header">
                <h2 id="titleuser" onclick=''>+ Folders</h2>
            </div>

            <div id="folder_list"></div>

            <div class="inside_dialog" id="tools_folder"><div class="dialog_container">
                <h3>TOOLS FOLDER</h3>
                <div class="message">
                    <span><button id="folderConfig" class="optionsIcon"></button><p>Folder settings</p></span>
                    <span><button id="feedAdd" class="addIcon"></button><p>Add feed</p></span>
                    <span><button id="folderDelete" class="deleteIcon"></button><p>Delete folder</p></span>
                    <span><button class="cancel backIcon"></button><p>Cancel</p></span>
                </div>
            </div></div>

            <div class="inside_dialog" id="tools_feed"><div class="dialog_container">
                <h3>TOOLS FEED</h3>
                <div class="message">
                    <span><button id="feedConfig" class="optionsIcon"></button><p>Feed settings</p></span>
                    <span><button id="feedDelete" class="deleteIcon"></button><p>Delete feed</p></span>
                    <span><button class="cancel backIcon"></button><p>Cancel</p></span>
                </div>
            </div></div>

            <div class="inside_dialog" id="confdel_folder"><div class="dialog_container">
                <h3>DELETE FOLDER</h3>
                <div class="message">
                    <form action="" method="POST">
                        <input type="hidden" name="folderId"/>
                        <div class="dialog_buttons">
                            <p>Are you sure you want to delete this folder?<p><br/>
                            <button class="deleteFolder" onclick="return false;">Yes</button>
                            <button class="cancel" onclick="return false;">No</button>
                        </div>
                    </form>
                </div>
            </div></div>

            <div class="inside_dialog" id="confadd_feed"><div class="dialog_container">
                <h3>NEW FEED TO A FOLDER</h3>
                <div class="message">
                    <p>Fill the feed information please?<p>
                    <form action="" method="POST">
                        <input type="hidden" name="folderId"/>
                        <table>
                        <tr><td>Feed name</td><td><input type="text" name="fname" autocomplete="off"/></td></tr>
                        <tr><td>RssLink</td><td><input type="text" name="rlink" autocomplete="off"/></td></tr>
                        <tr><td>Link</td><td><input type="text" name="link" autocomplete="off"/></td></tr>
                        </table>
                    <div class="dialog_buttons">
                        <button class="addFeed" onclick="return false;">Yes</button>
                        <button class="cancel" onclick="return false;">No</button>
                    </div>
                    </form>
                </div>
            </div></div>

            <div class="inside_dialog" id="clean_folder"><div class="dialog_container">
                <h3>FOLDER SETTINGS</h3>
                <div class="message">   
                    <form action="" method="POST">
                        <input type="hidden" name="folderId"/>
                        <table style="width:auto">
                        <tr><td style="text-align:left;padding-right:10px">Folder name</td><td><input type="text" name="fname" style="width:150px" autocomplete="off"/></td></tr>
                        <tr><td style="text-align:left;padding-right:10px">Hidden</td><td align="left"><input type="checkbox" name="hid"/></td></tr>
                        </table>
                        <div class="dialog_buttons">
                            <button class="editFolder" onclick="return false;">Edit configuration</button>
                        </div>
                    </form><br/><br/>
                    <form action="" method="POST">
                        <div class="dialog_buttons">
                            <input type="hidden" name="folderId"/>
                            Delete posts older than <input type="text" name="days" style="width: 30px; text-align:center" autocomplete="off"/> days<br/>
                            <input id="foldhidcheck" type="checkbox" name="unread"/><label for="foldhidcheck">Delete unread</label><br/><br/>
                        
                            <button class="cleanFolder" onclick="return false;">Clean</button>
                            <button class="cancel" onclick="return false;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div></div>

            <div class="inside_dialog" id="confdel_feed"><div class="dialog_container">
                <h3>DELETE FEED</h3>
                <div class="message">
                    <form action="" method="POST">
                        <input type="hidden" name="feedId"/>
                        <div class="dialog_buttons">
                            <p>Are you sure you want to delete this feed?<p><br/>
                            <button class="deleteFeed" onclick="return false;">Yes</button>
                            <button class="cancel" onclick="return false;">No</button>
                        </div>
                    </form>
                </div>
            </div></div>
            <div class="inside_dialog" id="clean_feed"><div class="dialog_container">
                <h3>FEED SETTINGS</h3>
                <div class="message">
                    <form action="" method="POST">
                        <input type="hidden" name="feedId"/>
                        <table>
                        <tr><td>Feed name</td><td><input type="text" name="fname" style="width:97%" autocomplete="off"/></td></tr>
                        <tr><td>RssLink</td><td><input type="text" name="rlink" style="width:97%" autocomplete="off"/></td><td id="goRss"><a target="_blank">&#65515;</a></td></tr>
                        <tr><td>Link</td><td><input type="text" name="link" style="width:97%" autocomplete="off"/></td><td id="goLink"><a target="_blank">&#65515;</a></td></tr>
                        <tr><td>Update time</td><td colspan="2"><input type="text" name="uptime" style="width:40px" autocomplete="off"/></td></tr>
                        <tr><td>Enabled</td><td colspan="2"><input type="checkbox" name="ena"/></td></tr>
                        </table>
                        <div class="dialog_buttons">
                            <button class="editFeed" onclick="return false;">Edit configuration</button>
                        </div>
                    </form><br/><br/>
                    <form action="" method="POST">
                        <div class="dialog_buttons">
                            <input type="hidden" name="feedId"/>
                            Delete posts older than <input type="text" name="days" style="width: 30px; text-align:center" autocomplete="off"/> days<br/>
                            <input id="feedhidcheck" type="checkbox" name="unread"/><label for="feedhidcheck">Delete unread</label><br/><br/>

                            <button class="cleanFeed" onclick="return false;">Clean</button>
                            <button class="cancel" onclick="return false;">Cancel</button>
                        </div>
                    </form>

                </div>
            </div></div>

            <div class="inside_dialog" id="addFolder"><div class="dialog_container">
                <h3>CREATE FOLDER</h3>
                <div class="message">
                    <form action="" method="POST">
                        <input type="hidden" name="feedId"/>
                        <div class="dialog_buttons">
                            <p>Are you sure you want to create a folder?<p><br/>
                            <button class="createFolder" onclick="return false;">CreateFolder</button>
                            <button class="cancel" onclick="return false;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div></div>
        </div>
        <div id="content_tags" class="disabled">
            <div id="extrabuttons">
                <span><a href="./">Main page</a></span>
                <button onclick="gotoFolders()">Folders</button>
                <button class="showHiddenButton <?=$hidClass?>" onclick="showHiddenDialog()">Set hidden</button>
                <button class="logoutButton" onclick="return false;">Logout</button>
            </div>
            <div id="header">
                <h2 id="titletags" onclick=''>+ Tags</h2>
            </div>

            <div id="tag_list"></div>

            <div class="inside_dialog" id="addTag"><div class="dialog_container">
                <h3>CREATE TAG</h3>
                <div class="message">
                    <form action="" method="POST">
                        <input type="hidden" name="feedId"/>
                        <div class="dialog_buttons">
                            <p>Are you sure you want to create a tag?<p><br/>
                            <button class="createTag" onclick="return false;">CreateTag</button>
                            <button class="cancel" onclick="return false;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div></div>
        </div>


        <div id="login_hidden"><div id="login_hidden_content">
            <form action="" method="POST">
                <table><tr><th colspan="2">Hidden Password</th></tr>
                <tr><td><input id="pwdHiddenField" type="password" name="hiddenPass" autocomplete="off" /></td></tr>
                <tr><td colspan="2" class="dialog_buttons">
                    <button class="unlockHidden" onclick="return false;">Unlock</button>
                    <button class="cancel" onclick="$('#login_hidden').fadeOut(100);return false;">Cancel</button>
                </td></tr>
                </table>

            </form>
        </div></div>
    </body>
</html>

<?php
    mysqli_close($con);
?>