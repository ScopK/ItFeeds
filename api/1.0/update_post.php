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

	$postid = isset($_REQUEST['postid'])?$_REQUEST['postid']:null;
	$unread = isset($_REQUEST['unread'])?$_REQUEST['unread']:null;
	$fav = isset($_REQUEST['fav'])?$_REQUEST['fav']:null;

	if (!havePostAccess($user,$hidden,$postid)){
		header("Content-Type: application/json; charset=utf-8");
		die("{\"error\":\"No access\"}");
	}

	if (isset($unread)){ $sql = "UPDATE posts SET unread=? WHERE id=?";  $value=$unread;}
	elseif (isset($fav)){$sql = "UPDATE posts SET favorite=? WHERE id=?";$value=$fav;}
	else {
		mysqli_close($con);
		header("Content-Type: application/json; charset=utf-8");
		die("{\"error\":\"Incorrect data entered\"}");
	}

	$stmt=mysqli_stmt_init($con);
	if (mysqli_stmt_prepare($stmt,$sql)){

		mysqli_stmt_bind_param($stmt,"ss", $value, $postid);
		mysqli_stmt_execute($stmt);

		$done = mysqli_affected_rows($con);

		mysqli_stmt_close($stmt);
	}

	mysqli_close($con);

	if (isset($done)){
		header("Content-Type: application/json; charset=utf-8");
		die("{\"ok\":\"ok\"}");
	} else {
		header("Content-Type: application/json; charset=utf-8");
		die("{\"error\":\"Incorrect data entered\"}");
	}
?>
