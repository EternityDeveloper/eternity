<?php
 
 if (!isset($protect)){
	exit;
 }
  
 if (isset($_REQUEST['view'])){
	include ("view/pl_periodo_cierre.php");
	exit;	
 }
 
 $anio           = isset($_REQUEST['anio'])   ? System::getInstance()->Decrypt($_REQUEST['anio'])    : 0; 
 $usuario        = UserAccess::getInstance()->getID();
 $fechau         = date("Y/m/d"); 
 $codigo_gerente = isset($_REQUEST['idgerente'])? System::getInstance()->Decrypt($_REQUEST['idgerente']) : 0;

 $mes         = isset($_REQUEST['periodo'])? System::getInstance()->Decrypt($_REQUEST['periodo']) : 0;
 $tipo_cierre = isset($_REQUEST['type'])   ? System::getInstance()->Decrypt($_REQUEST['type'])    : 0;
   
 
  if ( (isset($_REQUEST['accion']) && $_REQUEST['accion'] === "INSERT") ){
	 
   $insert = "insert into cm_detplanilla_gerente_tbl ( anio,
	                                             mes,
												 tipo_cierre,
												 codigo_gerente,
												 idconcepto,
												 monto,
												 usuario,
												 fechau )
									    values (".$anio.","
										         .$mes.",'"
												 .$tipo_cierre."',"
												 .$codigo_gerente.","
												 .$_REQUEST['idconcepto'].","
												 .$_REQUEST['monto'].",'"
												 .$usuario."','"
												 .$fechau."')";			 
    
	$insDatos = mysql_query($insert);
	$retur['mensaje']= "Registro Insertado Correctamente!";	
	echo json_encode($retur);
	exit;
				
 }
 
  if ( (isset($_REQUEST['accion']) && $_REQUEST['accion'] === "EDIT") ){
	 $numregistro = isset($_REQUEST['id'])   ? System::getInstance()->Decrypt($_REQUEST['id'])    : 0; 
	  
	 $upd = "update cm_detplanilla_gerente_tbl
	            set monto = ".$_REQUEST['monto']." where numregistro = " .$numregistro;
				  
	 
	 $updDatos = mysql_query($upd);
	 $retur['mensaje']= "Registro Insertado Correctamente!";		
	 echo json_encode($retur);
	 exit;	  
  }
 
 SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
  
 SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
 SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
  
 SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
 SystemHtml::getInstance()->addTagScript("script/jquery.blockUI.js");
 SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");
 
 SystemHtml::getInstance()->addTagScript("script/Class.js");
 SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
 SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");
 SystemHtml::getInstance()->addTagScriptByModule("class.opManplanillager.js","planillas/gerentes");
	
 /*Cargo el Header*/
 SystemHtml::getInstance()->addModule("header");
 SystemHtml::getInstance()->addModule("header_logo");
 /* cargo el modulo de top menu*/
 SystemHtml::getInstance()->addModule("main/topmenu");
 
  if (!(isset($_REQUEST['periodo']) && isset($_REQUEST['type']))){
 	 if(!isset($_REQUEST['noview'])){  
 ?>
   <!-- Despliega la Ventana Modal para Seleccionar los Datos de Periodo -->
	<script>
       var modalWindow;
          $(function(){
              modalWindow = new opManplanillager('content_dialog', '<?=$_REQUEST['choice']?>');
              modalWindow.doViewQuestion();
           });
    </script>
 
 <?php 
	 }
  }else{ 
      $cadena = '"anio='.$_REQUEST['anio'].'&periodo='.$_REQUEST['periodo'].'&type='.$_REQUEST['type'].'"';
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
                                
     });
    
   function editConcepto(registro){
	   var accion = 'EDIT'; 
	   
	   $.post("?mod_planillas/gerentes/view/pl_inout_ger",{"id":registro,"accion":accion},function(data){
		  var dialogABC = createNewDialog("Modificar Concepto",data,400);
		  var y = $('#'+dialogABC);
		  y.dialog('option', 'position', [(document.scrollLeft/550), 100]);
		  
	      $("#cancelar").click(function(){
			  $("#"+dialogABC).dialog("destroy");
			  $("#"+dialogABC).remove();
		  });
		  
		  $("#procesar").click(function(){
			 $.post("?mod_planillas/gerentes/man_planilla_ger",$("#frm_tbl_rubros").serializeArray(),function(data){
			     if(data.error){
					alert(data.mensaje);  
				 }else{
					alert(data.mensaje);
					$("#"+dialogABC).dialog("destroy");
					$("#"+dialogABC).remove(); 
					window.location.reload();
					/*detConceptos(id,cadena,periodo,type);	*/
				 }
			  },"json"); 
		  });
		 	  					   
	   }); <!-- Fin del POST --> 
	} 
   
   function detConceptos(id,cadena, periodo, type, anio){
	  
     <!-- Pagina detalle de conceptos por gerente --> 	
       $.post("?mod_planillas/gerentes/view/pl_detconceptos",{"idgerente":id, "periodo":periodo, "type":type, "anio":anio},function(data){
	      var dialog = createNewDialog("Ingresos y Descuentos (" + cadena + ")",data,650);
	   
	      var n = $('#'+dialog);
	      n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
		
	      $("#cancelar").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
				
	      $("#pbutton").click(function(){
		   var accion = 'INSERT';
		   
		     $.post("?mod_planillas/gerentes/view/pl_inout_ger",{"idgerente":id, "accion":accion, "periodo":periodo, "type":type, "anio":anio},function(data){
			    var dialogABC = createNewDialog("Ingreso de Conceptos",data,400);
			    var y = $('#'+dialogABC);
			    y.dialog('option', 'position', [(document.scrollLeft/550), 100]);
			   
			    $("#cancelar").click(function(){
					$("#"+dialogABC).dialog("destroy");
					$("#"+dialogABC).remove();
			    });			  
			  
			    $("#procesar").click(function(){
				  $.post("?mod_planillas/gerentes/man_planilla_ger",$("#frm_tbl_rubros").serializeArray(), function(data){ 
				     if (data.error){
						  alert(data.mensaje); 
					 }else{
					      alert(data.mensaje);
						 
						  $("#"+dialogABC).dialog("destroy");
						  $("#"+dialogABC).remove();
						  window.location.reload();	
						  
						  /*detConceptos(id,cadena,periodo,type);	*/ 
					 }
				  }, "json"); 
				  
			  });
	
			 });			 
			   
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
<?php	
  } ?>

<div id="principal">
   <h2>Mantenimiento Planilla Gerentes</h2>
   <?php
    if ((isset($_REQUEST['periodo']) && isset($_REQUEST['type']))){
	
   ?>
   
   <div id="canvas" class="fsPage" style="width:98%">
    <table border="0" class="display" id="catalogo" width="100%" style="font-size:12px;width:100%">
      <thead>
        <tr>
          <th width="13%">C&oacute;digo</th>
          <th width="53%">Nombre</th>
          <th width="10%">Tipo</th>
          <th width="10%">Mes</th>
          <th width="8%">A&ntilde;o</th>
          <th width="6%">&nbsp;</th>
        </tr>
      </thead>
      <tbody>
      
      <?php
	  
	   $sql =  "select a.anio,
					   a.mes,
					   a.tipo_cierre,
					   c.codigo_gerente_grupo AS codigo_gerente,
					   CONCAT(b.primer_nombre, ' ', b.primer_apellido) as nom_gerente
				  from cm_planilla_gerente_tbl a,
					   sys_gerentes_grupos c,
					   sys_personas b
				 where a.codigo_gerente = c.codigo_gerente
				   and c.id_nit = b.id_nit
				   and a.anio = ".$anio."
				   and a.mes  = ".$mes."
				   and a.tipo_cierre = '".$tipo_cierre."' order by c.codigo_gerente_grupo";	
				 
	    $resultSet = mysql_query($sql);		  
        while($row = mysql_fetch_array($resultSet)){
		  $encryptID  = System::getInstance()->Encrypt($row['codigo_gerente']);
		  $encryptPer = System::getInstance()->Encrypt($row['mes']);
		  $encryptTyp = System::getInstance()->Encrypt($row['tipo_cierre']);
		  $encryptAnio = System::getInstance()->Encrypt($row['anio']);
		  
	   ?>	 
		   <tr>
              <td><?=$row['codigo_gerente']?></td>
              <td><?=$row['nom_gerente']?></td>
              <td align="center"><?=$row['tipo_cierre']?></td>
              <td align="center"><?=$row['mes']?></td>
		      <td align="center"><?=$row['anio']?></td>
              <td align="center">
                    <a href="#" onclick="detConceptos('<?=$encryptID?>','<?=$row['nom_gerente']?>','<?=$encryptPer?>','<?=$encryptTyp?>','<?=$encryptAnio?>')"><img src="images/clipboard_edit.png"/></a>
              </td>
		   </tr> 	 
	   <?php	 
	    }
	  ?>  
 
      </tbody>
    </table>
    
   </div>
   <?php } ?>   
</div>


<div id="content_dialog" >
  <!-- Este DIV lo usan las ventanas emergentes -->
</div>
