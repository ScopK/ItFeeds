<?php
	$isServer=true;
	include "../func/initind.php";
	include "../func/functions.php";

	$hiddenPW = $_POST['lock']==1;
	$oldpass = $_POST['old'];
	$newpass = $_POST['new'];
	$user = $_SESSION['log_user'];

	if ($hiddenPW) {
		if (checkUserHiddenPassword($user,$oldpass) === false) die();
		$sql = "UPDATE users SET `hidden_pass`=? WHERE `username`=? AND `hidden_pass`=?";
	} else {
		if (checkUserPassword($user,$oldpass) === false) die();
		$sql = "UPDATE users SET `password`=? WHERE `username`=? AND `password`=?";
	}

	$stmt=mysqli_stmt_init($con);
	$done = 0;
	if (mysqli_stmt_prepare($stmt,$sql)){
		mysqli_stmt_bind_param($stmt,"sss", $newpass, $user, $oldpass);
		mysqli_stmt_execute($stmt);
		$done = mysqli_affected_rows($con);
	}
	mysqli_stmt_close($stmt);

	if ($done == 1){
		echo "ok";
		if ($hiddenPW && isset($_SESSION['hid_user']) && $_SESSION['hid_user']==$oldpass)
			$_SESSION['hid_user']=$newpass;
	}
?>
