<?php
	session_name("bmhsession");
	session_start();
	
	$_SESSION["menu"] = 98;
	include "inc_header.php";
	date_default_timezone_set('Asia/Bangkok'); 
	$date = new DateTime();
?>
<?php

	$host="192.168.2.254";
	$Datauser="opd";
	$Datapass="opd";
	$Dataname="hos";
	$con=mysqli_connect($host,$Datauser,$Datapass,$Dataname)or die("Cannot connect Host");
	mysqli_query($con,"SET NAMES UTF8");

?>


<div class="page-content" >
	<div class="page-header">
		<span class="blue bolder">
			<h4>ทะเบียนผู้ป่วยที่ PCU ออกเยี่ยม</h4>
		</span>
	</div><!-- /.page-header -->
     <script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<div class="widget-body">
	  <div class="row" >
		<div class="well well">
			<div class="form-horizontal" role="form">
				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> ช่วงวันที่รับบริการ : </label>
					<div class="col-sm-2">
						<div class="input-daterange input-group">
							<input type="text" class="input-sm form-control" name="start" id="dt_sta" value="<?php echo $date->format('01/m/Y'); ?>">
							<span class="input-group-addon"> <i class="fa fa-exchange"></i> </span>
							<input type="text" class="input-sm form-control" name="end" id="dt_end" value="<?php echo $date->format('d/m/Y'); ?>">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="form-field-1-1"> หน่วยบริการ : </label>
					<div class="col-sm-2">
						<select class="input-group chosen-select form-control" id="sl_rps" data-placeholder="เลือกหน่วยบริการ...">
							<option value="">  </option>
							<option value="99999" selected>ทั้งหมด</option>
							<option value="11101">รพ.โคกศรีสุพรรณ</option>
							<option value="05569">รพสต.บ้านห้วยหีบรุ่งอรุณ</option>
							<option value="05570">รพสต.บ้านโพนค้อ</option>
							<option value="05571">รพสต.บ้านม่วงไข่น้อย</option>
							<option value="05572">รพสต.บ้านโคกนาดี</option>
							<option value="05573">รพสต.บ้านโพนทองวัฒนา</option>
							<option value="00000">นอกเขตบริการ</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="form-field-1-1"> </label>
					<div class="col-sm-2">
							<button class="btn btn-white btn-info btn-bold" id="bt_process">
								<i class="ace-icon fa fa-search bigger-120 blue"></i>
								ค้นหา
							</button>
                        					
					</div>
				</div>
			</div>
		</div>
		</div>
		<div class="row">
			<div id="load_data"></div>
		</div>
	</div>
</div><!-- /.page-content -->
<?php
	include "inc_footer.php";
?>

<script type="text/javascript">

var d1 = document.getElementById("dt_sta").value;
var d2 = document.getElementById("dt_end").value;
var d3 = document.getElementById("sl_rps").value;
	
var dt1 = d1.split('/')[2]+"-"+d1.split('/')[1]+"-"+d1.split('/')[0];
var dt2 = d2.split('/')[2]+"-"+d2.split('/')[1]+"-"+d2.split('/')[0];

$('#load_data').html("<center><br><br><br><i class='ace-icon fa fa-spinner fa-spin orange bigger-300'></i></center>");
	setTimeout(function()
	{
		$('#load_data').load("palliative_view17.php?dt1="+dt1+"&dt2="+dt2+"&dt3="+d3);
	} , (500));

$('#bt_process').click(function(e) {  
	$('#load_data').html("<center><br><br><br><i class='ace-icon fa fa-spinner fa-spin orange bigger-300'></i></center>");
	setTimeout(function()
	{
		var d1 = document.getElementById("dt_sta").value;
		var d2 = document.getElementById("dt_end").value;
		var d3 = document.getElementById("sl_rps").value;
		var dt1 = d1.split('/')[2]+"-"+d1.split('/')[1]+"-"+d1.split('/')[0];
		var dt2 = d2.split('/')[2]+"-"+d2.split('/')[1]+"-"+d2.split('/')[0];
		$('#load_data').load("palliative_view17.php?dt1="+dt1+"&dt2="+dt2+"&dt3="+d3);
			
	} , (500));
		
});



</script>
<script type="text/javascript">
			jQuery(function($) {
				
				if(!ace.vars['touch']) {
					$('.chosen-select').chosen({allow_single_deselect:true}); 
					//resize the chosen on window resize
			
					$(window)
					.off('resize.chosen')
					.on('resize.chosen', function() {
						$('.chosen-select').each(function() {
							 var $this = $(this);
							 $this.next().css({'width': $this.parent().width()});
						})
					}).trigger('resize.chosen');
					//resize chosen on sidebar collapse/expand
					$(document).on('settings.ace.chosen', function(e, event_name, event_val) {
						if(event_name != 'sidebar_collapsed') return;
						$('.chosen-select').each(function() {
							 var $this = $(this);
							 $this.next().css({'width': $this.parent().width()});
						})
					});
			
			
					$('#chosen-multiple-style .btn').on('click', function(e){
						var target = $(this).find('input[type=radio]');
						var which = parseInt(target.val());
						if(which == 2) $('#form-field-select-4').addClass('tag-input-style');
						 else $('#form-field-select-4').removeClass('tag-input-style');
					});
				}
				
				//datepicker plugin
				//link
				$('.date-picker').datepicker({
					autoclose: true,
					todayHighlight: true
				})
				//show datepicker when clicking on the icon
				.next().on(ace.click_event, function(){
					$(this).prev().focus();
				});
			
				//or change it into a date range picker
				$('.input-daterange').datepicker({
					autoclose:true,
					format: 'dd/mm/yyyy'//use this option to display seconds
				});
			
			
				//to translate the daterange picker, please copy the "examples/daterange-fr.js" contents here before initialization
				$('input[name=date-range-picker]').daterangepicker({
					'applyClass' : 'btn-sm btn-success',
					'cancelClass' : 'btn-sm btn-default',
					locale: {
						applyLabel: 'Apply',
						cancelLabel: 'Cancel',
						//format: 'DD/MM/YYYY'//use this option to display seconds
					}
				})
				.prev().on(ace.click_event, function(){
					$(this).next().focus();
				});
			
			
				$('#timepicker1').timepicker({
					minuteStep: 1,
					showSeconds: true,
					showMeridian: false,
					disableFocus: true,
					icons: {
						up: 'fa fa-chevron-up',
						down: 'fa fa-chevron-down'
					}
				}).on('focus', function() {
					$('#timepicker1').timepicker('showWidget');
				}).next().on(ace.click_event, function(){
					$(this).prev().focus();
				});
				
				
			
				
				if(!ace.vars['old_ie']) $('#date-timepicker1').datetimepicker({
				 //format: 'MM/DD/YYYY h:mm:ss A',//use this option to display seconds
				 icons: {
					time: 'fa fa-clock-o',
					date: 'fa fa-calendar',
					up: 'fa fa-chevron-up',
					down: 'fa fa-chevron-down',
					previous: 'fa fa-chevron-left',
					next: 'fa fa-chevron-right',
					today: 'fa fa-arrows ',
					clear: 'fa fa-trash',
					close: 'fa fa-times'
				 }
				}).next().on(ace.click_event, function(){
					$(this).prev().focus();
				});
			
			
			})
			Highcharts.chart('container', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'กราฟแท่งแสดงข้อมูลทะเบียนผู้ป่วยหัวใจขาดเลือกเฉียบพลันแยกตาม รพสต.'
    },
    subtitle: {
        text: 'Source: <a href="http://en.wikipedia.org/wiki/List_of_cities_proper_by_population">Wikipedia</a>'
    },
    xAxis: {
        type: 'category',
        labels: {
            rotation: -45,
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'จำนวนผู้ป่วย (ราย)'
        }
    },
    legend: {
        enabled: false
    },
    tooltip: {
        pointFormat: 'ผู้ป่วยจำนวนคน: <b>{point.y:.1f} ราย</b>'
    },
    series: [{
        name: 'Population',
        data: [
  <?php 
				$sql = "SELECT Concat(p.pname, p.fname, '  ', p.lname) AS ptname,
pt.cid,
pp.name,
  pt.hn,
	pt.birthday,
	pt.deathday,
  p.age_y,
  pvt.*,
  pv.*,
  Concat('บ้านเลขที่', ' ', pt.addrpart, ' ', 'หมู่ที่', ' ', pt.moopart, ' ', 'ต.', ' ', t1.name, '  ', 'อ.', t2.name, ' ', t3.name) AS pt_address,
	(SELECT ov.pdx FROM vn_stat ov1 WHERE ov1.vn=ov.vn) AS C ,
	if(ov.pdx ='Z515' OR ov.dx0 ='Z515' OR ov.dx1 ='Z515' OR ov.dx1 ='Z515' OR ov.dx2 ='Z515' OR ov.dx3 ='Z515' OR ov.dx4 ='Z515' OR ov.dx5 ='Z515','Z515',NULL) AS Z 
	
FROM person_visit pv
  LEFT OUTER JOIN person p ON p.person_id = pv.person_id
  LEFT OUTER JOIN patient pt ON pt.hn = p.patient_hn
  LEFT OUTER JOIN thaiaddress t1 ON t1.chwpart = pt.chwpart AND t1.amppart = pt.amppart AND t1.tmbpart = pt.tmbpart AND t1.codetype = '3'
  LEFT OUTER JOIN thaiaddress t2 ON t2.chwpart = pt.chwpart AND t2.amppart = pt.amppart AND t2.codetype = '2'
  LEFT OUTER JOIN thaiaddress t3 ON t3.chwpart = pt.chwpart AND t3.codetype = '1'
  LEFT OUTER JOIN person_visit_type pvt ON pv.person_visit_type_id = pvt.person_visit_type_id
	LEFT JOIN pttype pp ON pp.pttype=pt.pttype 
	LEFT JOIN vn_stat ov on ov.vstdate = visit_date and pt.hn = ov.hn
	 
WHERE pv.visit_date BETWEEN '2018-10-01' AND '2019-09-30'
ORDER BY pv.visit_date,
  pv.person_visit_id ";
				$query=mysqli_query($con,$sql);
				while($result=mysqli_fetch_array($query)){
				?>

                ['<?=$result["rpst_name"]?>', <?=$result["cc"]?>],
				<?php }?>
        ],
        dataLabels: {
            enabled: true,
            rotation: -90,
            color: '#FFFFFF',
            align: 'right',
            format: '{point.y:.1f}', // one decimal
            y: 10, // 10 pixels down from the top
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    }]
});

</script>
