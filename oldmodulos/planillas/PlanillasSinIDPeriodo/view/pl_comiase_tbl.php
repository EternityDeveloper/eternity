<?php

  if (!isset($protect)){
	exit;
  }
  
  
  $titulo  = "Tabla de Comisiones Asesores";
  $retur   = array("mensaje"=>"No se pudo completar la operacion!","error"=>true);
  $usuario = UserAccess::getInstance()->getID();
  $fechau  = date("Y/m/d");
 
  if ( (isset($_REQUEST['accion']) && $_REQUEST['accion'] === 'INSERT') ){
		 
    $tipo = 1;	 
	$verificar = "select count(1) as conteo
	                 from cm_comisiones_tbl
				    where estatus = 1
					  and tipo       = " .(int)$tipo."
					  and rangoini   = ".(int)$_REQUEST['rangoini']."
					  and rangofin   = ".(int)$_REQUEST['rangofin']."
					  and porcentaje = ".(float)$_REQUEST['porcentaje'];
	  
	 $resulSet = mysql_query($verificar);
	 $inserta  = mysql_fetch_array($resulSet);
	 
	 
	 
	 if($inserta['conteo']==0 ){ 
	 
		  /* Verificar si toda la linea ya existe */
		  $query = "insert into cm_comisiones_tbl (tipo, 
												  rangoini, 
												  rangofin, 
												  porcentaje,
												  usuario,
												  fechau,
												  estatus)
										  values (".(int)$tipo.","
												  .(int)$_REQUEST['rangoini'].","
												  .(int)$_REQUEST['rangofin'].","
												  .(float)$_REQUEST['porcentaje'].",'"
												  .$usuario."','"
												  .$fechau."',
												  1)";
		 $resultado = mysql_query($query);
		 $retur['mensaje']="Registro agregado correctamente!";
		 $retur['error']=true;
		 /*$mensaje = "Registro agregado correctamente!";*/
	        
       }else{
		 $retur['mensaje']="Registro Ya Existente!";
		 $retur['error']=false;

	   }
	   
	   /*echo json_encode($mensaje);*/
	   echo json_encode($retur);
	   exit;
  }
  
  
  if ( (isset($_REQUEST['accion']) && $_REQUEST['accion'] === 'EDIT') ){

     /* Verificar si ya existe */
	 $verificar = "select count(1) as conteo
	                 from cm_comisiones_tbl
				    where estatus    = 1
					  and tipo       = " .(int)$_REQUEST['tipo']."
					  and rangoini   = ".(int)$_REQUEST['rangoini']."
					  and rangofin   = ".(int)$_REQUEST['rangofin']."
					  and porcentaje = ".(float)$_REQUEST['porcentaje'];
	  
	 $resulSet  = mysql_query($verificar);	
	 $resultado = mysql_fetch_array($resulSet);
	 
	 if( $resultado['conteo'] > 0 ){ 
	    $retur['mensaje'] = "Registro Ya existente!";
		$retur['error']   = true; 
		
	 }else{
	      
		 $query = "Update cm_comisiones_tbl
					  set rangoini   = " .(int)$_REQUEST['rangoini']. ",
						  rangofin   = " .(int)$_REQUEST['rangofin']. ",
						  porcentaje = " .(float)$_REQUEST['porcentaje']."
					where orden = " . (int)$_REQUEST['orden']; 
		
		 $resultado = mysql_query($query);
		 $retur['mensaje'] = "Registro actualizado correctamente!";
		 $retur['error']   = false;
	 }
	 
	 echo json_encode($retur);
	 exit;
	 
	        
  }
  
  
  if ( (isset($_REQUEST['accion']) && $_REQUEST['accion'] === 'DELETE') ){
     /* Estatus activo = 1 o inactivo = 2 */
	 
	 $query = "Update cm_comisiones_tbl
			      set estatus  = 2
				where orden = " . (int)$_REQUEST['orden']; 
		
	 $resultado = mysql_query($query);
	 $retur['mensaje'] = "Registro Eliminado.!";
	 $retur['error']   = false;	
	  
	 echo json_encode($retur);
	 exit;
	        
  }
  
  
  $sql    = "select * from cm_comisiones_tbl where tipo = 1 and estatus = 1 order by rangoini, rangofin";
  $result = mysql_query($sql); 
  $conteo = mysql_num_rows($result);
  
  
  SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");

	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");
	
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.mouse.js");
	SystemHtml::getInstance()->addTagScript("script/ui//jquery.ui.draggable.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.position.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.resizable.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.button.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.dialog.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");

	SystemHtml::getInstance()->addTagScript("script/ajaxfileupload.js");
	 
	
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
var oTable;

$(document).ready(function(){

	oTable = $("#catalogo").dataTable({
							"bFilter": true,
							"bInfo": false,
							"bPaginate": true,
							  "oLanguage": {
									"sLengthMenu": "regs x pag_MENU_",
									"sZeroRecords": "No se ha encontrado - lo siento",
									"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
									"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
									"sInfoFiltered": "(filtrado de _MAX_ total registros)",
									"sSearch":"Buscar"
								}
							});
							
    $('<button type="button" class="greenButton" name="pbutton"  id="pbutton" onclick="insertOpenDialog()">Agregar</button>').appendTo('div.dataTables_filter');
								
});


function insertOpenDialog(){
   
 var accion = "INSERT";
 $.post("?mod_planillas/view/component/tbl_asesores", {"accion":accion}, function(data){
	 
	 var dialog = createNewDialog("Tabla Asesores",data,430);
	 var n = $('#'+dialog);
	 n.dialog('option', 'position', [(document.scrollLeft/550), 100]);
	 
	 $("#cancelar").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
	  });
	  
	 $("#procesar").click(function(){
		 $.post("?mod_planillas/view/pl_comiase_tbl",$("#frm_tbl_config").serializeArray(), function(data){
         alert(data.mensaje);
	 	 $("#"+dialog).remove();
		 window.location.reload();
		  				
		},"json");
		
	 });
	 
  });  
   
} <!-- Fin InsertOpenDialog -->
   

function editOpenDialog(orden, accion){

   $.post("?mod_planillas/view/component/tbl_asesores",{"orden":orden, "accion":accion},function(data){
	   var dialog=createNewDialog("Tabla Asesores",data,430);
	   
	   var n = $('#'+dialog);
	   n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
	    
		
	   $("#cancelar").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		
	  $("#procesar").click(function(){
		 $.post("?mod_planillas/view/pl_comiase_tbl",$("#frm_tbl_config").serializeArray(), function(data){
			alert(data.mensaje);
	 
			$("#"+dialog).remove();
		    window.location.reload();
		  				
		},"json");		
	   
	});
	
  });	
}


function createDiv(){
	var rand="Dialog_"+Math.floor(Math.random() * (1000 - 1 + 1) + 1);
	$("#content_dialog").append("<div id=\""+rand+"\"></div>");
	return rand;
}

function createNewDialog(title,data,width){
	var rand=createDiv();
	var width_=500;
	if (width>0){
		width_=width;
	}
	
	$("#"+rand).attr("title",title);
	$("#"+rand).html(data);
	$("#"+rand).dialog({
		modal: true,
		width:width_,
		close: function (ev, ui) {
			$(this).dialog("destroy");
			$(this).remove();

		}
	});	
	return rand;
}

</script>
<style>
 div.dataTables_filter{
	
 }
	
.fsPage{
	width:60%;	
}
</style>
<div id="mantenimiento" class="fsPage">
<h2><?php echo $titulo; ?></h2>

	<table width="100%" border="0" class="display" id="catalogo" style="font-size:13px" align="center">
      <thead>
        <tr>
          <th width="139">Rango Inicial</th>
          <th width="162">Rango Final</th>
          <th width="140">Porcentaje</th>
          <th width="92">Tipo</th>
          <th width="79">&nbsp;</th>
          <th width="79">&nbsp;</th>
        </tr>
      </thead>
      <tbody>
       <?php 
	      if($conteo > 0){
             while($row = mysql_fetch_array($result)){
		?>		  
	           <tr>
                  
                  <td align="right"><?=$row['rangoini']?></td>
                  <td align="right"><?=$row['rangofin']?></td>
                  <td align="right"><?=$row['porcentaje']?></td>
                  <td align="right"><?=$row['tipo']?></td>
                  <td align="center">
                    <a href="#" onclick="editOpenDialog(<?=$row['orden']?>,'EDIT')"><img src="images/clipboard_edit.png"/></a>
                  </td>
                  <td align="center">
                    <a href="#" onclick="editOpenDialog(<?=$row['orden']?>,'DELETE')"><img src="images/cross.png"/></a>
                  </td>
                  
               </tr>
		<?php }			  			  
		  }
	   ?> 
      </tbody>
  </table>
 </div>
 <div id="content_dialog" ></div>
<?php SystemHtml::getInstance()->addModule("footer");?>