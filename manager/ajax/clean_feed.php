<?php
	include "../func/initind.php";
	
	$days = $_POST['days'];
	$feedId = $_POST['feedId'];

	$unread = (isset($_POST['unread']))? "1":"0";

	if (!$unread)
		$unreadSQL = "AND unread='0'";
	$sql = "DELETE FROM posts WHERE id_feed=? AND favorite='0' $unreadSQL AND date < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL ? DAY) AND id NOT IN (SELECT id_post FROM post_tags)";



	$stmt=mysqli_stmt_init($con);
	if (mysqli_stmt_prepare($stmt,$sql)){

		mysqli_stmt_bind_param($stmt,"ss", $feedId, $days); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		//mysqli_stmt_close($stmt); // Close statement
	}


	$sql = "SELECT count(*),IFNULL(sum(p.unread), 0) FROM posts p WHERE p.id_feed=?";

	//$stmt=mysqli_stmt_init($con);
	if (mysqli_stmt_prepare($stmt,$sql)){

		mysqli_stmt_bind_param($stmt,"s", $feedId); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		mysqli_stmt_bind_result($stmt,$feedCount,$feedUnread); // Bind result variables
		mysqli_stmt_fetch($stmt); // Fetch value

		//mysqli_stmt_close($stmt); // Close statement
	}


	$sql = "SELECT count(*),IFNULL(sum(p.unread), 0) FROM posts p WHERE p.id_feed IN (";
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