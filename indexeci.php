
<?php
	session_start();
	$_SESSION["menu"] = 20;
//	include "include/header.php";
	date_default_timezone_set('Asia/Bangkok'); 
	$date = new DateTime();

	include "condb.php";
	include "inc_header.php";
?>
			<div class="main-content">
				<div class="main-content-inner">
					<div class="breadcrumbs ace-save-state" id="breadcrumbs">
						<div class="breadcrumb">
							<h4 class=""><b>ทะเบียนผู้ป่วยส่งเบิก E-Claim ที่ได้รับเงินแล้ว</b></h4>
						</div><!-- /.breadcrumb -->
					</div>
					<div class="page-content">
						<div class="row">
							<div class="col-xs-12">
								<table id="dynamic-table" class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
											
											<th><center>HN</center></th>
											<th><center>เลขบัตรประชาชน</center></th>
											<th><center>ชื่อ - สกุล</center></th>
											<th><center>จำนวนเงิน</center></th>
											<th><center>วันที่ได้รับเงิน</center></th>
											<th><center>REP</center></th>
										</tr>
									</thead>
									<tbody>
										<?php  
										$sql = "SELECT * FROM rcmdb.repeclaim_copy  WHERE PallativeCare>0 ORDER BY Rep desc";
										$result = $conn_ec->query($sql);
										if ($result->num_rows > 0) {
											while($row = $result->fetch_assoc()) {				
												echo "<tr>";
												echo "	<td align='center'>".$row["HN"]."</td>";
												echo "	<td align='center'>".$row["PID"]."</td>";
												echo "	<td>".$row["PtNAme"]."</td>";
												echo "	<td align='center'class='bg-success'>".$row["PallativeCare"]."</td>";
												echo "	<td align='center'>".$row["AdmDate"]."</td>";
												echo "	<td align='center'>".$row["Rep"]."</td>";
												echo "</tr>";
											}
										}
										
										function DateThai($strDate)
										{
											if($strDate=="") return "-";
											else{
												$strYear = date("Y",strtotime($strDate))+543;
												$strMonth= date("n",strtotime($strDate));
												$strDay= date("j",strtotime($strDate));
												$strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
												$strMonthThai=$strMonthCut[$strMonth];
												return "$strDay $strMonthThai $strYear";
											}
										}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->
			
			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
			</a>
		</div><!-- /.main-container -->

<?php 
	include "inc_footer.php";
?>
		<script type="text/javascript">
			jQuery(function($) {
				var myTable = 
					$('#dynamic-table')
					.DataTable( {
						bAutoWidth: false,
						"aoColumns": [
							{ "bSortable": false },
							{ "bSortable": false },
							{ "bSortable": false },
							{ "bSortable": false },
							{ "bSortable": false },
							{ "bSortable": false }
						],
						"aaSorting": [],
						"iDisplayLength": 100,
					});				
			});
		</script>
	</body>
</html>
