<?php
	$isServer=true;
	include "../../func/initind.php";
	//include "../../func/functions.php";
	
	$days = 7;

 	//$user = $_SESSION['log_user'];

	$time = new DateTime();
	date_default_timezone_set('Europe/Madrid');
	$time->sub(new DateInterval('P'.$days.'D'));
	$date = date("Y-m-d H:i:s", $time->format('U')); 

	//$sql = "DELETE p.* FROM posts p WHERE p.favorite='0' AND p.unread='0' AND p.date < '$date' AND p.id NOT IN (SELECT id_post FROM post_tags)";
	$sql = "UPDATE posts p SET p.deleted=1 WHERE p.deleted=0 AND p.favorite='0' AND p.unread='0' AND p.date < '$date' AND p.id NOT IN (SELECT id_post FROM post_tags)";
	
	$stmt=mysqli_stmt_init($con);
	if (mysqli_stmt_prepare($stmt,$sql)){
		//mysqli_stmt_bind_param($stmt,"s", $user); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		$done = mysqli_affected_rows($con);
		mysqli_stmt_close($stmt); // Close statement
	}

    echo "Daily Cleaned $done posts";
    
	mysqli_close($con);
?>
