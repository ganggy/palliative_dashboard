<?php
	session_name("bmhsession");
	session_start();
	
	$_SESSION["menu"] = 12;
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
			<h4>ทะเบียนผู้ป่วย ทั้งหมด</h4>
		</span>
	</div><!-- /.page-header -->			
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
						<?php include "Serviceunit.php"; ?>
					</div>

				</div>
                				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="form-field-1-1"> ประเภทการเยี่ยม : </label>
					<div class="col-sm-2">
						<select class="input-group chosen-select form-control" id="sl_rps2" data-placeholder="เลือกประเภทบริการ...">
							<option value="">  </option>
							<option value="99999" selected>ทั้งหมด</option>
							<option value="1">1A000:เยี่ยมผู้ป่วยโรคความดันโลหิตสูง</option>
							<option value="2">1A001:เยี่ยมผู้ป่วยโรคเบาหวาน</option>
							<option value="3">1A002:เยี่ยมผู้ป่วยโรคมะเร็ง</option>
							<option value="4">1A003:เยี่ยมผู้ป่วยโรคระบบทางเดินหายใจ</option>
							<option value="5">1A004:เยี่ยมผู้ป่วยโรคสมองเสื่อม</option>
							<option value="6">1A005:เยี่ยมผู้ป่วยโรคหลอดเลือดสมอง</option>
							<option value="7">1A008:เยี่ยมผู้ป่วยโรคเรื้อรังอื่น ๆ</option>
							<option value="8">1A009:เยี่ยมผู้ป่วยโรคเรื้อรังที่ไม่ระบุรายละเอียด</option>
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
var d4 = document.getElementById("sl_rps2").value;
	
var dt1 = d1.split('/')[2]+"-"+d1.split('/')[1]+"-"+d1.split('/')[0];
var dt2 = d2.split('/')[2]+"-"+d2.split('/')[1]+"-"+d2.split('/')[0];

$('#load_data').html("<center><br><br><br><i class='ace-icon fa fa-spinner fa-spin orange bigger-300'></i></center>");
	setTimeout(function()
	{
		$('#load_data').load("palliative_view10.php?dt1="+dt1+"&dt2="+dt2+"&dt3="+d3+"&dt4="+d4);
	} , (500));

$('#bt_process').click(function(e) {  
	$('#load_data').html("<center><br><br><br><i class='ace-icon fa fa-spinner fa-spin orange bigger-300'></i></center>");
	setTimeout(function()
	{
		var d1 = document.getElementById("dt_sta").value;
		var d2 = document.getElementById("dt_end").value;
		var d3 = document.getElementById("sl_rps").value;
		var d4 = document.getElementById("sl_rps2").value;
		var dt1 = d1.split('/')[2]+"-"+d1.split('/')[1]+"-"+d1.split('/')[0];
		var dt2 = d2.split('/')[2]+"-"+d2.split('/')[1]+"-"+d2.split('/')[0];
		$('#load_data').load("palliative_view10.php?dt1="+dt1+"&dt2="+dt2+"&dt3="+d3+"&dt4="+d4);
			
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
</script>
