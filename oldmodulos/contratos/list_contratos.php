<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
SystemHtml::getInstance()->includeClass("estructurac","Asesores"); 
SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
 
 
if (isset($_REQUEST['view_search'])){
	include("view/view_search.php");
	exit;
}
 


if (isset($_REQUEST['add_producto'])){
	include("view/producto.php");
	exit;
}

if (isset($_REQUEST['add_descuento'])){
	include("view/add_descuento.php");
	exit;
}
 
 
 
	/*QUERY PARA BUSQUEDA */
	if (isset($_REQUEST['x_search'])){
		SystemHtml::getInstance()->includeClass("contratos","Contratos");
			
		$_contratos= new Contratos($protect->getDBLink(),$_REQUEST);
	 
		$result=$_contratos->getListContratos();
		 
		echo json_encode($result);
		
		exit;
		
	}
	
 	$_contratos=new Contratos($protect->getDBLink());
	//$_contratos->session_restart();
	
	
	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	 
	SystemHtml::getInstance()->addTagScript("script/Class.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.Contratos.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.PlanProductos.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.PlanServicios.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.Descuentos.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.PlanTotalDescuentos.js");  
	SystemHtml::getInstance()->addTagScriptByModule("class.Captura.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.ContratoCaja.js");
	
	SystemHtml::getInstance()->addTagScriptByModule("Class.prospectos.js","prospectos");   
	SystemHtml::getInstance()->addTagScriptByModule("class.inventario.js","inventario");  
	SystemHtml::getInstance()->addTagScriptByModule("class.Servicios.js","servicios");  
	 
	
	SystemHtml::getInstance()->addTagScriptByModule("Class.financiamiento.js","financiamiento");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.hotkeys.js");
	
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Empresa.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Persona.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Direccion.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Telefono.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Email.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Referencia.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Contactos.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Referidos.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.ModuloPersona.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.beneficiario.js");
	
	
	SystemHtml::getInstance()->addTagScript("script/Class.direcciones.js");
	SystemHtml::getInstance()->addTagScript("script/Class.phone.js");
	SystemHtml::getInstance()->addTagScript("script/Class.empresa.js");
	SystemHtml::getInstance()->addTagScript("script/Class.email.js");
	SystemHtml::getInstance()->addTagScript("script/Class.reference.js");
	SystemHtml::getInstance()->addTagScript("script/Class.contactos.js");
	SystemHtml::getInstance()->addTagScript("script/Class.AsesoresTree.js");
	SystemHtml::getInstance()->addTagScript("script/Class.Referidos.js");
	SystemHtml::getInstance()->addTagScript("script/personalData.js");
	SystemHtml::getInstance()->addTagScript("script/Class.reservas.js");
	
	SystemHtml::getInstance()->addTagScript("script/Class.AsesoresTree.js");

	SystemHtml::getInstance()->addTagScriptByModule("class.CDetalle.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.CDocument.js");	
	

	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");

	SystemHtml::getInstance()->addTagScript("script/jquery.timeentry.min.js");
	
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
	
	SystemHtml::getInstance()->addTagScript("script/qtip/jquery.qtip.min.js");
	SystemHtml::getInstance()->addTagStyle("script/qtip/jquery.qtip.min.css");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");
 
	SystemHtml::getInstance()->addTagStyle("css/jquery.ptTimeSelect.css");
	
	SystemHtml::getInstance()->addTagStyle("css/showLoading.css");
 
	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
	
	SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");
	SystemHtml::getInstance()->addTagStyle("css/select2-bootstrap.css");
	SystemHtml::getInstance()->addTagStyle("css/select2.css");
	SystemHtml::getInstance()->addTagStyle("css/stl_upload.css");	

	SystemHtml::getInstance()->addTagScript("script/jquery.fileupload.js"); 
	SystemHtml::getInstance()->addTagScript("script/jquery.knob.js"); 
 	
	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	SystemHtml::getInstance()->addModule("main/topmenu");
 
  
?>
<style>
.dataTables_filter{
	width:80%;	
	margin-top:0px;
	margin-left:10px;
}
.fsPage{
	width:99%;
}
.fields_hidden{
	display:none;	
}

.ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
.ui-timepicker-div dl { text-align: left; }
.ui-timepicker-div dl dt { float: left; clear:left; padding: 0 0 0 5px; }
.ui-timepicker-div dl dd { margin: 0 10px 10px 40%; }
.ui-timepicker-div td { font-size: 90%; }
.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }

.ui-timepicker-rtl{ direction: rtl; }
.ui-timepicker-rtl dl { text-align: right; padding: 0 5px 0 0; }
.ui-timepicker-rtl dl dt{ float: right; clear: right; }
.ui-timepicker-rtl dl dd { margin: 0 40% 10px 10px; }


.AlertColor5 td
{
 color:#000;
 background-color: #FFD24D !important;
}
.AlertColorDanger td
{
 color:#FFF;
 background-color: #D90000 !important;
}
.even{
 background-color: #E2E4FF !important;
}



.ui-tabs-vertical { width: 60em; }
.ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 15em; }
.ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
.ui-tabs-vertical .ui-tabs-nav li a { display:block; }
.ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; border-right-width: 1px; }
.ui-tabs-vertical .ui-tabs-panel { padding: 1em; float: right; width: 40em;}

table.detalle_costo{
	font-size:12px;	
}
.ui-dialog-content{
 padding:0px;
 margin:0px;
 	
}

</style>
<?php

	if (isset($_REQUEST['add_contrato'])){
		include("view/add_contrato.php");
		exit;
	}
	if (isset($_REQUEST['view_contrato'])){
		include("view/view_contrato.php");
		exit;
	}	
	
?>
<script>
 
var _contratos;

$(function(){
 						
  	_contratos= new Contratos('content_dialog');
	_contratos.createTableViewContrato('contratos_list');
	
 
	
});
 
 
</script>
 
<div  class="fsPage">
  <h2>Listado de Contratos</h2>
 	<table border="0" class="display" id="contratos_list" style="font-size:13px">
      <thead>
        <tr>
          <th>Contrato</th>
          <th>Cliente</th>
          <th>Fecha</th>
          <th>Fecha venta</th>
          <th>Empresa</th>
          <th>No. Productos</th>
          <th>Asesor</th>
          <th>Estatus </th>
          <th>Observaciones</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>

      </tbody>
  </table>
</div>
<div id="content_dialog" ></div>
<?php SystemHtml::getInstance()->addModule("footer");?>