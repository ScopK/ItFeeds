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
        <title>Fydeph Post - <?= $post->title ?></title>
        <link rel="shortcut icon" href="imgs/icon.png" />

        <link rel="stylesheet" type="text/css" href="style/poststandalone.css">

    </head>
    <body>

    <div id="top-bars">
        <div id="feed-bar"><a class="space-used" target="_blank" href="<?=$feed->link?>"><?=$feed->name?></a></div>
        <div class="title-bg selected">
            <a class="space-used post-title" target="_blank" href="<?=$post->link?>"><?=$post->title?></a>
            <p class="space-used post-date"><?=$post->date?></p>
        </div>
    </div>

    <div class="space-used"><div class="description"><?=$post->description?></div></div>



    <!--<div id="tags">
        <?php 
        foreach($post->tags as $tag){
            echo "<p>".$tag['name']."</p>";
        }
        ?>
    </div>-->

    </body>
</html><?php mysqli_close($con);  ?>