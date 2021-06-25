<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

if (isset($_REQUEST['view_movimiento'])){
	include("view/vtipo_mov.php");
	exit;	
}

if (isset($_REQUEST['buscador'])){
	include("view/buscador.php");
	exit;	
}
if (isset($_REQUEST['mov_cliente'])){
	include("cliente/estado_cuenta_persona.php");
	exit;	
}

/*PROCESA LAS TRANSACCIONES ABONO A CLIENTE*/
if (validateField($_REQUEST,"form_submit_abono_persona") ){
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja=new Caja($protect->getDBLink());
	echo json_encode($caja->generarTransaccion());
	 
	exit;
}
/*CALCULAR 10% DEL MONTO DE UNA PARCELA*/
if (validateField($_REQUEST,"getMontoMinimoPagarReserva") && validateField($_REQUEST,"id_nit") 
		&& validateField($_REQUEST,"no_reserva") ){
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja=new Caja($protect->getDBLink());
 
	$rt=$caja->getMontoReservaYpendiente(System::getInstance()->Decrypt($_REQUEST['id_nit']),
									System::getInstance()->Decrypt($_REQUEST['no_reserva']));
									
	SystemHtml::getInstance()->includeClass("caja","CTipoMovimiento"); 
	$TMOV= new CTipoMovimiento($protect->getDBLink());								
	$mov=$TMOV->getTipoMov(); 
	$rt['tipo_movimiento']	=$mov->TIPO_MOV;							
	echo json_encode($rt); 
	 
	exit;
}

/*CALCULAR EL MONTO A PAGAR DE UNA CUOTA DE UN CONTRATO*/
if (validateField($_REQUEST,"getMontoPagarContrato") && validateField($_REQUEST,"no_contrato")   ){
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	
	$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['no_contrato']));  
	$caja=new Caja($protect->getDBLink());
	 
	$rt=$caja->getMontoaPagarContrato($contrato->serie_contrato,$contrato->no_contrato);
									
	SystemHtml::getInstance()->includeClass("caja","CTipoMovimiento"); 
	$TMOV= new CTipoMovimiento($protect->getDBLink());								
	$mov=$TMOV->getTipoMov(); 
	$rt['tipo_movimiento']	=$mov->TIPO_MOV;							
	echo json_encode($rt); 
	 
	exit;
}


/*PROCESA LOS CIERRES DE CAJA*/
if (validateField($_REQUEST,"processCierreCaja")){
	if (validateField($_REQUEST,"id_caja") && validateField($_REQUEST,"tipo_cierre") ){ 
		SystemHtml::getInstance()->includeClass("caja","Caja");   
		$caja=new Caja($protect->getDBLink());
		
		$idCaja=json_decode(System::getInstance()->Decrypt($_REQUEST['id_caja']));
		
		echo json_encode($caja->generarCierre($idCaja->ID_CAJA,$_REQUEST['tipo_cierre']));
		//print_r($idCaja);
	
	}else{
		$rt=array("valid"=>false,"mensaje"=>"Error debe de seleccionar un tipo de cierre!");
		echo json_encode($rt);
		
	}
	
	exit;
}


/*PROCESA EL DETALLE DE LOS PAGOS DE CONTRATOS*/
if (validateField($_REQUEST,"payment_detalle_contrato")){
	if (validateField($_REQUEST,"id_nit") && validateField($_REQUEST,"contrato") ){ 
		SystemHtml::getInstance()->includeClass("caja","Caja");   
		$caja=new Caja($protect->getDBLink());
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));
	//	print_r($contrato);
		include("contrato/detalle_pago_contrato.php");
	
	} 
	
	exit;
}


  
SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js"); 
SystemHtml::getInstance()->addTagScript("script/Class.js");   
SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");

SystemHtml::getInstance()->addTagScriptByModule("class.COperacion.js"); 

SystemHtml::getInstance()->addTagStyle("css/smoothness/jquery.ui.combogrid.css");
SystemHtml::getInstance()->addTagScript("script/jquery.ui.combogrid-1.6.3.js");

SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");

SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
SystemHtml::getInstance()->addTagScript("script/jquery.blockUI.js");

SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");
   


/*Cargo el Header*/
SystemHtml::getInstance()->addModule("header");
SystemHtml::getInstance()->addModule("header_logo");
/* cargo el modulo de top menu*/
SystemHtml::getInstance()->addModule("main/topmenu");
 
?>
 
<script>
 
var _operacion;

$(function(){ 				
  	_operacion= new COperacion('content_dialog'); 
	_operacion.doViewQuestion();
	  
}); 
  
</script>
<div  id="c_view" style="width:98%"> 
 
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="">
  <tr>
    <td colspan="2" align="center">&nbsp; </td>
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