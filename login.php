<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    	<title>Fydeph Login</title>
    	<link rel="shortcut icon" href="imgs/icon.png" />
        <style>
			body {
			    font-size: small;
			    font-family: "Segoe UI",dejavu sans,"Verdana","Trebuchet MS",Tahoma,sans-serif;

			    background-image: url("imgs/back.jpg");
			    background-attachment: fixed;
			    background-size: 100% 100%;

			    overflow: hidden;
			}
			#invalid{
				position:fixed;
				top:-50px;left:0;right:0;
				background-color: rgba(250,50,0,0.3);
				border-bottom: 2px solid #000;
				transition: top 0.4s;
			}
			#invalid p{
				text-align: center;
				color:white;
				font-weight: bold;
				font-size: 14px;
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
    		alert("register");
    	}

		function doLogin(user,pass) {
			var xmlhttp;
			if (window.XMLHttpRequest) // code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp = new XMLHttpRequest();
			else // code for IE6, IE5
				xmlhttp = newActiveXObject("Microsoft.XMLHTTP");

			xmlhttp.onreadystatechange=function() {
				if (xmlhttp.readyState==4 && xmlhttp.status==200) {
					document.getElementById("invalid").style.top = "-50px";
					window.location = ".";
				} else if (xmlhttp.readyState==4 && xmlhttp.status==502) {
					document.getElementById("invalid").childNodes[1].innerHTML = "Invalid username or password, please, try again or register";
					document.getElementById("invalid").style.top = "0";
					setTimeout(function(){
						document.getElementById("invalid").style.top = "-50px";
					},3000);
				} else if (xmlhttp.readyState==4) {
					document.getElementById("invalid").childNodes[1].innerHTML = xmlhttp.status+": "+xmlhttp.statusText;
					document.getElementById("invalid").style.top = "0";
					setTimeout(function(){
						document.getElementById("invalid").style.top = "-50px";
					},3000);
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
		    	<tr><td colspan="2"><input id="username_field" type="text" autocomplete="off" placeholder="Username"/>
		    	</td></tr>
		    	<tr><td colspan="2"><input id="password_field" type="password" autocomplete="off" placeholder="Password"/>
		    	</td></tr>
		    	<tr><td align="right"><button onclick="login(this); return false;">Login</button></td>
		    		 <td align="left"><button onclick="register(); return false;">Register</button></td></tr>
			</table>
		</form>
    	</div>

    	<div id="invalid">
    		<p>Invalid username or password, please, try again or register</p>
    	</div>

    </body>
</html>