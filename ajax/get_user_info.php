<?php
	$isServer=true;
	include "../func/initind.php";

	if (!isset($_REQUEST['user']) && !isset($_SESSION['log_user']))
		die("No user entered");

	include "../func/classes.php";
	include "../func/functions.php";
	
	if (isset($_REQUEST['user'])){
		$user = $_REQUEST['user'];
		$hidden = false;
	} else {
		$user = $_SESSION['log_user'];
		if (isset($_SESSION['hid_user']))
			$hidden = checkUserHiddenPassword($con, $user,$_SESSION['hid_user']);
		else
			$hidden = false;
	}

	$folders = getFolders($con, $user,1,$hidden);
	$tags = getTags($con, $user,0,$hidden);

	//usleep(400000);
	$data = array("folders" => $folders, "tags" => $tags);

	echo json_encode($data);

	mysqli_close($con);
?>



