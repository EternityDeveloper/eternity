<?php ob_start();
include("includes/protect.php");
 	
 
$protect->login("admin","H31pd3skChange$");	
 	
SystemHTML::getInstance()->setSystemProtect($protect);
SystemHtml::getInstance()->includeClass("cobros","Cobros");  
SystemHtml::getInstance()->addTagStyle("css/demo_table.css");

include("modulos/cobros/screen_display.php");
?>