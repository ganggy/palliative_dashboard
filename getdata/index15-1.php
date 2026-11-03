<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<script type='text/javascript' src='https://www.hosthai.com/js/jquery-1.12.3.min.js'></script>
<script src="https://www.hosthai.com/js/jq-date/jquery-ui.js"></script>
<script src="https://www.hosthai.com/js/jquery-ui.js?v=1545898006">
</script>
<link href="https://www.hosthai.com/js/jq-date/jquery-ui.css?v=1545898006" rel="stylesheet">

<title>Untitled Document</title>
</head>
<p>&nbsp;</p>
<table width="80%" border="0" align="center">
  <tr>
    <th scope="col"><h4>จำนวนผู้ป่วยรายใหม่ <span class="badge badge-secondary">New</span></h4>    
  <tr>
    <th scope="col"><div class="alert alert-secondary" role="alert">
<form>
  <div class="form-row">
    <div class="col">
<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> วันที่เริ่มต้น : </label>
      <input type="text" class="form-control"  id="d1" placeholder="First name">
    </div>
    <div class="col">
    					<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> วันที่สิ้นสุด : </label>
      <input type="text" class="form-control" onChange="loaddata()" id="d2" placeholder="Last name">
    </div>
</div>

  
</table>
<table width="100%" border="0" align="center">
  <tr>
<th scope="col"> &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   &nbsp;&nbsp;   &nbsp;&nbsp;   &nbsp;&nbsp;   &nbsp;&nbsp;   &nbsp;&nbsp;   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <img src="img/ajax-loader.gif" width="36" height="33" id="img_load" /><span  width="100%" height="100%" id="sp_showdata"></span> 
  </tr>
    <th scope="col"><br />
     
      <br /> 
    </tr>
</table>
<table align="center" width="80%" border="0">
  <tr>
    <th scope="col"> <button type="button" class="btn btn-primary btn-lg btn-block">ย้อนกลับ</button></th>
  </tr>
</table>


<script type="text/javascript"> 
$( "#d1 ,#d2" ).datepicker();
	
	onbeforeunload();
	
function ex2excel(){
	$("#pframe").attr("src", "excel#.php?d1="+$("#d1").val()+"&d2="+$("#d2").val()+" "  );
	//alert(  );
	console.log("Excel Click : excel#.php?d1="+$("#d1").val()+"&d2="+$("#d2").val()+" " );
}
function loaddata(){
    $("#img_load").show();
 	$("#sp_showdata").html("");
	$("#sp_showdata").load("getdata15-1.php?d1="+$("#d1").val()+"&&d2="+$("#d2").val()+" " );
	
}
	
	
</script>
<iframe name="pframe" id="pframe" width="70%" height="1000" frameborder="0" style="display:none;"></iframe>

</script>
</body>
</html>