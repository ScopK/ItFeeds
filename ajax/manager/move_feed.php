<?php
	$isServer=true;
	include "../../func/classes.php";
	include "../../func/functions.php";
	include "../../func/initind.php";

	$folderId = $_POST['folderId'];
	$feedId = $_POST['feedId'];

	$stmt=mysqli_stmt_init($con);
	if (mysqli_stmt_prepare($stmt,"UPDATE feeds SET id_folder=? WHERE id=?")){

		mysqli_stmt_bind_param($stmt,"ss", $folderId, $feedId); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		$done = mysqli_affected_rows($con);

		mysqli_stmt_close($stmt); // Close statement
	}
	mysqli_close($con);

	if ($done != 1) {
		header('HTTP/1.1 501 Error moving feed');
		die('HTTP/1.1 501 Error moving feed');
	}
	echo "oK";
?>