<?php
	$isServer=true;
	include "../../func/initind.php";
	include "../../func/classes.php";
	include "../../func/functions.php";
	
	if (!isset($_REQUEST['token']) || !(isset($_REQUEST['feed']) || isset($_REQUEST['folder']) || isset($_REQUEST['tag']) )){
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
	header("Content-Type: application/json; charset=utf-8");
	die("{\"error\":\"Work in progress\"}");
	$posts = getPostsFeed($user, $feedId, $favorites, $unread, $sort, ($page-1)*$postsPage, $postsPage, $search);
	$posts = getPostsFolder($user, $folderId, $favorites, $unread, $sort, ($page-1)*$postsPage, $postsPage, $search);
	$posts = getPostsTag($tagId, $favorites, $unread, $sort, ($page-1)*$postsPage, $postsPage, $search);
?>