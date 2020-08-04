<?php ob_start();
include("includes/protect.php");
 	
 
$protect->login("admin","H31pd3sk01");	
 	
SystemHTML::getInstance()->setSystemProtect($protect);
SystemHtml::getInstance()->includeClass("cobros","Cobros");  
SystemHtml::getInstance()->addTagStyle("css/demo_table.css");

SystemHtml::getInstance()->addModule("header");	 

include("modulos/cobros/screen_display.php");
?>