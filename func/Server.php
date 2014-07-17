<?php
	require_once "PostsFetch.php";

    $con = mysqli_connect("localhost","root","admin","fydepdb");
    if (mysqli_connect_errno())
      die("Failed to connect to MySQL: " . mysqli_connect_error());

  	$pf = new PostsFetch();
  	$pf->setConnection($con);

	$sql = "SELECT * FROM feeds WHERE enabled='1' AND deleted='0'";
	$feeds = mysqli_query($con,$sql);
	$times = array();
	$timesLeft = array();
	foreach($feeds as $feed){
		$times[$feed['id']] = $feed['upd_time'];
		$timesLeft[$feed['id']] = 0;
	}

	$time = time();
	while(true){
		foreach($feeds as $feed){
			if ($timesLeft[$feed['id']] <= 0){
				$pf->fetchFeed($feed);
				$timesLeft[$feed['id']] = $times[$feed['id']];
			} else {
				$timesLeft[$feed['id']]--;
			}
		}

		echo "\n";
		$left = ($time+60)-time();
		if ($left>0)
			sleep($left);
		$time+=60;
	}

?>