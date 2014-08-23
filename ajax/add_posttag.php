<?php
	$isServer=true;
	include "../func/initind.php";
	include "../func/functions.php";
	include "../func/classes.php";

	$postid = $_REQUEST['postid'];
	$tagname = $_REQUEST['tagname'];
	if (!isset($tagname) || $tagname == ""){
		header('HTTP/1.1 500 No tag entered');
		die('HTTP/1.1 500 No tag entered');
	}
	$tags = explode(" ",$tagname);
	$user = $_SESSION['log_user'];

	$tagResults = array();
	foreach($tags as $tag){
		$stmt=mysqli_stmt_init($con);
		unset($idobt);
		unset($hidobt);
		$sql = "SELECT id,hidden FROM tags WHERE user=? AND tag_name=?";
		if (mysqli_stmt_prepare($stmt,$sql)){
			mysqli_stmt_bind_param($stmt,"ss", $user, utf8_decode($tag));
			mysqli_stmt_execute($stmt);

			mysqli_stmt_bind_result($stmt,$idobt,$hidobt);
			mysqli_stmt_fetch($stmt);
		}
		if (!$idobt){
			$idobt = getNewID();
			$sql = "INSERT INTO tags VALUES(?,?,?,'0')";
			if (mysqli_stmt_prepare($stmt,$sql)){

				mysqli_stmt_bind_param($stmt,"sss", $idobt, $user, utf8_decode($tag));
				mysqli_stmt_execute($stmt);

				$done = mysqli_affected_rows($con);
			}
			if ($done != 1){
				mysqli_stmt_close($stmt);
				continue;
				//header('HTTP/1.1 500 Error creating tag');
				//die('HTTP/1.1 500 Error creating tag');
			}
		} else {
			$sql = "SELECT count(*) AS c FROM post_tags WHERE id_post=? AND id_tag=?";
			if (mysqli_stmt_prepare($stmt,$sql)){
				mysqli_stmt_bind_param($stmt,"ss", $postid, $idobt);
				mysqli_stmt_execute($stmt);

				mysqli_stmt_bind_result($stmt,$done);
				mysqli_stmt_fetch($stmt);
			}
			if ($done != 0){	// Post already has this tag
				mysqli_stmt_close($stmt);
				continue;
				//header('HTTP/1.1 500 Error creating tag');
				//die('HTTP/1.1 500 Error creating tag');
			}
		}

		if ($hidobt==1){
			mysqli_stmt_close($stmt);
			if (!checkUserHiddenPassword($user,$_SESSION['hid_user'])){
				continue;
				//header('HTTP/1.1 500 Error, permission denied');
				//die('HTTP/1.1 500 Error, permission denied');
			}
			$stmt=mysqli_stmt_init($con);
		}

		$sql = "INSERT INTO post_tags VALUES(?,?)";
		if (mysqli_stmt_prepare($stmt,$sql)){

			mysqli_stmt_bind_param($stmt,"ss", $postid, $idobt);
			mysqli_stmt_execute($stmt);

			$done = mysqli_affected_rows($con);

			mysqli_stmt_close($stmt);
		}
		if ($done != 1){
			continue;
			//header('HTTP/1.1 500 Error tagging post');
			//die('HTTP/1.1 500 Error tagging post');
		}
		$tagResults[] = getTag($idobt);
	}
	echo json_encode($tagResults);
	mysqli_close($con);
?>