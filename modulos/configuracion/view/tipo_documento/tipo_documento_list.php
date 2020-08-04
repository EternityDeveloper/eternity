<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
 
	if (isset($_REQUEST['view_add_documento'])){
		include("view/agregar.php");
		exit; 
	}
	if (isset($_REQUEST['view_edit_documento'])){
		include("view/edit.php");
		exit; 
	}
 	/*VALIDA SI UN DOCUMENTO EXISTE*/
	if (isset($_REQUEST['validateDocExist'])){
		$data=array("error"=>true,"mensaje"=>'La información proporcionada no esta completa!');
		if (validateField($_REQUEST,"TIPO_DOC")){
			SystemHtml::getInstance()->includeClass("configuracion","MTipoDocumento"); 
			$man= new MTipoDocumento($protect->getDBLink()); 
			$result=$man->validateDocExist($_REQUEST['TIPO_DOC']); 
			if ($result==0){
				$data['exist']=false;
				$data['error']=false;
				$data['mensaje']='';
			}else{
				$data['exist']=true;
				$data['error']=true;
				$data['mensaje']='El tipo de documento existe, no puede haber duplicados';
			}
			
			echo json_encode($data); 
		}else{ 
			return $data;
		}
		
		exit; 
	}
		
	/*REQUEST PARA LA CREACION DE UN TIPO DE DOCUMENTO*/
	if (isset($_REQUEST['save_new_doc'])){
		
		$data=array("error"=>true,"mensaje"=>'La información proporcionada no esta completa!');
		/*
			VALIDO TODA LA INFORMACION
		*/
		if ((validateField($_REQUEST,"TIPO_DOC") && validateField($_REQUEST,"descripcion") )){
			
			SystemHtml::getInstance()->includeClass("configuracion","MTipoDocumento"); 
			$man= new MTipoDocumento($protect->getDBLink()); 
			echo json_encode($man->create($_REQUEST));
			
		}else{ 
			return json_encode($data);
		} 
		exit; 
	}
	
	
	/*REQUEST PARA LA EDITAR DE UN TIPO DE DOCUMENTO*/
	if (isset($_REQUEST['save_edit_doc'])){
		$data=array("error"=>true,"mensaje"=>'La información proporcionada no esta completa!');
		/*
			VALIDO TODA LA INFORMACION
		*/
		if ((validateField($_REQUEST,"TIPO_DOC") && validateField($_REQUEST,"descripcion") )){
			
			SystemHtml::getInstance()->includeClass("configuracion","MTipoDocumento"); 
			$man= new MTipoDocumento($protect->getDBLink()); 
			echo json_encode($man->edit($_REQUEST));
			
		}else{ 
			return json_encode($data);
		} 
		exit;  
	}
 
 
	/*QUERY PARA BUSQUEDA */
	if (isset($_REQUEST['x_search'])){
		SystemHtml::getInstance()->includeClass("configuracion","MTipoDocumento"); 
		$man= new MTipoDocumento($protect->getDBLink()); 
		$result=$man->getList(); 
		echo json_encode($result); 
		exit; 
	}
	
  
	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	 
	SystemHtml::getInstance()->addTagScript("script/Class.js");
	SystemHtml::getInstance()->addTagScriptByModule("Class.MTipoDocumento.js"); 

	SystemHtml::getInstance()->addTagStyle("css/smoothness/jquery.ui.combogrid.css");
	SystemHtml::getInstance()->addTagScript("script/jquery.ui.combogrid-1.6.3.js");
	
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
  	_man= new MTipoDocumento('content_dialog');
	_man.drawTable('v_list');
	
});
 
 
</script>
 
<div id="man_" class="fsPage">
  <h2>Mantenimiento de Tipo documentos</h2>
  <button id="create_caja" class="greenButton">Agregar tipo documento</button>
 	<table border="0" class="display" id="v_list" style="font-size:13px">
      <thead>
        <tr>
          <th>Documento</th>
          <th>Descripcion</th>
          <th>Fiscal</th>
          <th>Anula Mov.</th>
          <th>Rep. Ventas</th>
          <th>Impresion</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>

      </tbody>
  </table>
</div>
<div id="content_dialog" ></div>
<?php SystemHtml::getInstance()->addModule("footer");?>