<?php
	$isServer=true;
	include "../../func/initind.php";
	include "../../func/functions.php";
	/*
	mysqli_query($con,"DELETE FROM active_user");
	mysqli_query($con,"ALTER TABLE active_user AUTO_INCREMENT=0");
	die();
	/**/

	if (!isset($_REQUEST['user']) && !isset($_REQUEST['pass'])){
		header("HTTP/1.1 400 Bad Request");
		die("HTTP/1.1 400 Bad Request");
	}
	$user=$_REQUEST['user'];
	$pass=$_REQUEST['pass'];
	if ($user=checkUserPassword($user,$pass)){
		retry:

		$res = mysqli_query($con,"SELECT * FROM active_user WHERE user='$user'");
		$row = @array_map('utf8_encode',mysqli_fetch_assoc($res));
		if (!isset($row)){
			mysqli_query($con,"INSERT INTO active_user(user,token) VALUES('$user',newID(36,\"active_user\"))");
			goto retry;
		}

		$json=array("user"=>$user,"token"=>$row['token']);
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($json);
	} else {
		header("Content-Type: application/json; charset=utf-8");
		die("{\"error\":\"Incorrect login\"}");
	}

	/*
	if ($user=checkUserPassword($user,$pass)){
		mysqli_query($con,"INSERT INTO active_user(user,token) VALUES('$user',newID(36,\"active_user\"))");

		$res = mysqli_query($con,"SELECT * FROM active_user WHERE id='".mysqli_insert_id($con)."'");
		$row = @array_map('utf8_encode',mysqli_fetch_assoc($res));
		
		$json=array("user"=>$user,"token"=>$row['token']);
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($json);
	} else {
		header("HTTP/1.1 401 Unauthorized");
		die("HTTP/1.1 401 Unauthorized");
	}*/
?>
