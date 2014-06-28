<?php

	$isServer=true;
	include "../func/initind.php";
	include "../func/functions.php";
	include "../func/classes.php";

	$postid = $_REQUEST['postid'];
	$tagid = $_REQUEST['tag'];
	$user = $_SESSION['log_user'];


	// TODO: check user-idtag-hidden for permissions

	$sql = "DELETE FROM post_tags WHERE id_post=? AND id_tag=?";

	$stmt=mysqli_stmt_init($con);
	if (mysqli_stmt_prepare($stmt,$sql)){

		mysqli_stmt_bind_param($stmt,"ss", $postid, $tagid);
		mysqli_stmt_execute($stmt);

		$done = mysqli_affected_rows($con);
	}
	if ($done != 1){
		mysqli_stmt_close($stmt);
		header('HTTP/1.1 500 Error deleting tag');
		die('HTTP/1.1 500 Error deleting tag');
	} elseif ($done > 1){
		mysqli_stmt_close($stmt);
		header('HTTP/1.1 500 Deleted more than 1');
		die('HTTP/1.1 500 Deleted more than 1');
	}


	$sql = "SELECT count(*) FROM post_tags WHERE id_tag=?";
	if (mysqli_stmt_prepare($stmt,$sql)){

		mysqli_stmt_bind_param($stmt,"s", $tagid);
		mysqli_stmt_execute($stmt);

		mysqli_stmt_bind_result($stmt,$done);
		mysqli_stmt_fetch($stmt);
	}

	if ($done == 0){	// Tag empty, les delete it
		$sql = "DELETE FROM tags WHERE id=?";
		if (mysqli_stmt_prepare($stmt,$sql)){

			mysqli_stmt_bind_param($stmt,"s", $tagid);
			mysqli_stmt_execute($stmt);

			$done = mysqli_affected_rows($con);
		}
		if ($done != 1){
			mysqli_stmt_close($stmt);
			header('HTTP/1.1 500 Error deleting tag');
			die('HTTP/1.1 500 Error deleting tag');
		}
	}

	mysqli_stmt_close($stmt);

	mysqli_close($con);

?>