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
    if (!$tag->public){
        header("HTTP/1.1 403 Forbidden");
        die("HTTP/1.1 403 Forbidden");
    }

?><!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>

</body>
</html>