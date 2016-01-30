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

    $tag = getTag($id);
?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title>itFeeds Tag - <?= $tag->name ?></title>
    <link rel="shortcut icon" href="imgs/icon.png" />

    <link rel="stylesheet" type="text/css" href="style/poststandalone.css">
    <script src="scripts/lib/jquery.min.js"></script>
    <script>var idtag="<?=$id?>";
            if(navigator.userAgent.match(/(Android|webOS|iPhone|iPad|iPod|BlackBerry|Windows Phone)/i)){
                var postspage = 3;
            } else{
                var postspage = 10;
            }
    </script>
    <script src="scripts/standalone/actions.js"></script>
</head>
<body style="overflow:auto">
    <div id="navigation-bar" oncontextmenu="return false;"></div>
    <div id="tag-content" style="display:none"></div>
    <div style="position:absolute;width:100%; height:100%; background-color:#222;box-shadow: inset 0 10px 10px #000"></div>

    <div id="error-message" style="border-radius:20px;display:none;position:fixed;top:0;bottom:0;left:0;right:0;margin:auto;width:800px;height:300px;background-color: #ddd;border:2px solid #000">
        <p style="text-align:center;font-size:70px;margin-top:90px;">An error ocurred</p>
    </div>
</body>
</html>