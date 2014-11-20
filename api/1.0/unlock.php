<?php
	$isServer=true;
	include "../../func/initind.php";
	include "../../func/functions.php";

	if (!isset($_REQUEST['token']) && !isset($_REQUEST['pass'])){
		header("HTTP/1.1 400 Bad Request");
		die("HTTP/1.1 400 Bad Request");
	}
	$token=$_REQUEST['token'];
	$pass=$_REQUEST['pass'];

	// ####################### CHECK TOKEN AND PASS
	$user=getTokenUsername($token);
	if(!$user || !checkUserHiddenPassword($user,$pass)){
		header("HTTP/1.1 401 Unauthorized");
		die("HTTP/1.1 401 Unauthorized");
	}

	// ####################### CREATE NEW LOCK
	$stmt=mysqli_stmt_init($con);
	if (mysqli_stmt_prepare($stmt,"UPDATE active_user SET `lock`=newID(36,'active_lock') WHERE token=?")){
		mysqli_stmt_bind_param($stmt,"s", $token); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		$done = mysqli_affected_rows($con);
	}

	// ####################### GET LOCK TO SHOW
	if (mysqli_stmt_prepare($stmt,"SELECT `lock` FROM active_user WHERE token=?")){

		mysqli_stmt_bind_param($stmt,"s", $token); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		mysqli_stmt_bind_result($stmt,$lock); // Bind result variables
		mysqli_stmt_fetch($stmt); // Fetch value
	}
	mysqli_stmt_close($stmt); // Close statement

	if (isset($lock)){
		$json=array("user"=>$user,"locktoken"=>$lock);
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($json);
	} else {
		header("HTTP/1.1 500 Internal Server Error");
		die("HTTP/1.1 500 Internal Server Error");
	}
?>