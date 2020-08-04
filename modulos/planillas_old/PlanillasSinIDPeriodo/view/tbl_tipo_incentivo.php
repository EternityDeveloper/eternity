<?php

  if (!isset($protect)){
	exit;
  }
  
  
  $titulo = "Tipos de Incentivo";
  $retur  = array("mensaje" => "No se pudo completar la operacion!", 
                  "error"   => true); 
 
  $usuario = UserAccess::getInstance()->getID();
  $fechau  = date("Y/m/d");
				   
  if ( (isset($_REQUEST['accion']) && $_REQUEST['accion'] === "INSERT") ){
	  	 
         $query = "insert into tbl_tipo_incentivo ( descripcion, 
												    usuario,
												    fechau,
												    estatus)
							  		   values ('".strtoupper(mysql_real_escape_string($_REQUEST['descripcion']))."',
											  '".$usuario."',
											  '".$fechau."',
											  1)";
											  
	     $resultado = mysql_query($query);
		 $retur['mensaje']="Registro agregado correctamente!";		 
		 
	 echo json_encode($retur);
	 exit;				  
	        
  }
  
  
  if ( (isset($_REQUEST['accion']) && $_REQUEST['accion'] === "EDIT") ){
    
		 $query = "Update tbl_tipo_incentivo
					  set descripcion   = '" .strtoupper(mysql_real_escape_string($_REQUEST['descripcion'])). "'
					where tipo_incentivo    = " .(int)$_REQUEST['tipo_incentivo']; 
		
		 $resultado = mysql_query($query);
		 $retur['mensaje']="Registro Modificado correctamente!";		 
		 /*$retur['mensaje']=$query;*/
	 
	 echo json_encode($retur);
	 exit;				  
	         	       
  }
  
  
  if ( (isset($_REQUEST['accion']) && $_REQUEST['accion'] === "DELETE") ){

     $query = "update tbl_tipo_incentivo set estatus = 2 where tipo_incentivo = " .(int)$_REQUEST['tipo_incentivo']; 
	 $resultado = mysql_query($query);
	 $retur['mensaje']="Registro Eliminado correctamente!";
	 
	 echo json_encode($retur);
	 exit;		       
  }
  
  
  $sql    = "select * from tbl_tipo_incentivo where estatus = 1 order by tipo_incentivo";
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
   $.post("?mod_planillas/view/component/tbl_incentivos",{"accion":accion},function(data){
	   var dialog=createNewDialog("Incentivos",data,430);
	   
	   var n = $('#'+dialog);
	   n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
	    
	  
		
	   $("#cancelar").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		
		 $("#procesar").click(function(){
			 $.post("?mod_planillas/view/tbl_tipo_incentivo",$("#frm_tbl_config").serializeArray(), function(data){
			 alert(data.mensaje);
			 
			 $("#"+dialog).remove();
			 window.location.reload();
							
			},"json");
			
		 });
	   
	});
}





function editOpenDialog(orden, accion){

   $.post("?mod_planillas/view/component/tbl_incentivos",{"idincentivo":orden, "accion":accion},function(data){
	   var dialog=createNewDialog("Incentivos",data,430);
	   
	   var n = $('#'+dialog);
	   n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
	    
	  
		
	   $("#cancelar").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		
		 $("#procesar").click(function(){
			 $.post("?mod_planillas/view/tbl_tipo_incentivo",$("#frm_tbl_config").serializeArray(), function(data){
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
          <th width="81">Id</th>
          <th width="92">Descripcion</th>
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
                  <td align="right"><?=$row['tipo_incentivo']?></td>
                  <td align="left"><?=$row['descripcion']?></td>
                  
                  <td align="center">
                    <a href="#" onclick="editOpenDialog(<?=$row['tipo_incentivo']?>,'EDIT')"><img src="images/clipboard_edit.png"/></a>
                  </td>
                  <td align="center">
                    <a href="#" onclick="editOpenDialog(<?=$row['tipo_incentivo']?>,'DELETE')"><img src="images/cross.png"/></a>
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