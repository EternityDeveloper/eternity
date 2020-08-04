<?php

  if (!isset($protect)){
	exit;
  }
  
  $titulo = "Tabla Bono por Auxilio.";
  $retur  = array("mensaje" => "No se pudo completar la operacion!", 
                  "error"   => true);
				  
  $usuario = UserAccess::getInstance()->getID();
  $fechau  = date("Y/m/d");
  
  if ( (isset($_REQUEST['accion']) && $_REQUEST['accion'] === "INSERT") ){
	
	$sql = "select count(1) as conteo
	          from cm_bonoauxilio_tbl
			 where estatus       = 1
			   and mes           = " .(int)$_REQUEST['mes']."
			   and cumplimiento  = " .(int)$_REQUEST['cumplimiento']."
			   and bonoprimario  = " .(float)$_REQUEST['bonoprimario']."
			   and ventacontrato = " .(int)$_REQUEST['ventacontrato']."
			   and bonoadicional = " .(float)$_REQUEST['bonoadicional'];
			   
	$rsQuery   = mysql_query($sql);
	$rowQuery  = mysql_fetch_array($rsQuery);
	
	if( $rowQuery['conteo'] == 0  ){

		$query = "insert into cm_bonoauxilio_tbl (mes, 
												  cumplimiento, 
												  bonoprimario,
												  ventacontrato,
												  bonoadicional,
												  usuario,
												  fechau,
												  estatus) 
										  values (".(int)$_REQUEST['mes'].","
												   .(int)$_REQUEST['cumplimiento'].","
												   .(float)$_REQUEST['bonoprimario'].","
												   .(int)$_REQUEST['ventacontrato'].","
												   .(float)$_REQUEST['bonoadicional'].",'"
												   .$usuario."','"
												   .$fechau."',
												   1)";
												   
		 $resultado = mysql_query($query);		
		 $retur['mensaje']="Registro agregado correctamente!";	
		
	}else{
		$retur['mensaje']="Registro Ya Existente!";
			
	}
	
	echo json_encode($retur);
	exit;
  }
  
  if ( (isset($_REQUEST['accion']) && $_REQUEST['accion'] === "EDIT") ){

     $sql = "select count(1) as conteo
	           from cm_bonoauxilio_tbl
			  where estatus       = 1
			    and mes           = " .(int)$_REQUEST['mes']. "
				and cumplimiento  = " .(int)$_REQUEST['cumplimiento']. "
				and bonoprimario  = " .(float)$_REQUEST['bonoprimario']. "
				and ventacontrato = " .(int)$_REQUEST['ventacontrato']. "
				and bonoadicional = " .(float)$_REQUEST['bonoadicional'];
				
	 $rsQuery  = mysql_query($sql);
	 $rowQuery = mysql_fetch_array($rsQuery);
	 
	 if( $rowQuery['conteo'] == 0 ) {
		 
		  $query = "Update cm_bonoauxilio_tbl
					  set mes          = " .(int)$_REQUEST['mes']. ",
						  cumplimiento = " .(int)$_REQUEST['cumplimiento']. ",
						  bonoprimario = " .(float)$_REQUEST['bonoprimario']. ",
						  ventacontrato= " .(int)$_REQUEST['ventacontrato'].",
						  bonoadicional= " .(float)$_REQUEST['bonoadicional']." where orden = " . (int)$_REQUEST['orden']; 
		
		 $resultado = mysql_query($query);
		 $retur['mensaje'] = "Registro actualizado correctamente!";
		 $retur['error']   = false;
		 
	 }else{
		 $retur['mensaje'] = "Registro Ya existente!";
		 $retur['error']   = true; 
		 		 
	 }
	 
	 echo json_encode($retur);
	 exit;
	 
  }
  
  
  if ( (isset($_REQUEST['accion']) && $_REQUEST['accion'] === "DELETE") ){

     $query = "update cm_bonoauxilio_tbl set estatus = 2 where orden = " . (int)$_REQUEST['orden']; 
	 $resultado = mysql_query($query);
	 $retur['mensaje'] = "Registro Eliminado Correctamente!";
	 $retur['error']   = false;
	 
	 echo json_encode($retur);
	 exit;	        
  }
  
  
  $sql    = "select * from cm_bonoauxilio_tbl where estatus = 1 order by mes";
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
   $.post("?mod_planillas/view/component/tbl_bonoaux",{"accion":accion},function(data){
	   var dialog=createNewDialog("Tabla Bono Auxiliar",data,430);
	   
	   var n = $('#'+dialog);
	   n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
	    
	  
		
	   $("#cancelar").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		
		
		 $("#procesar").click(function(){
			 $.post("?mod_planillas/view/pl_bonoaux_tbl",$("#frm_tbl_config").serializeArray(), function(data){
			 alert(data.mensaje);
			 
			 $("#"+dialog).remove();
			 window.location.reload();
							
			},"json");
			
		 });
	   
	});
}



function editOpenDialog(orden, accion){

   $.post("?mod_planillas/view/component/tbl_bonoaux",{"orden":orden, "accion":accion}, function(data){
	   var dialog=createNewDialog("Tabla Bono Auxiliar",data,430);
	   
	   var n = $('#'+dialog);
	   n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
	    
	  
		
	   $("#cancelar").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		
		
		 $("#procesar").click(function(){
			 $.post("?mod_planillas/view/pl_bonoaux_tbl",$("#frm_tbl_config").serializeArray(), function(data){
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
          <th width="92">Mes</th>
          <th width="139">Cumplimiento</th>
          <th width="162">Bono Primario</th>
          <th width="140">Venta Contrato</th>
          <th width="140">Bono Adicional</th>
          
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
                  <td align="right"><?=$row['mes']?></td>
                  <td align="right"><?=$row['cumplimiento']?></td>
                  <td align="right"><?=number_format($row['bonoprimario'],2,".",",")?></td>
                  <td align="right"><?=$row['ventacontrato']?></td>
                  <td align="right"><?=number_format($row['bonoadicional'],2,".",",")?></td>
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