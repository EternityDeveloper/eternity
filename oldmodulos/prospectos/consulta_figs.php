<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
error_reporting(E_ALL);
ini_set("display_errors", 1);

 
	$ms_db = mssql_connect("DESARROLLO-PC","memorial","A123456a");
	mssql_select_db("SERVICIOSM",$ms_db);



	$SQL="SELECT PAGOS_CUOTAS.CONTRATO
			FROM PAGOS_CUOTAS
			WHERE 
			  (PAGOS_CUOTAS.VTA_FECHA >= '2014/07/01' AND 
			  PAGOS_CUOTAS.VTA_FECHA <= '2014/07/03 11:59 PM' OR 
			  PAGOS_CUOTAS.VTA_FECHAL_ULT_MODIF >= '2014/07/01' AND 
			  PAGOS_CUOTAS.VTA_FECHAL_ULT_MODIF <= '2014/07/03 11:59 PM' AND 
			  PAGOS_CUOTAS.VTA_FECHA < '2014/07/03' AND 
			  PAGOS_CUOTAS.VTA_IVA_RETENIDO <> 0) AND 
			  PAGOS_CUOTAS.VTA_TIPO = 3 
		group by PAGOS_CUOTAS.CONTRATO "; 	
		
	$rsx=mssql_query($SQL,$ms_db); 
	while($rowx=mssql_fetch_assoc($rsx)){ 
		print_r($rowx);
	}
	
	echo "fdad";
	
	
	exit;
	
	
	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	 
	SystemHtml::getInstance()->addTagScript("script/Class.js");
	SystemHtml::getInstance()->addTagScriptByModule("Class.ListadoAsesores.js");
	SystemHtml::getInstance()->addTagScriptByModule("Class.ActividadProspectos.js");
	
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
	
	
	SystemHtml::getInstance()->addTagScript("script/Class.direcciones.js");
	SystemHtml::getInstance()->addTagScript("script/Class.phone.js");
	SystemHtml::getInstance()->addTagScript("script/Class.empresa.js");
	SystemHtml::getInstance()->addTagScript("script/Class.email.js");
	SystemHtml::getInstance()->addTagScript("script/Class.reference.js");
	SystemHtml::getInstance()->addTagScript("script/Class.contactos.js");
	SystemHtml::getInstance()->addTagScript("script/Class.AsesoresTree.js");
	SystemHtml::getInstance()->addTagScript("script/Class.Referidos.js");

	

	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");

	SystemHtml::getInstance()->addTagScript("script/jquery.timeentry.min.js");
	
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
 
 
	SystemHtml::getInstance()->addTagStyle("css/jquery.ptTimeSelect.css");
	
	SystemHtml::getInstance()->addTagStyle("css/showLoading.css");
 
	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	SystemHtml::getInstance()->addModule("main/topmenu");

//echo $protect->getComercialID(); 
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
</style>
<script>
 
var _prospectos;

$(function(){
 						
  	_prospectos= new ListadoAsesores('content_dialog','asesores_list');
	_prospectos.listar_cartera_asesor('asesores_list');
 	
	 
	
});


function viewProspecto(data){
	_prospectos.chargeEditView(data);
}
 
</script>
 
<div id="inventario_page" class="fsPage" style="width:700px;">
  <h2>Cartera de Asesores</h2>
 	<table border="0" class="display" id="asesores_list" style="font-size:13px;">
      <thead>
        <tr>
          <th>&nbsp;</th>
          <th>Asesor</th>
        </tr>
      </thead>
      <tbody>

      </tbody>
  </table>
</div>
<div id="content_dialog" ></div>
<?php SystemHtml::getInstance()->addModule("footer");?>