<?php
	include "../func/classes.php";
	include "../func/functions.php";
	include "../func/initind.php";

	$days = $_POST['days'];
	$folderId = $_POST['folderId'];

	$unread = (isset($_POST['unread']))? "1":"0";


	if (!$unread)
		$unreadSQL = "AND unread='0'";
	$sql = "DELETE FROM posts WHERE id_feed IN (SELECT id FROM feeds WHERE id_folder=?) AND favorite='0' $unreadSQL AND date < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL ? DAY) AND id NOT IN (SELECT id_post FROM post_tags)";



	$stmt=mysqli_stmt_init($con);
	if (mysqli_stmt_prepare($stmt,$sql)){

		mysqli_stmt_bind_param($stmt,"ss", $folderId, $days); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		//mysqli_stmt_close($stmt); // Close statement
	}

	$folder = getFolder($con,$folderId,2);
    
	mysqli_close($con);
    echo json_encode($folder); // {"a":1,"b":2,"c":3,"d":4}
	
?>