<?php
	if (!isset($_REQUEST['user']))
		die("No user entered");

	$isServer=true;
	include "../func/classes.php";
	include "../func/functions.php";
	include "../func/initind.php";

	$ad_user = $_REQUEST['user'];
	if ($ad_user != $_SESSION['ad_user'])
		unset($_SESSION['ad_hidden']);
	$_SESSION['ad_user'] = $ad_user;

	if (isset($_SESSION['ad_hidden']))
		$ad_hidden = checkUserHiddenPassword($con, $ad_user,$_SESSION['ad_hidden']);

	//usleep(400000);
	$folders = getFolders($con, $ad_user,1);
	$tags = getTags($con, $ad_user,0);

	$data = array("folders" => $folders, "tags" => $tags);


	echo json_encode($data);

	mysqli_close($con);
?>



