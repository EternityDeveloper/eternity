<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
	
 
 
	if (isset($_REQUEST['dt_list'])){
 	//	if ($_REQUEST['iDisplayStart']!="1"){
		//	$_REQUEST['iDisplayStart']=$_REQUEST['iDisplayStart'];
	//	}


			$QUERY="";
			$HAVING="";
			if (isset($_REQUEST['sSearch'])){
			  if (trim($_REQUEST['sSearch'])!=""){
				$_REQUEST['sSearch']=mysql_escape_string($_REQUEST['sSearch']);
				$QUERY=" WHERE (sys_personas.id_nit LIKE '%".$_REQUEST['sSearch']."%' OR CONCAT(sys_personas.primer_nombre,' ' ,
	sys_personas.segundo_nombre) LIKE '%".$_REQUEST['sSearch']."%' OR sys_genero.descripcion LIKE '%".$_REQUEST['sSearch']."%' 
	OR sys_clase_local_extrangero.Descripcion LIKE '%".$_REQUEST['sSearch']."%'
	OR sys_clasificacion_persona.descripcion LIKE '%".$_REQUEST['sSearch']."%'
	OR sys_personas.fecha_nacimiento LIKE '%".$_REQUEST['sSearch']."%'
	OR CONCAT(sys_personas.primer_apellido,' ',sys_personas.segundo_apellido) LIKE '%".$_REQUEST['sSearch']."%' 
	OR CONCAT(sys_personas.primer_nombre,' ',sys_personas.primer_apellido) LIKE '%".$_REQUEST['sSearch']."%' 
	OR CONCAT(sys_personas.segundo_nombre,' ',sys_personas.primer_apellido) LIKE '%".$_REQUEST['sSearch']."%' 
	OR CONCAT(sys_documentos_identidad.`descripcion`,' ',sys_personas.id_nit) LIKE '%".$_REQUEST['sSearch']."%'
	) ";
	
				$HAVING=" HAVING 
					(direccion LIKE '%".$_REQUEST['sSearch']."%' 
					 OR telefono LIKE '%".$_REQUEST['sSearch']."%'
					 OR email LIKE '%".$_REQUEST['sSearch']."%')";
					 
			
			  }
			}

			$SQL=" SELECT count(*) as total FROM sys_personas
			INNER JOIN `sys_clase_local_extrangero` ON (sys_personas.`id_clase`=sys_clase_local_extrangero.`id_clase`)
			LEFT JOIN sys_clasificacion_persona ON (sys_clasificacion_persona.id_clasificacion=sys_personas.`id_clasificacion`)
			LEFT JOIN sys_genero ON (sys_genero.`id_genero`=sys_personas.`id_genero`)
			INNER JOIN `sys_documentos_identidad` ON (`sys_documentos_identidad`.`id_documento`=sys_personas.`id_genero`) ";
			
			$SQL.=$QUERY;
			 

			$rs=mysql_query($SQL);
			$row=mysql_fetch_assoc($rs);
			$total_row=$row['total'];
		
			$SQL="SELECT sys_personas.numero_de_archivo,
				sys_genero.descripcion AS genero,
				sys_personas.id_nit,
				concat(sys_personas.primer_nombre,' ' ,
				sys_personas.segundo_nombre) as nombre,
				concat(sys_personas.primer_apellido,' ',
				sys_personas.segundo_apellido) as apellido,
				sys_clase_local_extrangero.Descripcion AS clase,
				sys_clasificacion_persona.descripcion AS clasificacion,
				DATE_FORMAT(sys_personas.fecha_nacimiento, '%d-%m-%Y') as fecha_nacimiento,
				sys_documentos_identidad.`descripcion`,
				(SELECT
			 CONCAT(sys_ciudad.`Descripcion`,' \\ <br>',sys_provincia.`descripcion`,' \\ <br>',sys_sector.`descripcion`) AS direccion
			  FROM 
			`sys_direcciones` 
			INNER JOIN `sys_ciudad` ON (sys_ciudad.`idciudad`=sys_direcciones.idciudad)
			INNER JOIN `sys_provincia` ON (sys_provincia.`idprovincia`=sys_direcciones.idprovincia)
			INNER JOIN `sys_sector` ON (sys_sector.`idsector`=sys_direcciones.idsector)
			WHERE id_nit=sys_personas.id_nit LIMIT 1) as direccion,
			(SELECT CONCAT('(',IFNULL(sys_telefonos.`area`,''),') ',IFNULL(sys_telefonos.numero,''),' Ext:',IFNULL(sys_telefonos.extencion,'')) AS PHONE FROM `sys_telefonos` WHERE id_nit=sys_personas.id_nit LIMIT 1) as telefono,
			(SELECT direccion FROM `sys_cuentas_emails` WHERE `status`=1 AND id_nit=sys_personas.id_nit and direccion!='' LIMIT 1) as email	
			 FROM sys_personas
			LEFT JOIN `sys_clase_local_extrangero` ON (sys_personas.`id_clase`=sys_clase_local_extrangero.`id_clase`)
			LEFT JOIN sys_clasificacion_persona ON (sys_clasificacion_persona.id_clasificacion=sys_personas.`id_clasificacion`)
			LEFT JOIN sys_genero ON (sys_genero.`id_genero`=sys_personas.`id_genero`)
			LEFT JOIN `sys_documentos_identidad` ON (`sys_documentos_identidad`.`id_documento`=sys_personas.`id_documento`) 
			
			";
			$SQL.=$QUERY;
 
			$SQL.=" limit ".$_REQUEST['iDisplayStart'].",".$_REQUEST['iDisplayLength']."";
			
			 
			$rs=mysql_query($SQL);
			$result=array();
			$data=array(
				'sEcho'=>$_REQUEST['sEcho'],
				'iTotalRecords'=>10,
				'iTotalDisplayRecords'=>$total_row,
				'aaData' =>array()
			);
			
			SystemHtml::getInstance()->includeClass("client","PersonalData");
		 
			$person= new PersonalData($protect->getDBLink());			
			while($row=mysql_fetch_assoc($rs)){	
				$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_nit'],$protect->getSessionID());
				
				$person->checkTipoClasificacion($row['id_nit']);
				 
				array_push($data['aaData'],
					array( 
						"numero_de_archivo"=>$row['numero_de_archivo']."",
						"tipo_documento"=>$row['descripcion']."",
						"id_nit"=>$row['id_nit']."",
						"nombre"=>$row['nombre']."",
						"apellido"=>$row['apellido']."",
						"fecha_nacimiento"=>$row['fecha_nacimiento']."",
						"direccion"=>$row['direccion']."",
						"telefono"=>$row['telefono']."",
						"email"=>$row['email']."",
						"option1"=>'<a href="#" onclick="asignar_numero_archvio(\''.$encriptID.'\')"><img src="images/add_files_to_archive.png" width="27" height="28" /></a>',
						"id_nit_en"=>$encriptID 			
					)
				);
			 
			}
			
			echo json_encode($data);
		
		exit;
		
	} 
	
	
	SystemHtml::getInstance()->addTagScript("script/Class.js"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.Archivo.js","archivo");	
	
	SystemHtml::getInstance()->addTagStyle("css/jquery-ui-1.8.16.custom.css");
	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	SystemHtml::getInstance()->addModule("main/topmenu");



?>
<script>
var gobal_table;
var archivo = new ArchivoMovil("content_dialog");

function asignar_numero_archvio(id){
	archivo.doAsignarCorrelativoArchivo(id);
}

$(document).ready(function(){ 
	gobal_table=$("#role_list").dataTable({
							"bFilter": true,
							"bInfo": false,
							"bPaginate": true,
							"bProcessing": true,
							"bServerSide": true,
							"sAjaxSource": "./?mod_archivo/delegate&listado_persona&dt_list=1",
							"sServerMethod": "POST",
							"aoColumns": [
									{ "mData": "numero_de_archivo" },
									{ "mData": "tipo_documento" },
									{ "mData": "id_nit" },
									{ "mData": "nombre" },
									{ "mData": "apellido" },
									{ "mData": "fecha_nacimiento" },
									{ "mData": "direccion" },
									{ "mData": "telefono" },
									{ "mData": "email" },
									{ "mData": "option1" }
								],
							  "oLanguage": {
									"sLengthMenu": "Mostrar _MENU_ registros por pagina",
									"sZeroRecords": "No se ha encontrado - lo siento",
									"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
									"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
									"sInfoFiltered": "(filtrado de _MAX_ total registros)",
									"sSearch":"Buscar"
								}
							});
							

});
</script>
<style>
.fsPage{
	width:99%;	
}
</style>
<div class="fsPage">
<h2 style="margin:0;color:#FFF">ASIGNAR ID DE ARCHIVO</h2> 
	<table width="100%" border="0" class="display" id="role_list" style="font-size:13px">
      <thead>
        <tr>
          <th width="61">Numero de archivo</th>
          <th width="61">Tipo Documento</th>
          <th width="88">Numero documento </th>
          <th width="75">Nombres</th>
          <th width="72">Apellidos</th>
          <th width="59">Fecha nacimento </th>
          <th width="89">Direccion</th>
          <th width="47">Telefono</th>
          <th width="31">Email</th>
          <th width="27">&nbsp;</th>
        </tr>
      </thead>
      <tbody>

      </tbody>
  </table>
</div>
 <div id="content_dialog"  ></div>
<?php SystemHtml::getInstance()->addModule("footer");?>