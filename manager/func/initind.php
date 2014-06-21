<?php
    session_start();

    $con = mysqli_connect("localhost","root","admin","fydepdb");
    if (mysqli_connect_errno())
      die("Failed to connect to MySQL: " . mysqli_connect_error());
?>