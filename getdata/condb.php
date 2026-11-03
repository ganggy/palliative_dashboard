<?php 	

	$host = "192.168.2.254";
	$user = "opd";
	$pass = "opd";
	$data = "hos";
	$conn = new mysqli($host, $user, $pass, $data);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 
	$conn->query("SET NAMES UTF8");
?>