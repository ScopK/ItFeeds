<?php
	$name = $_POST['fname'];
	$folderId = $_POST['folderId'];
	$rssLink = $_POST['rlink'];
	$link = $_POST['link'];

	if (!isset($name) || $name=="") {
		header('HTTP/1.1 501 Not valid name');
		die('HTTP/1.1 501 Not valid name');
	}

	if (!isset($rssLink) || $rssLink=="") {
		header('HTTP/1.1 502 Not rss link');
		die('HTTP/1.1 502 Not rss link');
	}

	$isServer=true;
	include "../../func/initind.php";
	include "../../func/classes.php";
	include "../../func/functions.php";

	if ($link=="")
		$link=$rssLink;

	$id = getNewID();

  	$sql ="INSERT INTO feeds(id,id_folder,name,link,rss_link,enabled,deleted) VALUES('$id',?,?,?,?,1,0)";

	$stmt=mysqli_stmt_init($con);
	if (mysqli_stmt_prepare($stmt,$sql)){

		mysqli_stmt_bind_param($stmt,"ssss", $folderId, utf8_decode($name), $link, $rssLink); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		mysqli_stmt_close($stmt); // Close statement
	}


	$sql = "SELECT * FROM feeds WHERE id='$id'";
	$res = mysqli_query($con,$sql);
	$query = mysqli_fetch_array($res);

	$feed = new Feed();

	$feed->id = $query['id'];
	$feed->folderId = $query['id_folder'];
	$feed->name = $query['name'];
	$feed->upd_time = $query['upd_time'];
	$feed->link = $query['link'];
	$feed->rss_link = $query['rss_link'];
	$feed->last_date_post = $query['last_date_post'];
	$feed->enabled = $query['enabled'];
	$feed->deleted = $query['deleted'];

	mysqli_free_result($res);
	mysqli_close($con);

	echo json_encode($feed);

?>