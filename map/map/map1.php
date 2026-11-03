<?php
  $sql1 = "SELECT COUNT(DISTINCT ov.hn) AS 'sum' FROM vn_stat ov 
LEFT JOIN pttype pp ON pp.pttype=ov.pttype
LEFT JOIN ovst_community_service oc ON oc.vn=ov.vn AND oc.ovst_community_service_type_id BETWEEN 1 AND 103
LEFT JOIN patient pt ON pt.hn=ov.hn
LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart,pt.amppart,pt.tmbpart)
LEFT JOIN zbm_rpst zr ON zr.chwpart=pt.chwpart AND zr.amppart=pt.amppart AND zr.tmbpart=pt.tmbpart AND zr.moopart=pt.moopart
LEFT JOIN zbm_rpst_name zn ON zn.rpst_id=zr.rpst_id
WHERE ov.pdx LIKE 'C%' AND ov.vstdate BETWEEN '2018-10-01' AND '2019-11-31'and pt.death =\"N\";";

$result1=mysqli_query($con,$sql1);
	$result_show1 = mysqli_query($con,$sql1) or die(mysqli_error());
	$row_show1 = mysqli_fetch_array($result_show1);
	 while($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC) )
	 {	 ?>
      <?php
  $sql2 = "SELECT COUNT(DISTINCT ov.hn) AS 'sum' FROM vn_stat ov 
LEFT JOIN pttype pp ON pp.pttype=ov.pttype
LEFT JOIN ovst_community_service oc ON oc.vn=ov.vn AND oc.ovst_community_service_type_id BETWEEN 1 AND 103
LEFT JOIN patient pt ON pt.hn=ov.hn
LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart,pt.amppart,pt.tmbpart)
LEFT JOIN zbm_rpst zr ON zr.chwpart=pt.chwpart AND zr.amppart=pt.amppart AND zr.tmbpart=pt.tmbpart AND zr.moopart=pt.moopart
LEFT JOIN zbm_rpst_name zn ON zn.rpst_id=zr.rpst_id
WHERE (ov.pdx BETWEEN 'I60' AND 'I69' ) AND ov.vstdate BETWEEN '2018-10-01' AND '2019-11-31'and pt.death =\"N\";";

$result2=mysqli_query($con,$sql2);
	$result_show2 = mysqli_query($con,$sql2) or die(mysqli_error());
	$row_show2 = mysqli_fetch_array($result_show2);
	 while($row2=mysqli_fetch_array($result2, MYSQLI_ASSOC) )
	 {	 ?>
      <?php
  $sql3 = "SELECT COUNT(DISTINCT ov.hn) AS 'sum' FROM vn_stat ov 
LEFT JOIN pttype pp ON pp.pttype=ov.pttype
LEFT JOIN ovst_community_service oc ON oc.vn=ov.vn AND oc.ovst_community_service_type_id BETWEEN 1 AND 103
LEFT JOIN patient pt ON pt.hn=ov.hn
LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart,pt.amppart,pt.tmbpart)
LEFT JOIN zbm_rpst zr ON zr.chwpart=pt.chwpart AND zr.amppart=pt.amppart AND zr.tmbpart=pt.tmbpart AND zr.moopart=pt.moopart
LEFT JOIN zbm_rpst_name zn ON zn.rpst_id=zr.rpst_id
WHERE  ov.pdx LIKE 'J44%' AND ov.vstdate BETWEEN '2018-10-01' AND '2019-11-31'and pt.death =\"N\";";

$result3=mysqli_query($con,$sql3);
	$result_show3 = mysqli_query($con,$sql3) or die(mysqli_error());
	$row_show3 = mysqli_fetch_array($result_show3);
	 while($row3=mysqli_fetch_array($result3, MYSQLI_ASSOC) )
	 {	 ?>
        <?php
  $sql4 = "SELECT COUNT(DISTINCT ov.hn) AS 'sum' FROM vn_stat ov 
LEFT JOIN ovst_community_service oc ON oc.vn=ov.vn AND oc.ovst_community_service_type_id BETWEEN 1 AND 103
LEFT JOIN patient pt ON pt.hn=ov.hn
LEFT JOIN pttype pp ON pp.pttype=ov.pttype
LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart,pt.amppart,pt.tmbpart)
LEFT JOIN zbm_rpst zr ON zr.chwpart=pt.chwpart AND zr.amppart=pt.amppart AND zr.tmbpart=pt.tmbpart AND zr.moopart=pt.moopart
LEFT JOIN zbm_rpst_name zn ON zn.rpst_id=zr.rpst_id
WHERE  (ov.pdx BETWEEN 'S061' AND 'S069' ) AND ov.vstdate BETWEEN '2018-10-01' AND '2019-11-31'and pt.death =\"N\";";

$result4=mysqli_query($con,$sql4);
	$result_show4 = mysqli_query($con,$sql4) or die(mysqli_error());
	$row_show4 = mysqli_fetch_array($result_show4);
	 while($row4=mysqli_fetch_array($result4, MYSQLI_ASSOC) )
	 {	 ?>
     <?php
  $sql5 = "SELECT COUNT(DISTINCT ov.hn) AS 'sum' FROM vn_stat ov 
LEFT JOIN pttype pp ON pp.pttype=ov.pttype
LEFT JOIN ovst_community_service oc ON oc.vn=ov.vn AND oc.ovst_community_service_type_id BETWEEN 1 AND 103
LEFT JOIN patient pt ON pt.hn=ov.hn
LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart,pt.amppart,pt.tmbpart)
LEFT JOIN zbm_rpst zr ON zr.chwpart=pt.chwpart AND zr.amppart=pt.amppart AND zr.tmbpart=pt.tmbpart AND zr.moopart=pt.moopart
LEFT JOIN zbm_rpst_name zn ON zn.rpst_id=zr.rpst_id
WHERE (ov.pdx BETWEEN 'S140' AND 'S141' OR ov.pdx BETWEEN 'S240' AND 'S241' OR ov.pdx BETWEEN 'S340' AND 'S341' OR ov.pdx LIKE \"S343%\"  )   AND ov.vstdate BETWEEN '2018-10-01' AND '2019-11-31'and pt.death =\"N\";";

$result5=mysqli_query($con,$sql5);
	$result_show5 = mysqli_query($con,$sql5) or die(mysqli_error());
	$row_show5 = mysqli_fetch_array($result_show5);
	 while($row5=mysqli_fetch_array($result5, MYSQLI_ASSOC) )
	 {	 ?>
     
     <?php
  $sql6 = "SELECT COUNT(DISTINCT ov.hn) AS 'sum' FROM vn_stat ov 
LEFT JOIN ovst_community_service oc ON oc.vn=ov.vn AND oc.ovst_community_service_type_id BETWEEN 1 AND 103
LEFT JOIN patient pt ON pt.hn=ov.hn
LEFT JOIN pttype pp ON pp.pttype=ov.pttype
LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart,pt.amppart,pt.tmbpart)
LEFT JOIN zbm_rpst zr ON zr.chwpart=pt.chwpart AND zr.amppart=pt.amppart AND zr.tmbpart=pt.tmbpart AND zr.moopart=pt.moopart
LEFT JOIN zbm_rpst_name zn ON zn.rpst_id=zr.rpst_id
WHERE ov.pdx LIKE '%R54' AND ov.vstdate BETWEEN '2018-10-01' AND '2019-11-31'and pt.death =\"N\";";

$result6=mysqli_query($con,$sql6);
	$result_show6 = mysqli_query($con,$sql6) or die(mysqli_error());
	$row_show6 = mysqli_fetch_array($result_show6);
	 while($row6=mysqli_fetch_array($result6, MYSQLI_ASSOC) )
	 {	 ?>
      <?php
  $sql7 = "SELECT COUNT(DISTINCT ov.hn) AS 'sum' FROM vn_stat ov 
LEFT JOIN pttype pp ON pp.pttype=ov.pttype
LEFT JOIN ovst_community_service oc ON oc.vn=ov.vn AND oc.ovst_community_service_type_id BETWEEN 1 AND 103
LEFT JOIN patient pt ON pt.hn=ov.hn
LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart,pt.amppart,pt.tmbpart)
LEFT JOIN zbm_rpst zr ON zr.chwpart=pt.chwpart AND zr.amppart=pt.amppart AND zr.tmbpart=pt.tmbpart AND zr.moopart=pt.moopart
LEFT JOIN zbm_rpst_name zn ON zn.rpst_id=zr.rpst_id
WHERE   (ov.pdx LIKE' F03') AND ov.vstdate BETWEEN '2018-10-01' AND '2019-11-31'and pt.death =\"N\";";

$result7=mysqli_query($con,$sql7);
	$result_show7 = mysqli_query($con,$sql7) or die(mysqli_error());
	$row_show7 = mysqli_fetch_array($result_show7);
	 while($row7=mysqli_fetch_array($result7, MYSQLI_ASSOC) )
	 {	 ?>
     <?php
  $sql8 = "SELECT COUNT(DISTINCT ov.hn) AS 'sum' FROM vn_stat ov 
LEFT JOIN pttype pp ON pp.pttype=ov.pttype
LEFT JOIN ovst_community_service oc ON oc.vn=ov.vn AND oc.ovst_community_service_type_id BETWEEN 1 AND 103
LEFT JOIN patient pt ON pt.hn=ov.hn
LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart,pt.amppart,pt.tmbpart)
LEFT JOIN zbm_rpst zr ON zr.chwpart=pt.chwpart AND zr.amppart=pt.amppart AND zr.tmbpart=pt.tmbpart AND zr.moopart=pt.moopart
LEFT JOIN zbm_rpst_name zn ON zn.rpst_id=zr.rpst_id
WHERE (ov.pdx BETWEEN 'I20' AND 'I28' OR ov.pdx BETWEEN 'I30' AND 'I52') AND ov.vstdate BETWEEN'2018-10-01'AND'2019-11-31'and pt.death =\"N\";";

$result8=mysqli_query($con,$sql8);
	$result_show8 = mysqli_query($con,$sql8) or die(mysqli_error());
	$row_show8 = mysqli_fetch_array($result_show8);
	 while($row8=mysqli_fetch_array($result8, MYSQLI_ASSOC) )
	 {	 ?>
      <?php
  $sql9 = "SELECT COUNT(DISTINCT ov.hn) AS 'sum' FROM vn_stat ov 
  LEFT JOIN ovst_community_service oc ON oc.vn=ov.vn AND oc.ovst_community_service_type_id BETWEEN 1 AND 103
LEFT JOIN patient pt ON pt.hn=ov.hn
LEFT JOIN pttype pp ON pp.pttype=ov.pttype
LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart,pt.amppart,pt.tmbpart)
LEFT JOIN zbm_rpst zr ON zr.chwpart=pt.chwpart AND zr.amppart=pt.amppart AND zr.tmbpart=pt.tmbpart AND zr.moopart=pt.moopart
LEFT JOIN zbm_rpst_name zn ON zn.rpst_id=zr.rpst_id
WHERE (ov.pdx BETWEEN 'I210' AND 'I212' OR ov.pdx LIKE 'I214') AND ov.vstdate BETWEEN '2018-10-01' AND '2019-11-31'and pt.death =\"N\";";

$result9=mysqli_query($con,$sql9);
	$result_show9 = mysqli_query($con,$sql9) or die(mysqli_error());
	$row_show9 = mysqli_fetch_array($result_show9);
	 while($row9=mysqli_fetch_array($result9, MYSQLI_ASSOC) )
	 {	 ?>
               <?php
			 	 }
				 			}
			}
		}
	 }
	 }
	 }
	 }
	 }
?>