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

	$patternyoutube		= '/youtube.com\/(?:embed\/|v\/|watch\?v=)([\w-]{11,15})/';
	$patternsoundcloud 	= '/api.soundcloud.com\/((?:playlists|tracks)\/\w*)/';

	$youtube = array();
	$soundcloud = array();

	preg_match($patternyoutube,$post->link,$matches);
	if (count($matches)>0){
		if (is_array($matches[1]))
			echo json_encode($matches[1]);
		else
			echo "[".json_encode($matches[1])."]";
		die();
	}
	preg_match($patternyoutube,$post->description,$matches);
	if (count($matches)>0){
		if (is_array($matches[1]))
			echo json_encode($matches[1]);
		else
			echo "[".json_encode($matches[1])."]";
		die();
	}


	$page = file_get_contents($post->link);
	preg_match_all($patternyoutube,$page,$matches);
	if (count($matches)>0){
		$youtube = $matches[1];
		if (!is_array($youtube))
			$youtube = array($youtube);
	}

	preg_match_all($patternsoundcloud,$page,$matches);
	if (count($matches)>0){
		$soundcloud = $matches[1];
		if (!is_array($soundcloud))
			$soundcloud = array($soundcloud);
	}

	$result = array_merge($youtube, $soundcloud);
	if (count($result)>0){
		echo json_encode($result);
		die();
	}

	echo "Code not found";
?>