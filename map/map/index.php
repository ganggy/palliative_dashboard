<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<?php		 
$dbserver = '192.168.0.254';
$dbuser = 'hosbm2011' ; 
$dbpass= "bmhos2554";
$dbname= 'hos';
error_reporting(E_ALL ^ E_NOTICE);
$con=mysqli_connect($dbserver, $dbuser, $dbpass) or die("เชื่อมต่อฐานข้อมูลไม่ได้ ");
mysqli_select_db($con,$dbname) or die("เลือกฐานข้อมูลไม่ได้"); // เลือกฐานข้อมูล
mysqli_query($con,"SET NAMES UTF8");
if (!$_GET['xxx'])  //ถ้ามีี $get xxx เข้ามาไม่ต้องรีโหลด ดังนั้นเราจะทำการโหลดได้แค่ครั้งเดียว
{
//echo "<meta http-equiv='refresh' content='0;URL=index.php?xxx=1'>";
}
?>
<head>
<style>
html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, font, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td {
	margin: 0;
	padding: 0;
	border: 0;
	outline: 0;
	font-size: 100%;
	vertical-align: baseline;
	background: transparent;
}
.img_left{ float:left; margin-right:5px; margin-bottom:5px; border:1px dotted #999999; background-color:#f2f2f2; padding:2px;}
.cls{ clear:both;}
.font_map{ font-family:Tahoma, Arial, serif; font-size:13px;}
div#map_canvas { content: width:80%; height:100%; }
 #floating-panel {
	position: absolute;
	top: 171px;
	left: 5px;
	z-index: 5;
	background-color: #fff;
	padding: 5px;
	border: 0.5px solid #999;
	text-align: center;
	font-family: 'Roboto','sans-serif';
	line-height: 30px;
	padding-left: 10px;
	width: 215px;
	height: 278px;
	box-shadow: #999
}
<style>
html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, font, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td {
	margin: 0;
	padding: 0;
	border: 0;
	outline: 0;
	font-size: 100%;
	vertical-align: baseline;
	background: transparent;
}
.img_left{ float:left; margin-right:5px; margin-bottom:5px; border:1px dotted #999999; background-color:#f2f2f2; padding:2px;}
.cls{ clear:both;}
.font_map{ font-family:Tahoma, Arial, serif; font-size:13px;}
div#map_canvas { content: width:80%; height:100%; }
 #floating-panel1 {
	position: absolute;
	top: 504px;
	left: 6px;
	z-index: 5;
	background-color: #fff;
	padding: 5px;
	border: 0.5px solid #999;
	text-align: center;
	font-family: 'Roboto','sans-serif';
	line-height: 30px;
	padding-left: 10px;
	width: 313px;
	height: 300px;
	box-shadow: #999
}
</style>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAbUirp-8aEH-i9K3Zxn4WCzPSvURlV1bc&callback=map&sensor=false" type="text/javascript"></script>
<script type="text/javascript" src="http://www.cyberthai.net/gmap3.js"></script> 

 <script type="text/javascript"></script>
<style>

</style>
 </head>
 <?php
   $sql15 = "SELECT COUNT(DISTINCT ov.hn) as cc,zn.rpst_name
FROM vn_stat ov 
LEFT JOIN ovst_community_service oc ON oc.vn=ov.vn AND oc.ovst_community_service_type_id BETWEEN 1 AND 103
LEFT JOIN patient pt ON pt.hn=ov.hn
LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart,pt.amppart,pt.tmbpart)
LEFT JOIN zbm_rpst zr ON zr.chwpart=pt.chwpart AND zr.amppart=pt.amppart AND zr.tmbpart=pt.tmbpart AND zr.moopart=pt.moopart
LEFT JOIN zbm_rpst_name zn ON zn.rpst_id=zr.rpst_id
WHERE (ov.pdx ='Z515' OR ov.dx0 ='Z515' OR ov.dx1 ='Z515' OR ov.dx2 ='Z515' OR ov.dx3 ='Z515' OR ov.dx4 ='Z515' OR ov.dx5 ='Z515')
AND ov.vstdate BETWEEN '2018-10-01' AND '2019-05-31'GROUP BY zn.rpst_id ORDER BY ov.vstdate DESC";

$result15=mysqli_query($con,$sql15);
	$result_show15 = mysqli_query($con,$sql15) or die(mysqli_error());
	$row_show15 = mysqli_fetch_array($result_show15);
	 while($row15=mysqli_fetch_array($result15, MYSQLI_ASSOC) )
	 {	 ?>
 <?
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
			 	 }?>
<body>
<div id="floating-panel" class="">
 <div class="badge badge-primary text-wrap" style="width: 10rem;">
   <p><strong><br>

     จำนวนผู้ป่วยแยกตาม Diac.</strong><br>
   </p>
   <p>&nbsp;</p>
 </div>
 <table width="100%" border="0">
    <tr>
      <td align="left" bgcolor="#66CC99" scope="row">ทะเบียน มะเร็ง</td>
      <td><div class="badge badge-success text-wrap" style="width: 3rem;"><?=$row1["sum"]?></div></td>
    </tr>
    <tr>
      <td align="left" scope="row">ทะเบียน Stroke</td>
      <td><div class="badge badge-success text-wrap" style="width: 3rem;"><?=$row2["sum"]?></div></td>
    </tr>
    <tr>
      <td align="left" scope="row">ทะเบียน COPD </td>
      <td><div class="badge badge-success text-wrap" style="width: 3rem;"><?=$row3["sum"]?></div></td>
    </tr>
    <tr>
      <td align="left" scope="row">ทะเบียน TBI</td>
      <td><div class="badge badge-success text-wrap" style="width: 3rem;"><?=$row4["sum"]?></div></td>
    </tr>
    <tr>
      <td align="left" scope="row">ทะเบียน SCI</td>
      <td><div class="badge badge-success text-wrap" style="width: 3rem;"><?=$row5["sum"]?></div></td>
    </tr>
    <tr>
      <td align="left" scope="row">ทะเบียน Seniriy</td>
      <td><div class="badge badge-success text-wrap" style="width: 3rem;"><?=$row6["sum"]?></div></td>
    </tr>
    <tr>
      <td align="left" scope="row">ทะเบียน Dementia</td>
      <td><div class="badge badge-success text-wrap" style="width: 3rem;"><?=$row7["sum"]?></div></td>
    </tr>
    <tr>
      <td align="left" scope="row">ทะเบียน ACS</td>
      <td><div class="badge badge-success text-wrap" style="width: 3rem;"><?=$row8["sum"]?></div></td>
      
    </tr>
  </table>
</div>
<div id="floating-panel1" class="">
<table width="100%" border="0">
<thead>
  <tr>
    <th colspan="2" align="center" bgcolor="#00CC33" scope="col">จำนวนผู้ป่วยแต่ล่ะ รพสต.</th>
    </tr>
    <thead>
     <?php while($row15=mysqli_fetch_array($result15, MYSQLI_ASSOC) ){?>
  <tr>
    <th width="54%" align="right" scope="col"><?=$row15["cc"]?></th>
    <th width="46%" align="left" scope="col"><?=$row15["rpst_name"]?></th>
  </tr>
   <tbody>
   <?php } ?>
</table>
      
</div>
<div id="map_canvas"></div>
<script>

        $(function () {
            $('#map_canvas').gmap3({
                map: {
                    options: {
                        center: [17.850128, 103.569960],
                        zoom: 15,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        mapTypeControl: true,
                        mapTypeControlOptions: {
                            style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                        },
                    }
                },
                marker: {
                    values: [
					
<?php
$sql = mysqli_query($con,"SELECT MAX(ov.vstdate) AS vstdate,pp.`name`,ov.hn,pt.cid,CONCAT(pt.pname,pt.fname,' ',pt.lname) AS ptname,pt.birthday,(YEAR(NOW()) -YEAR(pt.birthday)) AS age
,CONCAT(pt.addrpart,' หมู่ ',pt.moopart,' ',th.full_name) AS addr,zn.rpst_name,zn.rpst_id
,(SELECT vn.pdx FROM vn_stat vn WHERE vn.vn=ov.vn) AS C 
,if(ov.pdx ='Z515' OR ov.dx0 ='Z515' OR ov.dx1 ='Z515' OR ov.dx1 ='Z515' OR ov.dx2 ='Z515' OR ov.dx3 ='Z515' OR ov.dx4 ='Z515' OR ov.dx5 ='Z515','Z515',NULL) AS Z 
,if(ISNULL(pt.death) OR pt.death='','N',pt.death) AS death
,(SELECT COUNT(*) AS dayc FROM ovst_community_service a1 INNER JOIN vn_stat vn ON vn.vn=a1.vn WHERE vn.hn=ov.hn AND a1.ovst_community_service_type_id BETWEEN 1 AND 103) AS dayc
,DATEDIFF(NOW(),(SELECT MAX(a1.entry_datetime)FROM ovst_community_service a1 INNER JOIN vn_stat vn ON vn.vn=a1.vn WHERE vn.hn=ov.hn AND a1.ovst_community_service_type_id BETWEEN 1 AND 103)) AS daym,h.latitude,h.longitude 
FROM vn_stat ov 
LEFT JOIN pttype pp ON pp.pttype=ov.pttype
LEFT JOIN ovst_community_service oc ON oc.vn=ov.vn AND oc.ovst_community_service_type_id BETWEEN 1 AND 103
LEFT JOIN patient pt ON pt.hn=ov.hn
LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart,pt.amppart,pt.tmbpart)
LEFT JOIN zbm_rpst zr ON zr.chwpart=pt.chwpart AND zr.amppart=pt.amppart AND zr.tmbpart=pt.tmbpart AND zr.moopart=pt.moopart
LEFT JOIN zbm_rpst_name zn ON zn.rpst_id=zr.rpst_id
LEFT JOIN person p on p.cid=pt.cid
LEFT JOIN house h on h.house_id=p.house_id
WHERE (ov.pdx ='Z515' OR ov.dx0 ='Z515' OR ov.dx1 ='Z515' OR ov.dx2 ='Z515' OR ov.dx3 ='Z515' OR ov.dx4 ='Z515' OR ov.dx5 ='Z515')
AND ov.vstdate BETWEEN '2018-10-01' AND '2019-10-31' GROUP BY ov.hn ORDER BY ov.vstdate DESC
");
$num = mysqli_num_rows($sql);
if ($num>0){
	while ($r=mysqli_fetch_array($sql))	{
		++$i;
		$i != $num ? $k=',' : $k='';
?>
{latLng:[<?php echo $r[latitude]?>, <?php echo $r[longitude]?>], data:"<div class='font_map'><img src='<?php echo $r[Pic]?>' width='75' height='75' alt='<?php echo $r[census_id]?>' class='img_left' /><strong><a href='#' title='<?php echo $r[census_id]?>' target='_blank'><?php echo $r[ptname]?></a></strong><br /><br /><b>ที่อยู่ :</b></font><?php echo $r[addr]?><br /><b>รหัสโรค :</b><?php echo $r[C]?><div class='cls'></div><a href='#' title='<?php echo $r[census_id]?>' target='_blank'>ดูที่เหลือ</a></div>", options:{icon: "https://cdn.icon-icons.com/icons2/152/PNG/64/local_21769.png"}}<?php echo $k?>
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
	 }
	 
	 }
	 
	 
?>

                    ],
                    events: {
                        mouseover: function (marker, event, context) {
                            var map = $(this).gmap3("get"),
                                infowindow = $(this).gmap3({
                                    get: {
                                        name: "infowindow"
                                    }
                                });
                            if (infowindow) {
                                infowindow.open(map, marker);
                                infowindow.setContent(context.data);
                            } else {
                                $(this).gmap3({
                                    infowindow: {
                                        anchor: marker,
                                        options: {
                                            content: context.data
                                        }
                                    }
                                });
                            }
                        },
                        closeclick: function () {
                            infowindow.close();
                        },
                        mouseout: function () {
                            var infowindow = $(this).gmap3({
                                get: {
                                    name: "infowindow"
                                }
                            });
                        }
                    }
                }
            });
        });
	

		
    </script>
  
</body>



