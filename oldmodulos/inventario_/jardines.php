<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	



if (isset($_REQUEST['remove_jardin_activo'])){
	$retur=array("mensaje"=>"Jardin eliminado","error"=>false);

	$dencryt=System::getInstance()->getEncrypt()->decrypt($_REQUEST['jardin'],$protect->getSessionID());
	$data=json_decode($dencryt);
	
	$jardines = new ObjectSQL();
	$jardines->estatus=2;
	
	
	$SQL=$jardines->getSQL("update","jardines_activos"," where id_jardin='". mysql_escape_string($data->id_jardin) ."' and id_fases='". mysql_escape_string($data->id_fases) ."' and estatus=1 ");
 
 
	mysql_query($SQL);
	
	echo json_encode($retur);

	exit;	
}


if (isset($_REQUEST['submit_jardin_activo'])){
	
	$retur=array("mensaje"=>"Jardin agregado","error"=>false);

	$dencryt=System::getInstance()->getEncrypt()->decrypt($_REQUEST['jardin'],$protect->getSessionID());
	$data=json_decode($dencryt);
	$fase=System::getInstance()->getEncrypt()->decrypt($_REQUEST['fase'],$protect->getSessionID());
	
	$jardines = new ObjectSQL();
	$jardines->id_jardin=$data->id_jardin;
	$jardines->id_fases=$fase;
	$jardines->costo=$_REQUEST['costo'];
	$jardines->cta_contable=$_REQUEST['cta_contable'];
	$jardines->precio_venta=$_REQUEST['precio'];
	
	//$jardines->minimo_abono_capital=$_REQUEST['minimo_abono_capital'];
	//$jardines->monto_m_abono_capital=$_REQUEST['monto_minimo'];
	
	$jardines->estatus="1";
	$jardines->precio_venta_local_nec=System::getInstance()->Decrypt($_REQUEST['precio_venta_local_nec']);
	$jardines->precio_venta_dolares_nec=System::getInstance()->Decrypt($_REQUEST['precio_venta_dolares_nec']);
	$jardines->precio_venta_local_pre=System::getInstance()->Decrypt($_REQUEST['precio_venta_local_pre']);
	$jardines->precio_venta_dolares_pre=System::getInstance()->Decrypt($_REQUEST['precio_venta_dolares_pre']);
	
	$jardines->precio_osario_local_nec=System::getInstance()->Decrypt($_REQUEST['osario_precio_venta_local_nec']);
	$jardines->precio_osario_dolares_nec=System::getInstance()->Decrypt($_REQUEST['osario_precio_venta_dolares_nec']);
	$jardines->precio_osario_local_pre=System::getInstance()->Decrypt($_REQUEST['osario_precio_venta_local_pre']);
	$jardines->precio_osario_dolares_pre=System::getInstance()->Decrypt($_REQUEST['osario_precio_venta_dolares_pre']);
	
 	
	$SQL=$jardines->getSQL("insert","jardines_activos");
 	 
	mysql_query($SQL);
	
	echo json_encode($retur);
	exit;	
 
}

if (isset($_REQUEST['submit_jardin_activo_edit'])){
	$dencryt=System::getInstance()->getEncrypt()->decrypt($_REQUEST['jardin'],$protect->getSessionID());
	$data=json_decode($dencryt);
	
	
	$retur=array("mensaje"=>"Registro actualizado","error"=>false);

	$dencryt=System::getInstance()->getEncrypt()->decrypt($_REQUEST['jardin'],$protect->getSessionID());
	$data=json_decode($dencryt);
	$fase=System::getInstance()->getEncrypt()->decrypt($_REQUEST['fase'],$protect->getSessionID());
	
	$jardines = new ObjectSQL();
///	$jardines->id_jardin=$data->id_jardin;
//	$jardines->id_fases=$fase;
	$jardines->estatus=System::getInstance()->Decrypt($_REQUEST['estado']);
	$jardines->precio_venta_local_nec=System::getInstance()->Decrypt($_REQUEST['precio_venta_local_nec']);
	$jardines->precio_venta_dolares_nec=System::getInstance()->Decrypt($_REQUEST['precio_venta_dolares_nec']);
	$jardines->precio_venta_local_pre=System::getInstance()->Decrypt($_REQUEST['precio_venta_local_pre']);
	$jardines->precio_venta_dolares_pre=System::getInstance()->Decrypt($_REQUEST['precio_venta_dolares_pre']);
	
	$jardines->precio_osario_local_nec=System::getInstance()->Decrypt($_REQUEST['osario_precio_venta_local_nec']);
	$jardines->precio_osario_dolares_nec=System::getInstance()->Decrypt($_REQUEST['osario_precio_venta_dolares_nec']);
	$jardines->precio_osario_local_pre=System::getInstance()->Decrypt($_REQUEST['osario_precio_venta_local_pre']);
	$jardines->precio_osario_dolares_pre=System::getInstance()->Decrypt($_REQUEST['osario_precio_venta_dolares_pre']);	

	$jardines->minimo_abono_capital=$_REQUEST['minimo_abono_capital'];
	$jardines->monto_m_abono_capital=$_REQUEST['monto_minimo'];		
		
	$jardines->costo=$_REQUEST['costo'];
	$jardines->cta_contable=$_REQUEST['cta_contable'];
	$jardines->precio_venta=$_REQUEST['precio'];
	$SQL=$jardines->getSQL("update","jardines_activos"," where id_jardin='". mysql_escape_string($data->id_jardin) ."' and id_fases='". mysql_escape_string($data->id_fases) ."'");
 	
 
//	print_r($SQL);
	mysql_query($SQL);
	
	echo json_encode($retur);
	exit;	
 
}


 
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

$(document).ready(function(){
	gobal_table=$("#role_list").dataTable({
							"bFilter": true,
							"bInfo": false,
							"bPaginate": true,
							  "oLanguage": {
									"sLengthMenu": "Mostrar _MENU_ registros por pagina",
									"sZeroRecords": "No se ha encontrado - lo siento",
									"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
									"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
									"sInfoFiltered": "(filtrado de _MAX_ total registros)",
									"sSearch":"Buscar"
								}
							});
							
	//$('<button id="refresh">Buscar</button>').appendTo('div.dataTables_filter');
	
	//$('<button id="crear">Agregar lote</button>').appendTo('div.dataTables_filter');
	
	
	
});


function addNewJardines(){
	$('#jardines_page').showLoading({'addClass': 'loading-indicator-bars'});	
	$.post("./?mod_inventario/component/jardin_activo_add",function(data){
		$('#jardines_page').hideLoading();	
		var dialog=createNewDialog("Agregar Jardines Activos",data);
		 
		 $("#form_user_edit").validate({
				rules: {
					jardin: {
						required: true 
					},
					fase: {
						required: true 
					},
					costo: {
						required: true,
						number: true
					},
					cta_contable: {
						required: true 
					},					
					precio: {
						required: true 
					}  
				},
				messages : {
					jardin : {
						required: "Este campo es obligatorio"
					},
					fase : {
						required: "Este campo es obligatorio"
					},
					costo : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},
					cta_contable : {
						required: "Este campo es obligatorio" 
					},
					precio : {
						required: "Este campo es obligatorio" 
					}  
				}
			
			});
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		
		$("#sbloque").change(function(){
		///	alert($(this).val());	
			switch($(this).val()){
				case "0":
					$("#bloques_1").hide();
					$("#bloques_2").show();	
				break;
				case "1":
					$("#bloques_1").show();
					$("#bloques_2").hide();	
				break;	
			}
		});


		$("#jardin").change(function(){
		 	$('#jardines_page').showLoading({'addClass': 'loading-indicator-bars'});	
			$.post("./?mod_inventario/component/jardin_activo_add",{"getListfase":"1","jardin":$(this).val()},function(data){
				$('#jardines_page').hideLoading();	
				$("#m_fase").html(data);
			},"text");
	 
		});
		
		$("#minimo_abono_capital").change(function(){
			   $("#monto_m_capital").hide();
			 if ($(this).val()=="MONTO"){
				 $("#monto_m_capital").show();
			}
		});
		
				
		$("#bt_save").click(function(){
			
		 	if ($("#form_user_edit").valid()){	
			 	$('#jardines_page').showLoading({'addClass': 'loading-indicator-bars'});
				$.post("./?mod_inventario/jardines",$("#form_user_edit").serializeArray(),function(data){
					$('#jardines_page').hideLoading();	
				//	alert(data);
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);
						window.location.reload();	
					}
				},"json");
			}
		 
		});
		
		
	});
	
}

function editJardines(value){
	$('#jardines_page').showLoading({'addClass': 'loading-indicator-bars'});	
	$.post("./?mod_inventario/component/jardin_activo_edit",{jardin:value},function(data){
		$('#jardines_page').hideLoading();	
		var dialog=createNewDialog("Agregar Jardines Activos",data);
		 
		 $("#form_user_edit").validate({
				rules: {
					jardin: {
						required: true 
					},
					fase: {
						required: true 
					},
					costo: {
						required: true,
						number: true
					},
					cta_contable: {
						required: true 
					},					
					precio: {
						required: true 
					}  
				},
				messages : {
					jardin : {
						required: "Este campo es obligatorio"
					},
					fase : {
						required: "Este campo es obligatorio"
					},
					costo : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},
					cta_contable : {
						required: "Este campo es obligatorio" 
					},
					precio : {
						required: "Este campo es obligatorio" 
					}  
				}
			
			});
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
 
 		$("#bt_remove").click(function(){
			var retur=confirm("Esta seguro de eliminar este jardin?");
			if (retur){
				$('#jardines_page').showLoading({'addClass': 'loading-indicator-bars'});
				$.post("./?mod_inventario/jardines",{"remove_jardin_activo":"1","jardin":$("#jardin").val()},function(data){
					$('#jardines_page').hideLoading();	
				//	alert(data);
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);
						window.location.reload();	
					} 
				},"json");
			}
		});
		
	 
		$("#minimo_abono_capital").change(function(){
			   $("#monto_m_capital").hide();
			 if ($(this).val()=="MONTO"){
				 $("#monto_m_capital").show();
			}
		});
		
		$("#bt_save").click(function(){
			
		 	if ($("#form_user_edit").valid()){	
			 	$('#jardines_page').showLoading({'addClass': 'loading-indicator-bars'});
				$.get("./?mod_inventario/jardines",$("#form_user_edit").serializeArray(),function(data){
					$('#jardines_page').hideLoading();	
				//	alert(data);
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);
						window.location.reload();	
					} 
				},"json");
			}
		 
		});
		
		
	});
	
}
function createDiv(){
	var rand="Dialog_"+Math.floor(Math.random() * (1000 - 1 + 1) + 1);
	$("#content_dialog").append("<div id=\""+rand+"\"></div>");
	return rand;
}
function createNewDialog(title,data){
	var rand=createDiv();
	$("#"+rand).attr("title",title);
	$("#"+rand).html(data);
	$("#"+rand).dialog({
		modal: true,
		width:500,
		close: function (ev, ui) {
			$(this).dialog("destroy");
			$(this).remove();

		}
	});	
	return rand;
}
</script>
<style type="text/css" title="currentStyle">
.fsPage{
	width:100%;
	padding-left:0px;
	margin-left:3px;
}
</style>

<div class="fsPage" id="jardines_page">
 
    <table width="100%" border="0">
      <tr>
        <td width="150%"><h2>Jardines</h2> </td>
      </tr>
 
      <tr>
        <td><button type="button" class="positive" name="pbutton"  id="pbutton" onClick="addNewJardines()" >
                        <img src="images/apply2.png" alt=""/>Agregar jardines activos</button>
                                     
                        </td>
      </tr>
      <tr>
        <td><table border="0" class="display" id="role_list" style="font-size:13px">
          <thead>
            <tr>
              <th>Jardin</th>
              <th>Fase </th>
              <th>Estatus</th>
              <th>&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            <?php
$SQL="SELECT 
*,
(SELECT COUNT(*) FROM `inventario_jardines` WHERE 
inventario_jardines.`id_fases`=jardines_activos.id_fases 
	AND inventario_jardines.`id_jardin`=jardines_activos.id_jardin) AS total
	
 FROM `jardines_activos` 
INNER JOIN jardines ON (jardines.`id_jardin`=jardines_activos.`id_jardin`)
INNER JOIN `fases` ON (fases.`id_fases`=jardines_activos.`id_fases`)
INNER JOIN `sys_status` ON (sys_status.`id_status`=jardines_activos.`estatus`)
where jardines_activos.estatus='1'
 ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
?>
            <tr>
              <td height="25"><?php echo $row['jardin']?></td>
              <td><?php echo $row['fase']?></td>
              <td><?php echo $row['descripcion'];?></td>
              <td align="center" ><a href="#" onClick="editJardines('<?php echo $encriptID;?>')"><img src="images/clipboard_edit.png"  /></a></td>
            </tr>
            <?php 
}
 ?>
          </tbody>
        </table></td>
      </tr>
    </table>
 
</div>

<div id="content_dialog" ></div>

<?php SystemHtml::getInstance()->addModule("footer");?>