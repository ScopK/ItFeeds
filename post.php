<?php
    $id = $_REQUEST['id'];
    if (!isset($id) || $id==""){
        header('HTTP/1.1 501 Not ID entered');
        die('HTTP/1.1 501 Not ID entered');
    }

    $isServer = true;
    require_once "func/initind.php";
    require_once "func/functions.php";
    require_once "func/classes.php";

    $post = getPost($id,false);

    $feed = getFeed($post->feedId);
?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title>Fydeph Manager</title>
        <link rel="shortcut icon" href="imgs/icon.png" />

        <link rel="stylesheet" type="text/css" href="style/poststandalone.css">
        
        <script src="scripts/lib/jquery.min.js"></script>
        <script src="scripts/manager/actions.js"></script>
        <script src="scripts/manager/webview.js"></script>

    </head>
    <body>
        <div id="content">
        <div id="feedBar"><a target="_blank" href="<?=$feed->link?>"><?=$feed->name?></a></div>
        <div id="titleBar">
            <a target="_blank" href="<?=$post->link?>"><?=$post->title?></a>
            <p id="date">Post date: <?=$post->date?></p>
            <div id="tags">
                <?php 
                foreach($post->tags as $tag){
                    echo "<p>".$tag['name']."</p>";
                }
                ?>
            </div>
        </div>
        <div id="description"><?=$post->description?></div>

        </div>
    </body>
</html><?php mysqli_close($con);  ?>