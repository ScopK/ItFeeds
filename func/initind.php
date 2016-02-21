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

		// ############## DATABASE FUNCTIONS
		function DB_query($query,$assoc=true){
			global $con;
			$array = array("rows"=>array());
			$result = $con->query($query);
			if ($result){
				$array["rows"] = $result->fetch_all($assoc? MYSQLI_ASSOC : MYSQLI_NUM);
			}
			$array["affected"] = $con->affected_rows;
			$array["error"] = $con->error;
			return $array;
		}

		function DB_safequery($query,$values,$assoc=true){
			global $con;
			$array = array("rows"=>array());

			if ($stmt = $con->prepare($query)){
				//refValues:
				if (strnatcmp(phpversion(),'5.3') >= 0){ //Reference is required for PHP 5.3+
					$value_refs = array();
					foreach($values as $k => $v)
						$value_refs[$k] = &$values[$k];
				}

				call_user_func_array(array($stmt,"bind_param"),
					array_merge(
						array(str_repeat("s",count($values))),
						$value_refs
					)
				);
				$stmt->execute();
				$stmt->store_result();


				$variables = array();
				$data = array();
				$meta = $stmt->result_metadata();

				if ($meta!==false){
					while($field = $meta->fetch_field())
						$variables[] = &$data[$field->name]; // pass by reference

					call_user_func_array(array($stmt, 'bind_result'), $variables);

					$i=0;
					while($stmt->fetch()){
						$array["rows"][$i] = array();
						if ($assoc)	foreach($data as $k=>$v)
							$array["rows"][$i][$k] = $v;

						else foreach($data as $k=>$v)
							$array["rows"][$i][] = $v;
						$i++;
					}
				}

				$array["affected"] = $stmt->affected_rows;
				$array["error"] = $stmt->error;

				$stmt->close();
			} else {
				$array["affected"] = $con->affected_rows;
				$array["error"] = $con->error;
			}
			return $array;
		}
		// !############# DATABASE FUNCTIONS

		
	}

	//mysqli_query($con,"SET NAMES utf8");
	//mysqli_query($con,"SET GLOBAL time_zone = '+1:00';");
	//mysqli_set_charset($con,'utf8');

	$REMIP = isset($_SERVER['HTTP_X_CLIENT_IP'])?$_SERVER['HTTP_X_CLIENT_IP']:(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:"localhost");
	$REMLA = time();

	$_SESSION['userweb_ip'] = $REMIP;
	$_SESSION['userweb_last'] = $REMLA;
?>
