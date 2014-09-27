<?php
    session_start();

    if (!isset($_SESSION['log_user'])){
        $data = http_build_query($_REQUEST);
        header('Location: ./login.php?manager=1&'.$data);
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
        
        <script src="scripts/lib/jquery.min.js"></script>
        <script src="scripts/lib/messages.js"></script>
        <script src="scripts/manager/actions.js"></script>
        <script src="scripts/manager/webview.js"></script>
        <script>
        $(document).ready(function(){
            $(".mainpageLink").attr("href","./index.php"+location.search);
            loadFolders();
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
                <a class="mainpageLink" href="./">Main page</a>
                <button onclick="gotoTags()">Tags</button>
                <button class="showHiddenButton <?=$hidClass?>" onclick="showHiddenDialog();">Set hidden</button>
                <button class="logoutButton" onclick="return false">Logout</button>
                <button onclick="showCleanAll();">Clean All</button>
            </div>
            <div id="header">
                <h2 id="titleuser" onclick=''>+ Folders</h2>
            </div>

            <div id="folder_list"></div>

            <div class="inside_dialog" id="clean_all"><div class="dialog_container">
                <h3>Clean All Posts</h3>
                <div class="message">   
                    <form action="" method="POST">
                        <div class="dialog_buttons">
                            Delete posts older than <input type="text" name="days" style="width: 30px; text-align:center" autocomplete="off"/> days<br/>
                            <input id="allhidcheck" type="checkbox" name="unread" style="cursor:pointer"/><label style="cursor:pointer" for="allhidcheck">Delete unread</label><br/><br/>

                            <button class="cleanAll" onclick="return false;">Clean</button>
                            <button class="cancel" onclick="return false;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div></div>

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
                            <p>Enter password to remove this folder:<p>
                            <input type="password" name="pass" autocomplete="off" style="display:block;width:70%;margin:10px auto;"/>
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
                        <tr><td style="text-align:left;padding-right:10px"><label style="cursor:pointer" for="hidfolcheckbox">Hidden</label></td><td align="left"><input id="hidfolcheckbox" type="checkbox" name="hid" style="cursor:pointer"/></td></tr>
                        </table>
                        <div class="dialog_buttons">
                            <button class="editFolder" onclick="return false;">Edit configuration</button>
                        </div>
                    </form><br/><br/>
                    <form action="" method="POST">
                        <div class="dialog_buttons">
                            <input type="hidden" name="folderId"/>
                            Delete posts older than <input type="text" name="days" style="width: 30px; text-align:center" autocomplete="off"/> days<br/>
                            <input id="foldhidcheck" type="checkbox" name="unread" style="cursor:pointer"/><label style="cursor:pointer" for="foldhidcheck">Delete unread</label><br/><br/>
                        
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
                        <tr><td>RssLink</td><td><input type="text" name="rlink" style="width:97%" autocomplete="off"/></td><td id="goRss"><a target="_blank">&#8611;</a></td></tr>
                        <tr><td>Link</td><td><input type="text" name="link" style="width:97%" autocomplete="off"/></td><td id="goLink"><a target="_blank">&#8611;</a></td></tr>
                        <tr><td>Update time</td><td colspan="2"><input type="text" name="uptime" style="width:40px" autocomplete="off"/></td></tr>
                        <tr><td><label style="cursor:pointer" for="enabfeedcheck">Enabled</label></td><td colspan="2"><input id="enabfeedcheck" type="checkbox" name="ena" style="cursor:pointer"/></td></tr>
                        </table>
                        <div class="dialog_buttons">
                            <button class="editFeed" onclick="return false;">Edit configuration</button>
                        </div>
                    </form><br/><br/>
                    <form action="" method="POST">
                        <div class="dialog_buttons">
                            <input type="hidden" name="feedId"/>
                            Delete posts older than <input type="text" name="days" style="width: 30px; text-align:center" autocomplete="off"/> days<br/>
                            <input id="feedhidcheck" type="checkbox" name="unread" style="cursor:pointer"/><label style="cursor:pointer" for="feedhidcheck">Delete unread</label><br/><br/>

                            <button class="cleanFeed" onclick="return false;">Clean</button>
                            <button class="cancel" onclick="return false;">Cancel</button>
                        </div>
                    </form>

                </div>
            </div></div>

            <div class="inside_dialog" id="add_folder"><div class="dialog_container">
                <h3>CREATE FOLDER</h3>
                <div class="message">
                    <form action="" method="POST">
                        <div class="dialog_buttons">
                            <p>Folder name:<p>
                            <input type="text" name="foldername" autocomplete="off" style="display:block;width:70%;margin:10px auto;"/>
                            <button class="addFolder" onclick="return false;">Create folder</button>
                            <button class="cancel" onclick="return false;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div></div>
        </div>
        <div id="content_tags" class="disabled">
            <div id="extrabuttons">
                <a class="mainpageLink" href="./">Main page</a>
                <button onclick="gotoFolders()">Folders</button>
                <button class="showHiddenButton <?=$hidClass?>" onclick="showHiddenDialog()">Set hidden</button>
                <button class="logoutButton" onclick="return false;">Logout</button>
            </div>
            <div id="header">
                <h2 id="titletags" onclick=''>+ Tags</h2>
            </div>

            <div id="tag_list"></div>


            <div class="inside_dialog" id="tools_tag"><div class="dialog_container">
                <h3>TOOLS TAG</h3>
                <div class="message">
                    <span><button id="tagConfig" class="optionsIcon"></button><p>Tag settings</p></span>
                    <span><button id="tagDelete" class="deleteIcon"></button><p>Delete tag</p></span>
                    <span><button class="cancel backIcon"></button><p>Cancel</p></span>
                </div>
            </div></div>

            <div class="inside_dialog" id="editTag"><div class="dialog_container">
                <h3>EDIT TAG</h3>
                <div class="message">
                    <form action="" method="POST">
                        <input type="hidden" name="tagId"/>
                        <table style="width: 300px">
                        <tr><td align="right"><label>Tag name:</label></td><td style="padding-left:10px"><input type="text" name="tagname" autocomplete="off"/></td></tr>
                        <tr><td align="right"><label style="cursor:pointer" for="taghidcheck">Hidden:</label></td><td align="left" style="padding-left:10px"><input id="taghidcheck" type="checkbox" name="hidden" style="cursor:pointer"/></td></tr>
                        <tr><td align="right"><label style="cursor:pointer" for="tagpubcheck">Public:</label></td><td align="left" style="padding-left:10px"><input id="tagpubcheck" type="checkbox" name="public" style="cursor:pointer"/></td></tr>
                        </table>
                        <div class="dialog_buttons">
                            <button class="editTag" onclick="return false;">Edit</button>
                            <button class="cancel" onclick="return false;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div></div>

            <div class="inside_dialog" id="confdel_tag"><div class="dialog_container">
                <h3>DELETE TAG</h3>
                <div class="message">
                    <form action="" method="POST">
                        <input type="hidden" name="tagId"/>
                        <div class="dialog_buttons">
                            <p>Are you sure you want to delete this tag?<p><br/>
                            <button class="deleteTag" onclick="return false;">Yes</button>
                            <button class="cancel" onclick="return false;">No</button>
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
        <div id="top_message"><p></p></div>
    </body>
</html>