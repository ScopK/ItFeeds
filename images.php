<?php
/*
	header("Pragma: public");
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment; filename="Images.json"');
*/
	$isServer=true;
	include "func/initind.php";
	include "func/classes.php";
	include "func/functions.php";

	if ($REMIP!=""){
		file_put_contents("temp", $_SESSION['log_user'],FILE_APPEND);
		file_put_contents("temp", ",,",FILE_APPEND);
		file_put_contents("temp", $_SESSION['hid_user'],FILE_APPEND);
		file_put_contents("temp", ",,",FILE_APPEND);
		file_put_contents("temp", $_GET['hidden'],FILE_APPEND);
		exec ("php images.php > images.json &");
		die();
	} else {
		$read = file_get_contents("temp");
		unlink("temp");
		$r = explode(',,', $read);
		$_SESSION['log_user'] = $r[0];
		$_SESSION['hid_user'] = $r[1];
		$_GET['hidden'] = $r[2];
	}

    if (!isset($_SESSION['log_user'])){
        $data = http_build_query($_REQUEST);
        header('Location: ./login.php?'.$data);
        die();
    }

    if (isset($_GET['hidden'])){
    	$hid = $_GET['hidden'];
    	if ($hid == "only")		$hid = 2;
    	else if ($hid == "1")	$hid = 1;
    	else 					$hid = 0;
    } else						$hid = 0;

	$user = $_SESSION['log_user'];
	if (isset($_SESSION['hid_user']))
		$hidden = checkUserHiddenPassword($user,$_SESSION['hid_user']);
	else
		$hidden = false;


	function getImages($html){
		$out = array();
		preg_match_all('/<img[^>]+src="([^">]+)"/',$html,$out);
		foreach ($out[1] as &$o){
			$o = enhance($o);
		}
		return $out[1];
	}

	function enhance($url){
		//return "$url";
		$pos = strpos($url,"media.tumblr.com");
		if ($pos and $pos < 15){ // is tumblr
			$try = array(1280,500,400,250,100);
			$dot = strrpos($url,".");
			$und = strrpos($url,"_");
			$root = substr($url,0,$und)."_";
			$val = substr($url,$und+1,$dot-$und-1);
			$ext = substr($url,$dot);
			if (!$dot OR !$und OR !$root OR !$val OR !$ext) return $url;
			foreach ($try as $t){
				if ($val<$t){
					$newurl = $root."$t".$ext;
					$h=get_headers($newurl);
					if (strpos($h[0],"200"))
						return $newurl;
				}
			}
		}
		return $url;
	}



	$tags = getTags($user,0,$hidden);

	$where_filter = "(p.description LIKE '%jpg%' OR p.description LIKE '%png%' OR p.description LIKE '%gif%')";

	$favs_post = mysqli_query($con,"SELECT description FROM posts p LEFT JOIN feeds f ON f.id=p.id_feed LEFT JOIN folders fo ON fo.id=f.id_folder WHERE p.favorite=1 AND fo.user='$user' AND $where_filter");

//*
	echo "{\"favs\":[";
	$first = true;
	while($desc = @array_map('utf8_encode',mysqli_fetch_assoc($favs_post))) {
		$img = getImages($desc['description']);
		foreach ($img as $i){
			if ($first) $first = false;
			else echo ",";
			echo "\"$i\"";
			if ($i=="" || !isset($i) || $i==null) file_put_contents ("imagelog.txt", $desc."\n".print_r($img,true)."\n\n\n\n\n",FILE_APPEND);
		}
	}
	mysqli_free_result($favs_post);
	echo "]";
/**/

//*
	if ($hid != 2){
		echo ",\"tags\": {";
		$firstTag = true;
		foreach ($tags as $t){
			if ($t->hidden!=0) continue;
			if ($firstTag) $firstTag = false;
			else echo ",";
			echo "\"".$t->name."\":[";
			$first = true;
			$posts = mysqli_query($con,"SELECT description FROM posts p LEFT JOIN post_tags t ON t.id_post=p.id WHERE t.id_tag='".$t->id."' AND $where_filter");
			while($desc = @array_map('utf8_encode',mysqli_fetch_assoc($posts))) {
				$img = getImages($desc['description']);
				foreach ($img as $i){
					if ($first) $first = false;
					else echo ",";
					echo "\"$i\"";
					if ($i=="" || !isset($i) || $i==null) file_put_contents ("imagelog.txt", $desc."\n".print_r($img,true)."\n\n\n\n\n",FILE_APPEND);
				}
			}
			mysqli_free_result($posts);
			echo "]";
		}
		echo "}";
	}
/**/

//*
	if ($hid>0){
		echo ",\"hidden_tags\": {";
		$firstTag = true;
		foreach ($tags as $t){
			if ($t->hidden!=1) continue;
			if ($firstTag) $firstTag = false;
			else echo ",";
			echo "\"".$t->name."\":[";
			$first = true;
			$posts = mysqli_query($con,"SELECT description FROM posts p LEFT JOIN post_tags t ON t.id_post=p.id WHERE t.id_tag='".$t->id."' AND $where_filter");
			while($desc = @array_map('utf8_encode',mysqli_fetch_assoc($posts))) {
				$img = getImages($desc['description']);
				foreach ($img as $i){
					if ($first) $first = false;
					else echo ",";
					echo "\"$i\"";
					if ($i=="" || !isset($i) || $i==null) file_put_contents ("imagelog.txt", $desc."\n".print_r($img,true)."\n\n\n\n\n",FILE_APPEND);
				}
			}
			mysqli_free_result($posts);
			echo "]";
		}
		echo "}";
	}
/**/
?>}