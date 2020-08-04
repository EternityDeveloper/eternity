<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
 
$id_contrato=$_REQUEST['id'];
$contrato=json_decode(System::getInstance()->Decrypt($id_contrato));
 
$id_nit=$contrato->id_nit; 
$lcontratos=array();
$SQL="SELECT serie_contrato,no_contrato,id_nit_cliente as id_nit FROM contratos WHERE id_nit_cliente='".$id_nit."'";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_object($rs)){
	array_push($lcontratos,$row);
}


	SysLog::getInstance()->Log($id_nit, 
							 $contrato->serie_contrato,
							 $contrato->no_contrato,
							 0,
							 0,
							 "INGRESANDO AL CONTRATO ",
							 '',
							 'CONTRATO_ACCESOS',
							 '',
							 '',
							 0);	
									 
									 
	/*SESSION QUE ME PERMITE GENERAR LOS DESISTIDOS*/
	STCSession::GI()->setSubmit("anular_form",true);

	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js"); 
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
 	SystemHtml::getInstance()->addTagScript("script/Class.js");  
	SystemHtml::getInstance()->addTagStyle("css/smoothness/jquery.ui.combogrid.css");
 
	SystemHtml::getInstance()->addTagScript("script/bootstrap-wizard.js"); 

	SystemHtml::getInstance()->addTagScriptByModule("class.ContratoView.js"); 
 	SystemHtml::getInstance()->addTagScript("script/jquery.timeentry.min.js");
	  
	SystemHtml::getInstance()->addTagScriptByModule("class.PagoComponent.js","caja");  
	SystemHtml::getInstance()->addTagScriptByModule("class.COperacion.js","caja"); 
 	SystemHtml::getInstance()->addTagScriptByModule("class.PagoContrato.js","caja"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.AbonoCapital.js","caja"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.PagoCuota.js","caja"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.FormaPago.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.TDocSerieRVenta.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.CFactura.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.GAbonoCapital.js","cobros");
	SystemHtml::getInstance()->addTagScriptByModule("class.CAbonoASaldo.js","cobros");
	SystemHtml::getInstance()->addTagScriptByModule("class.CCancelacionTotal.js","cobros");	
	SystemHtml::getInstance()->addTagScriptByModule("class.CPagoMantenimiento.js","cobros");	
	SystemHtml::getInstance()->addTagScriptByModule("class.CCambioPlan.js","cobros");	
	SystemHtml::getInstance()->addTagScriptByModule("class.GPagoMenor.js","cobros");
	SystemHtml::getInstance()->addTagScriptByModule("class.GNotaDebito.js","cobros");
	SystemHtml::getInstance()->addTagScriptByModule("class.GGestion.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.GReactivacion.js","cobros");	
	SystemHtml::getInstance()->addTagScriptByModule("class.CambioFormaPagoCT.js","cobros");	
	

	#ADDED BY ROBERTO ROJAS 
	SystemHtml::getInstance()->addTagScriptByModule("class.DetalleBeneficiario.js","cobros");
	SystemHtml::getInstance()->addTagScriptByModule("class.DetalleRepresentante.js","cobros");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.beneficiario.js");
	#FIN	
	
	
	SystemHtml::getInstance()->addTagScriptByModule("class.GOrdenInhumacion.js","cobros");
	SystemHtml::getInstance()->addTagScriptByModule("class.RegistrarInhumado.js","cobros");
	SystemHtml::getInstance()->addTagScriptByModule("class.FacturarProductos.js","cobros");
	SystemHtml::getInstance()->addTagScriptByModule("class.ServicioInhumacion.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.Facturar.js","caja");	
	SystemHtml::getInstance()->addTagScriptByModule("class.CDireccion.js","contratos");
	SystemHtml::getInstance()->addTagScriptByModule("class.inventario.js","inventario");	
	SystemHtml::getInstance()->addTagScriptByModule("class.CierreCaja.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.RepararContrato.js","cobros");
	SystemHtml::getInstance()->addTagScriptByModule("class.TMap.js","cobros"); 	
	 
  
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
	
	/*BOOSTRAP SCRIPT*/
//	SystemHtml::getInstance()->addTagScript("script/bootstrap/js/bootstrap.min.js");
	SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");
 		
	SystemHtml::getInstance()->addTagStyle("css/jquery.ptTimeSelect.css"); 
   	SystemHtml::getInstance()->addTagStyle("css/bootstrap-wizard.css");
	SystemHtml::getInstance()->addTagStyle("css/chosen/chosen.css");		
	 
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
  


 	

 ?> 
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
<script type="text/javascript" src="resource/OpenLayers/OpenLayers.js"></script>
<style>
  
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
 

.fixed-table-container {
  height: 200px; 
  position: relative; /* could be absolute or relative */
  width:900px;
} 
.fixed-table-container2 {
  height: 200px; 
  position: relative; /* could be absolute or relative */
  width:98%;
} 
.fixed-table-container-inner {
  overflow-x: hidden;
  overflow-y: auto;
  height: 100%;
}
.th-inner {
  position: absolute;
  width:90px;
  height:31px; 
  top: 0;
  line-height: 30px; 
  text-align: center; 
}
.th-inner2 {
  position: absolute;
  width:140px;
  height:40px; 
  top: 0; 
  line-height: 30px; 
  text-align: center; 
}
.th-inner_hb{
  position: absolute;
  width:150px;
  height:31px; 
  top: 0;
  line-height: 30px; 
  text-align: center; 
}
.first .th-inner {
	border-left: none;
	padding-left: 6px;
}
.header-background {
  background-color: #D5ECFF;
  height: 30px; /* height of header */
  position: absolute;
  top: 0;
  right: 0;
  left: 0;
}
._contrato {}
</style>
<script> 

var _dashboard= new ContratoView("content_dialog");
var _contrato='<?php echo $_REQUEST['id']?>'; 
  
$(document).ready(function(){ 
	_dashboard.doLoad();
	_dashboard.doInit('<?php echo $_REQUEST['id']?>'); 

});


function reparar_(id){
	 var op= new RepararContrato('content_dialog'); 	
	op.doFix(id);
} 

function print_(id){
	 var op= new CierreCaja('content_dialog'); 	
	op.doPrintRecibo(id);
} 
function sendmail_(id){
	 var op= new CierreCaja('content_dialog'); 	
	op.doSendEmailRecibo(id);
} 
function doAction(str,tipo_movimiento){ 
	var tipo=$(tipo_movimiento).attr("id");
	var obj = new window[str]('content_dialog');  
	obj.setToken(_dashboard.getToken());
	obj._id_nit='<?php echo System::getInstance()->Encrypt($cdata->id_nit_cliente);?>';
	obj.doView(tipo,_contrato);
}
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><span style="font-size:20px;">
          <input type="submit" name="lb_regresar" id="lb_regresar" value="REGRESAR" class="btn btn-default"  onclick="window.history.back();" />
        </span></td>
      </tr>
      <tr>
        <td align="center"> 
          <table width="300" border="0" cellspacing="1" cellpadding="1">
            <tr>
              <td><strong>Buscar:
                 
              </strong></td>
              <td><input name="search" type="text" class="textfield" id="search" style="width:220px;margin-left:10px;margin-top:5px;" /></td>
              <td><button type="button" id="_buscar_bt" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">BUSCAR</span></button></td>
            </tr>
          </table>
         </td>
      </tr>
      <tr>
        <td><ul id="myTab" class="nav nav-tabs">
        <?php 
		$i=0;
		foreach($lcontratos as $key=> $row){
				$id=System::getInstance()->Encrypt(json_encode($row));
			?>
   <li <?php if ($i==0){ echo 'class="active"';}?>><a href="#home" class="contrato" data-toggle="tab" id="<?php echo $id;?>" ><strong><?php echo $row->serie_contrato ." ".$row->no_contrato;?></strong></a></li> 
   <?php $i=1;
   } ?>
           <li><a href="#hlb" data-toggle="tab" id="">HISTORICO LB</a></li>
</ul></td>
      </tr>
      <tr>
        <td id="detalle_contrato">
<?php include("info.php");?>        
        </td>
      </tr>
    </table>
<div id="content_dialog" ></div>
<?php
//	SystemCache::GI()->doSave();	
?>