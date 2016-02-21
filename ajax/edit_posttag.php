<?php
	$isServer=true;
	include "../func/initind.php";
	include "../func/functions.php";
	include "../func/classes.php";

	$postid = $_REQUEST['postid'];
	$add = $_REQUEST['addtagname'];
	$rem = $_REQUEST['remtagname'];
	if (!isset($add) || !isset($rem) || "$rem$add"==""){
		header('HTTP/1.1 500 No tag entered');
		die('HTTP/1.1 500 No tag entered');
	}
	$add = explode(" ",$add);
	$rem = explode(" ",$rem);
	$user = $_SESSION['log_user'];

	$tagResults = array("added"=>array(),"removed"=>array(),"emptyTags"=>array());
	foreach($add as $addTag){
		if ($addTag=="") continue;
		// Check if tag exists
		$tagRow = DB_safequery("SELECT id,hidden FROM tags WHERE user=? AND tag_name=?",array($user, utf8_decode($addTag)));
		if ($tagRow['affected']==0){
			// Create tag
			$idtag = getNewID();
			$addResult = DB_safequery("INSERT INTO tags VALUES(?,?,?,0,0)",array($idtag, $user, utf8_decode($addTag)));
			if ($addResult['affected']!=1){
				continue;
			}
		} else {
			// Check if post already has this tag
			$idtag = $tagRow['rows'][0]['id'];
			$link = DB_safequery("SELECT count(*) AS c FROM post_tags WHERE id_post=? AND id_tag=?",array($postid, $idtag));
			if ($link['rows'][0]['c']!=0){
				continue;
			}
		}
		// If existing tag is hidden, checking for permision
		if ($tagRow['affected']>0 && $tagRow['rows'][0]['hidden']==1){
			if (!checkUserHiddenPassword($user,$_SESSION['hid_user'])){
				continue;
			}
		}

		$res = DB_safequery("INSERT INTO post_tags VALUES(?,?)",array($postid,$idtag));
		if ($res['affected']){
			$tagResults['added'][] = getTag($idtag);
		}
	}

	foreach($rem as $remTag){
		if ($remTag=="") continue;

		// Check if tag exists
		$tagRow = DB_safequery("SELECT id,hidden FROM tags WHERE user=? AND tag_name=?",array($user, utf8_decode($remTag)));
		if ($tagRow['affected']==0){
			continue;
		}
		// If existing tag is hidden, checking for permision
		if ($tagRow['rows'][0]['hidden']==1){
			if (!checkUserHiddenPassword($user,$_SESSION['hid_user'])){
				continue;
			}
		}

		// Delete link
		$idtag = $tagRow['rows'][0]['id'];
		$res = DB_safequery("DELETE FROM post_tags WHERE id_post=? AND id_tag=?",array($postid,$idtag));
		if ($res['affected']){
			$tagObject = getTag($idtag);
			$tagResults['removed'][] = $tagObject;
		} else continue;

		// Check if tag has 0 posts, and delete it then
		$count = DB_safequery("SELECT count(*) AS c FROM post_tags WHERE id_tag=?",array($idtag));
		if ($count['rows'][0]['c']<1){
			$res = DB_safequery("DELETE FROM tags WHERE id=?",array($idtag));
			if ($res['affected']){
				$tagResults['emptyTags'][] = $tagObject;
			}
		}
	}

	echo json_encode($tagResults);
	mysqli_close($con);
?>