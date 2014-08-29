<?php
	header("Content-Type: text/html;charset=utf-8");
	$nextId = isset($_REQUEST['nextid'])?$_REQUEST['nextid']:null;
	if (!isset($nextId) || $nextId==""){
		header("HTTP/1.1 501 No next entered");
		die("HTTP/1.1 501 No next entered");
	}

	$postsPage = isset($_REQUEST['postspage'])?$_REQUEST['postspage']:null;
	
	$favorites = isset($_REQUEST['fav'])?$_REQUEST['fav']:null;
	$unread = isset($_REQUEST['unread'])?$_REQUEST['unread']:null;
	
	$feedId = isset($_REQUEST['feed'])?$_REQUEST['feed']:null;
	$folderId = isset($_REQUEST['folder'])?$_REQUEST['folder']:null;
	$tagId = isset($_REQUEST['tag'])?$_REQUEST['tag']:null;
	$search = isset($_REQUEST['search'])?$_REQUEST['search']:"";
	
	$sort = isset($_REQUEST['sortBy'])?$_REQUEST['sortBy']:null;

	$public = isset($_REQUEST['public']);
	
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
	$hidden = (isset($_SESSION['hid_user']))?checkUserHiddenPassword($user,$_SESSION['hid_user']):false;
	switch($mode){
		case 0: // feeds
			if (!checkFeedAccess($user,$feedId)){
				header("HTTP/1.1 403 Forbidden");
				die("HTTP/1.1 403 Forbidden");
			}
			$posts = getPostsNextFeed($user, $feedId, $favorites, $unread, $sort, $postsPage, $nextId, $search);
			echo json_encode($posts);
			break;
		case 1: // folder
			if (!checkFolderAccess($user,$folderId)){
				header("HTTP/1.1 403 Forbidden");
				die("HTTP/1.1 403 Forbidden");
			}
			$posts = getPostsNextFolder($user, $folderId, $favorites, $unread, $sort, $postsPage, $nextId, $search);
			echo json_encode($posts);
			break;
		case 2: // tags
			if ($public && isTagPublic($tagId)){
				$hidden = 1;
				$posts = getPostsNextTag($tagId, 0, 0, $sort, $postsPage, $nextId, "");
				echo json_encode($posts);
				break;
			}
			if ($public || !checkTagAccess($user,$tagId)){
				header("HTTP/1.1 403 Forbidden");
				die("HTTP/1.1 403 Forbidden");
			}
			$posts = getPostsNextTag($tagId, $favorites, $unread, $sort, $postsPage, $nextId, $search);
			echo json_encode($posts);
			break;
		case 3: // all
			$posts = getPostsNextAll($user, $favorites, $unread, $sort, $postsPage, $nextId, $search);
			echo json_encode($posts);
			break;
		default:
			die("No reachable point");
			break;
	}
	mysqli_close($con);
?>