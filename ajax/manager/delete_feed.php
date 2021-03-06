<?php
	$isServer=true;
	include "../../func/initind.php";

	$feedId = $_POST['feedId'];
	if (!isset($feedId))
		die("feedId?");

	$sql = "SELECT count(*) FROM posts WHERE id_feed=? AND (favorite='1' OR id IN (SELECT id_post FROM post_tags))";
	$stmt=mysqli_stmt_init($con);
	if (mysqli_stmt_prepare($stmt,$sql)){

		mysqli_stmt_bind_param($stmt,"s", $feedId); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		mysqli_stmt_bind_result($stmt,$favesCount); // Bind result variables
		mysqli_stmt_fetch($stmt); // Fetch value

		//mysqli_stmt_close($stmt); // Close statement
	}

	if ($favesCount>0)
		$sql = "UPDATE feeds SET deleted='1' WHERE id=?";
	else 
		$sql = "DELETE FROM feeds WHERE id=?";

	if (mysqli_stmt_prepare($stmt,$sql)){

		mysqli_stmt_bind_param($stmt,"s", $feedId); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		$done = mysqli_affected_rows($con);

		mysqli_stmt_close($stmt); // Close statement
	}

	mysqli_close($con);
	if ($done == 1)
		echo "oK";
	elseif($done > 1)
		echo "Deleted more than 1 element!!";
	else
		echo "Element not found";
?>