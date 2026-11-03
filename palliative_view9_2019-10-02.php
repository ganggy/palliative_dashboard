<?php 
$dt1 = $_GET["dt1"];
$dt2 = $_GET["dt2"];
$dt3 = $_GET["dt3"];
$dt4 = $_GET["dt4"];
include "condb.php";

if($dt4=="99999"){
		$bbb_sql="";
	}
	else {
		$bbb_sql=" pt.death=\"$dt4\" AND ";
	}

if($dt3=="99999" || $dt3=="" ) $shp = "";
else if($dt3=="00000") $shp = " AND ISNULL(zr.rpst_id) ";
else $shp = " AND zr.rpst_id=".$dt3." ";
$sql = "SELECT MAX(ov.vstdate) AS vstdate,pp.`name`,ov.hn,pt.cid,CONCAT(pt.pname,pt.fname,' ',pt.lname) AS ptname,pt.birthday,(YEAR(NOW()) -YEAR(pt.birthday)) AS age
,CONCAT(pt.addrpart,' หมู่ ',pt.moopart,' ',th.full_name) AS addr,zn.rpst_name,zn.rpst_id
,(SELECT vn.pdx FROM vn_stat vn WHERE vn.vn=ov.vn) AS C 
,if(ov.pdx ='Z515' OR ov.dx0 ='Z515' OR ov.dx1 ='Z515' OR ov.dx1 ='Z515' OR ov.dx2 ='Z515' OR ov.dx3 ='Z515' OR ov.dx4 ='Z515' OR ov.dx5 ='Z515','Z515',NULL) AS Z 
,if(ISNULL(pt.death) OR pt.death='','N',pt.death) AS death
,(SELECT COUNT(*) AS dayc FROM ovst_community_service a1 INNER JOIN vn_stat vn ON vn.vn=a1.vn WHERE vn.hn=ov.hn AND a1.ovst_community_service_type_id BETWEEN 1 AND 103) AS dayc
,DATEDIFF(NOW(),(SELECT MAX(a1.entry_datetime) FROM ovst_community_service a1 INNER JOIN vn_stat vn ON vn.vn=a1.vn WHERE vn.hn=ov.hn AND a1.ovst_community_service_type_id BETWEEN 1 AND 103)) AS daym
FROM vn_stat ov 
LEFT JOIN pttype pp ON pp.pttype=ov.pttype
LEFT JOIN ovst_community_service oc ON oc.vn=ov.vn AND oc.ovst_community_service_type_id BETWEEN 1 AND 103
LEFT JOIN patient pt ON pt.hn=ov.hn
LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart,pt.amppart,pt.tmbpart)
LEFT JOIN zbm_rpst zr ON zr.chwpart=pt.chwpart AND zr.amppart=pt.amppart AND zr.tmbpart=pt.tmbpart AND zr.moopart=pt.moopart
LEFT JOIN zbm_rpst_name zn ON zn.rpst_id=zr.rpst_id
WHERE  $bbb_sql (ov.pdx LIKE' F03') AND ov.vstdate BETWEEN '".$dt1."' AND '".$dt2."' ".$shp."GROUP BY ov.hn ORDER BY ov.vstdate DESC";

?>

<div class="row">
	<div class="col-xs-12">
		<div class="clearfix">
			<div class="pull-right tableTools-container"></div>
		</div>
	<div>
		<table id="dynamic-table" class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>ที่</th>
					<th>วันที่ (ล่าสุด)</th>
					<th><center>HN</center></th>
                    <th><center>สิทธ์</center></th>
					<th><center>เลขบัตรประชาชน</center></th>
					<th><center>ชื่อ - สกุล</center></th>
					<th><center>วันเกิด</center></th>
					<th><center>อายุ</center></th>
					<th><center>ที่อยู่</center></th>
					<th><center>รพสต</center></th>
					<th><center>
					  รหัส Dementia
				  </center></th>
					<th><center>รหัส Z515</center></th>
					<th><center>เยี่ยมบ้าน<br />รพ.(ครั้ง)</center></th>
					<th><center>เยี่ยมบ้าน<br />รพ.สต.(ครั้ง)</center></th>
                    <th><center>หมายเหตุ</center></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					$num=1;
					while($row = $result->fetch_assoc()) {
						
						if($row["death"]=="Y") $red =" class='danger'";
						else $red ="'";
						if($row["dayc"]!="0") $view ='<a href="#" onclick="OnSel(\''.$row["hn"].'\')">'.$row["dayc"].' <i class="fa fa-eye" aria-hidden="true"></i><a/>';
						else $view ='';
						
						if($row["rpst_id"]!="11097"&&$row["rpst_id"]!=""){
								$host246 = "192.168.10.246";
							$user246 = "bmhos2011";
							$pass246 = "hosbm2554";
							$data246 = "jhcisdb".$row["rpst_id"];
							$conn246 = new mysqli($host246, $user246, $pass246, $data246);					
							if ($conn246->connect_error) {
								die("Connection failed: " . $conn->connect_error);
							} 						
							$conn246->query("SET NAMES UTF8");					
							$sql246="SELECT COUNT(DISTINCT vh.visitno) AS cc_thos,MAX(v.visitdate) AS thos_date
	FROM person p
	JOIN visit v ON p.pid=v.pid
	JOIN visithomehealthindividual vh ON vh.visitno=v.visitno
	WHERE
	p.idcard=\"".$row["cid"]."\"";
	
	/*
							$sql246="SELECT COUNT(*) AS cc_thos FROM person p 
JOIN visit v ON p.pid=v.pid 
JOIN visithomehealthindividual vh ON vh.visitno=v.visitno
JOIN visitdiag vd ON v.visitno=vd.visitno
WHERE p.idcard=\"".$row["cid"]."\"
AND vd.diagcode=\"Z51.5\"	";
	*/
						//	echo $sql246;
	
							$result246 = $conn246->query($sql246);
							$row246 = $result246->fetch_assoc();	if($row246["cc_thos"]>0){
								$view_thos="<a href=\"#\" onclick='OnSel_thos(\"".$row["cid"]."\",\"".$row["hn"]."\")'>".$row246["cc_thos"]." <i class=\"fa fa-eye\" aria-hidden=\"true\"></i><a/>";
							}
							
							}
						
						echo "<tr".$red .">";
						echo "	<td align='center'>".$num."</td>";
						echo "	<td align='center'>".DateThai($row["vstdate"])."</td>";
						echo "	<td align='center'>".$row["hn"]."</td>";
						echo "	<td align='center'>".$row["name"]."</td>";
						echo "	<td align='center'>".$row["cid"]."</td>";
						echo "	<td>".$row["ptname"]."</td>";
						echo "	<td>".DateThai($row["birthday"])."</td>";
						echo "	<td>".$row["age"]."</td>";
						echo "	<td>".$row["addr"]."</td>";
						echo "	<td>".$row["rpst_name"]."</td>";
						echo "	<td align='center'>".$row["C"]."</td>";
						echo "	<td align='center'>".$row["Z"]."</td>";
						echo "	<td align='center' class='blue bolder'>".$view."</td>";
						echo "	<td align='center' class='blue bolder'>".$view_thos."</td>";
						echo "	<td align='center'>".DayToTXT($row["daym"],$row["death"])."</td>";
						echo "</tr>";
						$num++;
					}
				}
				function DayToTXT($day,$death){
					if($death!="N") $rrs = "เสียชีวิตแล้ว";
					else{
						if($day!=""){
							if(intval($day)<30) $rrs = "<span class='green bolder'>เยี่ยม ".$day." วันที่แล้ว</span>";
							else if(intval($day)<60) $rrs = "<span class='blue bolder'>เยี่ยม 1 เดือนที่แล้ว</span>";
							else if(intval($day)<90) $rrs = "<span class='blue bolder'>เยี่ยม 2 เดือนที่แล้ว</span>";
							else if(intval($day)<120) $rrs = "<span class='orange bolder'>เยี่ยม 3 เดือนที่แล้ว</span>";
							else if(intval($day)<210) $rrs = "<span class='orange bolder'>เยี่ยม 6 เดือนที่แล้ว</span>";
							else if(intval($day)<420) $rrs = "<span class='red bolder'>เยี่ยม 1 ปีที่แล้ว</span>";
							else $rrs = "<span class='bolder'>เยี่ยมมากกว่า 1 ปี</span>";
						}
						else $rrs = "";
					}
					
					return $rrs;
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
			</tbody>
		</table>
	</div>
</div>
<div id="loda_dialog"/>
<script type="text/javascript">
	function OnSel(a) {
		var dt1="<?php echo $dt1; ?>";
		var dt2="<?php echo $dt2; ?>";
		$('#loda_dialog').load("palliative_view_home.php?hn="+a+"&dt1="+dt1+"&dt2="+dt2);
	}
	function OnSel_thos(cid,hn) {
		$('#loda_dialog').load("palliative_view_home_thos.php?cid="+cid+"&&hn="+hn);
	}
</script>
<script type="text/javascript">
			jQuery(function($) {

			
				$( "#id-btn-dialog1" ).on('click', function(e) {
					e.preventDefault();
					
				});
				
				
				
				//initiate dataTables plugin
				var myTable = 
				$('#dynamic-table')
				//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
				.DataTable( {
					bAutoWidth: false,
					"aoColumns": [
						{ "bSortable": false },
						{ "bSortable": false },
						{ "bSortable": false },
						{ "bSortable": false },
						{ "bSortable": false },
						{ "bSortable": false },
						{ "bSortable": false },
						{ "bSortable": false },
						{ "bSortable": false },
						{ "bSortable": false },
						{ "bSortable": false },
						{ "bSortable": false },
						{ "bSortable": false }
					],
					"aaSorting": [],
					
					
					//"bProcessing": true,
			        //"bServerSide": true,
			        //"sAjaxSource": "http://127.0.0.1/table.php"	,
			
					//,
					//"sScrollY": "200px",
					//"bPaginate": false,
			
					//"sScrollX": "100%",
					//"sScrollXInner": "120%",
					//"bScrollCollapse": true,
					//Note: if you are applying horizontal scrolling (sScrollX) on a ".table-bordered"
					//you may want to wrap the table inside a "div.dataTables_borderWrap" element
			
					"iDisplayLength": 25,
			
			
					
			    } );
			
				
				
				$.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons btn-overlap btn-group btn-overlap';
				
				new $.fn.dataTable.Buttons( myTable, {
					buttons: [
					  {
						"extend": "colvis",
						"text": "<i class='fa fa-search bigger-110 blue'></i> <span class='hidden'>Show/hide columns</span>",
						"className": "btn btn-white btn-primary btn-bold",
						columns: ':not(:first):not(:last)'
					  },
					  {
						"extend": "copy",
						"text": "<i class='fa fa-copy bigger-110 pink'></i> <span class='hidden'>Copy to clipboard</span>",
						"className": "btn btn-white btn-primary btn-bold"
					  },
					  {
						"extend": "csv",
						"text": "<i class='fa fa-database bigger-110 orange'></i> <span class='hidden'>Export to CSV</span>",
						"className": "btn btn-white btn-primary btn-bold"
					  },
					  {
						"extend": "excel",
						"text": "<i class='fa fa-file-excel-o bigger-110 green'></i> <span class='hidden'>Export to Excel</span>",
						"className": "btn btn-white btn-primary btn-bold"
					  },
					  {
						"extend": "pdf",
						"text": "<i class='fa fa-file-pdf-o bigger-110 red'></i> <span class='hidden'>Export to PDF</span>",
						"className": "btn btn-white btn-primary btn-bold"
					  },
					  {
						"extend": "print",
						"text": "<i class='fa fa-print bigger-110 grey'></i> <span class='hidden'>Print</span>",
						"className": "btn btn-white btn-primary btn-bold",
						autoPrint: false,
						message: 'This print was produced using the Print button for DataTables'
					  }		  
					]
				} );
				myTable.buttons().container().appendTo( $('.tableTools-container') );
				
				//style the message box
				var defaultCopyAction = myTable.button(1).action();
				myTable.button(1).action(function (e, dt, button, config) {
					defaultCopyAction(e, dt, button, config);
					$('.dt-button-info').addClass('gritter-item-wrapper gritter-info gritter-center white');
				});
				
				
				var defaultColvisAction = myTable.button(0).action();
				myTable.button(0).action(function (e, dt, button, config) {
					
					defaultColvisAction(e, dt, button, config);
					
					
					if($('.dt-button-collection > .dropdown-menu').length == 0) {
						$('.dt-button-collection')
						.wrapInner('<ul class="dropdown-menu dropdown-light dropdown-caret dropdown-caret" />')
						.find('a').attr('href', '#').wrap("<li />")
					}
					$('.dt-button-collection').appendTo('.tableTools-container .dt-buttons')
				});
			
				////
			
				setTimeout(function() {
					$($('.tableTools-container')).find('a.dt-button').each(function() {
						var div = $(this).find(' > div').first();
						if(div.length == 1) div.tooltip({container: 'body', title: div.parent().text()});
						else $(this).tooltip({container: 'body', title: $(this).text()});
					});
				}, 500);
				
				
				
				
				
				myTable.on( 'select', function ( e, dt, type, index ) {
					if ( type === 'row' ) {
						$( myTable.row( index ).node() ).find('input:checkbox').prop('checked', true);
					}
				} );
				myTable.on( 'deselect', function ( e, dt, type, index ) {
					if ( type === 'row' ) {
						$( myTable.row( index ).node() ).find('input:checkbox').prop('checked', false);
					}
				} );
			
			
			
			
				/////////////////////////////////
				//table checkboxes
				$('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);
				
				//select/deselect all rows according to table header checkbox
				$('#dynamic-table > thead > tr > th input[type=checkbox], #dynamic-table_wrapper input[type=checkbox]').eq(0).on('click', function(){
					var th_checked = this.checked;//checkbox inside "TH" table header
					
					$('#dynamic-table').find('tbody > tr').each(function(){
						var row = this;
						if(th_checked) myTable.row(row).select();
						else  myTable.row(row).deselect();
					});
				});
				
				//select/deselect a row when the checkbox is checked/unchecked
				$('#dynamic-table').on('click', 'td input[type=checkbox]' , function(){
					var row = $(this).closest('tr').get(0);
					if(this.checked) myTable.row(row).deselect();
					else myTable.row(row).select();
				});
			
			
			
				$(document).on('click', '#dynamic-table .dropdown-toggle', function(e) {
					e.stopImmediatePropagation();
					e.stopPropagation();
					e.preventDefault();
				});
				
				
				
				//And for the first simple table, which doesn't have TableTools or dataTables
				//select/deselect all rows according to table header checkbox
				var active_class = 'active';
				$('#simple-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function(){
					var th_checked = this.checked;//checkbox inside "TH" table header
					
					$(this).closest('table').find('tbody > tr').each(function(){
						var row = this;
						if(th_checked) $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
						else $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
					});
				});
				
				//select/deselect a row when the checkbox is checked/unchecked
				$('#simple-table').on('click', 'td input[type=checkbox]' , function(){
					var $row = $(this).closest('tr');
					if($row.is('.detail-row ')) return;
					if(this.checked) $row.addClass(active_class);
					else $row.removeClass(active_class);
				});
			
				
			
				/********************************/
				//add tooltip for small view action buttons in dropdown menu
				$('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});
				
				//tooltip placement on right or left
				function tooltip_placement(context, source) {
					var $source = $(source);
					var $parent = $source.closest('table')
					var off1 = $parent.offset();
					var w1 = $parent.width();
			
					var off2 = $source.offset();
					//var w2 = $source.width();
			
					if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
					return 'left';
				}
				
				
				
				
				/***************/
				$('.show-details-btn').on('click', function(e) {
					e.preventDefault();
					$(this).closest('tr').next().toggleClass('open');
					$(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
				});
				/***************/
				
				
				
				
				
				/**
				//add horizontal scrollbars to a simple table
				$('#simple-table').css({'width':'2000px', 'max-width': 'none'}).wrap('<div style="width: 1000px;" />').parent().ace_scroll(
				  {
					horizontal: true,
					styleClass: 'scroll-top scroll-dark scroll-visible',//show the scrollbars on top(default is bottom)
					size: 2000,
					mouseWheelLock: true
				  }
				).css('padding-top', '12px');
				*/
				
				
			})
		</script>
