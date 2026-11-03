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

	
	$data_ec = "eclaimdb";
	$conn_ec = new mysqli($host, $user, $pass, $data_ec);
	if ($conn_ec->connect_error) {
		die("Connection failed: " . $conn_ec->connect_error);
	} 
	$conn_ec->query("SET NAMES UTF8");

	$palliative_date = "2020-04-01";
?>