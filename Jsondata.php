<?php
header('Cotent-Type: application/json');
$objConnect = mysqli_connect("192.168.2.254","opd","opd");
$objDB = mysqli_select_db("hos");
mysql_query("SET NAME UTF8");
 $strSQL ="SELECT * FROM house";
 $objQuery = mysql_query($strSQL);
 $resulTArray = array();
 while($obResylt = mysqli_fetch_assoc($objQuery))
 {
	 array_push($resulTArray,$obResylt);
 }
 echo json_encode($resulTArray);
 ?>