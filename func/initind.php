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
  	$REMLA = time();
  	$fullstr = date('Y-d-m H:i:s', $REMLA)." >> $REMIP\n";



  	if ($REMIP != "::1"){
		file_put_contents("../cons.txt", "######## STATUS ########\n",FILE_APPEND);
		file_put_contents("../cons.txt", $fullstr,FILE_APPEND);
	  	if (isset($_SESSION['userweb_ip']) && isset($_SESSION['userweb_last']))
	  		file_put_contents("../cons.txt", "SESSION:\n".date('Y-d-m H:i:s', $_SESSION['userweb_last'])." >> ".$_SESSION['userweb_ip']."\n",FILE_APPEND);
	  	else
	  		file_put_contents("../cons.txt", "NO LAST SESSION\n",FILE_APPEND);
		file_put_contents("../cons.txt", "# END ## STATUS ########\n",FILE_APPEND);
	}

  	if ($REMIP != "::1"){
	  	if (isset($_SESSION['userweb_ip']) && $_SESSION['userweb_ip']!==$REMIP){
			file_put_contents("../cons.txt", date('Y-d-m H:i:s', $REMLA).">> CHANGED IP FROM ".$_SESSION['userweb_ip']." TO: $REMIP\n",FILE_APPEND);
	  	}

	  	if (isset($_SESSION['userweb_last'])) {
	  		$diff = $REMLA - $_SESSION['userweb_last'];

	  		if ($diff > 3600){
				file_put_contents("../cons.txt", "More than 1 hour later:\n".$fullstr,FILE_APPEND);
	  		}
		} else {
			//if (strpos(file_get_contents("../cons.txt"),$fullstr)===false)
			file_put_contents("../cons.txt", $fullstr,FILE_APPEND);
		}
	}

	$_SESSION['userweb_ip'] = $REMIP;
	$_SESSION['userweb_last'] = $REMLA;

?>