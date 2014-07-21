<?php
	$isServer=true;
	include "../../func/initind.php";

	$user = $_SESSION['log_user'];
	$tagId = $_POST['tagId'];

	$stmt=mysqli_stmt_init($con);

	if (mysqli_stmt_prepare($stmt,"DELETE FROM tags WHERE id=? AND user=?")){

		mysqli_stmt_bind_param($stmt,"ss",$tagId, $user); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		$done = mysqli_affected_rows($con);

		mysqli_stmt_close($stmt); // Close statement
	}
	if ($done != 1) {
		header('HTTP/1.1 501 Error deleting tag');
		die('HTTP/1.1 501 Error deleting tag');
	}

	echo "oK";
?>