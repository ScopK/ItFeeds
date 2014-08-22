<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    	<title>Fydeph Login</title>
    	<link rel="shortcut icon" href="imgs/icon.png" />
    	<script src="scripts/lib/messages.js"></script>
        <style>
			body{
				background: linear-gradient(#eee, #ddd);
				/*
				background: linear-gradient(yellowgreen, #687d3c);
				background-image: url("imgs/back.jpg");
				background-size: 100% 100%;*/
				background-attachment: fixed;
				overflow: hidden;
			}
			body, button, input{
				/*font-family: "Segoe UI",dejavu sans,"Verdana","Trebuchet MS",Tahoma,sans-serif;*/
				font-family: sans-serif,Tahoma, Geneva;
			}
			#dialog{
				position:fixed;
				margin:auto;top:0;bottom:100px;left:0;right:0;
				width: 255px; height: 155px;
				padding-bottom:5px;

				background-color:#fff;
				border: 2px solid #000;
			}
			#dialog table {
				border-spacing: 0;
				width:100%;height:100%;text-align:center;
			}
			#dialog table th {
				background-color: #000;
				color: #fff;
				font-weight: bold;
				padding: 5px 0;
			}
			#dialog table td {
				margin:0;
			}
			button::-moz-focus-inner { border:0; }
			button{
				font-size: 15px;
				margin: 0 10px;
				padding: 5px;
				border: 2px solid #000;
			}
			button:focus{ background-color: #ffdead; }
        </style>
        <script>
        window.onload=function(){
			document.getElementById('username_field').focus()
    	};
    	function login(){
    		var user = document.getElementById('username_field').value;
    		var pass = document.getElementById('password_field').value;
    		doLogin(user,pass);
    	}
    	function register(){
    		showMessage("Work in progress");
    	}

		function doLogin(user,pass) {
			var xmlhttp;
			if (window.XMLHttpRequest) // code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp = new XMLHttpRequest();
			else // code for IE6, IE5
				xmlhttp = newActiveXObject("Microsoft.XMLHTTP");

			xmlhttp.onreadystatechange=function() {
				if (xmlhttp.readyState==4 && xmlhttp.status==200) {
					var data = "<?=http_build_query($_REQUEST);?>";
					if (data.indexOf("manager=1")>=0)	window.location = "manager.php";
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
        </script>
    </head>
    <body>
    	<div id="dialog">
    	<form style="height:100%">
	    	<table ><tr><th colspan="2">User login</th></tr>
		    	<tr><td colspan="2"><input id="username_field" style="font-size: 15px;" autocomplete="off" placeholder="Username" type="text">
		    	</td></tr>
		    	<tr><td colspan="2"><input id="password_field" style="width: 140px; font-size: 12px;" type="password" autocomplete="off" placeholder="Password"/>
		    	</td></tr>
		    	<tr><td align="right"><button onclick="login(this); return false;">Login</button></td>
		    		 <td align="left"><button onclick="register(); return false;">Register</button></td></tr>
			</table>
		</form>
    	</div>
    </body>
</html>