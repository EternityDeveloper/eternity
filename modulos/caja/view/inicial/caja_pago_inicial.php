<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	



/*BUSCAR POR RESERVA */
if (validateField($_REQUEST,"search_peson")&& validateField($_REQUEST,"field")){
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja=new Caja($protect->getDBLink());
	$data=$caja->searchByPerson($_REQUEST['field']);
	
	include("listado_person.php");
	exit;
} 


if (validateField($_REQUEST,"caja_submit")&& validateField($_REQUEST,"id_nit")){
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja=new Caja($protect->getDBLink());
	
	echo json_encode($caja->generarPagoMovimientoIncial());
	 
	exit;
}

/*OBTENER LOS ABONOS A RESERVA DE UN CLIENTE*/
if (validateField($_REQUEST,"getTotalesMonto")&& validateField($_REQUEST,"no_reserva")){
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja=new Caja($protect->getDBLink());
	$no_reserva=System::getInstance()->Decrypt($_REQUEST['no_reserva']); 
 
	$reserva=$caja->getMontoAbonoFromReserva($no_reserva);
	
	$data=array("valid"=>false,"reserva"=>0);
	if ($reserva>0){
		$data['valid']=true;
		$data['reserva']=$reserva;	
	} 
	echo json_encode($data);
	exit;
}

/*CARGO EL ESTADO DE CUENTAS*/ 
if (validateField($_REQUEST,"view_statment")&& validateField($_REQUEST,"id_nit")){
	
	include("estado_cuenta_persona.php");
	exit;
}



	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	 
	SystemHtml::getInstance()->addTagScript("script/Class.js");
	SystemHtml::getInstance()->addTagScript("script/Class.PivotTable.js");
 	SystemHtml::getInstance()->addTagScriptByModule("class.AbonoPersona.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.FormaPago.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.blockUI.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");

	SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.css");
	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	SystemHtml::getInstance()->addModule("main/topmenu");


exit;
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
 
var _abono;

$(function(){ 				
  	_abono= new AbonoPersona('content_dialog'); 
	_abono.searchPerson();
	_abono.search('<?php echo isset($_GET['search'])?$_GET['search']:''?>');

	  
}); 
 
 
</script>
<div id="pago_persona" style="width:98%"> 
 
  <h2>Movimientos de Iniciales</h2>
   
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="">
  <tr>
    <td colspan="2" align="center"><table width="300" border="0" cellspacing="1" cellpadding="1">
      <tr>
        <td align="center"><strong> Buscar por documento </strong></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
    
        <td><input name="numero_documento" type="text" class="textfield" id="numero_documento" ></td>
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