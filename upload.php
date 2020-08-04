<?php 
ob_start(); 
header('Content-Type: text/html; charset=UTF-8'); 
session_start();
$_PATH="";
include("includes/protect.php");

 
	if ($protect->isLogin()){
	//listado de extensiones permitidas
	$allowed = array('png', 'jpg', 'gif','zip','pdf','docx','doc');
	
	if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0){ 
		$extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);
	
		if(!in_array(strtolower($extension), $allowed)){
			echo '{"status":"error"}';
			exit;
		} 
		if(move_uploaded_file($_FILES['upl']['tmp_name'], 'up_loads_contratos/'.$_FILES['upl']['name'])){
			echo '{"status":"success"}';
			exit;
		}
	}
}
echo '{"status":"error"}';
exit;
