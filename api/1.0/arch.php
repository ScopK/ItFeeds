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

	$folders = getFolders($user,1,$hidden);
	$tags = getTags($user,1,$hidden);
	header("Content-Type: application/json; charset=utf-8");
	echo json_encode(array('user'=>$user,'folders'=>$folders,'tags'=>$tags));
?>