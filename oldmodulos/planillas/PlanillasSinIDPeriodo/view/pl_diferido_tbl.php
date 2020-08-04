<?php

  if (!isset($protect)){
	exit;
  }
  $titulo = "Tabla de Diferidos";

  $retur  = array("mensaje" => "No se pudo completar la operacion!", 
                  "error"   => true);

  $usuario = UserAccess::getInstance()->getID();
  $fechau  = date("Y/m/d");
  
				  
  if ( (isset($_REQUEST['accion']) && $_REQUEST['accion'] === "INSERT") ){
	  	  
	  $sql = "select count(1) as conteo
	            from cm_diferidos_tbl
			   where estatus    = 1
				 and rangoini   = " .(float)$_REQUEST['rangoini']. "
				 and rangofin   = " .(float)$_REQUEST['rangofin'] ;
				 
	 $rsQuery = mysql_query($sql);
	 $rowQuery = mysql_fetch_array($rsQuery);
	 
	 if( $rowQuery['conteo'] == 0 ){
		  $query = "insert into cm_diferidos_tbl (rangoini, 
												  rangofin, 
												  diferido_1,
												  diferido_2,
												  diferido_3,
												  usuario,
												  fechau,
												  estatus)
										  values (".(float)$_REQUEST['rangoini'].","
												   .(float)$_REQUEST['rangofin'].","
												   .(float)$_REQUEST['diferido_1'].","
												   .(float)$_REQUEST['diferido_2'].","
												   .(float)$_REQUEST['diferido_3'].",'"
												   .$usuario."','"
												   .$fechau."',
												   1)";
		  $resultado = mysql_query($query);
		  
		  
		  $retur['mensaje'] = "Registro agregado correctamente!";	 
		 
	 }else{
		 
		$retur['mensaje'] = "Registro Ya Existente!";		 
	 }
	 	 
	 echo json_encode($retur);
	 exit;
	        
  }
  
  
  if ( (isset($_REQUEST['accion']) && $_REQUEST['accion'] === "EDIT") ){

	  $sql = "select count(1) as conteo
	            from cm_diferidos_tbl
			   where estatus    = 1
				 and rangoini   = " .(float)$_REQUEST['rangoini']. "
				 and rangofin   = " .(float)$_REQUEST['rangofin']. "
				 and diferido_1 = " .(float)$_REQUEST['diferido_1']. "
				 and diferido_2 = " .(float)$_REQUEST['diferido_2']. "
				 and diferido_3 = " .(float)$_REQUEST['diferido_3'];
				 
	 $rsQuery = mysql_query($sql);
	 $rowQuery = mysql_fetch_array($rsQuery);
	 
	 if($rowQuery['conteo'] == 0 ){

		 $query = "Update cm_diferidos_tbl
					  set rangoini   = " .(float)$_REQUEST['rangoini'].   ",
						  rangofin   = " .(float)$_REQUEST['rangofin'].   ",
						  diferido_1 = " .(float)$_REQUEST['diferido_1']. ",
						  diferido_2 = " .(float)$_REQUEST['diferido_2']. ",
						  diferido_3 = " .(float)$_REQUEST['diferido_3']. "
					where id = " . (int)$_REQUEST['id']; 
		
		 $resultado = mysql_query($query);	
		 $retur['mensaje'] = "Registro modificado correctamente!";		 
		 
	 }else{
		 
		 $retur['mensaje'] = "Registro Ya Existente!";		 
	 }
	 
	 echo json_encode($retur);
	 exit;
     
  }
  
  
  if ( (isset($_REQUEST['accion']) && $_REQUEST['accion'] === "DELETE") ){

     $query = "update cm_diferidos_tbl set estatus = 2 where id = " . (int)$_REQUEST['id']; 
	 $resultado = mysql_query($query);
	 $retur['mensaje'] = "Registro Eliminado Correctamente!";
	 
	 echo json_encode($retur);
	 exit;
	        
  }
  
  
  $sql    = "select * from cm_diferidos_tbl where estatus = 1 order by id, rangoini, rangofin";
  $result = mysql_query($sql); 
  $conteo = mysql_num_rows($result);
  
  
  SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
  
  SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
  SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
  
  SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
  SystemHtml::getInstance()->addTagScript("script/jquery.blockUI.js");
  SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");
  
  SystemHtml::getInstance()->addTagScript("script/Class.js");
  SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
  SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");
	
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
   $.post("?mod_planillas/view/component/tbl_diferido",{"accion":accion},function(data){
	   var dialog=createNewDialog("Tabla Diferidos",data,430);
	   
	   var n = $('#'+dialog);
	   n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
	    
	  
		
	   $("#cancelar").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		
		 $("#procesar").click(function(){
			 $.post("?mod_planillas/view/pl_diferido_tbl",$("#frm_tbl_config").serializeArray(), function(data){
			 alert(data.mensaje);
			 
			 $("#"+dialog).remove();
			 window.location.reload();
							
			},"json");
			
		 });

	   
	});
}





function editOpenDialog(id, accion){

   $.post("?mod_planillas/view/component/tbl_diferido",{"id":id, "accion":accion},function(data){
	   var dialog=createNewDialog("Tabla Diferidos",data,430);
	   
	   var n = $('#'+dialog);
	   n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
	    
	  
		
	   $("#cancelar").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		
		 $("#procesar").click(function(){
			 $.post("?mod_planillas/view/pl_diferido_tbl",$("#frm_tbl_config").serializeArray(), function(data){
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
          <th width="139">Desde</th>
          <th width="162">Hasta</th>
          <th width="140">Diferido 1 %</th>
          <th width="140">Diferido 2 %</th>
          <th width="140">Diferido 3 %</th>
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
                  <td align="right"><?=number_format( $row['diferido_1'],2 )?></td>
                  <td align="right"><?=number_format( $row['diferido_2'],2 )?></td>
                  <td align="right"><?=number_format( $row['diferido_3'],2 )?></td>
                  <td align="center">
                    <a href="#" onclick="editOpenDialog(<?=$row['id']?>,'EDIT')"><img src="images/clipboard_edit.png"/></a>
                  </td>
                  <td align="center">
                    <a href="#" onclick="editOpenDialog(<?=$row['id']?>,'DELETE')"><img src="images/cross.png"/></a>
                  </td>
                  
               </tr>
		<?php }			  			  
		  }
	   ?> 
      </tbody>
  </table>
 </div>
  <div id="content_dialog" ></div>
