<?php
	$isServer=true;
	include "../../func/initind.php";
	include "../../func/classes.php";
	include "../../func/functions.php";
	
	if (!isset($_REQUEST['token'])){
		header("HTTP/1.1 400 Bad Request");
		die("HTTP/1.1 400 Bad Request");
	}
	$token=$_REQUEST['token'];

	$user=getTokenUsername($token);
	if(!$user){
		header("HTTP/1.1 401 Unauthorized");
		die("HTTP/1.1 401 Unauthorized");
	}
	if ($user && isset($_REQUEST['lock']))
		$hidden=getTokenLock($token,$_REQUEST['lock']);
	else 
		$hidden=false;


#---------------------------------------

	$postsPage = isset($_REQUEST['postspage'])?$_REQUEST['postspage']:null;
	$page = isset($_REQUEST['page'])?$_REQUEST['page']:null;
	
	$favorites = isset($_REQUEST['fav'])?$_REQUEST['fav']:null;
	$unread = isset($_REQUEST['unread'])?$_REQUEST['unread']:null;
	
	$nextId = isset($_REQUEST['nextid'])?$_REQUEST['nextid']:null;
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

	if (!isset($page) || !is_numeric($page) || $page<1) $page=1;
	if (!isset($favorites)) $favorites = 0;
	if (!isset($unread)) $unread = 1;
	if ($favorites==1) $unread = 0;

	switch($mode){
		case 0: // feeds
			if (!checkFeedAccess($user,$feedId)){
				header("HTTP/1.1 403 Forbidden");
				die("HTTP/1.1 403 Forbidden");
			}
			if (!isset($nextId) || $nextId=="")
				$posts = getPostsFeed($user, $feedId, $favorites, $unread, $sort, ($page-1)*$postsPage, $postsPage, $search);
			else
				$posts = getPostsNextFeed($user, $feedId, $favorites, $unread, $sort, $postsPage, $nextId, $search);
			break;
		case 1: // folder
			if (!checkFolderAccess($user,$folderId)){
				header("HTTP/1.1 403 Forbidden");
				die("HTTP/1.1 403 Forbidden");
			}
			if (!isset($nextId) || $nextId=="")
				$posts = getPostsFolder($user, $folderId, $favorites, $unread, $sort, ($page-1)*$postsPage, $postsPage, $search);
			else
				$posts = getPostsNextFolder($user, $folderId, $favorites, $unread, $sort, $postsPage, $nextId, $search);
			break;
		case 2: // tags
			if ($public && isTagPublic($tagId)){
				$hidden = 1;
				if (!isset($nextId) || $nextId=="")
					$posts = getPostsTag($tagId, 0, 0, $sort, ($page-1)*$postsPage, $postsPage, "");
				else
					$posts = getPostsNextTag($tagId, 0, 0, $sort, $postsPage, $nextId, "");
				break;
			}
			if ($public || !checkTagAccess($user,$tagId)){
				header("HTTP/1.1 403 Forbidden");
				die("HTTP/1.1 403 Forbidden");
			}
			if (!isset($nextId) || $nextId=="")
				$posts = getPostsTag($tagId, $favorites, $unread, $sort, ($page-1)*$postsPage, $postsPage, $search);
			else
				$posts = getPostsNextTag($tagId, $favorites, $unread, $sort, $postsPage, $nextId, $search);
			break;
		case 3: // all
			if (!isset($nextId) || $nextId=="")
				$posts = getPostsAll($user, $favorites, $unread, $sort, ($page-1)*$postsPage, $postsPage, $search);
			else
				$posts = getPostsNextAll($user, $favorites, $unread, $sort, $postsPage, $nextId, $search);
			break;
		default:
			die("No reachable point");
			break;
	}
	echo json_encode($posts);
	mysqli_close($con);
?>