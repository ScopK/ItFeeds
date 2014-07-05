<?php
	$isServer=true;
	require_once "../func/classes.php";
	require_once "../func/initind.php";
	require_once "../func/functions.php";

	$postid = $_REQUEST['postid'];
	$unread = $_REQUEST['unread'];
	$fav = $_REQUEST['fav'];

	$stmt=mysqli_stmt_init($con);

	if (isset($unread)){
		$sql = "UPDATE posts SET unread=? WHERE id=?";
		$value=$unread;
		if (isset($_SESSION['las_user']))
			foreach($_SESSION['las_user'][posts] as $post)
				if ($post->id == $postid)
					$post->unread = $unread;

	} elseif (isset($fav)){
		$sql = "UPDATE posts SET favorite=? WHERE id=?";
		$value=$fav;
		if (isset($_SESSION['las_user']))
			foreach($_SESSION['las_user'][posts] as $post)
				if ($post->id == $postid)
					$post->favorite = $fav;
	} else {
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