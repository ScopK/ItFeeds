<?php
	if (!isset($_POST['hiddenPass']))
		die("No pass entered");

	$isServer=true;
	include "../func/classes.php";
	include "../func/functions.php";
	include "../func/initind.php";


	$ad_user = $_SESSION['ad_user'];
	$pass = $_POST['hiddenPass'];
	if ($pass == "")
		unset($_SESSION['ad_hidden']);
	else
		$_SESSION['ad_hidden'] = $pass;

	if (isset($_SESSION['ad_hidden']))
		$ad_hidden = checkUserHiddenPassword($con, $ad_user,$pass);

	//usleep(400000);
	$folders = getFolders($con, $ad_user,1);
	$tags = getTags($con, $ad_user,0);

	$data = array("folders" => $folders, "tags" => $tags);

	echo json_encode($data);

	mysqli_close($con);
?>