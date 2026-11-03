 
<table width="60%" align="left" border="0">
  <tr>
    <th scope="col">
    
    <?php
$dt1 = $_GET["dt1"];
$dt2 = $_GET["dt2"];
	
	include "condb.php";
	 $sql1 = "SELECT count(DISTINCT op.hn) AS 'Sum'
from opitemrece op 
LEFT JOIN drugitems nd ON nd.icode=op.icode 
LEFT OUTER JOIN an_stat an on an.an=op.an
LEFT OUTER JOIN vn_stat vn on vn.vn=op.vn
WHERE op.icode in (1580001,1590005,1000156,1000406,1000412,1000413,1000430,1550025) and op.vstdate BETWEEN  '".$dt1."' AND '".$dt2."' 
";
$result1=mysqli_query($conn,$sql1);
	$result_show1 = mysqli_query($conn,$sql1) or die(mysqli_error());
	$row_show1 = mysqli_fetch_array($result_show1);
	 while($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC) ){	 ?>
    
    <div class="w3-quarter">
      <div class="w3-container w3-blue w3-padding-16">
        <div class="w3-left"><i class="fa fa-users w3-xxxlarge"></i></div>
        <div class="w3-right">
          <h3><?=$row1["Sum"]?></h3>
        </div>
        <div class="w3-clear"></div>
        <h4>จำนวนผู้ป่วย</h4>
      </div>
    </div>
  
         <?php
			 	 }?></th>
  </tr>
</table>
 <p>&nbsp;</p>
 <p><br />
   
   
 </p>
<table class="table table-bordered">
  <thead>
    <tr>
      <th scope="col">ลำดับ</th>
      <th scope="col">hn</th>
		<th scope="col">PDx</th>
      <th scope="col">ชื่อ-สกุล</th>
      <th scope="col">ที่อยู่</th>
      <?php 

include "condb.php";


$sql = "SELECT DISTINCT op.hn AS 'hn',CONCAT(pt.pname,'',pt.fname,' ',pt.lname) as ชื่อ,pt.informaddr as ที่อยู่ ,vn.main_pdx as pdx_1
from opitemrece op 
LEFT JOIN drugitems nd ON nd.icode=op.icode 
LEFT OUTER JOIN an_stat an on an.an=op.an
LEFT OUTER JOIN vn_stat vn on vn.vn=op.vn
LEFT JOIN patient pt on pt.hn=op.hn
WHERE op.icode in (1580001,1590005,1000156,1000406,1000412,1000413,1000430,1550025) and op.vstdate  between '".$dt1."' AND '".$dt2."'
";
$result=mysqli_query($conn,$sql);
	$result_show = mysqli_query($conn,$sql) or die(mysqli_error());
	$row_show = mysqli_fetch_array($result_show);
	$i = 1;
			   while($row=mysqli_fetch_array($result, MYSQLI_ASSOC) ){	
?>
    </tr>
  </thead>
  <tbody> 
    <tr>
      <td scope="col"><?=$i;?></td>
      <td scope="col"><?=$row["hn"]?></td>
		<td scope="col"><?=$row["pdx_1"]?></td>
      <td scope="col"><?=$row["ชื่อ"]?></td>
      <td scope="col"><?=$row["ที่อยู่"]?></td>
             <?php
			 $i++;	 }?>
    </tr>
  </tbody>
</table>

