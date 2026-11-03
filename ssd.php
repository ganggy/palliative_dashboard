<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<style>
@import 'https://code.highcharts.com/css/highcharts.css';

#container1 {
	height: 400px;
	max-width: 800px;
	min-width: 320px;
	margin: 0 auto;
}
.highcharts-pie-series .highcharts-point {
	stroke: #EDE;
	stroke-width: 2px;
}
.highcharts-pie-series .highcharts-data-label-connector {
	stroke: silver;
	stroke-dasharray: 2, 2;
	stroke-width: 2px;
}
</style>
<?php
	session_name("bmhsession");
	session_start();
	
	$_SESSION["menu"] = 0 ;
	include "inc_header.php";
	date_default_timezone_set('Asia/Bangkok'); 
	$date = new DateTime();
?>
<?php

	$host="192.168.0.251";
	$Datauser="4select";
	$Datapass="!Q@W3e4r5t";
	$Dataname="hos";
	$con=mysqli_connect($host,$Datauser,$Datapass,$Dataname)or die("Cannot connect Host");
	mysqli_query($con,"SET NAMES UTF8");




?>
<div class="page-content" >
	<div class="page-header">
		<span class="blue bolder">
			<h4>กราฟแสดงข้อมูล Palliative Care </h4>
		</span>
	</div><!-- /.page-header -->			
	<div class="widget-body">
		<div class="row" >
		<div class="well well">

<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

		</div>
	</div>
</div><!-- /.page-content -->
  <div class="col-md-12"></div>
          <div class="tile">
            <div class="tile-body">
            <div class="form-group col-md-4 align-self-end">
    <div id="container1"></div>
             
                  </div>
              </div>
              </div>
            </div>
              <div class="col-md-6"></div>
          <div class="tile">
            <div class="tile-body">
            <div class="form-group col-md-4 align-self-end">
        
<div id="container2" style="height: 400px"></div>
             
                  </div>
              </div>
              </div>
            </div>
        
<div id="container" style="height: 400px"></div>
<script>
// Create the chart
Highcharts.chart('container', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'ข้อมูลคนป่วยที่อยู่ใน Palliative Care ในระยะ 1 ปี'
    },
    subtitle: {
        text: 'Click the columns to view versions. Source: <a href="http://statcounter.com" target="_blank">statcounter.com</a>'
    },
    xAxis: {
        type: 'category'
    },
    yAxis: {
        title: {
            text: 'Total percent market share'
        }

    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:.1f}%'
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
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
AND ov.vstdate BETWEEN '2018-10-01' AND '2019-05-31'GROUP BY zn.rpst_id ORDER BY ov.vstdate DESC";
				$query=mysqli_query($con,$sql);
				while($result=mysqli_fetch_array($query)){
				?>

                ['<?=$result["rpst_name"]?>', <?=$result["cc"]?>],
				<?php }?>

            ]
        }
    ],
  
});

Highcharts.chart('container1', {

    chart: {
        styledMode: true
    },

    title: {
        text: 'Pie point CSS'
    },

    xAxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
    },

    series: [{
        type: 'pie',
        allowPointSelect: true,
        keys: ['name', 'y', 'selected', 'sliced'],
        data: [
            ['Apples', 29.9, false],
            ['Pears', 71.5, false],
            ['Oranges', 106.4, false],
            ['Plums', 129.2, false],
            ['Bananas', 144.0, false],
            ['Peaches', 176.0, false],
            ['Prunes', 135.6, true, true],
            ['Avocados', 148.5, false]
        ],
        showInLegend: true
    }]
});
Highcharts.chart('container2', {
    chart: {
        type: 'pie',
        options3d: {
            enabled: true,
            alpha: 45,
            beta: 0
        }
    },
    title: {
        text: 'Browser market shares at a specific website, 2014'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            depth: 35,
            dataLabels: {
                enabled: true,
                format: '{point.name}'
            }
        }
    },
    series: [{
        type: 'pie',
        name: 'Browser share',
        data: [
            ['Firefox', 45.0],
            ['IE', 26.8],
            {
                name: 'Chrome',
                y: 12.8,
                sliced: true,
                selected: true
            },
            ['Safari', 8.5],
            ['Opera', 6.2],
            ['Others', 0.7]
        ]
    }]
});
</script>