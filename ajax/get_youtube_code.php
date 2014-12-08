<?php
	if (!isset($_REQUEST['postid']))
		die("No post ID entered");

	$isServer=true;
	include "../func/initind.php";
	include "../func/classes.php";
	include "../func/functions.php";

	$id = $_REQUEST['postid'];
	$post = getPost($id,false);
	$code = "";

	$pattern = '/youtube.com\/(?:embed\/|v\/|watch\?v=)(\w{11,15})/';

	preg_match($pattern,$post->link,$matches);
	if (count($matches)>0){
		if (is_array($matches[1]))
			echo json_encode($matches[1]);
		else
			echo "[".json_encode($matches[1])."]";
		die();
	}
	preg_match($pattern,$post->description,$matches);
	if (count($matches)>0){
		if (is_array($matches[1]))
			echo json_encode($matches[1]);
		else
			echo "[".json_encode($matches[1])."]";
		die();
	}

	$page = file_get_contents($post->link);
	preg_match_all($pattern,$page,$matches);
	if (count($matches)>0){
		if (is_array($matches[1]))
			echo json_encode($matches[1]);
		else
			echo "[".json_encode($matches[1])."]";
		die();
	}

	echo "Code not found";
?>