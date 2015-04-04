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
        <link rel="stylesheet" type="text/css" href="style/postalone.css?color=<?= isset($_SESSION['log_color'])?$_SESSION['log_color']:'3386C6' ?>">

        <script src="scripts/lib/jquery.min.js"></script>
        <script type="text/javascript">
            var description = $("<span>"+<?= json_encode($post->description) ?>+"</span>");
        </script>
        <script src="scripts/standalone/single_post.js"></script>


    </head>
    <body>


    <div id="page" class="noprofile">
        <div id="profile"></div>
        <div id="post">
            <div id="title-bar">
                <div id="feedname"><a target="_blank" href="<?= $feed->link ?>"><?= $feed->name ?></a></div>
                <div id="postinfo"><span><a target="_blank" href="<?= $post->link ?>"><?= $post->title ?></a><?= $post->date ?></span></div>
            </div>
            <div id="description"></div>
            <a id="see_more" target="_blank" href="<?= $post->link ?>">View source...</a>
        </div>
    </div>

    </body>
</html><?php mysqli_close($con);  ?>