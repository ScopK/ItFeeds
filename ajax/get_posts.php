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

	$last = $_REQUEST['useLast'];

// CHECK INPUTS
	if (!isset($postsPage) || !is_numeric($postsPage) || $postsPage<0) $pPage=10;
	else $pPage=($postsPage>100)?100:$postsPage;
	if (!isset($page) || !is_numeric($page) || $page<1) $page=1;

	if (isset($last)){
		$isServer=true;
		require_once "../func/classes.php";
		require_once "../func/initind.php";
		require_once "../func/functions.php";

		if (!isset($_SESSION["las_user"])){
			die("Last array not found");
		}

		$array = $_SESSION["las_user"];
		$listacut = array_slice($array[posts], ($page-1)*$pPage, $pPage);
		$data = array("posts" => $listacut, "total" => $array[total]);

		echo json_encode($data);
		mysqli_close($con);
	} else {
		if (isset($feedId))	$mode=0;
		elseif (isset($folderId)) $mode=1;
		elseif (isset($tagId)) $mode=2;
		else $mode=3;

		if (!isset($sort)) $sort="DESC";
		else $sort=($sort==1)?"DESC":"ASC";

		if (!isset($favorites)) $favorites = 0;
		if (!isset($unread)) $unread = 1;
		if ($favorites==1) $unread = 0;

		$isServer=true;
		require_once "../func/classes.php";
		require_once "../func/initind.php";
		require_once "../func/functions.php";

		//usleep(400000);
		$user = $_SESSION['log_user'];
		$hidd = (isset($_SESSION['hid_user']))?checkUserHiddenPassword($con, $user,$_SESSION['hid_user']):false;
		switch($mode){
			case 0: // feeds
				$posts = getPostsFeed($con, $user, $hidd, $feedId, $favorites, $unread, $sort, ($page-1)*$pPage, $pPage);
				echo json_encode($posts);
				break;
			case 1: // folder
				$posts = getPostsFolder($con, $user, $hidd, $folderId, $favorites, $unread, $sort, ($page-1)*$pPage, $pPage);
				echo json_encode($posts);
				break;
			case 2: // tags
				$posts = getPostsTag($con, $user, $hidd, $tagId, $favorites, $unread, $sort, ($page-1)*$pPage, $pPage);
				echo json_encode($posts);
				break;
			case 3: // all
				$posts = getPostsAll($con, $user, $hidd, $favorites, $unread, $sort, ($page-1)*$pPage, $pPage);
				echo json_encode($posts);
				break;
			default:
				die("No reachable point");
				break;

		}
		mysqli_close($con);
	}
?>