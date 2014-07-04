<?php
	$isServer=true;
	include "../func/initind.php";
	include "../func/functions.php";
	include "../func/classes.php";

	$postid = $_REQUEST['postid'];
	$unread = $_REQUEST['unread'];
	$fav = $_REQUEST['fav'];

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

	echo json_encode(getPost($con,$postid));
	mysqli_close($con);
?>