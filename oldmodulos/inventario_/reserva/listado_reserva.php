<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	




	/*QUERY PARA BUSQUEDA */
	if (isset($_REQUEST['dt_list'])){

			$QUERY="";
			$HAVING="";
			if (isset($_REQUEST['sSearch'])){
			  if (trim($_REQUEST['sSearch'])!=""){
				$_REQUEST['sSearch']=mysql_escape_string($_REQUEST['sSearch']);
				$QUERY="  
				AND (sys_status.`descripcion` LIKE '%".$_REQUEST['sSearch']."%'
					OR  tipos_reservas.`reserva_descrip` LIKE '%".$_REQUEST['sSearch']."%'
					OR  reserva_inventario.`fecha_inicio` LIKE '%".$_REQUEST['sSearch']."%'
					OR  `reserva_inventario`.`fecha_fin` LIKE '%".$_REQUEST['sSearch']."%'  
					OR  `reserva_inventario`.`no_reserva` LIKE '%".$_REQUEST['sSearch']."%'
					OR  `tipos_reservas`.`reserva_descrip` LIKE '%".$_REQUEST['sSearch']."%'
					OR CONCAT(asesor.`primer_nombre`,' ',asesor.`primer_apellido`) LIKE '%".$_REQUEST['sSearch']."%'
					OR `reserva_inventario`.no_reserva  LIKE '%".$_REQUEST['sSearch']."%'
					OR CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`primer_apellido`) LIKE '%".$_REQUEST['sSearch']."%'
					 ) ";
				 
 			  }
			}
 
		$SQL=" SELECT reserva_inventario.no_recibo AS serie_recibo_no 
			 FROM reserva_inventario
			INNER JOIN `reserva_ubicaciones` ON (`reserva_ubicaciones`.`no_reserva`=reserva_inventario.no_reserva)
			INNER JOIN `sys_status` ON (sys_status.`id_status`=reserva_inventario.`estatus`)
			INNER JOIN tipos_reservas ON (tipos_reservas.`id_reserva`=reserva_inventario.id_reserva)
			INNER JOIN `sys_personas` ON (sys_personas.`id_nit`=reserva_inventario.`id_nit`)
			INNER JOIN `sys_personas` AS asesor ON (`asesor`.`id_nit`=reserva_inventario.`nit_comercial`) 
			WHERE reserva_ubicaciones.estatus=1 
				 ";
			
		$SQL.=$QUERY;
		$SQL.=" GROUP BY reserva_inventario.no_reserva ";
	 
	   
		$rs=mysql_query($SQL);
		//$row=mysql_fetch_assoc($rs);
		$total_row=mysql_num_rows($rs);
		
			$SQL="SELECT *, `reserva_inventario`.no_reserva AS no_reserva_id,
					reserva_inventario.no_recibo AS serie_recibo_no,
					DATEDIFF(reserva_inventario.`fecha_fin`,CURDATE()) AS day_restantes,
					DATE_FORMAT(reserva_inventario.fecha_reserva, '%d-%m-%Y %h:%m:%s') AS  fecha_reserva,
					DATE_FORMAT(reserva_inventario.fecha_fin, '%d-%m-%Y %h:%m:%s') AS  fecha_fin,
					CONCAT(asesor.`primer_nombre`,' ',asesor.`primer_apellido`) AS nombre_asesor,
					CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`primer_apellido`) AS nombre_cliente 
				 FROM reserva_inventario
				INNER JOIN `reserva_ubicaciones` ON (`reserva_ubicaciones`.`no_reserva`=reserva_inventario.no_reserva) 
				INNER JOIN `sys_status` ON (sys_status.`id_status`=reserva_inventario.`estatus`)
				INNER JOIN tipos_reservas ON (tipos_reservas.`id_reserva`=reserva_inventario.id_reserva)
				INNER JOIN `sys_personas` ON (sys_personas.`id_nit`=reserva_inventario.`id_nit`)
				INNER JOIN `sys_personas` AS asesor ON (`asesor`.`id_nit`=reserva_inventario.`nit_comercial`)
				WHERE reserva_ubicaciones.estatus=1   ";
			$SQL.=$QUERY;
 			//$SQL.=$HAVING;
			
			$SQL.=" GROUP BY reserva_inventario.no_reserva ";
			
			$SQL.=" ORDER BY reserva_inventario.`fecha_reserva` DESC ";
			
			$SQL.=" limit ".$_REQUEST['iDisplayStart'].",".$_REQUEST['iDisplayLength']."";
			
			
		 
			 
			$rs=mysql_query($SQL);
			$result=array();
			$data=array(
				'sEcho'=>$_REQUEST['sEcho'],
				'iTotalRecords'=>10,
				'iTotalDisplayRecords'=>$total_row,
				'aaData' =>array()
			); 
			
			while($row=mysql_fetch_assoc($rs)){	
 
 				$encriptID=System::getInstance()->Encrypt(json_encode($row));
				$row['bt_reserva']='';
				$row['bt_editar']='<a href="#" class="editItems" id="'.$encriptID.'"><img src="images/subtract_from_cart.png" width="24" heigth="24"  /></a>';
                 
				array_push($data['aaData'],$row);
			}
			
			echo json_encode($data);
		
		exit;
		
	}






	/* AGREGAR LOTES */
	if (isset($_REQUEST['submit_lotes_add'])){
		if ($_REQUEST['submit_lotes_add']=="1"){
			//System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
			
			$jardin=json_decode(System::getInstance()->Request("jardin"));
			$fase=json_decode(System::getInstance()->Request("fase"));
			
		//	echo $_REQUEST['bloque'];
		//	print_r($_REQUEST);
			$retur=array("mensaje"=>"Operacion agregada correctamente!","error"=>false);


			if ($_REQUEST['sbloque']=="1"){
				$b_from=$_REQUEST['bloque_from'];
				$b_to=$_REQUEST['bloque_to'];
			}else{
				$b_from=$_REQUEST['bloque'];
				$b_to=$_REQUEST['bloque'];
			}
			
							
			$l_from=$_REQUEST['lotes_from'];
			$l_to=$_REQUEST['lotes_to'];

			for ($b=$b_from;$b<=$b_to;$b++){
				
				for ($l=$l_from;$l<=$l_to;$l++){
					 
					$obj= new ObjectSQL();
					$obj->id_jardin=$jardin->id_jardin;
					$obj->id_fases=$fase->id_fases;
					$obj->cavidades=$_REQUEST['cavidades'];
					$obj->osarios=$_REQUEST['osarios'];
					$obj->bloque=$b;
					$obj->lote=$l;
					$obj->estatus="1";
					$SQL=$obj->getSQL("insert","inventario_jardines");
					mysql_query($SQL);
			
				}
			}
			
			echo json_encode($retur);
			exit;	
		}
	}

	/* EDITAR UN LOTE */
	if (isset($_REQUEST['submit_lote_edit'])){
		if ($_REQUEST['submit_lote_edit']=="1"){
			$retur=array("mensaje"=>"Registro actualizado correctamente!","error"=>false);


			$jardin=json_decode(System::getInstance()->Request("id"));			
			
			$obj= new ObjectSQL();
		//	$obj->id_jardin=$jardin->id_jardin;
		//	$obj->id_fases=$jardin->id_fases;
			$obj->cavidades=$_REQUEST['cavidades'];
			$obj->osarios=$_REQUEST['osarios'];
			$obj->estatus="1";
			$SQL=$obj->getSQL("update","inventario_jardines"," where id_jardin='". mysql_escape_string($jardin->id_jardin) ."' AND id_fases='". mysql_escape_string($jardin->id_fases) ."' AND lote='". mysql_escape_string($jardin->lote) ."' ");
			
			mysql_query($SQL);
			
			echo json_encode($retur);
			exit;
		}
	}




	/* Editar LOTES */
	if (isset($_REQUEST['submit_group_lotes_edit'])){
		if ($_REQUEST['submit_group_lotes_edit']=="1"){
			//System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
			
			$jardin=json_decode(System::getInstance()->Request("jardin"));
			$fase=json_decode(System::getInstance()->Request("fase"));
			
		//	echo $_REQUEST['bloque'];
		//	print_r($_REQUEST);
			$retur=array("mensaje"=>"Operacion realizada correctamente!","error"=>false);


			if ($_REQUEST['sbloque']=="1"){
				$b_from=$_REQUEST['bloque_from'];
				$b_to=$_REQUEST['bloque_to'];
			}else{
				$b_from=$_REQUEST['bloque'];
				$b_to=$_REQUEST['bloque'];
			}
			
							
			$l_from=$_REQUEST['lotes_from'];
			$l_to=$_REQUEST['lotes_to'];

			for ($b=$b_from;$b<=$b_to;$b++){
				
				for ($l=$l_from;$l<=$l_to;$l++){
					 
					$obj= new ObjectSQL();
			//		$obj->id_jardin=$jardin->id_jardin;
			//		$obj->id_fases=$fase->id_fases;
					$obj->cavidades=$_REQUEST['cavidades'];
					$obj->osarios=$_REQUEST['osarios'];
			//		$obj->bloque=$b;
			//		$obj->lote=$l;
				//	$obj->estatus="1";
					$SQL=$obj->getSQL("update","inventario_jardines"," where id_jardin='". mysql_escape_string($jardin->id_jardin) ."' AND id_fases='". mysql_escape_string($fase->id_fases) ."' AND lote='". mysql_escape_string($l) ."' AND bloque='". mysql_escape_string($b) ."'");
					mysql_query($SQL);
			
				}
			}
			
			echo json_encode($retur);
			exit;	
		}
	}

 
	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.hotkeys.js");
	
	SystemHtml::getInstance()->addTagScript("script/Class.js");
	SystemHtml::getInstance()->addTagScript("script/Class.reservas.js");
	SystemHtml::getInstance()->addTagScript("script/Class.AsesoresTree.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");
	
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.mouse.js");
	SystemHtml::getInstance()->addTagScript("script/ui//jquery.ui.draggable.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.position.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.resizable.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.button.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.dialog.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
	
	SystemHtml::getInstance()->addTagStyle("css/showLoading.css");


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
var _reservas;


$(function(){
	
	_reservas= new Reservas("content_dialog");
	
	gobal_table=$("#role_list").dataTable({
							"bFilter": true,
							"bLengthChange": false,
							"bInfo": false,
							"bPaginate": true,
							"bProcessing": true,
							"bServerSide": true,
							"sAjaxSource": "./?mod_inventario/reserva/listado_reserva&dt_list=1",
							"sServerMethod": "GET",
							"aoColumns": [
									{ "mData": "no_reserva_id" },
									{ "mData": "reserva_descrip" },
									{ "mData": "nombre_cliente" },
									{ "mData": "serie_recibo_no" },
									{ "mData": "fecha_reserva" },
									{ "mData": "fecha_fin" }, 
									{ "mData": "nombre_asesor" },  
									{ "mData": "bt_editar" }
								],
							"oLanguage": {
									"sLengthMenu": "Mostrar _MENU_ registros por pagina",
									"sZeroRecords": "No se ha encontrado - lo siento",
									"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
									"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
									"sInfoFiltered": "(filtrado de _MAX_ total registros)",
									"sSearch":"Buscar"
								},
						  	  "fnDrawCallback": function( oSettings ) {
									_reservas.doEditItemsFromList('inventario_page');
							   }
							});
							
	$('<button id="refresh">Buscar</button>').appendTo('div.dataTables_filter'); 
	

	
});
 

 
</script>
 
<div id="inventario_page" class="fsPage" style="width:98%">
<h2>Listado de Reservas</h2>

	<table border="0" class="display" id="role_list" style="font-size:13px">
      <thead>
        <tr>
          <th>No. Reserva</th>
          <th>Tipo reserva</th>
          <th>Cliente</th>
          <th>Recibo Serie Reserva</th>
          <th>Inicia Reserva</th>
          <th>Termina Reserva</th>
          <th>Asesor</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>

      </tbody>
  </table>
</div>
<div id="content_dialog" ></div>
<?php SystemHtml::getInstance()->addModule("footer");?>