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
	<title>Fydeph Tag - <?= $tag->name ?></title>
    <link rel="shortcut icon" href="imgs/icon.png" />

    <link rel="stylesheet" type="text/css" href="style/poststandalone.css">
    <script src="scripts/lib/jquery.min.js"></script>
    <script>var idtag="<?=$id?>";</script>
    <script src="scripts/standalone/actions.js"></script>
</head>
<body>
    <div id="tag-content" style="display:none"></div>
</body>
</html>