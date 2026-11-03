<?php
	session_name("bmhsession");
	session_start();
	
	$_SESSION["menu"] = 17;
	include "inc_header1.php";
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
			<h4>รายงานจำนวนผู้ป่วย (รายใหม/รายเก่า)</h4>
		</span>
	</div><!-- /.page-header -->	
    <script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</style>
<table width="100%" align="left" border="0">
  <tr>
    <th scope="col">	
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
                    <button class="btn btn-white btn-info btn-bold" id="bt_process">
								<i class="ace-icon fa fa-search bigger-120 blue"></i>
								ค้นหา
							</button>
				</div>

<br>
</div></div>		
<div id="load_data">
</th>
  </tr>
</table>
<?php
	include "inc_footer1.php";
?>

<script type="text/javascript">

var d1 = document.getElementById("dt_sta").value;
var d2 = document.getElementById("dt_end").value;
var dt1 = d1.split('/')[2]+"-"+d1.split('/')[1]+"-"+d1.split('/')[0];
var dt2 = d2.split('/')[2]+"-"+d2.split('/')[1]+"-"+d2.split('/')[0];

$('#load_data').html("<center><br><br><br><i class='ace-icon fa fa-spinner fa-spin orange bigger-300'></i></center>");
	setTimeout(function()
	{
		$('#load_data').load("palliative_view12.php?dt1="+dt1+"&dt2="+dt2);
	} , (500));

$('#bt_process').click(function(e) {  
	$('#load_data').html("<center><br><br><br><i class='ace-icon fa fa-spinner fa-spin orange bigger-300'></i></center>");
	setTimeout(function()
	{
		var d1 = document.getElementById("dt_sta").value;
		var d2 = document.getElementById("dt_end").value;
		var dt1 = d1.split('/')[2]+"-"+d1.split('/')[1]+"-"+d1.split('/')[0];
		var dt2 = d2.split('/')[2]+"-"+d2.split('/')[1]+"-"+d2.split('/')[0];
		$('#load_data').load("palliative_view12.php?dt1="+dt1+"&dt2="+dt2);
			
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
        text: 'กราฟแท่งแสดงข้อมูลทะเบียนผู้ป่วยโรคมะเร็งแยกตาม รพสต.'
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
				$sql = "SELECT COUNT(DISTINCT ov.hn) as cc,zn.rpst_name
FROM vn_stat ov 
LEFT JOIN ovst_community_service oc ON oc.vn=ov.vn AND oc.ovst_community_service_type_id BETWEEN 1 AND 103
LEFT JOIN patient pt ON pt.hn=ov.hn
LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart,pt.amppart,pt.tmbpart)
LEFT JOIN zbm_rpst zr ON zr.chwpart=pt.chwpart AND zr.amppart=pt.amppart AND zr.tmbpart=pt.tmbpart AND zr.moopart=pt.moopart
LEFT JOIN zbm_rpst_name zn ON zn.rpst_id=zr.rpst_id
WHERE ov.pdx LIKE 'C%' AND ov.vstdate BETWEEN '2018-10-01' AND '2020-12-30'GROUP BY zn.rpst_id ORDER BY ov.vstdate DESC ";
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
