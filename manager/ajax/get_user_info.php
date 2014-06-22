<?php
	$isServer=true;
	include "../func/initind.php";

	if (!isset($_REQUEST['user']) && !isset($_SESSION['log_user']))
		die("No user entered");

	include "../func/classes.php";
	include "../func/functions.php";
	
	if (isset($_REQUEST['user'])){
		$ad_user = $_REQUEST['user'];
		if ($ad_user != $_SESSION['ad_user'])
			unset($_SESSION['ad_hidden']);
		$_SESSION['ad_user'] = $ad_user;

		if (isset($_SESSION['ad_hidden']))
			$ad_hidden = checkUserHiddenPassword($con, $ad_user,$_SESSION['ad_hidden']);

		$folders = getFolders($con, $ad_user,1,$ad_hidden);
		$tags = getTags($con, $ad_user,0,$ad_hidden);
	} else {
		$user = $_SESSION['log_user'];
		$hidden = checkUserHiddenPassword($con, $user,$_SESSION['hid_user']);
		$folders = getFolders($con, $user,1,$hidden);
		$tags = getTags($con, $user,0,$hidden);
	}

	//usleep(400000);
	$data = array("folders" => $folders, "tags" => $tags);

	echo json_encode($data);

	mysqli_close($con);
?>



