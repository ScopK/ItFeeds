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
	//mysqli_set_charset($con,'utf8');

  	$REMIP = $_SERVER['REMOTE_ADDR'];
  	if ($REMIP == "::1") $REMIP = "127.0.0.1";
  	$REMLA = time();
  	$fullstr = date('Y-d-m H:i:s', $REMLA)." - $REMIP";

  	$fileroute = "cons.txt";
  	if (!file_exists($fileroute)) $fileroute = "../cons.txt";

  	if ($REMIP != "127.0.0.1" && $REMIP != $_SERVER["HTTP_HOST"]){
	  	if (isset($_SESSION['userweb_ip']) && $_SESSION['userweb_ip']!==$REMIP){
			file_put_contents($fileroute, date('Y-d-m H:i:s', $REMLA)." - CHANGED IP FROM ".$_SESSION['userweb_ip']." TO $REMIP\n",FILE_APPEND);
	  	}

	  	if (isset($_SESSION['userweb_last'])) {
	  		$diff = $REMLA - $_SESSION['userweb_last'];

	  		if ($diff > 3600){
				file_put_contents($fileroute, "$fullstr (1 hour later)\n",FILE_APPEND);
	  		}
		} else {
			//if (strpos(file_get_contents($fileroute),$fullstr)===false)
			file_put_contents($fileroute, "$fullstr\n",FILE_APPEND);
		}
	}

	$_SESSION['userweb_ip'] = $REMIP;
	$_SESSION['userweb_last'] = $REMLA;

?>