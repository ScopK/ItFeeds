<?php
	$isServer=true;
	include "../../func/initind.php";
	
	$days = $_POST['days'];
	$feedId = $_POST['feedId'];

	if (!is_numeric($days)){
		header("HTTP/1.1 403 Forbidden");
		die("HTTP/1.1 403 Forbidden");
	}

	$unread = (isset($_POST['unread']))? "1":"0";
	$unreadSQL = (!$unread)?"AND unread='0'":"";

	$time = new DateTime();
	date_default_timezone_set('Europe/Madrid');
	$time->sub(new DateInterval('P'.$days.'D'));
	$date = date("Y-m-d H:i:s", $time->format('U')); 

	//$sql = "DELETE FROM posts WHERE id_feed=? AND favorite='0' $unreadSQL AND date < '$date' AND id NOT IN (SELECT id_post FROM post_tags)";
	$sql = "UPDATE posts SET deleted=1 WHERE deleted=0 AND id_feed=? AND favorite='0' $unreadSQL AND date < '$date' AND id NOT IN (SELECT id_post FROM post_tags)";

	$stmt=mysqli_stmt_init($con);
	if (mysqli_stmt_prepare($stmt,$sql)){

		mysqli_stmt_bind_param($stmt,"s", $feedId); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		//mysqli_stmt_close($stmt); // Close statement
	}

	$sql = "SELECT count(*),IFNULL(sum(p.unread), 0) FROM posts p WHERE p.deleted=0 AND p.id_feed=?";

	//$stmt=mysqli_stmt_init($con);
	if (mysqli_stmt_prepare($stmt,$sql)){

		mysqli_stmt_bind_param($stmt,"s", $feedId); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		mysqli_stmt_bind_result($stmt,$feedCount,$feedUnread); // Bind result variables
		mysqli_stmt_fetch($stmt); // Fetch value

		//mysqli_stmt_close($stmt); // Close statement
	}


	$sql = "SELECT count(*),IFNULL(sum(p.unread), 0) FROM posts p WHERE p.deleted=0 AND p.id_feed IN (";
		$sql .= "SELECT id FROM feeds WHERE id_folder=(";
			$sql .= "SELECT id_folder FROM feeds WHERE id=?))";

	//$stmt=mysqli_stmt_init($con);
	if (mysqli_stmt_prepare($stmt,$sql)){

		mysqli_stmt_bind_param($stmt,"s", $feedId); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		mysqli_stmt_bind_result($stmt,$folderCount,$folderUnread); // Bind result variables
		mysqli_stmt_fetch($stmt); // Fetch value

		mysqli_stmt_close($stmt); // Close statement
	}


    $arr = array ('feedUnread'=>$feedUnread,'feedCount'=>$feedCount,'folderUnread'=>$folderUnread,'folderCount'=>$folderCount);
    
	mysqli_close($con);
    echo json_encode($arr); // {"a":1,"b":2,"c":3,"d":4}
	
?>