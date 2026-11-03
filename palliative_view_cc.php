<?php 
$hn = $_GET["hn"];
$dt1 = $_GET["dt1"];
$dt2 = $_GET["dt2"];
include "condb.php";

$sql_t ="SELECT pt.hn,pt.cid,CONCAT(pt.pname,pt.fname,' ',pt.lname) AS ptname,CONCAT(pt.addrpart,' หมู่ ',pt.moopart,' ',th.full_name) AS addr,zn.rpst_name
FROM patient pt 
LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart,pt.amppart,pt.tmbpart)
LEFT JOIN zbm_rpst zr ON zr.chwpart=pt.chwpart AND zr.amppart=pt.amppart AND zr.tmbpart=pt.tmbpart AND zr.moopart=pt.moopart
LEFT JOIN zbm_rpst_name zn ON zn.rpst_id=zr.rpst_id
WHERE pt.hn='".$hn."' LIMIT 1";
$result_t = $conn->query($sql_t);
if ($result_t->num_rows > 0) {
	$row_t = $result_t->fetch_assoc();
}

$sql = "SELECT *from (select rx.vstdate as ovdate,rx.rxtime,concat(du.name1,' ',du.name2,' ',du.name3) as drugu, 
 concat(d.name,' ',d.strength,' ',d.units) as drugname  
 FROM ovst ov 
 LEFT JOIN opitemrece rx on rx.vn=ov.vn 
 INNER JOIN drugitems d on d.icode=rx.icode 
 LEFT JOIN drugusage du on du.drugusage=rx.drugusage 
 where ov.vn = \"600803130258\"
UNION 
select rx.vstdate as ovdate,rx.rxtime,concat(du.name1,' ',du.name2,' ',du.name3) as drugu, 
 concat(d.name,' ',d.strength,' ',d.units) as drugname  
 FROM an_stat ov 
 LEFT JOIN opitemrece rx on rx.an=ov.an 
LEFT OUTER JOIN an_stat an on an.an=rx.an
 INNER JOIN drugitems d on d.icode=rx.icode 
 LEFT JOIN drugusage du on du.drugusage=rx.drugusage 
 where ov.vn = \"600803130258\") as tb  ORDER BY ovdate,rxtime DESC";
?>


<div id="dialog-message" class="hide">
	<div class="profile-user-info profile-user-info-striped">
		<div class="profile-info-row">
			<div class="profile-info-name"> ผู้ป่วย </div>
				<div class="profile-info-value">
					<div class="row">
						<div class="col-xs-2">
							<i class="fa fa-address-card light-green bigger-110"></i><span class='blue bolder'> <?php echo $row_t["hn"]; ?></span>
						</div>
						<div class="col-xs-5">
							<i class="fa fa-id-card light-green bigger-110"></i><span class='blue bolder'> <?php echo $row_t["cid"]; ?></span>
						</div>

						<div class="col-xs-5">
							<i class="fa fa-user-circle light-green bigger-110"></i> <span class='blue bolder'> <?php echo $row_t["ptname"]; ?></span>			
						</div>
					</div>
				</div>
		</div>
		<div class="profile-info-row">
			<div class="profile-info-name"> ที่อยู่ </div>
			<div class="profile-info-value">
				<div class="row">
						<div class="col-xs-7">
							<i class="fa fa-map-marker light-orange bigger-110"></i>
							<span class='blue bolder'> <?php echo $row_t["addr"]; ?></span>
						</div>
						<div class="col-xs-5">
							<i class="fa fa-map-signs light-orange bigger-110"></i>
							<span class='blue bolder'> <?php echo $row_t["rpst_name"]; ?></span>			
						</div>
					</div>
			</div>
		</div>
	</div>
	<div class="widget-box transparent">
		<div class="widget-header widget-header-small">
			<h4 class="widget-title blue smaller">
				<i class="ace-icon fa fa-list orange"></i>
				ประวัติการรับยา
			</h4>
		</div>
		<div class="widget-body">
			<div class="widget-main padding-8">
				<div id="profile-feed-1" class="profile-feed">
				
				<?php
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {

					echo '<div class="profile-activity clearfix">';
					echo '	<div class="clearfix">';
					echo '		<div class="row">';
					echo '			<div class="col-xs-4">';
					echo '			<div class="col-xs-8">';
					echo '				<div><span class="red bolder">ชื่อยา : </span> '.$row["drugname"].'</div>';
					echo '				<div><span class="red bolder">วิธีรับประทานยา : </span> '.$row["drugu"].'</div>';
					echo '				<div class="pull-left">';
										$lists = explode(",", $row["csname"]);
										foreach($lists as $list) {
										 echo "<li>" . $list . "</li>";
										}
					echo '				</div>';
					echo '			</div>';
					echo '		</div>';
					echo '	</div>';
					echo '</div>';
					
					}
				}
function SePPS($hpi) {
	
    if (strpos($hpi, 'PPS = 10 %') || strpos($hpi, '10%') || strpos($hpi, '10 %')) $rss="PPS=10%";
	else if (strpos($hpi, 'PPS = 20 %') || strpos($hpi, '20%') || strpos($hpi, '20 %')) $rss="PPS=20%";
	else if (strpos($hpi, 'PPS = 30 %') || strpos($hpi, '30%') || strpos($hpi, '30 %')) $rss="PPS=30%";
	else if (strpos($hpi, 'PPS = 40 %') || strpos($hpi, '40%') || strpos($hpi, '40 %')) $rss="PPS=40%";
	else if (strpos($hpi, 'PPS = 50 %') || strpos($hpi, '50%') || strpos($hpi, '50 %')) $rss="PPS=50%";
	else if (strpos($hpi, 'PPS = 60 %') || strpos($hpi, '60%') || strpos($hpi, '60 %')) $rss="PPS=60%";
	else if (strpos($hpi, 'PPS = 70 %') || strpos($hpi, '70%') || strpos($hpi, '70 %')) $rss="PPS=70%";
	else if (strpos($hpi, 'PPS = 80 %') || strpos($hpi, '80%') || strpos($hpi, '80 %')) $rss="PPS=80%";
	else if (strpos($hpi, 'PPS = 90 %') || strpos($hpi, '90%') || strpos($hpi, '90 %')) $rss="PPS=90%";
	else $rss="PPS= -";
    return $rss;
}
function SePS($hpi) {
    if (strpos($hpi, 'PS = 1/10') || strpos($hpi, '1/10')) $rss="PS=1/10";
	else if (strpos($hpi, 'PS = 2/10') || strpos($hpi, '2/10')) $rss="PS=2/10";
	else if (strpos($hpi, 'PS = 3/10') || strpos($hpi, '3/10')) $rss="PS=3/10";
	else if (strpos($hpi, 'PS = 4/10') || strpos($hpi, '4/10')) $rss="PS=4/10";
	else if (strpos($hpi, 'PS = 5/10') || strpos($hpi, '5/10')) $rss="PS=5/10";
	else if (strpos($hpi, 'PS = 6/10') || strpos($hpi, '6/10')) $rss="PS=6/10";
	else if (strpos($hpi, 'PS = 7/10') || strpos($hpi, '7/10')) $rss="PS=7/10";
	else if (strpos($hpi, 'PS = 8/10') || strpos($hpi, '8/10')) $rss="PS=8/10";
	else if (strpos($hpi, 'PS = 9/10') || strpos($hpi, '9/10')) $rss="PS=9/10";
	else $rss="PS= -";
    return $rss;
}
function SeBed($hpi) {
    if (strpos($hpi, 'เตียง1') || strpos($hpi, 'เตียง 1') || strpos($hpi, 'เตียง=1') || strpos($hpi, 'เตียง = 1') || strpos($hpi, 'เตียง= 1') || strpos($hpi, 'เตียง =1')) $rss="เตียง 1";
	else if (strpos($hpi, 'เตียง2') || strpos($hpi, 'เตียง 2') || strpos($hpi, 'เตียง=2') || strpos($hpi, 'เตียง = 2') || strpos($hpi, 'เตียง= 2') || strpos($hpi, 'เตียง =2')) $rss="เตียง 2";
	else if (strpos($hpi, 'เตียง3') || strpos($hpi, 'เตียง 3') || strpos($hpi, 'เตียง=3') || strpos($hpi, 'เตียง = 3') || strpos($hpi, 'เตียง= 3') || strpos($hpi, 'เตียง =3')) $rss="เตียง 3";
	else if (strpos($hpi, 'เตียง4') || strpos($hpi, 'เตียง 4') || strpos($hpi, 'เตียง=4') || strpos($hpi, 'เตียง = 4') || strpos($hpi, 'เตียง= 4') || strpos($hpi, 'เตียง =4')) $rss="เตียง 4";
	else $rss="เตียง= -";
    return $rss;
}
function SeBi($hpi) {
	for($i=100;$i>=0;$i--){
		if (strpos(strtolower($hpi), 'bi'.$i) || strpos(strtolower($hpi), 'bi '.$i) || strpos(strtolower($hpi), 'bi  '.$i) || strpos(strtolower($hpi), 'bi='.$i) || strpos(strtolower($hpi), 'bi = '.$i) || strpos(strtolower($hpi), 'bi= '.$i) || strpos(strtolower($hpi), 'bi ='.$i))
		{ 
			$ress="BI = ".$i;
			
		 	break;
		}
		else $ress="BI = -";		
	}
		
	return $ress;
	//return "test".$i;
}
function DateThai($strDate)
				{
					$strYear = date("Y",strtotime($strDate))+543;
					$strMonth= date("n",strtotime($strDate));
					$strDay= date("j",strtotime($strDate));
					$strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
					$strMonthThai=$strMonthCut[$strMonth];
					return "$strDay $strMonthThai $strYear";
				}
?>
				</div>
			</div>
		</div>
	</div>
</div><!-- #dialog-message -->
<script type="text/javascript">

		var dialog = $( "#dialog-message" ).removeClass('hide').dialog({
			width: '800',
			modal: true,
			title: "ข้อมูลการเยี่ยมบ้าน",
			title_html: true,
			buttons: [ 
			{
				text: "ปิดหน้านี้",
				"class" : "btn btn-minier",
				click: function() {
					$( this ).dialog( "close" ); 
				} 
			}]
		});
	
</script>
