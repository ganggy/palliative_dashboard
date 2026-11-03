<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
<?php
	session_name("bmhsession");
	session_start();
	
	$serverName = "192.168.2.254";
	$userName = "opd";
	$userPassword = "opd";
	$dbName = "hos";
$logintype = "'แพทย์','พยาบาล','ผู้ดูแลระบบ','ห้องยา','นักกายภาพบำบัด'";
	$objCon = mysqli_connect($serverName,$userName,$userPassword,$dbName);
mysqli_set_charset($objCon,"utf8");
	$strSQL = "SELECT * FROM opduser WHERE loginname = '".mysqli_real_escape_string($objCon,$_POST['uname'])."' 
	and passweb = md5('".$_POST['psw']."') ";//and groupname in (".$logintype.") ";
	$objQuery = mysqli_query($objCon,$strSQL);
	$objResult = mysqli_fetch_array($objQuery,MYSQLI_ASSOC);
	if(!$objResult)
	{
			echo "Username หรือ Password ไม่ถูกต้อง หรือ ไม่ได้รับอนุญาติให้ใช้ระบบ!";
						echo "<script>
setTimeout(function() {
  window.location.href = 'login.php';
}, 3000);
</script> ";
	}
	else
	{
			$_SESSION["UserID"] = $objResult["loginname"];
$_SESSION["name1"] = $objResult["name"];
			session_write_close();
			echo $_SESSION["UserID"];
			if($_SESSION["UserID"] <> "")
			{
				echo "<script>
setTimeout(function() {
  window.location.href = 'index.php';
}, 3000);
</script> ";
				//header("location:index.php");
			}
			else
			{
				echo "Username หรือ Password ไม่ถูกต้อง หรือ ไม่ได้รับอนุญาติให้ใช้ระบบ!";
			}
	}
	mysqli_close($objCon);
?>
	</body>
</html>