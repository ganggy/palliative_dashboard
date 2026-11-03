<?php 
 header('Content-Type: application/json');
 $objConnect = mysqli_connect("192.168.2.254","opd","opd","hos");
 $strSQL = "SELECT * FROM house";
 $objQuery = mysqli_query($objConnect,$strSQL);
 $resultArray = array();
 while($obResult = mysqli_fetch_assoc($objQuery))
 {
	 array_push($resultArray,$obResuit);
 }
 echo json_encode($resultArray);
?>