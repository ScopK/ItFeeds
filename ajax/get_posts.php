<?php
	header("Content-Type: text/html;charset=utf-8");
	
	$postsPage = isset($_REQUEST['postspage'])?$_REQUEST['postspage']:null;
	$page = isset($_REQUEST['page'])?$_REQUEST['page']:null;
	
	$favorites = isset($_REQUEST['fav'])?$_REQUEST['fav']:null;
	$unread = isset($_REQUEST['unread'])?$_REQUEST['unread']:null;
	
	$feedId = isset($_REQUEST['feed'])?$_REQUEST['feed']:null;
	$folderId = isset($_REQUEST['folder'])?$_REQUEST['folder']:null;
	$tagId = isset($_REQUEST['tag'])?$_REQUEST['tag']:null;
	$search = isset($_REQUEST['search'])?$_REQUEST['search']:"";
	
	$sort = isset($_REQUEST['sortBy'])?$_REQUEST['sortBy']:null;

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
	$user = $_SESSION['log_user'];
	$hidden = (isset($_SESSION['hid_user']))?checkUserHiddenPassword($con, $user,$_SESSION['hid_user']):false;
	switch($mode){
		case 0: // feeds
			$posts = getPostsFeed($con, $user, $feedId, $favorites, $unread, $sort, ($page-1)*$postsPage, $postsPage, $search);
			echo json_encode($posts);
			break;
		case 1: // folder
			$posts = getPostsFolder($con, $user, $folderId, $favorites, $unread, $sort, ($page-1)*$postsPage, $postsPage, $search);
			echo json_encode($posts);
			break;
		case 2: // tags
			$posts = getPostsTag($con, $user, $tagId, $favorites, $unread, $sort, ($page-1)*$postsPage, $postsPage, $search);
			echo json_encode($posts);
			break;
		case 3: // all
			$posts = getPostsAll($con, $user, $favorites, $unread, $sort, ($page-1)*$postsPage, $postsPage, $search);
			echo json_encode($posts);
			break;
		default:
			die("No reachable point");
			break;
	}
	mysqli_close($con);
?>