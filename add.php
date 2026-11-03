
<!DOCTYPE html>
<meta charset="utf-8">
</style>
<?php

session_name("bmhsession");
	session_start();
	
	$_SESSION["menu"] = 88;
include "inc_header.php";
	date_default_timezone_set('Asia/Bangkok'); 
	$date = new DateTime();
?>

<head>
<script type='text/javascript' src='https://www.hosthai.com/js/jquery-1.12.3.min.js'></script>
<script src="https://www.hosthai.com/js/jq-date/jquery-ui.js"></script>
<script src="https://www.hosthai.com/js/jquery-ui.js?v=1545898006">
</script>
<link href="https://www.hosthai.com/js/jq-date/jquery-ui.css?v=1545898006" rel="stylesheet">
  <title>ติดตามการรักษา</title>

  <!-- Custom fonts for this template -->
 

</head>   
<form id="form1" name="form1" method="post" action="insert.php" autocomplete="off">

         <div class="container-fluid" style="margin-top: 1em;">
    <!-- Sign up form -->
    <form>
        <!-- Sign up card -->
        <div class="card person-card">
            <div class="card-body">
                <h2 id="who_message" class="card-title">Advance Care Plan (ACP) สถานะ :<font color="#FF0000">กำลังปรับปรุง</font></h2>
                <button type="button" class="btn btn-success">ดูข้อมูล ผู้ป่วยที่ทำ ACP</button>   
                <!-- First row (on medium screen) -->
                                  <div class="alert alert-secondary" role="alert">  <div class="row">
                    <div class="form-group col-md-1">

                     <label for="email" class="col-form-label">คำนำหน้า</label>
                        <select id="mr" class="form-control">
                            <option value="นาย">นาย</option>
                            <option value="นาง">นาง</option>
                        </select>
                    </div>
                    
                    <div class="form-group col-md-3">
                     <label for="name" class="col-form-label">ชื่อ</label>
                        <input id="name" type="text" class="form-control" placeholder="">
                        <div id="first_name_feedback" class="invalid-feedback">
                            
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                     <label for="lastname" class="col-form-label">สกุล</label>
                        <input id="lastname" type="text" class="form-control" placeholder="">
                        <div id="last_name_feedback" class="invalid-feedback">
                            
                        </div>
                    </div>
 <div class="form-group col-md-1">
                     <label for="email" class="col-form-label">อายุ</label>
                        <input id="age" type="text" class="form-control" placeholder="">
                        <div id="last_name_feedback" class="invalid-feedback">
                            
                        </div>
                    </div>

                        <div class="form-group col-md-2">
                            <label for="text" class="col-form-label">HN</label>
                            <input type="text" class="form-control" id="hn" placeholder="7 หลัก" required>
                            <div class="email-feedback">
                            
</div>
</div>
  

<div class="form-group col-md-2">
                            <label for="daytt" class="col-form-label">วันที่</label><br>
<input type="text" class="col-form-label" name="startday" id="startday" value="">
                            <div class="email-feedback">
                            
</div>
</div><div class="card person-card">
            <div class="card-body">
                    <div class="form-group col-md-6">
                     <label for="disease" class="col-form-label">การรักษาที่ได้รับ :</label>
                         <input id="disease" type="text" class="form-control" placeholder="">
                    </div>
                    <div class="form-group col-md-6">
                     <label for="physician" class="col-form-label">แพทย์เจ้าของไข้ :</label>
                        <input id="physician" type="text" class="form-control" placeholder="">
                        <div id="first_name_feedback" class="invalid-feedback">
                            
                        </div>
                    </div>
          
</div>
</div>
</div>
                <h2 id="who_message" class="card-title">1. ความรับรู้/และการมีส่วนร่วมในการตัดสินใจ</h2>  
<div class="alert alert-info" role="alert">
 <table class="table">
  <thead class="black white-text">
    <tr>
      <th scope="col">ข้อมูล</th>
      <th scope="col">ผู้ป่วย</th>
      <th align="center" scope="col">ครอบครัว</br>(ผู้ตัดสินใจหลัก)</th>
      <th scope="col">ปัญหาที่มี/รายละเอียด</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">การรับรู็เรื่องโรค,ระยะของโรค</th>
      <td><select class="form-control" id="pt1">
      <option>รู้</option>
      <option>ไม่รู้</option>

    </select></td>
      <td><select class="form-control" id="pt2">
      <option>รู้</option>
      <option>ไม่รู้</option>
    </select></td>
      <td><input id="pt3" type="text" class="form-control" placeholder=""></td>
    </tr>
    <tr>
      <th scope="row">การรับรู้พยากรณ์โรค</th>
       <td><select class="form-control" id="pd1">
      <option>รู้</option>
      <option>ไม่รู้</option>
    </select></td>
      <td><select class="form-control" id="pd2">
      <option>รู้</option>
      <option>ไม่รู้</option>
    </select></td>
      <td><input id="pd3" type="text" class="form-control" placeholder=""></td>
    </tr>
    <tr>
      <th scope="row">เป้าหมายการรักษา</th>
       <td><select class="form-control" id="pc1">
      <option>รู้</option>
      <option>ไม่รู้</option>
    </select></td>
      <td><select class="form-control" id="pc2">
      <option>รู้</option>
      <option>ไม่รู้</option>
    </select></td>
      <td><input id="pc3" type="text" class="form-control" placeholder=""></td>
    </tr>
  </tbody>
</table>
</div>
                <h2 id="who_message" class="card-title">2. คำสั่งการช่วยเพื้นคืนชีพ</h2>  
<div class="alert alert" role="alert">
 <div class="form-group row">
    <label for="staticEmail" class="col-sm-2 col-form-label"><h5>2.1 การใส่ท่อช่วยหายใจ :</h5></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="tt21">
    </div>
  </div>
  <div class="form-group row">
    <label for="inputPassword" class="col-sm-2 col-form-label"><h5>2.2 การนวดหัวใจ :</h5></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="tt22">
    </div>
  </div>
  <div class="form-group row">
    <label for="inputPassword" class="col-sm-2 col-form-label"><h5>2.3 การใช้ยากระตุ้นความดัน  :</h5></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="tt23">
    </div>
  </div>
</div>
<h2 id="who_message" class="card-title">3. ความประสงค์เกี่ยวกับการทำงาน Living will</h2>  
<div class="alert alert-light" role="alert">
   <div class="form-group row">
    <label for="inputPassword" class="col-sm-2 col-form-label"><h5>การใช้ยากระตุ้นความดัน  :</h5></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="cc3">
    </div>
  </div>
</div>
  <h2 id="who_message" class="card-title">การให้ข้อมูล</h2>  
<div class="alert alert" role="alert">
 <div class="form-group row">
    <h5><label for="staticEmail" class="col-sm-2 col-form-label"><h5>ผู้ให้ข้อมูล :</h5></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="ee1">
    </div>
  </div>
  <div class="form-group row">
    <label for="inputPassword" class="col-sm-2 col-form-label"><h5>ผู้ตัดสินใจคำสั่งช่วยพื้นคืนชีพ :</h5></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="ee2">
    </div>
  </div>
  <div class="form-group row">
    <label for="inputPassword" class="col-sm-2 col-form-label"><h5>ผู้ดูแลหลัก  :</h5></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="ee3">
    </div>
  </div>
    <div class="form-group row">
    <label for="inputPassword" class="col-sm-2 col-form-label"><h5>พยาบาลผู้ประสาน  :</h5></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="ee4">
    </div>
  </div>
  
</div>
<button type="submit" class="btn btn-primary btn-lg btn-block">บันทึก</button>
<button type="reset" class="btn btn-secondary btn-lg btn-block">ยกเลิก</button>
</form>
<!-- Modal: modalCart --><!-- Modal: modalCart --><!-- /.container-fluid --><!-- End of Main Content -->

      <!-- Footer --><!-- End of Footer --><!-- End of Content Wrapper --><!-- End of Page Wrapper -->

  <!-- Scroll to Top Button--><!-- Bootstrap core JavaScript-->

</body>
</span>

</html>
