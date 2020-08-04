<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
 
	if (isset($_REQUEST['view_add_caja'])){
		include("caja/agregar.php");
		exit; 
	}
	if (isset($_REQUEST['view_edit_caja'])){
		include("caja/edit.php");
		exit; 
	}

	if (isset($_REQUEST['getListUsuario'])){
		SystemHtml::getInstance()->includeClass("caja","MantenimientoCaja"); 
		$man= new MantenimientoCaja($protect->getDBLink()); 
		echo json_encode($man->getListUsuarios());
		exit;
	}
	if (isset($_REQUEST['validateCajaExist'])){
		$data=array("error"=>true,"mensaje"=>'La información proporcionada no esta completa!');
		if (validateField($_REQUEST,"id_caja")){
			SystemHtml::getInstance()->includeClass("caja","MantenimientoCaja"); 
			$man= new MantenimientoCaja($protect->getDBLink()); 
			$result=$man->validateCajaExist($_REQUEST['id_caja']); 
			if ($result==0){
				$data['exist']=false;
				$data['error']=false;
				$data['mensaje']='';
			}else{
				$data['exist']=true;
				$data['error']=true;
				$data['mensaje']='Esta caja existe, no puede haber duplicados';
			}
			
			echo json_encode($data); 
		}else{ 
			return $data;
		}
		
		exit; 
	}
		
	/*REQUEST PARA LA CREACION DE UNA CAJA*/
	if (isset($_REQUEST['save_new_caja'])){
		
		$data=array("error"=>true,"mensaje"=>'La información proporcionada no esta completa!');
		/*
			VALIDO TODA LA INFORMACIO
		*/
		if ((validateField($_REQUEST,"id_caja") && validateField($_REQUEST,"descripcion") && 
			validateField($_REQUEST,"id_cajero")  && validateField($_REQUEST,"ip_caja")
			&& validateField($_REQUEST,"monto_inicial") )){
			
			SystemHtml::getInstance()->includeClass("caja","MantenimientoCaja"); 
			$man= new MantenimientoCaja($protect->getDBLink()); 
			echo json_encode($man->createCaja($_REQUEST));
			
		}else{ 
			return json_encode($data);
		} 
		exit; 
	}
	
	
	/*REQUEST PARA LA EDITAR DE UNA CAJA*/
	if (isset($_REQUEST['save_edit_caja'])){
			 
		$data=array("error"=>true,"mensaje"=>'La información proporcionada no esta completa!');
		/* VALIDO TODA LA INFORMACIO */
		if ((validateField($_REQUEST,"id_caja") && validateField($_REQUEST,"descripcion") && 
			validateField($_REQUEST,"id_cajero")  && validateField($_REQUEST,"ip_caja")
			&& validateField($_REQUEST,"monto_inicial") )){
			
			SystemHtml::getInstance()->includeClass("caja","MantenimientoCaja"); 
			$man= new MantenimientoCaja($protect->getDBLink()); 
			echo json_encode($man->editCaja($_REQUEST));
			
		}else{ 
			return json_encode($data);
		} 
		exit; 
	}
 
 
	/*QUERY PARA BUSQUEDA */
	if (isset($_REQUEST['x_search'])){
		SystemHtml::getInstance()->includeClass("caja","MantenimientoCaja"); 
		$man= new MantenimientoCaja($protect->getDBLink()); 
		$result=$man->getListCaja(); 
		echo json_encode($result); 
		exit; 
	}
	
  
	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	 
	SystemHtml::getInstance()->addTagScript("script/Class.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.MEmpresa.js"); 
  
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.hotkeys.js"); 
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");

	SystemHtml::getInstance()->addTagScript("script/jquery.timeentry.min.js");
	
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");
	 
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
 						
  	_man= new MEmpresa('content_dialog');
	_man.drawTable('caja_list');
 	 
	
});
 
</script>
 
<div id="man_caja" class="fsPage">
  <h2>Mantenimiento de Empresa</h2>
  <button id="create_caja" class="greenButton">Agregar</button>
 	<table border="0" class="display" id="caja_list" style="font-size:13px">
      <thead>
        <tr>
          <th>Codigo</th>
          <th>Nombre</th>
          <th>NIT</th>
          <th>Interes PG</th>
          <th>% Interes local</th>
          <th>% interes Dolar</th>
          <th>% Enganche</th>
          <th>% Impuesto</th>
          <th>Necesidad</th>
          <th>Pre-Necesidad</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>

      </tbody>
  </table>
</div>
<div id="content_dialog" ></div>
<?php SystemHtml::getInstance()->addModule("footer");?>