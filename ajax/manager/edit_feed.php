<?php
	$isServer=true;
	include "../../func/classes.php";
	include "../../func/initind.php";

	$feedId = $_POST['feedId'];
	$name = $_POST['fname'];

	$rsslink = $_POST['rlink'];
	$link = $_POST['link'];

	$uptime = $_POST['uptime'];

	$enable = (isset($_POST['ena']))? "1":"0";

	$stmt=mysqli_stmt_init($con);
	if (mysqli_stmt_prepare($stmt,"UPDATE feeds SET name=? ,link=? ,rss_link=? ,upd_time=? ,enabled=? WHERE id=?")){

		mysqli_stmt_bind_param($stmt,"ssssss", $name, $link, $rsslink, $uptime, $enable, $feedId); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		mysqli_stmt_close($stmt); // Close statement
	}

	$feed = new Feed();

	$feed->id = $feedId;
	$feed->name = $name;
	$feed->upd_time = $uptime;
	$feed->link = $link;
	$feed->rss_link = $rsslink;
	$feed->enabled = $enable;

	mysqli_close($con);	

	echo json_encode($feed);

?>