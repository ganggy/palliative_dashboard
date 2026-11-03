<?php
	session_name("bmhsession");
	session_start();
	
	$_SESSION["menu"] = 0;
	include "inc_header.php";
	date_default_timezone_set('Asia/Bangkok'); 
	$date = new DateTime();
?>
<?php

	include "conlis.php";
?>

<div class="page-content" >
	<div class="page-header">
		<span class="blue bolder">
			<h4>ทะเบียนผู้ป่วยระยะสุดท้ายแบบประคับประคอง และข้อมูลการเยี่ยมบ้าน</h4>
		</span>
	</div><!-- /.page-header -->	
  <script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
 <div class="row">
        <div class="col-md-12">
        <div class="tile">
        <div class="tile-body">
              
                <div class="form-group col-md-6">
<div id="container1" style="min-width: 310px; max-width: 600px; height: 400px; margin: 0 auto"></div>
                </div>

<div id="container2" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
<!-- Data from www.netmarketshare.com. Select Browsers => Desktop share by version. Download as tsv. -->
<pre id="tsv" style="display:none">Browser Version    Total Market Share
Microsoft Internet Explorer 8.0    26.61%
Microsoft Internet Explorer 9.0    16.96%
Chrome 18.0    8.01%
Chrome 19.0    7.73%
Firefox 12    6.72%
Microsoft Internet Explorer 6.0    6.40%
Firefox 11    4.72%
Microsoft Internet Explorer 7.0    3.55%
Safari 5.1    3.53%
Firefox 13    2.16%
Firefox 3.6    1.87%
Opera 11.x    1.30%
Chrome 17.0    1.13%
Firefox 10    0.90%
Safari 5.0    0.85%
Firefox 9.0    0.65%
Firefox 8.0    0.55%
Firefox 4.0    0.50%
Chrome 16.0    0.45%
Firefox 3.0    0.36%
Firefox 3.5    0.36%
Firefox 6.0    0.32%
Firefox 5.0    0.31%
Firefox 7.0    0.29%
Proprietary or Undetectable    0.29%
Chrome 18.0 - Maxthon Edition    0.26%
Chrome 14.0    0.25%
Chrome 20.0    0.24%
Chrome 15.0    0.18%
Chrome 12.0    0.16%
Opera 12.x    0.15%
Safari 4.0    0.14%
Chrome 13.0    0.13%
Safari 4.1    0.12%
Chrome 11.0    0.10%
Firefox 14    0.10%
Firefox 2.0    0.09%
Chrome 10.0    0.09%
Opera 10.x    0.09%
Microsoft Internet Explorer 8.0 - Tencent Traveler Edition    0.09%</pre><br />
<br />
<br />

<div id="container" style="min-width: 300px; height: 400px; margin: 0 auto"></div>
	
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
					<label class="col-sm-3 control-label no-padding-right" for="form-field-1-1"> สถานะ : </label>
					<div class="col-sm-2">
						<select class="input-group chosen-select form-control" id="sl_rps1" data-placeholder="เลือกสถานะ...">
							<option value="">  </option>
                        	<option value="99999" selected>ทั้งหมด</option>
							<option value="Y">เสียชีวิต</option>
							<option value="N">ยังมีชีวิต</option>
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
var d4 = document.getElementById("sl_rps1").value;
	
var dt1 = d1.split('/')[2]+"-"+d1.split('/')[1]+"-"+d1.split('/')[0];
var dt2 = d2.split('/')[2]+"-"+d2.split('/')[1]+"-"+d2.split('/')[0];

	
$('#load_data').html("<center><br><br><br><i class='ace-icon fa fa-spinner fa-spin orange bigger-300'></i></center>");
	setTimeout(function()
	{
		$('#load_data').load("palliative_view.php?dt1="+dt1+"&dt2="+dt2+"&dt3="+d3+"&dt4="+d4);
	} , (500));

$('#bt_process').click(function(e) {  
	$('#load_data').html("<center><br><br><br><i class='ace-icon fa fa-spinner fa-spin orange bigger-300'></i></center>");
	setTimeout(function()
	{
		var d1 = document.getElementById("dt_sta").value;
		var d2 = document.getElementById("dt_end").value;
		var d3 = document.getElementById("sl_rps").value;
		var d4 = document.getElementById("sl_rps1").value;
		var dt1 = d1.split('/')[2]+"-"+d1.split('/')[1]+"-"+d1.split('/')[0];
		var dt2 = d2.split('/')[2]+"-"+d2.split('/')[1]+"-"+d2.split('/')[0];
		$('#load_data').load("palliative_view.php?dt1="+dt1+"&dt2="+dt2+"&dt3="+d3+"&dt4="+d4);
			
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
        text: 'จำนวนผู้ป่วยที่เสียชีวิตใน Palliative Care แยก diag ปี 2561 - 2563 '
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
            text: 'จำนวนผู้เสียชีวิต (ราย)'
        }
    },
    legend: {
        enabled: false
    },
    tooltip: {
        pointFormat: 'จำนวนผู้เสียชีวิต <b>{point.y:.1f} ราย</b>'
    },
    series: [{
        name: 'Population',
        data: [
            <?php 
				$sql = "SELECT COUNT(DISTINCT ov.hn) as kk,ov.pdx 
FROM vn_stat ov 
LEFT JOIN ovst_community_service oc ON oc.vn=ov.vn AND oc.ovst_community_service_type_id BETWEEN 1 AND 103
LEFT JOIN patient pt ON pt.hn=ov.hn
LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart,pt.amppart,pt.tmbpart)
LEFT JOIN zbm_rpst zr ON zr.chwpart=pt.chwpart AND zr.amppart=pt.amppart AND zr.tmbpart=pt.tmbpart AND zr.moopart=pt.moopart
LEFT JOIN zbm_rpst_name zn ON zn.rpst_id=zr.rpst_id
WHERE (ov.pdx ='Z515' OR ov.dx0 ='Z515' OR ov.dx1 ='Z515' OR ov.dx2 ='Z515' OR ov.dx3 ='Z515' OR ov.dx4 ='Z515' OR ov.dx5 ='Z515')
AND ov.vstdate BETWEEN '2018-10-01' AND '2099-10-31' AND pt.death =\"Y\" GROUP BY ov.pdx ORDER BY ov.vstdate  ";
				$query=mysqli_query($con,$sql);
				while($result=mysqli_fetch_array($query)){
				?>

                ['<?=$result["pdx"]?>', <?=$result["kk"]?>],
				<?php }?>
        ],
        dataLabels: {
            enabled: true,
            rotation: -90,
            color: '#FFFFFF',
            align: 'right',
            format: '{point.y:.1f}', // one decimal
            y: 15, // 10 pixels down from the top
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    }]
});
// Create the chart
Highcharts.chart('container1', {
    chart: {
        type: 'pie'
    },
    title: {
        text: 'กราฟวงกลมแสดงข้อมูลผู้ป่วย Palliative Care แยกตาม รพสต.'
    },
    subtitle: {
        text: 'Click the slices to view versions. Source: <a href="http://statcounter.com" target="_blank">statcounter.com</a>'
    },
    plotOptions: {
        series: {
            dataLabels: {
                enabled: true,
                format: '{point.name}: {point.y:.1f} ราย'
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f} ราย</b><br/>'
    },

    series: [
        {
            name: "Browsers",
            colorByPoint: true,
            data: [
                 <?php 
				$sql = "SELECT COUNT(DISTINCT ov.hn) as cc,zn.rpst_name
FROM vn_stat ov 
LEFT JOIN ovst_community_service oc ON oc.vn=ov.vn AND oc.ovst_community_service_type_id BETWEEN 1 AND 103
LEFT JOIN patient pt ON pt.hn=ov.hn
LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart,pt.amppart,pt.tmbpart)
LEFT JOIN zbm_rpst zr ON zr.chwpart=pt.chwpart AND zr.amppart=pt.amppart AND zr.tmbpart=pt.tmbpart AND zr.moopart=pt.moopart
LEFT JOIN zbm_rpst_name zn ON zn.rpst_id=zr.rpst_id
WHERE (ov.pdx ='Z515' OR ov.dx0 ='Z515' OR ov.dx1 ='Z515' OR ov.dx2 ='Z515' OR ov.dx3 ='Z515' OR ov.dx4 ='Z515' OR ov.dx5 ='Z515')
AND ov.vstdate BETWEEN '2018-10-01' AND '2099-10-31'GROUP BY zn.rpst_id ORDER BY ov.vstdate DESC";
				$query=mysqli_query($con,$sql);
				while($result=mysqli_fetch_array($query)){
				?>

                ['<?=$result["rpst_name"]?>', <?=$result["cc"]?>],
				<?php }?>
            ]
        }
    ],

});
// Radialize the colors
Highcharts.setOptions({
    colors: Highcharts.map(Highcharts.getOptions().colors, function (color) {
        return {
            radialGradient: {
                cx: 0.5,
                cy: 0.3,
                r: 0.7
            },
            stops: [
                [0, color],
                [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
            ]
        };
    })
});
// Create the chart
Highcharts.chart('container2', {
    chart: {
        type: 'pie'
    },
    title: {
        text: 'กราฟวงกลมแสดงข้อมูลผู้ป่วยที่ยังมีชีวิต แยกตาม Diag ปัจจุบัน'
    },
    subtitle: {
        text: 'Click the slices to view versions. Source: <a href="http://statcounter.com" target="_blank">statcounter.com</a>'
    },
    plotOptions: {
        series: {
            dataLabels: {
                enabled: true,
                format: '{point.name}: {point.y:.1f} ราย'
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f} ราย</b><br/>'
    },

    series: [
        {
            name: "Browsers",
            colorByPoint: true,
            data: [
                 <?php 
				$sql = "SELECT COUNT(DISTINCT ov.hn) as kk,ov.pdx 
FROM vn_stat ov 
LEFT JOIN ovst_community_service oc ON oc.vn=ov.vn AND oc.ovst_community_service_type_id BETWEEN 1 AND 103
LEFT JOIN patient pt ON pt.hn=ov.hn
LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart,pt.amppart,pt.tmbpart)
LEFT JOIN zbm_rpst zr ON zr.chwpart=pt.chwpart AND zr.amppart=pt.amppart AND zr.tmbpart=pt.tmbpart AND zr.moopart=pt.moopart
LEFT JOIN zbm_rpst_name zn ON zn.rpst_id=zr.rpst_id
WHERE (ov.pdx ='Z515' OR ov.dx0 ='Z515' OR ov.dx1 ='Z515' OR ov.dx2 ='Z515' OR ov.dx3 ='Z515' OR ov.dx4 ='Z515' OR ov.dx5 ='Z515')
AND ov.vstdate BETWEEN '2018-10-01' AND '2099-10-31' AND pt.death =\"N\" GROUP BY ov.pdx ORDER BY ov.vstdate   ";
				$query=mysqli_query($con,$sql);
				while($result=mysqli_fetch_array($query)){
				?>

                ['<?=$result["pdx"]?>', <?=$result["kk"]?>],
				<?php }?>
            ]
        }
    ],

});
</script>
