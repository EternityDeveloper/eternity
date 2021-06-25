<?php ob_start();
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
header('Content-Type: text/html; charset=UTF-8'); 
session_start();
$_PATH="";
include("includes/protect.php");

 
if ($protect->isLogin()){
 
 
	SystemHTML::getInstance()->setSystemProtect($protect);
	/* Agrego una funcion script al header html*/
	SystemHtml::getInstance()->addTagScript("script/functions.js");
	SystemHtml::getInstance()->addTagStyle("css/south-street/jquery-ui-1.10.3.custom.css");
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery-ui-1.10.3.custom.js");
//	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");
	
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.core.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.widget.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.tabs.js");
	SystemHtml::getInstance()->addTagScript("script/bootstrap/js/bootstrap.min.js");
	SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");	
	SystemHtml::getInstance()->addTagStyle("css/jquery.dataTables.css"); 
//	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.tabs.js");
//	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");
//	SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");

	if (System::getInstance()->getCurrentModulo()==""){
		SystemHtml::getInstance()->addModule("main/inicio");
	}else{ 
		SystemHtml::getInstance()->addModule(System::getInstance()->getCurrentModulo());
	}

}else{
	SystemHtml::getInstance()->addModule("header");
	/*Cargo el modulo de login por que el usuario no ha iniciado session */
	SystemHtml::getInstance()->addModule("login/login_form");

	SystemHtml::getInstance()->addModule("footer");

}	

	





?>
