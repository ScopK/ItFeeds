<?php
	$isServer=true;
	include "../scripts/manager/func/initind.php";
	include "../scripts/manager/func/functions.php";

	$user = $_POST['user'];
	$pass = $_POST['pass'];

	if ($user = checkUserPassword($con,$user,$pass)) {
		$_SESSION['log_user'] = $user;
		unset($_SESSION['hid_user']);
	} else {
		header('HTTP/1.1 502 User not found');
		die('HTTP/1.1 502 User not found');
	}
?>