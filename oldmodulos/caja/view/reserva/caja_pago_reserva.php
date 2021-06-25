<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
 
/*BUSCAR POR PERSONA*/
if (validateField($_REQUEST,"search_peson")&& validateField($_REQUEST,"field")){
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja=new Caja($protect->getDBLink());
	$data=$caja->searchByReserva($_REQUEST['field']);
	
	include("listado_reserva.php");
	exit;
}

if (validateField($_REQUEST,"caja_submit")&& validateField($_REQUEST,"no_reserva")){
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja=new Caja($protect->getDBLink());
 
	echo json_encode($caja->generarPagoReserva());
	 
	exit;
}

if (validateField($_REQUEST,"caja_submit_pago_inicial")&& validateField($_REQUEST,"no_reserva")){
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja=new Caja($protect->getDBLink());
 
	echo json_encode($caja->generarPagoInicial());
	 
	exit;
}

/*PROCESOS QUE GESTIONAN LOS PAGOS A INICIAL*/
if (validateField($_REQUEST,"payment")&& validateField($_REQUEST,"reserva")){
	include("payment_reserva.php");
	exit;
}
/*VISTA DE PAGO INICIAL*/
if (validateField($_REQUEST,"payment_inicial")&& validateField($_REQUEST,"reserva")){
	include("payment_inicial.php");
	exit;
}

/*CARGO EL ESTADO DE CUENTAS*/ 
if (validateField($_REQUEST,"view_statment")&& validateField($_REQUEST,"no_reserva")){
	include("estado_de_cuenta_reserva.php");
	exit;
}


	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	 
	SystemHtml::getInstance()->addTagScript("script/Class.js");
	SystemHtml::getInstance()->addTagScript("script/Class.PivotTable.js"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.PagoReserva.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.PagoInicial.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.FormaPago.js");
		
	SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");
	
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.tabs.js");	

	SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.blockUI.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");
 
//	SystemHtml::getInstance()->addTagStyle("css/showLoading.css");
	
//	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
//	SystemHtml::getInstance()->addTagStyle("css/demo_table.css");

	SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.css");
 
 
 	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	SystemHtml::getInstance()->addModule("main/topmenu");


 
?>
<style>
h2 { 
	padding:0.5em 0 0.5em 20px; 
	font-size:12pt; 
	font-family:Georgia; 
	color:white; 
	background:silver; 
	text-shadow:1px 1px 2px gray; 
	clear:both; 
	-moz-border-radius:2px; 
	border-radius:2px; 
	-webkit-border-radius:2px;
	background:#65BB56; 
	margin-bottom:5px;
}

.tb_forma_pago tr{
  padding: 7px;
  line-height: 1.42857143; 
}
</style>
<script>
 
var _pago_reserva;

$(function(){ 				
  	_pago_reserva= new PagoReserva('content_dialog'); 
	_pago_reserva.searchPerson();
	_pago_reserva.search('<?php echo isset($_GET['search'])?$_GET['search']:''?>');
	  
}); 
  
</script>
<div id="caja_reserva_view" style="width:98%"> 
 
  <h2>Movimientos de Reserva</h2>
   
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="">
  <tr>
    <td colspan="2" align="center"><table width="300" border="0" cellspacing="1" cellpadding="1">
      <tr>
        <td align="center"><strong> Buscar por documento / No. Reserva</strong></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
    
        <td><input type="text" id="numero_documento" name="numero_documento" style="width:220px;margin-left:10px;margin-top:5px;" class="textfield" ></td>
        <td>&nbsp;</td>
        <td><button type="button" id="_buscar" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">BUSCAR</span></button></td>
      </tr>
    </table></td>
    </tr>
  <tr>
    <td colspan="2" id="detalle_search" style="padding-top:10px;">&nbsp;</td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  </table>



</div>
<div id="content_dialog" ></div>