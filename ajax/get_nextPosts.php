<?php
	header("Content-Type: text/html;charset=utf-8");
	$nextId = $_REQUEST['nextid'];
	if (!isset($nextId) || $nextId==""){
		header("HTTP/1.1 501 No next entered");
		die("HTTP/1.1 501 No next entered");
	}

	$postsPage = $_REQUEST['postspage'];

	$favorites = $_REQUEST['fav'];
	$unread = $_REQUEST['unread'];

	$feedId = $_REQUEST['feed'];
	$folderId = $_REQUEST['folder'];
	$tagId = $_REQUEST['tag'];

	$sort = $_REQUEST['sortBy'];

// CHECK INPUTS
	if (isset($feedId))	$mode=0;
	elseif (isset($folderId)) $mode=1;
	elseif (isset($tagId)) $mode=2;
	else $mode=3;

	if (!isset($sort)) $sort="DESC";
	else $sort=($sort==1)?"DESC":"ASC";

	if (!isset($postsPage) || !is_numeric($postsPage) || $postsPage<0) $postsPage=10;
	else $postsPage=($postsPage>100)?100:$postsPage;

	if (!isset($favorites)) $favorites = 0;
	if (!isset($unread)) $unread = 1;
	if ($favorites==1) $unread = 0;

	$isServer=true;
	include "../func/initind.php";
	include "../func/functions.php";
	include "../func/classes.php";

	//usleep(400000);
	$user = $_SESSION['log_user'];
	$hidd = (isset($_SESSION['hid_user']))?checkUserHiddenPassword($con, $user,$_SESSION['hid_user']):false;
	switch($mode){
		case 0: // feeds
			$posts = getPostsNextFeed($con, $user, $hidd, $feedId, $favorites, $unread, $sort, $postsPage, $nextId);
			echo json_encode($posts);
			break;
		case 1: // folder
			$posts = getPostsNextFolder($con, $user, $hidd, $folderId, $favorites, $unread, $sort, $postsPage, $nextId);
			echo json_encode($posts);
			break;
		case 2: // tags
			$posts = getPostsNextTag($con, $user, $hidd, $tagId, $favorites, $unread, $sort, $postsPage, $nextId);
			echo json_encode($posts);
			break;
		case 3: // all
			$posts = getPostsNextAll($con, $user, $hidd, $favorites, $unread, $sort, $postsPage, $nextId);
			echo json_encode($posts);
			break;
		default:
			die("No reachable point");
			break;

	}
	mysqli_close($con);

?>