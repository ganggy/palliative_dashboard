<?php
	session_name("bmhsession");
	session_start();
	
	$_SESSION["menu"] = 17;
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
			<h4>รายงานผู้ป่วย palliative Care โรงพยาบาลบ้านโคกศรีสุพรรณ พ.ศ 2561 - 2562 </h4>
		</span>
	</div><!-- /.page-header -->	
    <script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<table class="table table-borderless">
  <thead>
    <tr>
      <th scope="col">ลำดับ</th>
      <th scope="col">ชื่อรายงาน</th>

    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">1</th>
      <td><a href="index15-1.php" class="badge badge-info"><h5>จำนวนผู้ป่วยรายใหม่</h5></a></td>

    </tr>
    <tr>
      <th scope="row">2</th>
      <td><a href="#" class="badge badge-info"><h5>จำนวนผู้ป่วยทั้งหมด (รายใหม่/รายเก่า)</h5></a></td>

    </tr>
    <tr>
      <th scope="row">3</th>
      <td colspan="2"><a href="#" class="badge badge-info"><h5>จำนวนผู้ป่วยทั้งหมด (รายใหม่/รายเก่า)</h5></a></td>
    </tr>
     <tr>
      <th scope="row">4</th>
      <td colspan="2"><a href="#" class="badge badge-info"><h5>จำนวนผู้ป่วยที่ได้รับมอร์ฟีน</h5></a></td>
    </tr>
     <tr>
      <th scope="row">5</th>
      <td colspan="2"><a href="#" class="badge badge-info"><h5>จำนวนผู้ป่วยที่ได้รับออกซิเจน (ถัง,เครื่องผลิตออกซิเจน)</h5></a></td>
    </tr>
         <tr>
      <th scope="row">6</th>
      <td colspan="2"><a href="#" class="badge badge-info"><h5>จำนวนผู้ป่วยที่ได้รับมอร์ฟีน,ออกซิเจน</h5></a></td>
    </tr>
         <tr>
      <th scope="row">7</th>
      <td colspan="2"><a href="#" class="badge badge-info"><h5>จำนวนผู้ปาวยที่ได้รับการเยี่ยมบ้าน</h5></a></td>
    </tr>
         <tr>
      <th scope="row">8</th>
      <td colspan="2"><a href="#" class="badge badge-info"><h5>จำนวนผู้ป่วยที่ได้ทำ ACP (Z718)</h5></a></td>
    </tr>
         <tr>
      <th scope="row">9</th>
      <td colspan="2"><a href="#" class="badge badge-info"><h5>จำนวนผู้ป่วยเสียชีวิต</h5></a></td>
    </tr>
         <tr>
      <th scope="row">10</th>
      <td colspan="2"><a href="#" class="badge badge-info"><h5>จำนวนผู้ป่วยมะเล็ง</h5></a></td>
    </tr>
  </tbody>
</table>

</div><!-- /.page-content -->

<?php
	include "inc_footer.php";
?>

<script type="text/javascript">

var d1 = document.getElementById("dt_sta").value;
var d2 = document.getElementById("dt_end").value;

var dt1 = d1.split('/')[2]+"-"+d1.split('/')[1]+"-"+d1.split('/')[0];
var dt2 = d2.split('/')[2]+"-"+d2.split('/')[1]+"-"+d2.split('/')[0];


$('#bt_process').click(function(e) {  
	$('#load_data').html("<center><br><br><br><i class='ace-icon fa fa-spinner fa-spin orange bigger-300'></i></center>");
	setTimeout(function()
	{
		var d1 = document.getElementById("dt_sta").value;
		var d2 = document.getElementById("dt_end").value;

		var dt1 = d1.split('/')[2]+"-"+d1.split('/')[1]+"-"+d1.split('/')[0];
		var dt2 = d2.split('/')[2]+"-"+d2.split('/')[1]+"-"+d2.split('/')[0];
		$('#load_data').load("palliative_view1.php?dt1="+dt1+"&dt2="+dt2);
			
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
