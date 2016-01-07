<?php
	if(!isset($isServer) || !$isServer){
		header("HTTP/1.1 403 Forbidden");
		die("HTTP/1.1 403 Forbidden");
	}

	session_start();

	if (!isset($nodb)){
		$con = mysqli_connect("localhost","fydep_u","4syouwI5h","fydepdb");
		if (mysqli_connect_errno())
			die("Failed to connect to MySQL: " . mysqli_connect_error());
	}

	//mysqli_query($con,"SET NAMES utf8");
	//mysqli_query($con,"SET GLOBAL time_zone = '+1:00';");
	//mysqli_set_charset($con,'utf8');

	$REMIP = isset($_SERVER['HTTP_X_CLIENT_IP'])?$_SERVER['HTTP_X_CLIENT_IP']:(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:"localhost");
	$REMLA = time();

	$_SESSION['userweb_ip'] = $REMIP;
	$_SESSION['userweb_last'] = $REMLA;
?>
