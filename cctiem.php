<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<!DOCTYPE html>
<meta charset="utf-8">
<?php
session_name("bmhsession");
	session_start();
	
	$_SESSION["menu"] = 55;
include "inc_header.php";

	date_default_timezone_set('Asia/Bangkok'); 
	$date = new DateTime();
?>
<?php

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
<?php
?>
<head>

 

  <title>ติดตามการรักษา</title>

  <!-- Custom fonts for this template -->
 

</head>

<body id="page-top">
  <div id="content">
    <div class="row">
      <div class="col-sm-6">
    <div class="card">
      <div class="card-body">
     
<?php
include "condb.php";
$no = $_POST ['no'];
$pro_search = $_POST ['pro_search'];
$day1 = $_POST ['day1'];
$day2 = $_POST ['day2'];
	$p = ''.$pro_search.'';
	$no = ''.$no.'';
	$d1 = ''.$day1.'';
	$d2 = ''.$day2.'';

function date2db($str){
		$temp=explode("/",$str);
		return ("".$temp[0]."".$temp[2].$temp[1]);
	}
	
function date1db($str){
		$temp=explode("/",$str);
		return $temp[1]."-".$temp[0]."-".($temp[2]+543);
	}
echo $sql ?>
<?
$sql = "  SELECT * FROM ((SELECT p.hn,p.cid,CONCAT(pname,p.fname,' ',p.lname) AS pname,timestampdiff(year,p.birthday,curdate()) as cnt_year,DATE_FORMAT(DATE_ADD(p.birthday, INTERVAL 543 YEAR),'%Y-%m-%d') as \"birthday\" ,p.drugallergy,p.clinic,CONCAT(p.addrpart,' หมู่ ',p.moopart,' ',
                          (SELECT t.full_name FROM thaiaddress t WHERE t.addressid=CONCAT(p.chwpart,p.amppart,p.tmbpart))) AS addr
                          ,v.vn,v.vstdate AS vst,s.bw,s.height,CONCAT( LEFT(s.bps,3),\"/\",LEFT(s.bpd,2))as bp,s.temperature,s.pulse,s.rr,v.pdx,v.dx0,v.dx1,v.dx2,v.dx3,v.dx4,v.dx5,s.cc,l.lab_order_number,l.form_name,'OPD' AS tp-- ,COUNT(l.lab_order_number)
                          FROM vn_stat v 
                         LEFT OUTER JOIN lab_head l ON l.vn = v.vn 
                          INNER JOIN patient p ON p.hn = v.hn
                          INNER JOIN opdscreen s ON s.vn=v.vn
                          WHERE p.cid = '$no' )
                          UNION
                          (SELECT p.hn,p.cid,CONCAT(pname,p.fname,' ',p.lname) AS pname,timestampdiff(year,p.birthday,curdate()) as cnt_year,DATE_FORMAT(DATE_ADD(p.birthday, INTERVAL 543 YEAR),'%Y-%m-%d') as \"birthday\",p.drugallergy,p.clinic,CONCAT(p.addrpart,' หมู่ ',p.moopart,' ',
                          (SELECT t.full_name FROM thaiaddress t WHERE t.addressid=CONCAT(p.chwpart,p.amppart,p.tmbpart))) AS addr
                          ,v.an,l.order_date AS vst,s.bw,s.height,CONCAT(s.bpd,\"/\",s.bps)as bp,s.temperature,s.pulse,s.rr,v.pdx,v.dx0,v.dx1,v.dx2,v.dx3,v.dx4,v.dx5,i.prediag AS cc,l.lab_order_number,l.form_name,'IPD' AS tp-- ,COUNT(l.lab_order_number)
                          FROM an_stat v 
                          LEFT OUTER JOIN lab_head l ON l.vn = v.an 
                          INNER JOIN patient p ON p.hn = v.hn 
                          INNER JOIN ipt i ON i.an=v.an
                          INNER JOIN opdscreen s ON s.vn=v.vn
                         WHERE p.cid = '$no')) AS tb ORDER BY vn DESC";
 // 
 
	$result=mysqli_query($conn,$sql);
	$result_show = mysqli_query($conn,$sql) or die(mysqli_error());
	$row_show = mysqli_fetch_array($result_show);
		
?>
<form name="frmFind" id="frmFind" action="" method="post">
                        <div class="card-body">
          <div class="form-group col-md-12">                          
          <div class="form-group row">
                                    <label class="col-md-12"><h3>ค้นหาข้อมูล สถานะ :<font color="#FF0000">กำลังปรับปรุง</font></h3></label>
            <div class="col-md-4">
                                        <input type="text" class="form-control" name="no" id="no" size="0" placeholder="เลข  13 หลัก">
                              <small id="emailHelp" class="form-text text-muted">กรุณาระบุเลขประชาชน</small>
                              
                                          
            </div>

            <div class="col-md-4">
                                        <button type="reset" class="btn btn-secondary">ยกเลิก</button>
                                          

              <div class="col-md-4">
                                        
                  <button type="submit" class="btn btn-success">ค้นหา</button>            
            </div>
            </div>
</div>
          </div>                          
         
                                <hr>
        <form>
            <div class="form-row text-dark ">
    <div class="form-group col-md-4 text-dark">
      <label for="inputCity">ชื่อ</label>
      <input type="text" class="form-control" id="inputCity" value="<?=$row_show["pname"]?>" readonly="readonly">
    </div>
    <div class="form-group col-md-3 text-dark">
      <label for="inputState">HN</label>
      <input type="text" class="form-control" id="inputZip "value="<?=$row_show["hn"]?>" readonly="readonly">
    </div>
    <div class="form-group col-md-3 text-dark">
      <label for="inputZip">วันเกิด</label>
      <input type="text" class="form-control" id="inputZip" value="<?=$row_show["birthday"]?>" readonly="readonly">
    </div>
     <div class="form-group col-md-2">
      <label for="inputZip">อายุ</label>
      <input type="text" class="form-control" id="inputZip" value="<?=$row_show["cnt_year"]?>"readonly="readonly">
    </div>
</div>
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="inputEmail4">ที่อยู่</label>
      <input type="text" class="form-control" id="inputEmail4" value="<?=$row_show["addr"]?>"readonly="readonly">
    </div>
    <div class="form-group col-md-6">
      <label for="inputPassword4">คลินิก</label>
      <input type="text" class="form-control" id="inputPassword4" value="<?=$row_show["clinic"]?>" readonly="readonly">
    </div>
  </div>
  <div class="form-group">
   <div class="form-group col-md-6">
    <label for="inputAddress">แพ้ยา</label>
    <input type="text" class="form-control" id="inputAddress" value="<?=$row_show["drugallergy"]?>"readonly="readonly">
  </div>
  </div>

                         
          </form>
      </div>
    </div>
  </div>
</div>
</div>

    <!-- End of Topbar -->

       

          <!-- DataTales Example -->
<table id="dynamic-table" class="table table-striped table-bordered table-hover ">

                  <thead>
                    <tr>
                    <th bgcolor="#FFFFFF"  class="p-3 mb-2 bg- text-white">VN</th>
                     <th bgcolor="#FFFFFF"  class="p-3 mb-2 bg-dark text-white">วันที่รับบริการ</th>
                      <th bgcolor="#FFFFFF"  class="p-3 mb-2 bg-dark text-white">pdx</th> 
                      <th bgcolor="#FFFFFF"  class="p-3 mb-2 bg-dark text-white">dx0</th> 
                     <th bgcolor="#FFFFFF"  class="p-3 mb-2 bg-dark text-white">dx1</th> 
                     <th bgcolor="#FFFFFF"  class="p-3 mb-2 bg-dark text-white">dx2</th> 
                      <th bgcolor="#FFFFFF"  class="p-3 mb-2 bg-dark text-white">dx3</th> 
                      <th bgcolor="#FFFFFF"  class="p-3 mb-2 bg-dark text-white">dx4</th> 
                      <th bgcolor="#FFFFFF"  class="p-3 mb-2 bg-dark text-white">dx5</th> 
                       <th bgcolor="#FFFFFF"  class="p-2 mb-2 bg-dark text-white">น้ำหนัก</th>
                      <th bgcolor="#FFFFFF"  class="p-2 mb-1 bg-dark text-white">ส่วนสูง</th> 
                      <th bgcolor="#FFFFFF"  class="p-2 mb-2 bg-dark text-white">BP</th> 
                      <th bgcolor="#FFFFFF"  class="p-2 mb-2 bg-dark text-white">อุณภูมิ</th> 
                      <th bgcolor="#FFFFFF"  class="p-3 mb-2 bg-dark text-white">ชีพจร</th> 
                      <th bgcolor="#FFFFFF"  class="p-3 mb-2 bg-dark text-white">หายใจ</th> 
                      <th bgcolor="#FFFFFF"  class="p-2 mb-2 bg-dark text-white">อาการสำคัญ</th>  
                      <th bgcolor="#FFFFFF"  class="p-3 mb-2 bg-dark text-white">Lab</th> 
                      <th bgcolor="#FFFFFF"  class="p-3 mb-2 bg-dark text-white">ยา</th> 

                    </tr>
                  </thead>
                  
               <?php 
			
			   while($row=mysqli_fetch_array($result, MYSQLI_ASSOC) ){?>
             
					
              <tr>
              
                 <td align="center" bgcolor="#F8F8FF" class="text-dark"><?=$row["vn"]?></td>
                     <? echo "	<td align='center' bgcolor='#FFFFCC' >".DateThai($row["vst"])."</td>"; ?>
                <td  align="center" bgcolor="#F8F8FF" class="text-dark"><?=$row["pdx"]?></td>
                 <td  align="center" bgcolor="#F8F8FF" class="text-dark"><?=$row["dx0"]?></td>
                  <td align="center" bgcolor="#F8F8FF" class="text-dark"><?=$row["dx1"]?></td>
                <td align="center" bgcolor="#F8F8FF" class="text-dark"><?=$row["dx2"]?></td>
                <td  align="center" bgcolor="#F8F8FF" class="text-dark"><?=$row["dx3"]?></td>
                 <td  align="center" bgcolor="#F8F8FF" class="text-dark"><?=$row["dx4"]?></td>
				 <td align="center" bgcolor="#F8F8FF" class="text-dark"><?=$row["dx5"]?></td>
                <td align="center" bgcolor="#F8F8FF" class="text-dark"><?=$row["bw"]?></td>
                <td  align="center" bgcolor="#F8F8FF" class="text-dark"><?=$row["height"]?></td>
                 <td  align="center" bgcolor="#F8F8FF" class="text-dark"><?=$row["bp"]?></td>
                  <td align="center" bgcolor="#F8F8FF" class="text-dark"><?=$row["temperature"]?></td>
                <td align="center" bgcolor="#F8F8FF" class="text-dark"><?=$row["pulse"]?></td>
                <td  align="center" bgcolor="#F8F8FF" class="text-dark"><?=$row["rr"]?></td>
                 <td  align="left" bgcolor="#F8F8FF" class="text-dark"><?=$row["cc"]?></td>
                <td  align="center" bgcolor="#FFFFCC" class="text-white"><button type="button" class="button button-icon-only bbcode-img" name="namedep" id="namedep" value="<?=$row["vn"]?>" onclick="exam_changeso()" >
    <i class="icon fa fa-file-image-o" aria-hidden="true"></i>
</button></a> </td>
               
              </tr>
              <?php 
			  }?>
           
            </tbody>
</table>
<span id="sp_opdu">

<!-- Button trigger modal-->
<?php
$sql2 = "  SELECT *from (select rx.vstdate as ovdate,rx.rxtime,concat(du.name1,' ',du.name2,' ',du.name3) as drugu, 
 concat(d.name,' ',d.strength,' ',d.units) as drugname  
 FROM ovst ov 
 LEFT JOIN opitemrece rx on rx.vn=ov.vn 
 INNER JOIN drugitems d on d.icode=rx.icode 
 LEFT JOIN drugusage du on du.drugusage=rx.drugusage 
 where ov.vn = \"620425053657\"
UNION 
select rx.vstdate as ovdate,rx.rxtime,concat(du.name1,' ',du.name2,' ',du.name3) as drugu, 
 concat(d.name,' ',d.strength,' ',d.units) as drugname  
 FROM an_stat ov 
 LEFT JOIN opitemrece rx on rx.an=ov.an 
LEFT OUTER JOIN an_stat an on an.an=rx.an
 INNER JOIN drugitems d on d.icode=rx.icode 
 LEFT JOIN drugusage du on du.drugusage=rx.drugusage 
 where ov.vn = \"620425053657\") as tb  ORDER BY ovdate,rxtime DESC
";
 // 
 $i = 1; 
	$result2=mysqli_query($conn,$sql2);
	$result_show2 = mysqli_query($conn,$sql2) or die(mysqli_error());
	$row_show2 = mysqli_fetch_array($result_show2); 
?>
<!-- Modal: modalCart --><!-- Modal: modalCart --><!-- /.container-fluid --><!-- End of Main Content -->

      <!-- Footer --><!-- End of Footer --><!-- End of Content Wrapper --><!-- End of Page Wrapper -->

  <!-- Scroll to Top Button--><!-- Bootstrap core JavaScript-->
<script type="text/javascript"> 
$( "#day1 ,#day2" ).datepicker();
$.fn.datepicker.defaults.format = "yyyy/mm/dd";
$('.datepicker').datepicker({
    startDate: '-3d'
});
	
	onbeforeunload();
	
	function DateThai($strDate)
				{
					$strYear = date("Y",strtotime($strDate))+543;
					$strMonth= date("n",strtotime($strDate));
					$strDay= date("j",strtotime($strDate));
					$strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
					$strMonthThai=$strMonthCut[$strMonth];
					return "$strDay $strMonthThai $strYear";
				}
	</script>
<script>
$(document).ready(function(){
	$('.edit-customer.').click(function(){
		$('#formEditCustomer').modal()
	});
});
</script>
<script>
 function mpopimg()
    {
        document.getElementById('light').style.display = 'block';
        document.getElementById('fade').style.display = 'block';
    }

</script>
<script src="js/sb-admin-2.min.js">
	function exam_changeso(vn) {
		$('#sp_opdu').load("userdata.php?vn="+vn);
		
	}
</script>
</body>
</span>
</form>
</html>
