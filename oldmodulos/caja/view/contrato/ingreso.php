<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

 
/*BUSCAR POR CONTRATO*/ 
if (validateField($_REQUEST,"search_contrato")&& validateField($_REQUEST,"field")){
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja=new Caja($protect->getDBLink());
	$data=$caja->searchByContrato($_REQUEST['field']);
	
	include("listado.php");
	exit;
}

/*MOSTRAR VISTA */ 
if (validateField($_REQUEST,"viewPagoReserva")&& validateField($_REQUEST,"contrato")){ 
	include("view/view_pago_reserva.php");
	exit;
}
 
/*OPTENER MONTOS TOTALES DE RESERVA DE UN CONTRATO*/
if (validateField($_REQUEST,"getTotalesMonto")&& validateField($_REQUEST,"contrato")){
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	$caja=new Caja($protect->getDBLink());
	$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
	
	$data=array("valid"=>false,"data"=>array());
	
	$reserva=$caja->getMontoFromReserva($contrato->serie_contrato,$contrato->no_contrato);
	if ($reserva>0){
		$data['valid']=true;
	}
	$data['data']['reserva']=$reserva;	
	
	$enganche=$caja->getMontoEnganche($contrato->serie_contrato,$contrato->no_contrato);
	if (($enganche>0)){
		$data['valid']=true;
	}	
	$data['data']['enganche']=$enganche;
	
	//sleep(2);	
	echo json_encode($data);
	exit;
} 

/*MUESTRA VISTA DE PAGOS A CONTRATOS*/
if (validateField($_REQUEST,"payment")&& validateField($_REQUEST,"contrato")){
	include("payment_contrato.php");
	exit;
}

/*CARGO EL ESTADO DE CUENTAS*/ 
if (validateField($_REQUEST,"view_statment")&& validateField($_REQUEST,"no_contrato")){
	include("estado_de_cuenta_gral.php");
	exit;
}


 
SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
 
SystemHtml::getInstance()->addTagScript("script/Class.js");
SystemHtml::getInstance()->addTagScript("script/Class.PivotTable.js");
SystemHtml::getInstance()->addTagScriptByModule("class.PagoContrato.js");

SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");

SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");

SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
SystemHtml::getInstance()->addTagScript("script/jquery.blockUI.js");

SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");


SystemHtml::getInstance()->addTagStyle("css/showLoading.css");

SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.css");


/*Cargo el Header*/
SystemHtml::getInstance()->addModule("header");
SystemHtml::getInstance()->addModule("header_logo");
/* cargo el modulo de top menu*/
SystemHtml::getInstance()->addModule("main/topmenu");
 
?>
<style>
#sortable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
#sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
#sortable li span { position: absolute; margin-left: -1.3em; }

.sort{
	text-decoration:none;
	list-style:none;
	width:98%; 
	padding:18px;
	background:#CCC;
}
.sort li{
	display:inline;
	width:50px;
	height:10px;
	margin:5px;
	padding:5px;
}

.sort_v{
	text-decoration:none;
	list-style:none;
	width:200px;
	height:300px;
	padding:18px;
	background:#CCC;
}
.sort_v li{
	width:100px;
	height:10px;
	margin:5px;
	padding:5px;
} 
.ui-state-highlight { height: 1.5em; line-height: 1.2em; }

.content_item{
	border:#999 solid 1px;
	border-radius:3px;
	padding:2px;
	background:#D7D7D7;
}
.pvtTriangle{
	display:inline-block;
	cursor:pointer;
	background:url(images/sort_desc.png);
	background-position:3px -6px;
	background-repeat:no-repeat;
	width:19px; 
	height:19px; 
}

h2{
	color:#FFF;	
}
  
.tb_detalle > tbody > tr > th,
.tb_detalle > tfoot > tr > th,
.tb_detalle > thead > tr > td,
.tb_detalle > tbody > tr > td,
.tb_detalle > tfoot > tr > td {
  padding: 7px;
  line-height: 1.42857143;
  vertical-align: top;
  border-top: 1px solid #ddd;
}


	
</style>
<script>
 
var _caja;

$(function(){ 				
  	_caja= new PagoContrato('content_dialog'); 
	_caja.enableSearch();
	  
}); 

$(function() {
	 
	 /*
    $("#sortable1  > li" ).draggable({
      appendTo: "body",
      helper: "clone"
    });
    $("#sortable2  > li" ).draggable({
      appendTo: "body",
      helper: "clone"
    });	

	$("#sortable1").droppable({
		  activeClass: "ui-state-highlight",
		  hoverClass: "ui-state-highlight",
		  accept: "#sortable2 > li",
		  drop: function( event, ui ) {   
			$('<li class="ui-state-highlight placeholder"></li>').text( ui.draggable.text() ).appendTo( this );
			ui.draggable.remove();
		  }
	  });
	  
	$("#sortable2").droppable({
		  activeClass: "ui-state-highlight",
		  hoverClass: "ui-state-highlight",
		  accept: "#sortable1 > li",
		  drop: function( event, ui ) {  
			$('<li class="ui-state-highlight placeholder"></li>').text( ui.draggable.text() ).appendTo( this );
			ui.draggable.remove();
		  }
	  }); 

	$("#sortable1").sortable({
	   placeholder: "ui-state-highlight" 	   
    });
	$("#sortable2").sortable({
	   placeholder: "ui-state-highlight" 
    });*/
	 
	
//	setTimeout("addItem('conainer_top_item')",1000);
	
	var tablet= new PivotTable();
	tablet.create();
	
}); 


function addItem(items){ 
	for(i=0;i<5;i++){
		$('<li class="content_item"></li>').text("item "+i).appendTo($("#"+items));	
	}	
}
 
</script>
<div  id="caja_contrato_view" style="width:98%"> 

  <h2>Movimientos Contratos</h2>
   
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="">
  <tr>
    <td colspan="2" align="center"><table width="300" border="0" cellspacing="1" cellpadding="1">
      <tr>
        <td align="center"><strong> Buscar por documento / contrato </strong></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
    
        <td><input name="numero_documento" type="text" class="textfield" id="numero_documento" style="width:220px;margin-left:10px;margin-top:5px;" value="" ></td>
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