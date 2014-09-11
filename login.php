<?php
	$nodb = true;
	$isServer=true;
	include "func/initind.php";
?><!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<title>Fydeph Login</title>
		<link rel="shortcut icon" href="imgs/icon.png" />
		<script src="scripts/lib/messages.js"></script>
		<link rel="stylesheet" type="text/css" href="style/login.css">
		<script>
		var logintab = true;
		window.onresize=function(){
			if (logintab)
				window.scroll(0,0);
			else
				window.scroll(0,document.getElementById("registertab").offsetTop);
		};
		window.addEventListener("load",function(){
			document.getElementById('username_field').focus();
			window.scroll(0,0);
		});
		function login(){
			var user = document.getElementById('username_field').value;
			var pass = document.getElementById('password_field').value;
			doLogin(user,pass);
		}
		function register(){
			var user = document.getElementById('r_username_field').value;
			var pass = document.getElementById('r_password_field').value;
			var pass2 = document.getElementById('r_password_field2').value;
			var kw = document.getElementById('r_keyword_field').value;
			var kw2 = document.getElementById('r_keyword_field2').value;
			if (user.length<4) 	showMessage("Username too short");
			else if (pass!=pass2)	showMessage("Passwords does not match");
			else if (kw!=kw2)		showMessage("Keywords does not match");
			else if (pass.length<4)	showMessage("Passwords too short");
			else if (kw.length<4)	showMessage("Keywords too short");
			else doReg(user,pass,kw);
		}
		function moveToReg() {
			var toY = document.getElementById("registertab").offsetTop-window.scrollY;
			if (toY > 50) toY=50;
			window.scroll(0,window.scrollY+toY);
			if (toY == 50)
				setTimeout(moveToReg, 10);
			else{
				document.getElementById('r_username_field').focus();
				document.getElementById("arrowslog").style.display="";
				logintab = false;
			}
		}
		function moveToLog() {
			var toY = window.scrollY;
			if (toY > 50) toY=50;
			window.scroll(0,window.scrollY-toY);
			if (toY == 50)
				setTimeout(moveToLog, 10);
			else {
				document.getElementById('username_field').focus();
				document.getElementById("arrowslog").style.display="none";
				logintab = true;
			}
		}
		function doLogin(user,pass) {
			var xmlhttp;
			if (window.XMLHttpRequest) // code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp = new XMLHttpRequest();
			else // code for IE6, IE5
				xmlhttp = newActiveXObject("Microsoft.XMLHTTP");

			xmlhttp.onreadystatechange=function() {
				console.log(xmlhttp);
				if (xmlhttp.readyState==4 && xmlhttp.status==200) {
					var data = "<?=http_build_query($_REQUEST);?>";
					if (data.indexOf("manager=1")>=0){
						var i = data.indexOf("manager=1");
						var go = data.substring(0,i)+data.substring(i+10);
						if (go.charAt(go.length-1) == "&") go = go.substring(0,go.length-1);
						window.location = "manager.php?"+go;
					}
					else	window.location = "index.php?"+data;
				} else if (xmlhttp.readyState==4 && xmlhttp.status==502) {
					showMessage("Invalid username or password, please, try again or register");
				} else if (xmlhttp.readyState==4) {
					showMessage(xmlhttp.status+": "+xmlhttp.statusText);
				}
			}
			xmlhttp.open("POST","ajax/login.php",true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlhttp.send("user="+user+"&pass="+pass);
		}
		function doReg(user,pass,kw) {
			var xmlhttp;
			if (window.XMLHttpRequest) // code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp = new XMLHttpRequest();
			else // code for IE6, IE5
				xmlhttp = newActiveXObject("Microsoft.XMLHTTP");

			xmlhttp.onreadystatechange=function() {
				if (xmlhttp.readyState==4 && xmlhttp.status==200) {
					doLogin(user,pass);
				} else if (xmlhttp.readyState==4 && xmlhttp.status==501) {
					showMessage("Username already exists");
				} else if (xmlhttp.readyState==4) {
					showMessage(xmlhttp.status+": "+xmlhttp.statusText);
				}
			}
			xmlhttp.open("POST","ajax/register.php",true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlhttp.send("user="+user+"&pass="+pass+"&kw="+kw);
		}
		</script>
	</head>
	<body>
	<div id="logintab" class="tab">
		<div id="dialog">
		<form style="height:100%">
			<table ><tr><th colspan="2">User login</th></tr>
				<tr><td colspan="2"><input id="username_field" style="width: 167px;font-size: 15px;" autocomplete="off" placeholder="Username" type="text">
				</td></tr>
				<tr><td colspan="2"><input id="password_field" style="width: 140px;font-size: 12px;" type="password" autocomplete="off" placeholder="Password"/>
				</td></tr>
				<tr><td align="right"><button onclick="login(this); return false;">Login</button></td>
					 <td align="left"><button onclick="moveToReg(); return false;">Register</button></td></tr>
			</table>
		</form>
		</div>
	</div>
	<div id="registertab" class="tab">
		<div id="arrowslog" onclick="moveToLog();" style="display:none;cursor:pointer;color:#333">
			<h1>▲</h1><h2>▲</h2>
		</div>

		<div id="r_dialog">
		<form style="height:100%">
			<table ><tr><th colspan="2">User register</th></tr>
				<tr><td><input id="r_username_field" style="width: 167px;font-size: 15px;" autocomplete="off" placeholder="Username" type="text">
				</td></tr>
				<tr><td><input id="r_password_field" style="width: 167px;font-size: 15px;margin-top:5px" type="password" autocomplete="off" placeholder="Password"/>
				</td></tr>
				<tr><td><input id="r_password_field2" style="width: 167px;font-size: 15px" type="password" autocomplete="off" placeholder="Repeat Password"/>
				</td></tr>
				<tr><td><input id="r_keyword_field" style="width: 140px; font-size: 12px;margin-top:5px" type="password" autocomplete="off" placeholder="Hidden keyword"/>
				</td></tr>
				<tr><td><input id="r_keyword_field2" style="width: 140px; font-size: 12px;" type="password" autocomplete="off" placeholder="Repeat hidden keyword"/>
				</td></tr>
				<tr><td align="center"><button onclick="register(this); return false;" style="width:200px">Register</button></td></tr>
			</table>
		</form>
		</div>
	</div>
	</body>
</html>
