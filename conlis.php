<?php

	$host="192.168.2.254";
	$Datauser="opd";
	$Datapass="opd";
	$Dataname="hos";
	$con=mysqli_connect($host,$Datauser,$Datapass,$Dataname)or die("Cannot connect Host");
	mysqli_query($con,"SET NAMES UTF8");

?>