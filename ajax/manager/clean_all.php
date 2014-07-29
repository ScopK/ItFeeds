<?php
	$isServer=true;
	include "../../func/initind.php";
	
	$days = $_POST['days'];
	$unread = (isset($_POST['unread']))? "1":"0";

	if (!$unread)
		$unreadSQL = "AND unread='0'";
	$sql = "DELETE FROM posts WHERE favorite='0' $unreadSQL AND date < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL ? DAY) AND id NOT IN (SELECT id_post FROM post_tags)";

	$stmt=mysqli_stmt_init($con);
	if (mysqli_stmt_prepare($stmt,$sql)){

		mysqli_stmt_bind_param($stmt,"s", $days); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		$done = mysqli_affected_rows($con);
		mysqli_stmt_close($stmt); // Close statement
	}

    $arr = array ('postsDeleted'=>$done);
    
	mysqli_close($con);
    echo json_encode($arr); // {"a":1,"b":2,"c":3,"d":4}
?>