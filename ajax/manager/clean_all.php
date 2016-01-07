<?php
	$isServer=true;
	include "../../func/initind.php";
	include "../../func/functions.php";
	
	$days = $_POST['days'];
	$unread = (isset($_POST['unread']))? "1":"0";

	if (!is_numeric($days)){
		header("HTTP/1.1 403 Forbidden");
		die("HTTP/1.1 403 Forbidden");
	}

	$unreadSQL="";
	if (!$unread)
		$unreadSQL = "AND p.unread='0'";

 	$user = $_SESSION['log_user'];
 	$hidPass = (isset($_SESSION['hid_user']))?$_SESSION['hid_user']:"";
 	$hidden = checkUserHiddenPassword($user,$hidPass);

	$hiddenSQL = "";
	if (!$hidden)
		$hiddenSQL = "AND fo.hidden='0'";
	
	$time = new DateTime();
	date_default_timezone_set('Europe/Madrid');
	$time->sub(new DateInterval('P'.$days.'D'));
	$date = date("Y-m-d H:i:s", $time->format('U')); 

	//$sql = "DELETE p.* FROM posts p JOIN feeds fe ON fe.id=p.id_feed JOIN folders fo ON fo.id=fe.id_folder WHERE fo.user=? AND p.favorite='0' $unreadSQL $hiddenSQL AND p.date < '$date' AND p.id NOT IN (SELECT id_post FROM post_tags)";
	$sql = "UPDATE posts p JOIN feeds fe ON fe.id=p.id_feed JOIN folders fo ON fo.id=fe.id_folder SET p.deleted=1 WHERE p.deleted=0 AND fo.user=? AND p.favorite='0' $unreadSQL $hiddenSQL AND p.date < '$date' AND p.id NOT IN (SELECT id_post FROM post_tags)";
	
	$stmt=mysqli_stmt_init($con);
	if (mysqli_stmt_prepare($stmt,$sql)){

		mysqli_stmt_bind_param($stmt,"s", $user); // Bind parameters
		mysqli_stmt_execute($stmt); // Execute query

		$done = mysqli_affected_rows($con);
		mysqli_stmt_close($stmt); // Close statement
	}

    $arr = array ('postsDeleted'=>$done);
    
	mysqli_close($con);
    echo json_encode($arr); // {"a":1,"b":2,"c":3,"d":4}
?>