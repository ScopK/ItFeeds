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
	$html5 = array();

	function exists($main,$sear){
		foreach ($main as $e) {
			$found = true;
			foreach ($sear as $k=>$v) {
				$mv = $e[$k];
				if ($mv!=$v){
					$found=false;
					break;
				}
			}
			if ($found) return true;
		}
		return false;
	}

	function analyze($pattern,$code,$nick,&$array){
		preg_match_all($pattern,$code,$matches);
		if (count($matches)>0){
			$matchs = $matches[1];
			if (!is_array($matchs))
				$matchs=array($matchs);
			foreach ($matchs as $m) {
				$element = array("type"=>$nick,"src"=>$m);
				if (!exists($array,$element)){
					array_push($array,array("type"=>$nick,"src"=>$m));
				}
			}
		}
	}

	/* SEARCH IN DESCRIPTION */
	analyze($patternyoutube,$post->description,"yt",$youtube);
	analyze($patternsoundcloud,$post->description,"sc",$soundcloud);

	//html5
	$dom = @DOMDocument::loadHTML('<?xml encoding="UTF-8">'.$post->description);
	$videos = $dom->getElementsByTagName("video");
	foreach ($videos as $v) {
		$sources = $v->getElementsByTagName("source");
		foreach ($sources as $s) {
			array_push($html5,array("type"=>"h5","src"=>$s->getAttribute("src")));
		}
	}

	$result = array_merge($youtube, $soundcloud,$html5);
	if (count($result)>0){
		echo json_encode($result);
		die();
	}


	/* SEARCH IN HTML */
	$page = file_get_contents($post->link);

	analyze($patternyoutube,$page,"yt",$youtube);
	analyze($patternsoundcloud,$page,"sc",$soundcloud);

	//html5
	$dom = @DOMDocument::loadHTML('<?xml encoding="UTF-8">'.$page);
	$videos = $dom->getElementsByTagName("video");
	foreach ($videos as $v) {
		$sources = $v->getElementsByTagName("source");
		foreach ($sources as $s) {
			array_push($html5,array("type"=>"h5","src"=>$s->getAttribute("src")));
		}
	}

	$result = array_merge($youtube, $soundcloud,$html5);
	if (count($result)>0){
		echo json_encode($result);
		die();
	}

	echo "Code not found";
?>