<?php
	$isServer=true;
	include "../../func/classes.php";
	include "../../func/initind.php";

	$folderId = $_POST['folderId'];
	$name = $_POST['fname'];

	$hidden = (isset($_POST['hid']))? "1":"0";

	$stmt=mysqli_stmt_init($con);
	if (mysqli_stmt_prepare($stmt,"UPDATE folders SET name=? ,hidden=? WHERE id=?")){

		mysqli_stmt_bind_param($stmt,"sss", $name, $hidden, $folderId); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		mysqli_stmt_close($stmt); // Close statement
	}

	$folder = new Folder();

	$folder->id = $folderId;
	$folder->name = $name;
	$folder->hidden = $hidden;

	mysqli_close($con);	

	echo json_encode($folder);

?>