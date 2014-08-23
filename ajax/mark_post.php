<?php
	$isServer=true;
	include "../func/initind.php";
	include "../func/functions.php";
	include "../func/classes.php";

	$postid = isset($_REQUEST['postid'])?$_REQUEST['postid']:null;
	$unread = isset($_REQUEST['unread'])?$_REQUEST['unread']:null;
	$fav = isset($_REQUEST['fav'])?$_REQUEST['fav']:null;

	$stmt=mysqli_stmt_init($con);

	if (isset($unread)){ $sql = "UPDATE posts SET unread=? WHERE id=?"; $value=$unread;}
	elseif (isset($fav)){$sql = "UPDATE posts SET favorite=? WHERE id=?";$value=$fav;}
	else {
		mysqli_stmt_close($stmt);
		mysqli_close($con);
		die("ERROR");
	}

	if (mysqli_stmt_prepare($stmt,$sql)){

		mysqli_stmt_bind_param($stmt,"ss", $value, $postid);
		mysqli_stmt_execute($stmt);

		$done = mysqli_affected_rows($con);

		mysqli_stmt_close($stmt);
	}

	$hid_user = isset($_SESSION['hid_user'])?$_SESSION['hid_user']:null;
	$hidden = checkUserHiddenPassword($_SESSION['log_user'],$hid_user);
	echo json_encode(getPost($postid,$hidden));
	mysqli_close($con);
?>