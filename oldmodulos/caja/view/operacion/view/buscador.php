<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	


SystemHtml::getInstance()->includeClass("caja","CTipoMovimiento"); 
$TMOV= new CTipoMovimiento($protect->getDBLink());

/*BUSCAR POR CONTRATO*/ 
if (validateField($_REQUEST,"search_")&& validateField($_REQUEST,"document")){
	if (validateField($_REQUEST,"type")){
		SystemHtml::getInstance()->includeClass("caja","Caja"); 
		$caja=new Caja($protect->getDBLink());
		if ($_REQUEST['type']=="CLIENTE"){
			$TMOV->setTMovSelected("AFEC_CLIENTE");
			$data=$caja->searchByPerson($_REQUEST['document']);
			include("listado_result.php");
		}
		if ($_REQUEST['type']=="RESERVA"){
			$TMOV->setTMovSelected("AFEC_RESERVA");
			$data=$caja->searchByReserva($_REQUEST['document']);
			include("listado_reserva.php");
		}	 

		if ($_REQUEST['type']=="CONTRATO"){
			$TMOV->setTMovSelected("AFEC_CONTRATO");
			$data=$caja->searchByContrato($_REQUEST['document']);
			include("listado_contrato.php");
		}	 		
		
	}
	exit;
}
   

if (validateField($_REQUEST,"id") && validateField($_REQUEST,"setid")){
	/*PASO UN OBJECTO TIPO MOVIMIENTO*/
	$TMOV->setTMov(json_decode(System::getInstance()->Decrypt($_REQUEST['id'])));
}
  
 
SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js"); 
SystemHtml::getInstance()->addTagScript("script/Class.js");  

SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
SystemHtml::getInstance()->addTagStyle("css/showLoading.css");

SystemHtml::getInstance()->addTagScriptByModule("class.COperacion.js"); 
SystemHtml::getInstance()->addTagScriptByModule("class.AbonoPersona.js"); 
SystemHtml::getInstance()->addTagScriptByModule("class.PagoReserva.js"); 
SystemHtml::getInstance()->addTagScriptByModule("class.PagoContrato.js"); 
SystemHtml::getInstance()->addTagScriptByModule("class.FormaPago.js");
SystemHtml::getInstance()->addTagScriptByModule("class.TDocSerieRVenta.js");
SystemHtml::getInstance()->addTagScriptByModule("class.CFactura.js");

SystemHtml::getInstance()->addTagScript("script/persona/Class.Empresa.js");
SystemHtml::getInstance()->addTagScript("script/persona/Class.Persona.js");
SystemHtml::getInstance()->addTagScript("script/persona/Class.Direccion.js");
SystemHtml::getInstance()->addTagScript("script/persona/Class.Telefono.js");
SystemHtml::getInstance()->addTagScript("script/persona/Class.Email.js");
SystemHtml::getInstance()->addTagScript("script/persona/Class.Referencia.js");
SystemHtml::getInstance()->addTagScript("script/persona/Class.Contactos.js");
SystemHtml::getInstance()->addTagScript("script/persona/Class.Referidos.js");
SystemHtml::getInstance()->addTagScript("script/persona/Class.ModuloPersona.js");
 
SystemHtml::getInstance()->addTagStyle("css/smoothness/jquery.ui.combogrid.css");
SystemHtml::getInstance()->addTagScript("script/jquery.ui.combogrid-1.6.3.js");

SystemHtml::getInstance()->addTagScript("script/Class.direcciones.js");
SystemHtml::getInstance()->addTagScript("script/Class.phone.js");
SystemHtml::getInstance()->addTagScript("script/Class.empresa.js");
SystemHtml::getInstance()->addTagScript("script/Class.email.js");
SystemHtml::getInstance()->addTagScript("script/Class.reference.js");
SystemHtml::getInstance()->addTagScript("script/Class.contactos.js");
SystemHtml::getInstance()->addTagScript("script/Class.AsesoresTree.js");
SystemHtml::getInstance()->addTagScript("script/Class.Referidos.js");

SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");

SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");
 
SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
SystemHtml::getInstance()->addTagStyle("css/demo_table.css");

SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");
SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.css");
 
 
/*Cargo el Header*/
SystemHtml::getInstance()->addModule("header");
SystemHtml::getInstance()->addModule("header_logo");
/* cargo el modulo de top menu*/
SystemHtml::getInstance()->addModule("main/topmenu");
 
?>
<script>
 
var _op;

$(function(){ 				
  	_op= new COperacion('content_dialog'); 
	_op.enableSearch('numero_documento','_buscar','search');
}); 
 
</script>
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

</style>

<div  id="caja_view" style="width:98%"> 

  <h2>Buscador</h2>
   
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="">
  <tr>
    <td colspan="2" align="center"><table width="300" border="0" cellspacing="1" cellpadding="1">
      <tr>
        <td colspan="2" align="center"><strong> Buscar por:</strong>&nbsp;
           <?php echo $TMOV->getLabelToSearch();?>
           </td>
        </tr>
      <tr>
    
        <td><input name="numero_documento" type="text" class="textfield" id="numero_documento" style="width:220px;margin-left:10px;margin-top:5px;" ></td>
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