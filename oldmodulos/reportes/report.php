<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

if (isset($_REQUEST['filter'])){
	switch($_REQUEST['filter']){
		case 1:
			include("view/detalle/detalle_x_asesor.php");
			exit;
		break;	
		case 2:
			include("view/detalle/detalle_x_cliente.php");
			exit;
		break;			
	}
}

	//removeTagScript 
	SystemHtml::getInstance()->addTagScript("script/functions.js");
//	SystemHtml::getInstance()->removeTagStyle("css/south-street/jquery-ui-1.10.3.custom.css");
	SystemHtml::getInstance()->removeTagScript("script/jquery/jquery-ui-1.10.3.custom.js");
 	
//	SystemHtml::getInstance()->removeTagScript("script/ui/jquery.ui.core.js");
//	SystemHtml::getInstance()->removeTagScript("script/ui/jquery.ui.widget.js");
	SystemHtml::getInstance()->removeTagScript("script/ui/jquery.ui.tabs.js");
	SystemHtml::getInstance()->removeTagScript("script/bootstrap/js/bootstrap.min.js");
	SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");	
	SystemHtml::getInstance()->removeTagStyle("css/jquery.dataTables.css"); 	
	 
	SystemHtml::getInstance()->addTagScript("script/jquery.blockUI.js");
	SystemHtml::getInstance()->addTagStyle("css/style.css");
	
	SystemHtml::getInstance()->addTagStyle("css/bar_menu.css"); 
	SystemHtml::getInstance()->removeTagScript("script/select2.min.js"); 
	SystemHtml::getInstance()->addTagScript("script/jquery.base64.min.js"); 
	SystemHtml::getInstance()->removeTagStyle("css/select2-bootstrap.css");
	SystemHtml::getInstance()->removeTagStyle("css/select2.css");
	SystemHtml::getInstance()->removeTagScript("script/tinyeditor.js"); 
	SystemHtml::getInstance()->removeTagStyle("css/tinyeditor.css"); 	
	//////////////////////////
	 
	SystemHtml::getInstance()->addTagScript("script/Class.js"); 
	 
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js"); 
	SystemHtml::getInstance()->addTagStyle("css/showLoading.css");  
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
		case "12":
			include("view/reporte_venta_x_director_divicion.php");
		break;												
	}

exit;	
}
?>