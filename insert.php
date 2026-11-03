<?php 

	$host="192.168.0.254";
	$Datauser="hosbm2011";
	$Datapasss="bmhos2554";
	$Datanamee="hos";
	$conn = mysqli_connect($host,$Datauser,$Datapasss,$Datanamee)or die("Cannot connect Host");
	mysqli_query($conn,"SET NAMES UTF8");	


function date2db($str){
		$temp_e=explode("/",$str);
		return ($temp_e[2]-543)."-".$temp_e[1]."-".$temp_e[0];
		
		}
	
	$mr=$_POST["mr"];
	$name=$_POST["name"];
	$lastname=$_POST["lastname"];
	$age=$_POST["age"];
	$hn=$_POST["hn"];
	$daytt=$_POST["daytt"];
	$disease=$_POST["disease"];
	$physician=$_POST["physician"];
	$pt1=$_POST["pt1"];
	$pt2=$_POST["pt2"];
	$pt3=$_POST["pt3"];
	$pd1=$_POST["pd1"];
	$pd2=$_POST["pd2"];
	$pd3=$_POST["pd3"];
	$pc1=$_POST["pc1"];
	$pc2=$_POST["pc2"];
	$pc3=$_POST["pc3"];
	$tt21=$_POST["tt21"];
	$tt22=$_POST["tt22"];
	$tt23=$_POST["tt23"];
	$cc3=$_POST["cc3"];
	$ee1=$_POST["ee1"];
	$ee2=$_POST["ee2"];
	$ee3=$_POST["ee3"];
	$ee4=$_POST["ee4"];
	
$sql="INSERT INTO zpt_acp
(mr,name,lastname,age,hn,daytt,disease,physician,pt1,pt2,pt3,pd1,pd2,pd3,pc1,pc2,pc3,tt21,tt22,tt23,cc3,ee1,ee2,ee3,ee4)";
$sql.="  VALUES (\"$mr\",\"$name\",\"$lastname\",\"$age\",\"$hn\",\"$daytt\",\"$disease\",\"$physician\",\"$pt1\",\"$pt2\",\"$pt3\",\"$pd1\",\"$pd2\",\"$pd3\",\"$pc1\",\"$pc2\",\"$pc3\",\"$tt21\",\"$tt22\",\"$tt23\",\"$cc3\",\"$ee1\",\"$ee2\",\"$ee3\",\"$ee4\")";
	
	mysqli_query($conn, $sql)or die ("ERROR<br>".$sql);
	if($sqli_query) {
       
    }else{
       	echo "<script type='text/javascript'>alert('บันทึกข้อมูลเรียบร้อยแล้ว')</script>";
	  /*  echo "<meta http-equiv ='refresh'content='0;URL=main.php'>";*/
    }
$report_id=mysqli_insert_id($conn);
	
$token="Jd7UpAXQIQJhdfe5hXLocSJpl2W3WbVGFUWbZWJEfnU";
$message="เพิ่มข้อมูลเรียบรเอยแล้ว   
กรุณาตรวจสอบแล้วจัดทำด้วยค่ะค่ะ;
https://program.bmhos.com/WFM/manual.php?id=".$hn;
// http://127.0.0.1/fromzax/WFM/manual.php?id=".$report_id;
line_notify($token, $message);




function line_notify($token, $message)
{
		$lineapi = $token; 
		$mms =  trim($message);
		date_default_timezone_set("Asia/Bangkok");
		$chOne = curl_init(); 
		curl_setopt( $chOne, CURLOPT_URL, "https://notify-api.line.me/api/notify");
		curl_setopt( $chOne, CURLOPT_SSL_VERIFYHOST, 0); 
		curl_setopt( $chOne, CURLOPT_SSL_VERIFYPEER, 0); 
		curl_setopt( $chOne, CURLOPT_POST, 1); 
		curl_setopt( $chOne, CURLOPT_POSTFIELDS, "message=$mms"); 
		curl_setopt( $chOne, CURLOPT_FOLLOWLOCATION, 1); 
		$headers = array( 'Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer '.$lineapi.'', );
			curl_setopt($chOne, CURLOPT_HTTPHEADER, $headers); 
		curl_setopt( $chOne, CURLOPT_RETURNTRANSFER, 1); 
		$result = curl_exec( $chOne ); 
		curl_close( $chOne );   
}

 ?>

<body>
<script type="text/javascript">
	window.location.href="main.php?page=borrow_list";
</script>
</body>