<?php
	if (!isset($_POST['hiddenPass']))
		die("No pass entered");

	$isServer=true;
	include "../func/classes.php";
	include "../func/functions.php";
	include "../func/initind.php";

	$user = $_SESSION['log_user'];
	$pass = $_POST['hiddenPass'];
	
	if ($pass == "")
		unset($_SESSION['hid_user']);
	else
		$_SESSION['hid_user'] = $pass;

	if (isset($_SESSION['hid_user']))
		$hidden = checkUserHiddenPassword($user,$_SESSION['hid_user']);
	else $hidden = false;

	//usleep(400000);
	$folders = getFolders($user,1,$hidden);
	$tags = getTags($user,0,$hidden);

	$data = array("folders" => $folders, "tags" => $tags, "unlocked" => ($hidden?"true":"false") );

	echo json_encode($data);

	mysqli_close($con);
?>