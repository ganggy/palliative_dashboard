<?php 
$hn = $_GET["hn"];
include "condb.php";

$sql_t ="SELECT pt.hn,pt.cid,CONCAT(pt.pname,pt.fname,' ',pt.lname) AS ptname,CONCAT(pt.addrpart,' หมู่ ',pt.moopart,' ',th.full_name) AS addr,zn.rpst_name,zn.rpst_id
FROM patient pt 
LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart,pt.amppart,pt.tmbpart)
LEFT JOIN zbm_rpst zr ON zr.chwpart=pt.chwpart AND zr.amppart=pt.amppart AND zr.tmbpart=pt.tmbpart AND zr.moopart=pt.moopart
LEFT JOIN zbm_rpst_name zn ON zn.rpst_id=zr.rpst_id
WHERE pt.hn='".$hn."' LIMIT 1";
$result_t = $conn->query($sql_t);
$row_t = $result_t->fetch_assoc();

?>

<div class="modal fade" id="modal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title blue">HN: <?php echo $hn; ?></h4>
			</div>
			<div class="modal-body">
<div class="profile-user-info profile-user-info-striped">
	<div class="profile-info-row">
		<div class="profile-info-name"> เลขบัตรประชาชน </div>
		<div class="profile-info-value">
			<?php echo $row_t["cid"]; ?>
		</div>
	</div>
	<div class="profile-info-row">
		<div class="profile-info-name"> ชื่อ - สกุล ผู้ป่วย </div>
		<div class="profile-info-value">
			<?php echo $row_t["ptname"]; ?>
		</div>
	</div>
	<div class="profile-info-row">
		<div class="profile-info-name"> ที่อยู่ </div>
		<div class="profile-info-value">
			<?php echo $row_t["addr"]; ?>
		</div>
	</div>

	<div class="profile-info-row">
		<div class="profile-info-name"> เขตรับผิดชอบ </div>
		<div class="profile-info-value">
			<?php echo $row_t["hname"]; ?>
		</div>
	</div>
</div>
<div class="space-12"></div>
<div class="widget-box transparent">
	<div class="widget-header widget-header-small">
		<h4 class="widget-title blue smaller">
			<i class="ace-icon fa fa-list orange"></i>
			การเบิกเงินจากโปรแกรม E-Claim
		</h4>
	</div>
	<div class="widget-body">
		<div class="widget-main padding-8">
			<div id="profile-feed-1" class="profile-feed">
			
			<?php
			$sql = "SELECT r.ECLAIM_NO,r.REP,r.PDX,r.FILENAME_SEND,p.CHILD_DOB,r.DATETIMEADJ
			,(SELECT s.PallativeCare FROM rcmdb.repeclaim s WHERE s.HN=r.HN AND s.Rep=r.REP) AS money 
			FROM eclaimdb.m_registerdata r 
			LEFT JOIN eclaimdb.m_ppcom p ON p.ECLAIM_NO=r.ECLAIM_NO
			WHERE NOT ISNULL(p.ECLAIM_NO) AND p.GR_ITEMNAME='3 Palliative Care' AND r.HN='".$hn."' AND NOT ISNULL(r.REP) ORDER BY r.DATETIMEADJ DESC";
		//	echo $sql;
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {

				echo '<div class="profile-activity clearfix">';
				echo '	<div class="clearfix">';
				echo '		<div class="row">';
				echo '			<div class="col-xs-12">';
				echo '				<div class="profile-user-info profile-user-info-striped">';
				echo '					<div class="profile-info-row">';
				echo '						<div class="profile-info-name"> วันที่ </div>';
				echo '						<div class="profile-info-value">';
				echo '							'.DateThai2($row["DATETIMEADJ"]).'';
				echo '						</div>';
				echo '					</div>';
				echo '					<div class="profile-info-row">';
				echo '						<div class="profile-info-name"> ECLAIM_NO </div>';
				echo '						<div class="profile-info-value">';
				echo '							'.$row["ECLAIM_NO"].'';
				echo '						</div>';
				echo '					</div>';
				echo '					<div class="profile-info-row">';
				echo '						<div class="profile-info-name"> วินิจฉัย </div>';
				echo '						<div class="profile-info-value">';
				echo '							<b>'.$row["PDX"].'</b>';
				echo '						</div>';
				echo '					</div>';
				echo '					<div class="profile-info-row">';
				echo '						<div class="profile-info-name"> FILENAME_SEND </div>';
				echo '						<div class="profile-info-value">';
				echo '							'.$row["FILENAME_SEND"].'';
				echo '						</div>';
				echo '					</div>';
				echo '					<div class="profile-info-row">';
				echo '						<div class="profile-info-name"> วันที่เยี่ยมครั้งแรก </div>';
				echo '						<div class="profile-info-value">';
				echo '							'.DateThai($row["CHILD_DOB"]).'';
				echo '						</div>';
				echo '					</div>';
				echo '					<div class="profile-info-row">';
				echo '						<div class="profile-info-name"> จ่ายเงิน </div>';
				echo '						<div class="profile-info-value">';
				echo '							'.$row["money"].'';
				echo '						</div>';
				echo '					</div>';
				echo '				</div>';
				echo '			</div>';
				echo '		</div>';
				echo '	</div>';
				echo '</div>';
				
				}
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
			function DateThai2($strDate)
			{
				$strYear = substr($strDate,0,4);
				$strMonth= substr($strDate,4,2);
				$strDay= substr($strDate,6,2);
				$strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
				$strMonthThai=$strMonthCut[$strMonth*1];
				return "$strDay $strMonthThai $strYear";
			}
?>
</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

	$('#modal').modal('show');

</script>
