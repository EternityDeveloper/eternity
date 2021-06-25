<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	


/*PROCESO DE CAMBIO DE PROSPECTO A OTRO ASESOR*/
if (isset($_REQUEST['submit_change_prospecto_to_asesor'])){
	$result=array("mensaje"=>"Datos incompletos","error"=>true); 
	if (validateField($_REQUEST,"prospect") && validateField($_REQUEST,"asesor_data")){
		if (validateField($_REQUEST['asesor_data'],"code")){
			SystemHtml::getInstance()->includeClass("prospectos","Prospectos"); 
			$prospecto= new Prospectos($protect->getDBLink(),$_REQUEST); 
			$prospect=json_decode(System::getInstance()->Decrypt($_REQUEST['prospect']));
			$asesor_data=json_decode(System::getInstance()->Decrypt($_REQUEST['asesor_data']['code']));
			$result=$prospecto->changeOfProspectoToAsesor($asesor_data->id_comercial,$prospect->correlativo);
		}
		
		
	
	}
	echo json_encode($result);
	exit;
}

/*PROCESO DE REASIGNACION DEL DIRECTOR DE DIVICION*/
if (isset($_REQUEST['submit_reasig_asesor'])){
	
	SystemHtml::getInstance()->includeClass("prospectos","Prospectos");
	$prospecto= new Prospectos($protect->getDBLink(),$_REQUEST);
	$result=$prospecto->ReasignacionProspectoToAsesor(); 
	echo json_encode($result);
	exit;
}


if (isset($_REQUEST['asig_view'])){
	include("view/reasignacion/view_reasign_c.php");
	exit;
} 

if (isset($_REQUEST['reporte_venta_asesor'])){
	include("reporte_venta_asesor.php");
	exit;	
}
if (isset($_REQUEST['reporte_asesor'])){
	include("reporte_asesor.php");
	exit;	
}
if (isset($_REQUEST['report_gerente'])){
	include("reporte_gerente.php");
	exit;	
}

if (isset($_REQUEST['report_gerente_esp'])){
	include("reporte_gerente_especial.php");
	exit;	
}



if (isset($_REQUEST['report_ase_detalle'])){
	include("reporte_g_asesor_detalle.php");
	exit;	
}

if (isset($_REQUEST['report_gerente_ase'])){
	include("reporte_gerente_asesor.php");
	exit;	
}
/*LISTADO DE ASESORES QUE NO HAN VENDIDO*/
if (isset($_REQUEST['report_asesor_a_cero'])){
	include("reporte_asesor_nventas.php");
	exit;	
}

if (isset($_REQUEST['validate_phone'])){
	
	SystemHtml::getInstance()->includeClass("client","PersonalData");
	$person= new PersonalData($protect->getDBLink(),$_REQUEST);
	$id_nit=System::getInstance()->Decrypt($_REQUEST['id_nit']);
        $prueba=$_REQUEST['id_nit']; 
	// $total=$person->getTotalTelefonos($id_nit);
        $total = 1;
 	$return=array("total_phone"=>$total);
        // print_r($person);
        // $nit2 = $_REQUEST['id_nit'];
        // die ('este es el prueba'.$prueba);
        // print_r($return);
        // if (!$return) {
           //die ('error qui en telefono');
       // }  
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
	$rt=$prospecto->validateIsProspect(System::getInstance()->Decrypt($_REQUEST['tipo_documento']),str_replace("-","",$_REQUEST['numero_documento']));
	 
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

	
if (isset($_REQUEST['prospectos_direct_submit'])){
	
	SystemHtml::getInstance()->includeClass("prospectos","Prospectos");
	$prospecto= new Prospectos($protect->getDBLink(),$_REQUEST);
	$result=$prospecto->createProspectacionDirect();
	 
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


if (isset($_REQUEST['agenda'])){
	include("agenda_citas.php");
	exit;
} 


if (isset($_REQUEST['reporte'])){
	include("report/reporte.php");
	exit;
} 

if (isset($_REQUEST['reporte_excel'])){
	include("report/reporte2.php");
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


if (isset($_REQUEST['view_prospecto_direct'])){
	include("view/add_prospecto_direct.php");
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
		$result=$prospecto->getList();
		 
		echo json_encode($result);
		
		exit;
		
	}
	
	/*QUERY PARA BUSQUEDA */
	if (isset($_REQUEST['filter_gestion'])){
		SystemHtml::getInstance()->includeClass("prospectos","Prospectos");
		$prospecto= new Prospectos($protect->getDBLink(),$_REQUEST);
		$data=json_decode(base64_decode($_REQUEST['type'])); 
		$result=$prospecto->getListDetalleGestion($data);
		 
		echo json_encode($result);
		
		exit;
		
	}	

	
 

	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	 
	SystemHtml::getInstance()->addTagScript("script/Class.js");
	SystemHtml::getInstance()->addTagScriptByModule("Class.prospectos.js");
	SystemHtml::getInstance()->addTagScriptByModule("Class.ActividadProspectos.js");
	
	//SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
	

	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
	SystemHtml::getInstance()->addTagScript("script/jquery.timeentry.min.js"); 
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");
	
		
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
	
	SystemHtml::getInstance()->addTagScript("script/jquery.base64.min.js");
	 
	SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
 	SystemHtml::getInstance()->addTagStyle("css/showLoading.css");
 
	SystemHtml::getInstance()->addTagStyle("css/jquery.ptTimeSelect.css");
	
	SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");
	SystemHtml::getInstance()->addTagStyle("css/select2-bootstrap.css");
	SystemHtml::getInstance()->addTagStyle("css/select2.css");	
  
	
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
	_prospectos.createListTable('prospecto_list');
 	
	<?php 
		
		$comercial=$protect->getComercialData();
 
		if (count($comercial>0)){ 
		
			if ($comercial['tabla']=="Asesor de Familia"){
		 
				$code=array("id_comercial"=>$comercial['id_comercial'],"idnit"=>$comercial['id_nit']);
				?>
					_prospectos.setAsesorData('<?php echo $comercial['nombre_completo']?>',
						'<?php echo System::getInstance()->Encrypt(json_encode($code)); ?>',
						'<?php echo System::getInstance()->Encrypt($comercial['id_comercial'])?>');
				<?php
			}
		}
		
	
	?>
	
});


function viewProspecto(data){
	_prospectos.chargeEditView(data);
}
 
</script>
 
<div id="inventario_page" class="fsPage">
  <h2 style="margin:0;color:#FFF">Listado de Prospectos</h2>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td><button id="pro_create" class="greenButton">Nuevo</button><a href="?mod_prospectos/listar&reporte" target="xxx" id="pro_create" class="greenButton" style="display:none">MOSTRAR REPORTE</a><a href="?mod_prospectos/listar&reporte_excel" target="xxx" id="pro_create" class="greenButton" style="display:none">EXPORTAR REPORTE A EXCEL</a></td> 
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
  </table>
<table border="0" class="table table-hover" id="prospecto_list" style="font-size:12px;"  >
    <thead>
        <tr style="background-color:#CCC">
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
          <th>&nbsp;</th>
        </tr>
    </thead>
      <tbody>

      </tbody>
  </table>
</div>
<div id="content_dialog" ></div>
<?php SystemHtml::getInstance()->addModule("footer");?>
