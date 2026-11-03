<?php 
$dt1 = $_GET["dt1"];
$dt2 = $_GET["dt2"];
include "condb.php";

$sql = "SELECT op.vn,op.vstdate,os.vsttime,pt.hn,CONCAT(pt.pname,pt.fname,' ',pt.lname) AS ptname,vn.pdx,vn.dx0,vn.dx1,vn.dx2,vn.dx3,vn.dx4,vn.dx5,os.cc
,IF(op.icode in('1000156','1000406','1000412','1000413','1000430','1550025','1580001'),CONCAT(dr.name,' ',dr.strength)
,IF(op.icode in('3003603','3003604') ,nd.name
,IF(op.icode in('3003056','3003409','3003533','3003631'),nd.name,''))) AS x1 
,IF(op.icode in('1000156','1000406','1000412','1000413','1000430','1550025','1580001') 
,CONCAT(ROUND(op.unitprice,2),' x ',op.qty,' ',dr.units,' = ',ROUND(dr.unitprice*op.qty,2)) 
,IF(op.icode in('3003603','3003604')  
,CONCAT(op.qty,' x ',ROUND(op.unitprice,0),' = ',ROUND(op.sum_price,0)) 
,IF(op.icode in('3003056','3003409','3003533','3003631') 
,CONCAT(op.qty,' x ',ROUND(op.unitprice,0),' = ',ROUND(op.sum_price,0)),''))) AS x2 
,IF(op.icode in('1000156','1000406','1000412','1000413','1000430','1550025','1580001'),CONCAT(u.name1,' ',u.name2,' ',u.name3),NULL) AS x3
FROM vn_stat vn
INNER JOIN patient pt ON pt.hn=vn.hn
INNER JOIN opitemrece op ON op.vn=vn.vn
INNER JOIN opdscreen os ON os.vn=vn.vn
LEFT JOIN drugitems dr ON dr.icode=op.icode
LEFT JOIN nondrugitems nd ON nd.icode=op.icode 
LEFT JOIN s_drugitems s on s.icode=op.icode
LEFT JOIN drugusage d on d.drugusage=op.drugusage 
LEFT JOIN sp_use u on u.sp_use = op.sp_use
WHERE op.vstdate BETWEEN '".$dt1."' AND '".$dt2."' AND op.icode in('1000156','1000406','1000412','1000413','1000430','1550025','1580001','3003603','3003604','3003056','3003409','3003533','3003631')
AND (vn.pdx='Z515' OR vn.dx0='Z515' OR vn.dx1='Z515' OR vn.dx2='Z515' OR vn.dx3='Z515' OR vn.dx4='Z515' OR vn.dx5='Z515')
ORDER BY vn.vn DESC,vn.hn
";

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
					<th>ข้อมูลการรับบริการ</th>
					<th>รายการเวชภัณฑ์/เวชภัณฑ์มิใช่ยา</th>
					<th>จำนวน มุลค่า</th>
					<th>วิธีการใช้ยา</th>
				</tr>
			</thead>
		<tbody>
				<?php
				$shn="";
				$i=0;
				$r=0;
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						if($i==0) $shn=$row["vn"];
						
						if($row["vn"]== $shn){
							$s1[$i] = $row["vstdate"]." ".$row["vsttime"];
							$s2[$i] = $row["hn"];
							$s3[$i] = $row["ptname"];
							$icd10="";
							if($row["pdx"]!="")$icd10=$icd10.$row["pdx"];
							if($row["dx0"]!="")$icd10=$icd10.", ".$row["dx0"];
							if($row["dx1"]!="")$icd10=$icd10.", ".$row["dx1"];
							if($row["dx2"]!="")$icd10=$icd10.", ".$row["dx2"];
							if($row["dx3"]!="")$icd10=$icd10.", ".$row["dx3"];
							if($row["dx4"]!="")$icd10=$icd10.", ".$row["dx4"];
							if($row["dx5"]!="")$icd10=$icd10.", ".$row["dx5"];
							$s4[$i] = $icd10;
							$s5[$i] = $row["cc"];
							$x1[$i] = $row["x1"];
							$x2[$i] = $row["x2"];
							$x3[$i] = $row["x3"];
							$i++;
						}
						else{
							
							echo'	<tr>';
							echo'		<td>';
							echo'			<label class="control-label bolder red">'.$s1[0].'</label><br>';
							echo'			<label><span class="blue bolder"> HN : '.$s2[0].'&emsp; ชื่อ-สกุล :  '.$s3[0].'</span></label><br>';	
							echo'			<label><span class="bolder"> การวินิจฉัย : '.$s4[0].'</span></label><br>';	
							echo'			<label><span class="bolder"> อาการสำคัญ : '.$s5[0].'</span></label>';			
							echo'		</td>';
							echo'		<td>';
												for($j=0;$j<=$i;$j++){
							echo'					<h6> '.$x1[$j].' </h6>';
												}
							echo'		</td>';
							echo'		<td>';
												for($j=0;$j<=$i;$j++){
							echo'					<h6> '.$x2[$j].' </h6>';
												}
							echo'		</td>';
							echo'		<td>';
												for($j=0;$j<=$i;$j++){
							echo'					<h6> '.$x3[$j].' </h6>';
												}
	
							echo'		</td>';
							echo'	</tr>';


							$i=0;
							$shn=$row["vn"];
							$s1[$i] = $row["vstdate"]." ".$row["vsttime"];
							$s2[$i] = $row["hn"];
							$s3[$i] = $row["ptname"];
							$icd10="";
							if($row["pdx"]!="")$icd10=$icd10.$row["pdx"];
							if($row["dx0"]!="")$icd10=$icd10.", ".$row["dx0"];
							if($row["dx1"]!="")$icd10=$icd10.", ".$row["dx1"];
							if($row["dx2"]!="")$icd10=$icd10.", ".$row["dx2"];
							if($row["dx3"]!="")$icd10=$icd10.", ".$row["dx3"];
							if($row["dx4"]!="")$icd10=$icd10.", ".$row["dx4"];
							if($row["dx5"]!="")$icd10=$icd10.", ".$row["dx5"];
							$s4[$i] = $icd10;
							$s5[$i] = $row["cc"];
							$x1[$i] = $row["x1"];
							$x2[$i] = $row["x2"];
							$x3[$i] = $row["x3"];
							$i++;
						}
					}
				}
				?>
				<tbody>
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
			
					"iDisplayLength": 10,
			
			
					
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
