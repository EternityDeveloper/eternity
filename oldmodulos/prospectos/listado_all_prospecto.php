<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

if (isset($_REQUEST['validate_phone'])){
	
	SystemHtml::getInstance()->includeClass("client","PersonalData");
	$person= new PersonalData($protect->getDBLink(),$_REQUEST);
	$id_nit=System::getInstance()->Decrypt($_REQUEST['id_nit']);
	$total=$person->getTotalTelefonos($id_nit);
 	$return=array("total_phone"=>$total);  
	echo json_encode($return);
	exit;
}
if (isset($_REQUEST['validate_actividad'])){
	
	SystemHtml::getInstance()->includeClass("prospectos","Prospectos");
	$prospecto= new Prospectos($protect->getDBLink(),$_REQUEST);
	
	 
	$prosp=json_decode(System::getInstance()->Decrypt($_REQUEST['prospecto_id']));
//	print_r($prosp->id_nit);
	$actividad=$prospecto->getLastActividad($prosp->id_nit,$prosp->correlativo);
  
	$id_actividad=System::getInstance()->Encrypt(json_encode($actividad));
	 
	$return=array("hasActividad"=>false,"id_actividad"=>$id_actividad,"time_diff"=>$actividad['TIME_DIFERENCE']);
	if (count($actividad)>1){
		if ($actividad['id_actividad']=="CIE"){
			$return['hasActividad']=false;
		}else{
			$return['hasActividad']=true;	
		}
	}
	 
	echo json_encode($return);
	exit;
}

if (isset($_REQUEST['validate_identifaction'])){
	
	SystemHtml::getInstance()->includeClass("prospectos","Prospectos");
	$prospecto= new Prospectos($protect->getDBLink(),$_REQUEST);
	$rt=$prospecto->validateIsProspect(System::getInstance()->Decrypt($_REQUEST['tipo_documento']),$_REQUEST['numero_documento']);
	 
	echo json_encode($rt);
	exit;
}


if (isset($_REQUEST['submitActividad'])){
	
	SystemHtml::getInstance()->includeClass("prospectos","Prospectos");
	$prospecto= new Prospectos($protect->getDBLink(),$_REQUEST);
 
	$result=$prospecto->registrarActividad();
	 
	echo json_encode($result);
	exit;
}
	
if (isset($_REQUEST['prospectos_submit'])){
	
	SystemHtml::getInstance()->includeClass("prospectos","Prospectos");
	$prospecto= new Prospectos($protect->getDBLink(),$_REQUEST);
	$result=$prospecto->createProspectacion();
	 
	echo json_encode($result);
	exit;
}

if (isset($_REQUEST['reporte'])){
	include("report/reporte.php");
	exit;
}  
if (isset($_REQUEST['actividad_detail'])){
	include("view/actividad/view_question_actividad.php");
	exit;
} 
if (isset($_REQUEST['actividad_add'])){
	include("view/actividad/new_actividad.php");
	exit;
} 
if (isset($_REQUEST['actividades_view'])){
	include("view/view_actividades.php");
	exit;
}
if (isset($_REQUEST['tipo_pilar'])){
	include("mantenimiento/tipo_pilar.php");
	exit;
}

if (isset($_REQUEST['add_tipo_pilar'])){
	include("mantenimiento/add_tipo_pilar.php");
	exit;
}
if (isset($_REQUEST['edit_tipo_pilar'])){
	include("mantenimiento/edit_tipo_pilar.php");
	exit;
}

if (isset($_REQUEST['add_pregunta'])){
	include("mantenimiento/add_pregunta.php");
	exit;
}

if (isset($_REQUEST['edit_pregunta'])){
	include("mantenimiento/edit_pregunta.php");
	exit;
}

if (isset($_REQUEST['add_prospecto'])){
 
	include("prospecto.php");
	exit;
}
if (isset($_REQUEST['tipo_pilar_question'])){
	include("mantenimiento/pilar_question_add.php");
	exit;
}
if (isset($_REQUEST['prospecto_view'])){
	include("view/prospecto_view.php");
	exit;
}
if (isset($_REQUEST['simple_list'])){
	include("view/list_prospect.php");
	exit;
} 


	/*QUERY PARA BUSQUEDA */
	if (isset($_REQUEST['remove_prospecto'])){
		SystemHtml::getInstance()->includeClass("prospectos","Prospectos");
			
		$prospecto= new Prospectos($protect->getDBLink(),$_REQUEST);
	 
		$result=$prospecto->remove($_REQUEST['id']);
		 
		echo json_encode($result);
		
		exit;
		
	}
 
	/*QUERY PARA BUSQUEDA */
	if (isset($_REQUEST['x_search'])){
		SystemHtml::getInstance()->includeClass("prospectos","Prospectos");
			
		$prospecto= new Prospectos($protect->getDBLink(),$_REQUEST);
	  
		$result=$prospecto->getListAllProspecto();
		 
		echo json_encode($result);
		
		exit;
		
	}	
 

	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	 
	SystemHtml::getInstance()->addTagScript("script/Class.js");
	SystemHtml::getInstance()->addTagScriptByModule("Class.prospectos.js");
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
 						
  	_prospectos= new Prospectos('content_dialog','prospecto_list');
	_prospectos.createAllListTable('prospecto_list');
 	
	 
	
});


function viewProspecto(data){
	_prospectos.chargeEditView(data);
}
 
</script>
 
<div id="inventario_page" class="fsPage">
  <h2>Listado de Prospectos</h2>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td><button id="pro_create" class="greenButton">Nuevo</button></td>
    </tr>
    <tr>
      <td><table border="0" class="display" id="prospecto_list" style="font-size:13px">
        <thead>
          <tr>
            <th>Prospecto</th>
            <th>Tipo Pilar</th>
            <th>Protecion Inicio</th>
            <th>Proteccion Fin</th>
            <th>Dias Restante</th>
            <th>Asesor</th>
            <th>Estatus </th>
            <th>Observaciones</th>
            <th>Ult. actividad</th>
            <th>Ult. comentario</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table></td>
    </tr>
  </table>
</div>
<div id="content_dialog" ></div>
<?php SystemHtml::getInstance()->addModule("footer");?>