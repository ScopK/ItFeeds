<?php
	header("Content-Type: text/html;charset=utf-8");
	$postsPage = $_REQUEST['postspage'];
	$page = $_REQUEST['page'];

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

	if (!isset($page) || !is_numeric($page) || $page<1) $page=1;
	if (!isset($favorites)) $favorites = 0;
	if (!isset($unread)) $unread = 1;
	if ($favorites==1) $unread = 0;

	$isServer=true;
	include "../func/initind.php";
	include "../func/functions.php";
	include "../func/classes.php";

	//usleep(400000);
	switch($mode){
		case 0: // feeds
			$posts = getPostsFeed($con, $_SESSION['log_user'], $feedId, $favorites, $unread, $sort, ($page-1)*$postsPage, $postsPage);
			echo json_encode($posts);
			break;
		case 1: // folder
			$posts = getPostsFolder($con, $_SESSION['log_user'], $folderId, $favorites, $unread, $sort, ($page-1)*$postsPage, $postsPage);
			echo json_encode($posts);
			break;
		case 2: // tags
			$posts = getPostsTag($con, $_SESSION['log_user'], $tagId, $favorites, $unread, $sort, ($page-1)*$postsPage, $postsPage);
			echo json_encode($posts);
			break;
		case 3: // all
			$posts = getPostsAll($con, $_SESSION['log_user'], $_SESSION['hid_user'], $favorites, $unread, $sort, ($page-1)*$postsPage, $postsPage);
			echo json_encode($posts);
			break;
		default:
			die("No reachable point");
			break;

	}
	mysqli_close($con);
?>