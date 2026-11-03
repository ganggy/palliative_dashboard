<?php 
$dt1 = $_GET["dt1"];
$dt2 = $_GET["dt2"];
$dt3 = $_GET["dt3"];
include "condb.php";

if($dt3=="0" || $dt3=="" ) $shp = "";
else if($dt3=="1") $shp = " AND pt.hipdata_code in ('UCS') ";
else if($dt3=="2") $shp = " AND pt.hipdata_code in ('OFC','LGO') ";
else if($dt3=="3") $shp = " AND pt.hipdata_code in ('SSS') ";
else if($dt3=="4") $shp = " AND pt.hipdata_code not in ('SSS','OFC','LGO','UCS') ";


$sql = "SELECT op.vstdate,pa.cid,pa.hn,CONCAT(pa.pname,pa.fname,' ',pa.lname) AS paname,pt.pttype,pt.name AS ptname,pt.hipdata_code,dr.name AS ctname,op.qty,ROUND(op.unitprice,0) AS unitprice,
ROUND(op.sum_price,0) AS sum_price,if(ISNULL(wa.name),if(ISNULL(er.vn),'OPD','ER'),wa.name) AS depname,if(ISNULL(GROUP_CONCAT(idx.icd9)),GROUP_CONCAT(d.icd10),GROUP_CONCAT(idx.icd9))AS icd9
FROM opitemrece op
LEFT JOIN nondrugitems dr ON dr.icode=op.icode
LEFT JOIN patient pa ON pa.hn=op.hn
LEFT JOIN pttype pt ON pt.pttype=op.pttype
LEFT JOIN an_stat an ON an.an=op.an
LEFT JOIN ward wa ON wa.ward=an.ward
LEFT JOIN er_regist er ON er.vn=op.vn
LEFT JOIN iptoprt idx ON idx.an=op.an
LEFT JOIN icd9cm1 on icd9cm1.code=idx.icd9
LEFT JOIN ovstdiag d ON d.vn=op.vn AND substring(d.icd10,1,1) in ('0','1','2','3','4','5','6','7','8','9')
LEFT JOIN icd9cm1 i on i.code = d.icd10
WHERE op.icode in('3003202','3003205','3003207','3003209','3003210','3003214','3003222','3003224','3003225','3003226','3003228','3003229','3003230','3003614','3003618')
AND op.vstdate BETWEEN '".$dt1."' AND '".$dt2."'".$shp." GROUP BY op.vstdate,op.hn,op.icode ORDER BY op.vstdate,op.hn";
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
					<th>วันที่ </th>
					<th><center>HN</center></th>
					<th><center>เลขบัตรประชาชน</center></th>
					<th><center>ชื่อ - สกุล</center></th>
					<th><center>สิทธิการรักษา</center></th>
					<th><center>แผนก</center></th>
					<th><center>ICD9</center></th>
					<th><center>รายการ  CT-Scan</center></th>
                    <th><center>ราคาต่อหน่วย</center></th>
					<th><center>จำนวน</center></th>
					<th><center>ราคารวม</center></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					$sum=0;
					$hn="";
					while($row = $result->fetch_assoc()) {					
						echo "<tr>";
						if($hn!=$row["hn"]){
							echo "	<td align='center'>".$row["vstdate"]."</td>";
							echo "	<td align='center'>".$row["hn"]."</td>";
							echo "	<td align='center'>".$row["cid"]."</td>";
							echo "	<td>".$row["paname"]."</td>";
							echo "	<td>".$row["ptname"]."</td>";
							echo "	<td>".$row["depname"]."</td>";
							echo "	<td>".$row["icd9"]."</td>";
							$hn=$row["hn"];
						}
						else{
							echo "	<td align='center'></td>";
							echo "	<td align='center'></td>";
							echo "	<td align='center'></td>";
							echo "	<td></td>";
							echo "	<td></td>";
							echo "	<td></td>";
							echo "	<td></td>";
						}
						echo "	<td>".$row["ctname"]."</td>";
						echo "	<td align='right'>".number_format($row["unitprice"])."</td>";
						echo "	<td align='center'>".$row["qty"]."</td>";
						echo "	<td align='right'>".number_format($row["sum_price"])."</td>";
						echo "</tr>";
						
						$sum=$sum+$row["sum_price"];
					}
					
				}
				?>
			</tbody>
			<span class="bigger-175">จำนวนเงินรวม </span><span class="bigger-175 blue"> <?php echo number_format($sum);?> </span> <span class="bigger-175"> บาท</span>
		</table>
		
	</div>
</div>
<script type="text/javascript">
			jQuery(function($) {

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
			
					"iDisplayLength": 50,
			
			
					
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
