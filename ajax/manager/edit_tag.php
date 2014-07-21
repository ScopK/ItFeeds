<?php
	$isServer=true;
	include "../../func/classes.php";
	include "../../func/functions.php";
	include "../../func/initind.php";

	$tagId = $_POST['tagId'];
	$name = $_POST['tagname'];

	$hidden = (isset($_POST['hidden']))? "1":"0";

	$stmt=mysqli_stmt_init($con);
	if (mysqli_stmt_prepare($stmt,"UPDATE tags SET tag_name=?, hidden=? WHERE id=?")){

		mysqli_stmt_bind_param($stmt,"sss", utf8_decode($name), $hidden, $tagId); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		mysqli_stmt_close($stmt); // Close statement
	}

	$tag = getTag($con,$tagId);

	mysqli_close($con);	

	echo json_encode($tag);

?>