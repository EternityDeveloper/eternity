<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	


	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	 
	SystemHtml::getInstance()->addTagScript("script/Class.js"); 
	 
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js"); 
	SystemHtml::getInstance()->addTagStyle("css/showLoading.css");
	
	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo"); 

 
if (isset($_REQUEST['figs'])){
	echo '<iframe src="http://190.167.211.194:8081/" width="100%" height="500px"></iframe>';

exit;	
}


?><style>
.header_day{
	
}
.header_day a {
	text-decoration:none;
	font-size:13px;
	color:#000;	
}

.line_one{
  background-color:#EEE;	
}
.line_two{
  background-color:#FFF;	
}


</style>
<?php

if (isset($_REQUEST['type'])){
	switch($_REQUEST['type']){
		case "2":
			include("view/general.php");
		break;
		case "3":
			include("view/asesor.php");
		break;
		case "4":
			include("view/analisis.php");
		break;
		case "5":
			include("view/analisis.php");
		break;
		case "6":
			include("view/auditores.php");
		break;	
		case "8":
			include("view/miproduccion.php");
		break;	
		case "9":
			include("view/gerentes.php");
		break;	
		case "10":
			include("view/detalle_ingreso.php");
		break;								
		case "11":
			include("view/detalle_ingreso_agrupado.php");
		break;										
	}

exit;	
}
?>