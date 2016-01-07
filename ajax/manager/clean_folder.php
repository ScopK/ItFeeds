<?php
	$isServer=true;
	include "../../func/classes.php";
	include "../../func/functions.php";
	include "../../func/initind.php";

	$days = $_POST['days'];
	$folderId = $_POST['folderId'];

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

	//$sql = "DELETE FROM posts WHERE id_feed IN (SELECT id FROM feeds WHERE id_folder=?) AND favorite='0' $unreadSQL AND date < '$date' AND id NOT IN (SELECT id_post FROM post_tags)";
	$sql = "UPDATE posts SET deleted=1 WHERE deleted=0 AND id_feed IN (SELECT id FROM feeds WHERE id_folder=?) AND favorite='0' $unreadSQL AND date < '$date' AND id NOT IN (SELECT id_post FROM post_tags)";

	$stmt=mysqli_stmt_init($con);
	if (mysqli_stmt_prepare($stmt,$sql)){

		mysqli_stmt_bind_param($stmt,"s", $folderId); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		//mysqli_stmt_close($stmt); // Close statement
	}

	$folder = getFolder($folderId,2);
    
	mysqli_close($con);
    echo json_encode($folder); // {"a":1,"b":2,"c":3,"d":4}
	
?>