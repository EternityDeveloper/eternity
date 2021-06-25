<?php 
 if (!isset($protect)){
	exit;
 }
 
 if (isset($_REQUEST['view'])){
	include ("view/pl_periodo_cierre.php");
	exit;	
 }

 
 SystemHtml::getInstance()->addTagScriptByModule("jquery.dataTables.js","planillas"); 
 /*SystemHtml::getInstance()->addTagStyle("css/demo_table.css"); */
 SystemHtml::getInstance()->addTagStyle("css/jquery.dataTables.css"); 
 
 SystemHtml::getInstance()->addTagScript("script/Class.js");  
  
/* SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
   SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js"); */

 SystemHtml::getInstance()->addTagStyle("css/smoothness/jquery.ui.combogrid.css");
 SystemHtml::getInstance()->addTagScript("script/jquery.ui.combogrid-1.6.3.js");

 SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
 SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");
  
 /*SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");*/

 SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
 SystemHtml::getInstance()->addTagScript("script/jquery.blockUI.js");

 SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");
 SystemHtml::getInstance()->addTagScriptByModule("class.opPeriodo.js","planillas/asesores"); 
 
 
 /*Cargo el Header*/
 SystemHtml::getInstance()->addModule("header");
 SystemHtml::getInstance()->addModule("header_logo");
	
 /*Top Menu*/
 SystemHtml::getInstance()->addModule("main/topmenu");
  
  if (!(isset($_REQUEST['periodo']) && isset($_REQUEST['type']))){
 	 if(!isset($_REQUEST['noview'])){  

?>
   <!-- Despliega la Ventana Modal para Seleccionar los Datos de Periodo -->
	 
	 <script>
       var modalWindow;
          $(function(){
              modalWindow = new opPeriodo('content_dialog', '<?=$_REQUEST['choice']?>');
              modalWindow.doViewQuestion();
           });
    </script>
<?php 
	  }
   }else{ 
       $cadena = '"anio='.$_REQUEST['anio'].'&periodo='.$_REQUEST['periodo'].'&type='.$_REQUEST['type'].'"';
	   	   
   ?>
	
    <!-- Desplegamos el Data Table que Trae los datos calculados -->
	<script>
	
	$(document).ready(function() {
	   function format ( d ) {
		   		var ajax_data = {
					'codigo_asesor' : d
				};
				
				var innerTableData  = '';
				var contador = 0;
				var color    = '';
			
				$.ajax({
			        url     : './?mod_planillas/asesores/view/calc_comi_ase&opc=1&'+ <?php echo $cadena; ?>,				    
			        dataType: 'json',
				    type    : 'post',
					data    : ajax_data,
					async   : false,
			        success : function(newVal) {
			        	returnData = newVal;
			        },
			        cache: false
			    });
				
				$.each(returnData, function (ind, elem) {
					   
						innerTableData+= '<tr>'+
										     '<td>'+elem['contrato']+'</td>'+
											 '<td>'+elem['cliente']+'</td>'+
										     '<td>'+elem['fecha_ingreso']+'</td>'+
										     '<td class="derecha">'+elem['precio_lista']+'</td>'+
										     '<td class="derecha">'+elem['enganche']+'</td>'+
										     '<td class="derecha">'+elem['porcentaje']+'</td>'+
										     '<td class="derecha">'+elem['comision']+'</td>'+
											 '<td class="derecha">'+elem['cuotas']+'</td>'+
											 '<td class="derecha">'+elem['diferido_1']+'</td>'+
											 '<td class="derecha">'+elem['diferido_2']+'</td>'+
											 '<td class="derecha">'+elem['diferido_3']+'</td>'+
										 '</tr>';
						
					});
		   
		  return  '<div class="as_gridder" id="as_gridder">' +
		          '<table style="width: 70%; padding-left:50px;">' +
		             '<thead>' +
					     '<tr>' +
					         '<th>Contrato</th>' +
							 '<th>Cliente</th>' +
					         '<th>Fecha Venta</th>' +
					         '<th>Precio Lista</th>' +
					         '<th>% Enganche</th>' +
					         '<th>% Comisi&oacute;n</th>' +
					         '<th>Monto</th>' +
							 '<th>Cuotas</th>' +
							 '<th>Diferido 1</th>' +
							 '<th>Diferido 2</th>' +
							 '<th>Diferido 3</th>' +
					     '</tr>' +
					 '</thead>' +
					 '</tbody>' +
					     innerTableData +
					 '</tbody>' +
				 '</table>' + 
				 '</div>';
       }

              var table = $("#ventas").DataTable({
				 "sAjaxSource": "./?mod_planillas/asesores/view/calc_comi_ase&"+ <?php echo $cadena; ?>,
					/*"columns"    : [
									{ "title": "No. Asesor", "class": "right" },
									{ "title": "Nombre"    },
									{ "title": "Apellidos" },
									{ "title": "Grupo", "class": "right" },
									{ "title": "Contratos", "class": "right" },
									{ "title": "Monto Vta." }
									],		
		            "data"       : "aaData",*/
					"sServerMethod": "POST",
					"aoColumns": [
									{ "mDataProp"       : "row-detail",
									  "sClass"          : 'details-control',
									  "sOrderable"      : false,
									  "sWidth"          : "1%"
									},
									{ "mDataProp": "codigo_asesor", "sClass": "derecha" },
									{ "mDataProp": "nombre" },
									{ "mDataProp": "apellidos" },
									{ "mDataProp": "idgrupos", "sClass": "derecha" },
									{ "mDataProp": "contratos", "sClass": "derecha" },
									{ "mDataProp": "monto", "sClass": "derecha" },
									{ "mDataProp": "diferido_1", "sClass": "derecha" },
									{ "mDataProp": "diferido_2", "sClass": "derecha" },
									{ "mDataProp": "diferido_3", "sClass": "derecha" }
								 ],
					<!-- Habilitar los botones de paginacion true/false -->
					"bPaginate"  : true, 
					<!-- Habilitar la casilla de Search true/false      -->             
					"bFilter"    : true,   
					<!-- Habilitar el mensaje de la cantidad de registros a mostrar (true/false) -->           
					"bInfo"      : true, 
					<!-- Cuando sSearch lleva una cadena realiza el filtro en la data de una vez -->
					oSearch      : { "sSearch": "", "bRegex":false, "bSmart": false },
					oLanguage    : { "sLengthMenu": "Mostrar _MENU_ registros por pagina",
						             "sZeroRecords": "No se ha encontrado - lo siento",
						             "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
						             "sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
						             "sInfoFiltered": "(filtrado de _MAX_ total registros)",
						             "sSearch":"Buscar",
									 "decimal": "."
									 
								   }
				});
				
				// Add event listener for opening and closing details
				$('#ventas tbody').on('click', 'td.details-control', function () {
					var tr = $(this).parents('tr');
					var row = table.row( tr );
					var td = tr.children('td');
			 
					if ( row.child.isShown() ) {
						// This row is already open - close it
						row.child.hide();
						tr.removeClass('shown');
					}
					else {
						// Open this row
						row.child( format( td.eq(1).text()) ).show();
						tr.addClass('shown');
					}
				} );
		   
	  });

	  function saveData(parUno, parDos, parTres){

        $.post("?mod_planillas/view/component/tbl_confirmacion",{"periodo":parUno, "type":parDos, "anio":parTres},function(data){
	    var dialog=createNewDialog("Procesar",data,430);
	   
	    var n = $('#'+dialog);
	    n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
		
	    $("#cancelar").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		
		$("#procesar").click(function(){
			 $.post("?mod_planillas/asesores/view/pl_graba_datos",$("#frm_tbl_config").serializeArray(), function(data){
			 alert(data.mensaje);
			 
			 $("#"+dialog).remove();
			  window.location.href="?mod_planillas/asesores/pl_genera_calculos&noview"
							
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
	
<?php	
	} ?>

<div id="principal">
   <?php
    if ((isset($_REQUEST['periodo']) && isset($_REQUEST['type']))){ 
	 
   ?>
   
   <div id="canvas" class="fsPage" style="width:98%">
     
     <button id="refresh" class="greenButton" style="float:right" onclick="saveData('<?=$_REQUEST['periodo']?>', '<?=$_REQUEST['type']?>', '<?=$_REQUEST['anio']?>' )">Grabar Datos</button>
     <br/>
	<table border="0" class="display" id="ventas" width="100%" style="font-size:12px;width:100%">
      <thead>
        <tr>
          <th width="1%"></th>
          <th width="7%">No. Asesor</th>
          <th width="19%">Nombre </th>
          <th width="17%">Apellidos</th>
          <th width="7%">Grupo</th>
          <th width="8%">Contratos</th>
          <th width="11%">Comision Total</th>
          <th width="9%">Diferido 1</th>
          <th width="10%">Diferido 2</th>
          <th width="11%">Diferido 3</th>
        </tr>
      </thead>
      <tbody>
 
      </tbody>
    </table>
    
   </div>
   <?php } ?>   
</div>


<div id="content_dialog" >
  <!-- Este DIV lo usan las ventanas emergentes -->
</div>

