<?php
	session_name("bmhsession");
	session_start();
	session_destroy();
	header("location:login.php");
?>