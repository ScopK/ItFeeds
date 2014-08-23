<?php
	$isServer=true;
	include "../func/initind.php";
	include "../func/functions.php";

	$user = $_POST['user'];
	$pass = $_POST['pass'];
	$hiddenpass = $_POST['kw'];

	if (strlen($user)<4 || strlen($pass)<4 || strlen($hiddenpass)<4){
		header('HTTP/1.1 500 Field too short');
		die('HTTP/1.1 500 Field too short');
	}

	if (userExists($user)){
		header('HTTP/1.1 501 User already exists');
		die('HTTP/1.1 501 User already exists');
	}

	$sql = "INSERT INTO users(username,password,hidden_pass) VALUES(?,?,?)";
	$stmt=mysqli_stmt_init($con);
	$done = 0;
	if (mysqli_stmt_prepare($stmt,$sql)){
		mysqli_stmt_bind_param($stmt,"sss", $user, $pass, $hiddenpass);
		mysqli_stmt_execute($stmt);
		$done = mysqli_affected_rows($con);
	}
	mysqli_stmt_close($stmt);

	if ($done != 1){
		mysqli_close($con);
		header('HTTP/1.1 502 An error ocurred creating user');
		die('HTTP/1.1 502 An error ocurred creating user');
	}

	$sql = "INSERT INTO folders(id,name,user,hidden) VALUES(newID(36,\"folders\"),'null',?,'0')";
	$stmt=mysqli_stmt_init($con);
	$done = 0;
	if (mysqli_stmt_prepare($stmt,$sql)){
		mysqli_stmt_bind_param($stmt,"s", $user);
		mysqli_stmt_execute($stmt);
		$done = mysqli_affected_rows($con);
	}
	mysqli_stmt_close($stmt);

	mysqli_close($con);
	if ($done != 1){
		header('HTTP/1.1 503 An error ocurred initializing database');
		die('HTTP/1.1 503 An error ocurred initializing database');
	}
?>
