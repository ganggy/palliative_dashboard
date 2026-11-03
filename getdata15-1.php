<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
<?php
$d1=$_GET["d1"];
$d2=$_GET["d2"];
	
	$host="192.168.2.254";
	$Datauser="opd";
	$Datapass="opd";
	$Dataname="hos";
	$conn=mysqli_connect($host,$Datauser,$Datapass,$Dataname)or die("Cannot connect Host");
	mysqli_query($conn,"SET NAMES UTF8");


	$sql = "select DISTINCT o.hn,CONCAT( pt.pname,pt.lname,' ',pt.fname) as ชื่อ ,pt.informaddr
from ovstdiag o
left outer join vn_stat v on v.vn=o.vn
LEFT JOIN patient pt on pt.hn=v.hn
WHERE o.icd10 in (\"Z515\") and v.vstdate BETWEEN \"".date2db($d1)."\" AND \"".date2db($d2)."\"
and o.hn not in (select distinct(o.hn) from ovstdiag  o left outer join vn_stat v on v.vn=o.vn where o.icd10 in (\"Z515\") and v.vstdate <='2018-10-01' )";
	$result=mysqli_query($conn,$sql);
	$result_show = mysqli_query($conn,$sql) or die(mysqli_error());
	$row_show = mysqli_fetch_array($result_show);
	
	function db2date($ddate){
		$array_temp=explode("-",$ddate);
	return $array_temp[2]."/".$array_temp[1]."/".($array_temp[0]+543);}

?>
</table>
<table width="80%" border="0" align="center">
  <tr>
    <th scope="col"><div class="alert alert-dark" role="alert">
<table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">ลำดับ</th>
      <th scope="col" align="right">HN</th>
      <th scope="col">ชื่อ-สกุล</th>
      <th scope="col">ที่อยู่</th>
    </tr>
  </thead>
  <tbody>
         <?php $i = 1;
		 while($row=mysqli_fetch_array($result, MYSQLI_ASSOC) ){?>
              <tr>
              <td height="20" align="center" bgcolor="#F8F8FF"><?=$i;?></td>
                <td height="20" align="center" bgcolor="#F8F8FF"><?=$row["hn"]?></td>
                <td align="left" bgcolor="#F8F8FF"><?=$row["ชื่อ"]?></td>
                <td  align="left" bgcolor="#F8F8FF"><?=$row["informaddr"]?></td>
              </tr>
              
              <?php $i++; }?>
        </tbody>
          </table>
<p>
  </td>
      </p></tr>
  <tr>
    <th scope="col">
    </tr>
    </table>
    <script type="text/javascript">
$("#img_load").hide();
</script>
<body>
</body>
</html>