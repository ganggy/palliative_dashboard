<?php 
 header('Content-Type: application/json');
 $objConnect = mysqli_connect("192.168.0.254","hosbm2011","bmhos2554","hos");
 $strSQL = "SELECT * FROM house";
 $objQuery = mysqli_query($objConnect,$strSQL);
 $resultArray = array();
 while($obResult = mysqli_fetch_assoc($objQuery))
 {
	 array_push($resultArray,$obResuit);
 }
 echo json_encode($resultArray);
?>