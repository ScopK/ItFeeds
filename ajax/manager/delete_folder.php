<?php
	$isServer=true;
	include "../../func/initind.php";
	include "../../func/functions.php";

	$user = $_SESSION['log_user'];
	$pass = $_POST['pass'];
	$folderId = $_POST['folderId'];

	if (checkUserPassword($user,$pass)) {
		$stmt=mysqli_stmt_init($con);

		if (mysqli_stmt_prepare($stmt,"DELETE FROM folders WHERE id=? AND name!='null'")){

			mysqli_stmt_bind_param($stmt,"s",$folderId); // Bind parameters
			mysqli_stmt_execute($stmt); // Execute query

			$done = mysqli_affected_rows($con);

			mysqli_stmt_close($stmt); // Close statement
		}
		if ($done != 1) {
			header('HTTP/1.1 501 Error deleting folder');
			die('HTTP/1.1 501 Error deleting folder');
		}
	}
	else {
		header('HTTP/1.1 501 Wrong password');
		die('HTTP/1.1 501 Wrong password');
	}
	echo "oK";
?>