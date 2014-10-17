<?php
	$isServer=true;
	include "../func/initind.php";
	include "../func/functions.php";

	$user = $_POST['user'];
	$pass = $_POST['pass'];

	if ($user = checkUserPassword($user,$pass)) {
		$_SESSION['log_user'] = $user;
		unset($_SESSION['hid_user']);

		if (!in_array($REMIP, $ip_whitelist))
			file_put_contents("../cons.txt", date('Y-d-m H:i:s', time())." - $REMIP Logged as: '".$user."'\n",FILE_APPEND);
	} else {
		header('HTTP/1.1 502 User not found');
		die('HTTP/1.1 502 User not found');
	}
?>