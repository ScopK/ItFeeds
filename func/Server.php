<?php
	// CLEAR COMMAND:
	//SELECT * FROM feeds WHERE deleted='1' AND id NOT IN (SELECT id_feed FROM posts WHERE favorite='1') AND id NOT IN (SELECT id_feed FROM posts WHERE id IN (SELECT id_post FROM post_tags))
	require_once "PostsFetch.php";

    $con = mysqli_connect("localhost","fydep_u","4syouwI5h","fydepdb");
    if (mysqli_connect_errno())
      die("Failed to connect to MySQL: " . mysqli_connect_error());

  	$pf = new PostsFetch();
  	$pf->setConnection($con);

	$sql = "SELECT * FROM feeds WHERE enabled='1' AND deleted='0'";
	$timesLeft = array();

	$time = time();
	while(true){
		$feeds = mysqli_query($con,$sql);
		foreach($feeds as $feed){
			if (controlFeed($feed['id'],$feed['upd_time'])){
				$pf->fetchFeed($feed);
			}
		}
		mysqli_free_result($feeds);
		$left = ($time+60)-time();
		if ($left>0)
			sleep($left);
		$time+=60;
	}

	function controlFeed($id, $time){
		global $timesLeft;
		if (!isset($timesLeft[$id])){
			$timesLeft[$id] = $time;
			return true;
		} else {
 			if ($timesLeft[$id] > $time)
 				$timesLeft[$id] = $time;
			$timesLeft[$id]--;
			if ($timesLeft[$id] <= 0){
				$timesLeft[$id] = $time;
				return true;
			} else {
				return false;
			}
		}
	}
?>