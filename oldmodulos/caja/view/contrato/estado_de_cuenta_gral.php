<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	


if (isset($_REQUEST['no_contrato'])){
	SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
	SystemHtml::getInstance()->includeClass("client","PersonalData");
	
	$con=new Contratos($protect->getDBLink()); 
	$person= new PersonalData($protect->getDBLink(),$_REQUEST);
	
	
	$no_contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['no_contrato']));
	  
	$cdata=$con->getInfoContrato($no_contrato->serie_contrato,$no_contrato->no_contrato);
	
	$peron_data=$person->getClientData($cdata->id_nit_cliente);
	
	$listContract=$con->getContractListFromPerson($cdata->id_nit_cliente);
	
	//print_r($listContract);
	//id_nit_cliente
}
 
$data=$con->getDetalleGeneralFromContrato($no_contrato->serie_contrato,$no_contrato->no_contrato);

$product=$con->getDetalleProductsFromContrato($no_contrato->serie_contrato,$no_contrato->no_contrato);

 

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

SystemHtml::getInstance()->addTagScript("script/Class.direcciones.js");
SystemHtml::getInstance()->addTagScript("script/Class.phone.js");
SystemHtml::getInstance()->addTagScript("script/Class.empresa.js");
SystemHtml::getInstance()->addTagScript("script/Class.email.js");
SystemHtml::getInstance()->addTagScript("script/Class.reference.js");
SystemHtml::getInstance()->addTagScript("script/Class.contactos.js");
SystemHtml::getInstance()->addTagScript("script/Class.AsesoresTree.js");
SystemHtml::getInstance()->addTagScript("script/Class.Referidos.js"); 
SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");

SystemHtml::getInstance()->addTagStyle("css/smoothness/jquery.ui.combogrid.css");
SystemHtml::getInstance()->addTagScript("script/jquery.ui.combogrid-1.6.3.js");



SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");

SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");
 
SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
SystemHtml::getInstance()->addTagStyle("css/demo_table.css"); 

SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.css"); 
/*Cargo el Header*/
SystemHtml::getInstance()->addModule("header");
SystemHtml::getInstance()->addModule("header_logo");
 
  
?> 
<script>
 
var _caja;

$(function(){ 				
  	_caja= new PagoContrato('content_dialog'); 
	_caja.registerMovimiento('<?php echo $_REQUEST['no_contrato']?>','<?php echo $_REQUEST['id_nit']?>');
	  
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
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:0px;padding:0px">
 
  <tr>
    <td align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td valign="top"><table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="2" style="margin:0px;padding:0px;border:#FFF solid 0px;"><h2 style="margin:0px;">INFO CONTRATO (<?php echo $cdata->serie_contrato ."-".$cdata->no_contrato;?>)</h2></td>
          </tr>
          <tr>
            <td><table width="800" border="0" cellspacing="0" cellpadding="0"   class="tb_detalle fsDivPage">
              <tr>
                <td><strong>Cliente</strong></td>
                <td><strong>Telefono</strong></td>
                <td><strong>Fecha nacimiento</strong></td>
                <td><strong>Lugar de nacimiento</strong></td>
              </tr>
              <tr>
                <td><?php echo $peron_data['primer_nombre']." ".$peron_data['segundo_nombre']." ".$peron_data['primer_apellido']." ".$peron_data['segundo_apellido'];?></td>
                <td><?php echo trim($person_data['telefono'])==""?'N/A':$person_data['telefono'];?></td>
                <td><?php echo $peron_data['fecha_nac'];?></td>
                <td><?php echo $peron_data['lugar_nacimiento'];?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td valign="top">&nbsp;</td>
      </tr>
      <tr>
        <td valign="top"><table width="800" border="1" style="border-spacing:10px;" class="tb_detalle fsDivPage">
          <tr  >
            <td width="208" align="center"><strong>PRODUCTO</strong></td>
            <td width="276" align="center"><strong>UBICACION</strong></td>
            <td width="276" align="center"><strong>PLAN</strong></td>
            <td width="276" align="center"><strong>OFICIAL DE COBROS</strong></td>
            <td width="276" align="center"><strong>MOTORIZADO</strong></td>
          </tr>
          <?php 		   
		
		foreach($product as $key =>$producto){

?>
          <tr >
            <td align="center"><?php echo  $producto['jardin']?></td>
            <td align="center"><?php echo  $producto['id_jardin']."-".$producto['id_fases']."-".$producto['lote']."-".$product['bloque']?></td>
            <td align="center"><?php echo  trim($data['CODIGO_TP']);?></td>
            <td align="center">N/A</td>
            <td align="center">N/A</td>
          </tr>
          <?php } ?>
        </table></td>
      </tr>
      <tr>
        <td valign="top">&nbsp;</td>
      </tr>
      <tr>
        <td valign="top"><table width="800" border="1" style="border-spacing:10px;" class="tb_detalle fsDivPage">
          <tr  style="background:#CCC" >
            <td width="251"><strong>RESUMEN</strong></td>
            <td width="144" align="right"><strong>CANCELADO</strong></td>
            <td width="196" align="right"><strong>PENDIENTE</strong></td>
            <td width="181" align="right"><strong>TOTAL</strong></td>
          </tr>
          <?php 		   
		  $data=$con->getDetalleGeneralFromContrato($no_contrato->serie_contrato,$no_contrato->no_contrato);
?>
          <tr >
            <td><strong>CAPITAL DE CUOTAS</strong></td>
            <td align="right"><?php echo  number_format($data['capital_pagado'],2)?></td>
            <td align="right"><?php echo  number_format($data['precio_neto']-$data['capital_pagado'],2)?></td>
            <td align="right"><?php echo  number_format(($data['precio_neto']-$data['capital_pagado'])+$data['capital_pagado'],2)?></td>
          </tr>
          <tr >
            <td><strong>INTERESES DE CUOTAS</strong></td>
            <td align="right"><?php echo  number_format($data['intereses_pagados'],2)?></td>
            <td align="right"><?php echo  number_format($data['interes']-$data['intereses_pagados'],2)?></td>
            <td align="right"><?php echo  number_format(($data['interes']-$data['intereses_pagados'])+$data['intereses_pagados'],2)?></td>
          </tr>
          <tr >
            <td><strong>CUOTAS DE MANTENIMIENTO</strong></td>
            <td align="right">0</td>
            <td align="right">0</td>
            <td align="right">0</td>
          </tr>
          <tr >
            <td><strong>TOTALES</strong></td>
            <td align="right"><?php echo  number_format($data['capital_pagado']+$data['intereses_pagados'],2)?></td>
            <td align="right"><?php echo  number_format(($data['precio_neto']-$data['capital_pagado'])+($data['interes']-$data['intereses_pagados']),2)?></td>
            <td align="right"><?php echo  number_format((($data['precio_neto']-$data['capital_pagado'])+$data['capital_pagado'])+(($data['interes']-$data['intereses_pagados'])+$data['intereses_pagados']),2)?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
 
          <tr>
            <td valign="top">&nbsp;</td>
          </tr>
          <tr>
            <td valign="top">&nbsp;<button type="button" class="btn btn-warning bt_movimiento" id="c_bt_transpacion" >Registrar Movimiento</button></td>
          </tr>
  </table>
 <div id="content_dialog" ></div>