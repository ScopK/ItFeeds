<?php
	include "../manager/func/initind.php";
	include "../manager/func/functions.php";

	$user = $_POST['user'];
	$pass = $_POST['pass'];

	if (checkUserPassword($con,$user,$pass)) {
		$_SESSION['log_user'] = $user;
	} else {
		header('HTTP/1.1 502 User not found');
		die('HTTP/1.1 502 User not found');
	}
?>