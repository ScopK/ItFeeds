<?php
	$isServer=true;
	include "../func/initind.php";

	$postid = $_REQUEST['postid'];
	$tag = $_REQUEST['tagname'];
	$user = $_SESSION['log_user'];

	$sql = "SELECT id,hidden FROM tags WHERE user='$user' AND tag_name='$tag'";
	if (){
		$sql = "INSERT INTO tags VALUES(UUID(),'$user','$tag','0')";
	}

	$sql = "INSERT INTO post_tags VALUES('$postid','$idtag')";

	echo "$postid\n$tag";
?>