<?php
	$name = $_POST['foldername'];

	if (!isset($name) || $name=="") {
		header('HTTP/1.1 501 Not valid name');
		die('HTTP/1.1 501 Not valid name');
	}


	$isServer=true;
	include "../../func/initind.php";
	include "../../func/classes.php";
	include "../../func/functions.php";

	$user = $_SESSION['log_user'];
	if (!isset($user) || $user=="") {
		header('HTTP/1.1 501 Not logged user');
		die('HTTP/1.1 501 Not logged user');
	}

	$id = getUUID();

  	$sql ="INSERT INTO folders(id,name,user,hidden) VALUES('$id',?,?,0)";

	$stmt=mysqli_stmt_init($con);
	if (mysqli_stmt_prepare($stmt,$sql)){

		mysqli_stmt_bind_param($stmt,"ss", $name, $user); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		$done = mysqli_affected_rows($con);

		mysqli_stmt_close($stmt); // Close statement
	}

	if ($done != 1){
		header('HTTP/1.1 501 Error creating user');
		die('HTTP/1.1 501 Error creating user');
	}

	$folder = new Folder();

	$folder->id = $id;
	$folder->name = $name;
	$folder->user = $user;
	$folder->hidden = false;

	mysqli_close($con);

	echo json_encode($folder);

?>