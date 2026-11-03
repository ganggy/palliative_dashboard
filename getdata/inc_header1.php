<?php
/*
if(isset($_SESSION["user_11097"])){
	$hos_user=$_SESSION["user_11097"];
}
else if(isset($_COOKIE["user_11097"])){
	$hos_user=$_COOKIE["user_11097"];
}
else header("Location: http://www.bmhos.com/login?url=http://program.bmhos.com/palliative",TRUE,302);
*/
$thos_user = array("05569", "05570", "05571", "05572", "05573");

$thos_name["05569"]="รพ.สต.บ้านห้วยหีบรุ่งอรุณ";
$thos_name["05570"]="รพ.สต.บ้านโพนค้อ";
$thos_name["05571"]="รพ.สต.บ้านม่วงไข่น้อย";
$thos_name["05572"]="รพ.สต.บ้านโคกนาดี";
$thos_name["05573"]="รพ.สต.บ้านโพนทองวัฒนา";


include "condb.php";

?>

<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>Palliative Care</title>
		<meta name="description" content="DataCenter Temperature" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		
		<link rel="shortcut icon" href="assets/images/icon.ico" />
		
		<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="assets/font-awesome/4.7.0/css/font-awesome.min.css" />
		<link rel="stylesheet" href="assets/css/fonts.googleapis.com.css" />
		<link rel="stylesheet" href="assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
		<link rel="stylesheet" href="assets/css/ace-skins.min.css" />
		<link rel="stylesheet" href="assets/css/ace-rtl.min.css" />
        <link rel="stylesheet" href="assets/css/chosen.min.css" />
        <link rel="stylesheet" href="assets/css/buttons.dataTables.min.css" />
        <link rel="stylesheet" href="assets/css/jquery-ui.min.css" />
		<link rel="stylesheet" href="assets/css/jquery-ui.custom.min.css" />
		<link rel="stylesheet" href="assets/css/bootstrap-datepicker3.min.css" />
		<link rel="stylesheet" href="assets/css/bootstrap-timepicker.min.css" />
		<link rel="stylesheet" href="assets/css/daterangepicker.min.css" />
		<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css" />
		<link rel="stylesheet" href="assets/css/bootstrap-colorpicker.min.css" />


		
	</head>
	<body class="no-skin">
		<!-- #section:basics/navbar.layout -->
		<div id="navbar" class="navbar navbar-default">
			<script type="text/javascript">
				try{ace.settings.check('navbar' , 'fixed')}catch(e){}
			</script>

			<div class="navbar-container" id="navbar-container">
				<!-- #section:basics/sidebar.mobile.toggle -->
				<button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
					<span class="sr-only">Toggle sidebar</span>

					<span class="icon-bar"></span>

					<span class="icon-bar"></span>

					<span class="icon-bar"></span>
				</button>

				<!-- /section:basics/sidebar.mobile.toggle -->
				<div class="navbar-header pull-left">
					<!-- #section:basics/navbar.layout.brand -->
					<a href="#" class="navbar-brand">
						
							<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
							ระบบรายงาน Palliative Care
                             
					</a>
				</div>
				
			</div><!-- /.navbar-container -->
		</div>

		<!-- /section:basics/navbar.layout -->
		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>
			
			<div id="sidebar" class="sidebar responsive">
				<script type="text/javascript">
					try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
				</script>
				<ul class="nav nav-list">
					 
                    <li class="<?php if($_SESSION["menu"]==2) echo "active"; else echo "";?>">
						<a href="index15.php">
							<i class="menu-icon fa fa-address-card"></i>
							<span class="menu-text">จำนวนผู้ป่วยรายใหม่</span>
						</a>
					</li>
                    <li class="<?php if($_SESSION["menu"]==17) echo "active"; else echo "";?>">
						<a href="index1.php">
							<i class="menu-icon fa fa-address-card"></i>
							<span class="menu-text">(รายใหม่/รายเก่า)</span>
						</a>
					</li>
                            <li class="<?php if($_SESSION["menu"]==3) echo "active"; else echo "";?>">
						<a href="index2.php">
							<i class="menu-icon fa fa-address-card"></i>
							<span class="menu-text">(Pain,Morphine)</span>
						</a>
					</li>
                     <li class="<?php if($_SESSION["menu"]==4) echo "active"; else echo "";?>">
						<a href="index3.php">
							<i class="menu-icon fa fa-address-card"></i>
							<span class="menu-text">ได้รับ oxygen</span>
						</a>
					</li>
					<li class="<?php if($_SESSION["menu"]==5) echo "active"; else echo "";?>">
						<a href="index4.php">
							<i class="menu-icon fa fa-address-card"></i>
							<span class="menu-text">ไม่ได้รับการเยี่ยม</span>
						</a>
					</li>
					<li class="<?php if($_SESSION["menu"]==6) echo "active"; else echo "";?>">
						<a href="index5.php">
							<i class="menu-icon fa fa-address-card"></i>
							<span class="menu-text">ได้รับการเยี่ยม</span>
						</a>
					</li>
					<li class="<?php if($_SESSION["menu"]==7) echo "active"; else echo "";?>">
						<a href="index6.php">
							<i class="menu-icon fa fa-address-card"></i>
							<span class="menu-text">สรุปกลุ่มโรค</span>
						</a>
					</li>
					<li class="<?php if($_SESSION["menu"]==8) echo "active"; else echo "";?>">
						<a href="index7.php">
							<i class="menu-icon fa fa-address-card"></i>
							<span class="menu-text">ผู้ป่วยที่เสียชีวิตแล้ว</span>
						</a>
					</li>
					<li class="<?php if($_SESSION["menu"]==9) echo "active"; else echo "";?>">
						<a href="index17.php">
							<i class="menu-icon fa fa-address-card"></i>
							<span class="menu-text">ผู้ป่วยที่ไม่มี  Z718</span>
						</a>
					</li>
                   <li  class="<?php if($_SESSION["menu"]==10) echo "active"; else echo "";?>">
						<a href="../index.php">
							<i  class="menu-icon fa fa-reply"></i>
							<span  class="menu-text">กลับหน้าหลัก</span>
						</a>
					</li>
                   
                
                   
					
					
				</ul><!-- /.nav-list -->

				<!-- #section:basics/sidebar.layout.minimize -->
				<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
					<i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
				</div>

				<!-- /section:basics/sidebar.layout.minimize -->
				<script type="text/javascript">
					try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
				</script>
			</div>


			<div class="main-content">
				<div class="main-content-inner">