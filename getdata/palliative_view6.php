 

 <p>&nbsp;</p>
 <p><br />
   
   
 </p>
<table class="table table-sm table-hover">
  <thead>
    <tr>
      <th scope="col">ลำดับ</th>
      <th scope="col">hn</th>
		<th scope="col">PDx</th>
      <th scope="col">ชื่อ-สกุล</th>
		<th scope="col">อายุ</th>
      <th scope="col">ที่อยู่</th>
      <?php 

include "condb.php";


$sql = "select DISTINCT o.hn AS 'hn',CONCAT(pt.pname,'',pt.fname,' ',pt.lname) as ชื่อ,pt.informaddr as ที่อยู่, v.main_pdx as pdx, v.age_y as age1
from ovstdiag o
left outer join vn_stat v on v.vn=o.vn
LEFT JOIN patient pt on pt.hn=o.hn
WHERE o.icd10 in (\"Z515\") and v.vstdate between '".$dt1."' AND '".$dt2."' order BY v.main_pdx
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
      <td ><?=$i;?></td>
      <td scope="col"><?=$row["hn"]?></td>
		<td scope="col"><?=$row["pdx"]?></td>
      <td scope="col"><?=$row["ชื่อ"]?></td>
		<td scope="col"><?=$row["age1"]?></td>
      <td scope="col"><?=$row["ที่อยู่"]?></td>

             <?php
			 $i++;	 }?>
    </tr>
  </tbody>
</table>

