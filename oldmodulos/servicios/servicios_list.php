<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

if (isset($_REQUEST['view_simple_servicio'])){
	include("view/simple_servicios_list.php");
	exit;
}

 	/* AGREGAR UN SERVICIO NUEVO */
	if (isset($_REQUEST['submit_services'])){
		if ($_REQUEST['submit_services']=="1"){
			
			$obj= new ObjectSQL();
			$obj->EM_ID=System::getInstance()->Decrypt($_REQUEST['EM_ID']);
			$obj->serv_codigo=$_REQUEST['serv_codigo'];
			$obj->serv_descripcion=$_REQUEST['serv_descripcion'];
			$obj->serv_costo=$_REQUEST['serv_costo'];
			$obj->serv_cta_contable=$_REQUEST['serv_cta_contable'];
			$obj->serv_promocion=$_REQUEST['serv_promocion'];
			$obj->serv_precio_venta_local_pre=System::getInstance()->Decrypt($_REQUEST['serv_precio_venta_local_pre']);
			$obj->serv_precio_venta_dolares_pre=System::getInstance()->Decrypt($_REQUEST['serv_precio_venta_dolares_pre']);
			$obj->serv_precio_venta_local_nec=System::getInstance()->Decrypt($_REQUEST['serv_precio_venta_local_nec']);
			$obj->serv_precio_venta_dolares_nec=System::getInstance()->Decrypt($_REQUEST['serv_precio_venta_dolares_nec']);
					
			 				
			$retur=array("mensaje"=>"No se pudo completar la operacion!","error"=>true,"id"=>"");
			
			$SQL="SELECT count(*) as total FROM `servicios` WHERE `serv_codigo`='".$obj->serv_codigo."'";
			$rs=mysql_query($SQL);
			$row=mysql_fetch_assoc($rs);
			
			if ($row['total']==0){
				/*ELIMINO LOS OBJECTOS QUE NO NECESITO*/
				unset($obj->submit_services);
				unset($obj->PHPSESSID); 
				$SQL=$obj->getSQL("insert","servicios");
			//	print_r($obj);
				mysql_query($SQL);
			
				$retur['mensaje']="Registro agregado correctamente!";
				$retur['error']=false;
				$retur['id']=System::getInstance()->getEncrypt()->encrypt($obj->serv_codigo,$protect->getSessionID());		
				
			}else{
				$retur['mensaje']="Error el componente existe!";
				$retur['error']=true;
			//	$retur['id']=;	
			}
			
			
			//array("mensaje"=>,"error"=>false,"id"=>$obj->id_componente);
			echo json_encode($retur);
			exit;
		}
	}

 	/* AGREGAR UN SERVICIO NUEVO */
	if (isset($_REQUEST['submit_services_edit'])){
		if ($_REQUEST['submit_services_edit']=="1"){
			
			$obj= new ObjectSQL();
			$obj->EM_ID=System::getInstance()->Decrypt($_REQUEST['EM_ID']);
			$obj->serv_descripcion=$_REQUEST['serv_descripcion'];
			$obj->serv_costo=$_REQUEST['serv_costo'];
			$obj->serv_cta_contable=$_REQUEST['serv_cta_contable'];
			$obj->serv_promocion=$_REQUEST['serv_promocion'];
			$obj->serv_precio_venta_local_pre=System::getInstance()->Decrypt($_REQUEST['serv_precio_venta_local_pre']);
			$obj->serv_precio_venta_dolares_pre=System::getInstance()->Decrypt($_REQUEST['serv_precio_venta_dolares_pre']);
			$obj->serv_precio_venta_local_nec=System::getInstance()->Decrypt($_REQUEST['serv_precio_venta_local_nec']);
			$obj->serv_precio_venta_dolares_nec=System::getInstance()->Decrypt($_REQUEST['serv_precio_venta_dolares_nec']);
		//	$obj->serv_precio_venta_dolares=$_REQUEST['serv_precio_venta_dolares'];
 			
	 
			$servID=$_REQUEST['serv_codigo'];
			
			$retur=array("mensaje"=>"No se pudo completar la operacion!","error"=>true,"id"=>"");
 
			$SQL=$obj->getSQL("update","servicios"," where serv_codigo='".$servID."'");
	 
			mysql_query($SQL);
		
			$retur['mensaje']="Registro actualizado!";
			$retur['error']=false;
			$retur['id']=System::getInstance()->getEncrypt()->encrypt($obj->serv_codigo,$protect->getSessionID());		
			

			
			//array("mensaje"=>,"error"=>false,"id"=>$obj->id_componente);
			echo json_encode($retur);
			exit;
		}
	}
	
	
 	/* REMUEVE UN COMPONENTE DE UN SERVICIO */
	if (isset($_REQUEST['remove_componet_submit'])){
		if ($_REQUEST['remove_componet_submit']=="1"){

			$retur=array("mensaje"=>"No se pudo completar la operacion!","error"=>true,"id"=>"");

			$data=json_decode(System::getInstance()->Request("id"));
		//	print_r($data);
			//$obj= new ObjectSQL();
			
		//	if ($data->sub_subcomponente=="0"){
				
 
			$SQL="DELETE FROM componentes_servicio where 
				id_componente='". mysql_escape_string($data->id_componente) ."' and
				sub_subcomponente='". mysql_escape_string($data->sub_subcomponente) ."' AND
				serv_codigo='". mysql_escape_string($data->serv_codigo) ."' ";

			mysql_query($SQL);
		
			
			//}
			$retur['mensaje']="Registro Removido!";
			$retur['error']=false;

			echo json_encode($retur);	
			
			exit;
		}
	}
	
	
  	/* LISTADO DE COMPONENTES */
	if (isset($_REQUEST['list_component_submit'])){
		$retur=array("mensaje"=>"No se pudo completar la operacion!","error"=>true);
		
		if ($_REQUEST['list_component_submit']=="1"){
			$serv_codigo=System::getInstance()->Request("serv_codigo");
			
			$retur['mensaje']="Registro agregado correctamente!";
			$retur['error']=false;
			
			/* INSERTANTO LOS COMPONENTES */
			if (isset($_REQUEST['id_component'])){
				
				foreach($_REQUEST['id_component'] as $key =>$val){
					$id_componente=System::getInstance()->Decrypt($val);
					$obj= new ObjectSQL();
					$obj->serv_codigo=$serv_codigo;
					$obj->id_componente=$id_componente;
					$obj->sub_subcomponente=0;
					$SQL=$obj->getSQL("insert","componentes_servicio");
			 		mysql_query($SQL);
				}
				
			}
			
			
			if (isset($_REQUEST['id_subcomponent'])){
				 
				foreach($_REQUEST['id_subcomponent'] as $key =>$val){	
					$subcomponent=json_decode(System::getInstance()->Decrypt($val));
					$obj= new ObjectSQL();
					$obj->serv_codigo=$serv_codigo;
					$obj->id_componente=$subcomponent->id_componente;
					$obj->sub_subcomponente=$subcomponent->sub_subcomponente;
					$SQL=$obj->getSQL("insert","componentes_servicio");
			 		mysql_query($SQL);
				//	print_r($SQL);				
				}
			}
			 
		}
	
		echo json_encode($retur);
		exit;		
	}

	/*QUERY PARA BUSQUEDA */
	if (isset($_REQUEST['dt_list'])){
		$QUERY="";

		$SQL=" SELECT count(*) as total FROM servicios WHERE 1=1   ";
		$SQL.=$QUERY;
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		$total_row=$row['total'];
		
		
		$SQL="SELECT * FROM `servicios`  WHERE 1=1  ";
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
				$row['bt_editar']='<a href="#" class="servicios_edit" id="'.$encriptID.'" ><img src="images/plus.png"  /></a>';
			 	
				array_push($data['aaData'],$row);
			}
			
			echo json_encode($data);
			
			exit;
	}
	/*CONFIGURACION GENERAL DE LA PAGINA*/ 
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

 	SystemHtml::getInstance()->addTagScript("script/Class.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.blockUI.js");
 
 	SystemHtml::getInstance()->addTagScript("script/Class.AdicionarComponente.js");
 
	SystemHtml::getInstance()->addTagStyle("css/showLoading.css");


	SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.hotkeys.js");
 

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


function openDialogNewService(){
	$('#loading_page').showLoading({'addClass': 'loading-indicator-bars'});	
	$.post("./?mod_servicios/component/servicios_add",function(data){
		$('#loading_page').hideLoading();	
		var dialog=createNewDialog("Agregar Servicios",data);
		 
		 $("#form_user_edit").validate({
				rules: {
					serv_codigo: {
						required: true 
					},
					serv_descripcion: {
						required: true 
					},
					serv_costo: {
						required: true ,
						number: true
					},
					serv_precio_venta: {
						required: true,
						number: true
					}  
					
				},
				messages : {
					serv_codigo : {
						required: "Este campo es obligatorio"
					},
					serv_descripcion : {
						required: "Este campo es obligatorio"
					},
					serv_costo : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},
					serv_precio_venta : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					}  
					
				}
			
			});
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		 
		$("#bt_save").click(function(){
		  if ($("#form_user_edit").valid()){
		    $('#loading_page').showLoading({'addClass': 'loading-indicator-bars'});	
			$.post("./?mod_servicios/servicios_list",$("#form_user_edit").serializeArray(),function(data){
			//	alert(data.id);
				$('#loading_page').hideLoading();	
			 	if (data.error){
					alert(data.mensaje);	
				}else{
					alert(data.mensaje);
					$("#"+dialog).dialog("destroy");
					$("#"+dialog).remove();
					openDialogEditService(data.id);
					//window.location.reload();	
				}
			},"json");
		  }
		});
		
		
	});
	
}

var dialog_edit_serv;
var servicio_id;
function openDialogEditService(id){
	servicio_id=id;
	//$('#loading_page').showLoading({'addClass': 'loading-indicator-bars'});	
	var Posting = new Class({initialize : function(){}});
	var ps= new Posting();
	ps.post("./?mod_servicios/component/servicios_edit",{"id":id},function(data){
		//$('#loading_page').hideLoading();	
		dialog_edit_serv=createNewDialog("Editar Servicios",data,850);
		 
		createTable("servicios_table",
			{
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
						}		 
		 );
		 
		$("#adicionar_componente").click(function(){
			  var componente= new AdicionarComponente(id);
			  componente.addListener("SubmitComponenteAdicional",function(data){
				  $('#loading_page').showLoading({'addClass': 'loading-indicator-bars'});	
				  
				  $.post("./?mod_servicios/servicios_list",data,function(data){
					//  alert(data);
						if (data.error){
							alert(data.mensaje);	
						}else{
							alert(data.mensaje);
							componente.closeView();
							$("#"+dialog_edit_serv).dialog("destroy");
							$("#"+dialog_edit_serv).remove();
							openDialogEditService(id);
						}
					  $('#loading_page').hideLoading();	
				  },"json");
				 
				  
			  }); 
			  componente.create();
		 });		 
		 
		 
		$("#form_user_edit").validate({
				rules: {
					serv_codigo: {
						required: true 
					},
					serv_descripcion: {
						required: true 
					},
					serv_costo: {
						required: true ,
						number: true
					},
					serv_precio_venta: {
						required: true,
						number: true
					}  
					
				},
				messages : {
					serv_codigo : {
						required: "Este campo es obligatorio"
					},
					serv_descripcion : {
						required: "Este campo es obligatorio"
					},
					serv_costo : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},
					serv_precio_venta : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					}  
					
				}
			
			});
		$("#bt_cancel").click(function(){
			$("#"+dialog_edit_serv).dialog("destroy");
			$("#"+dialog_edit_serv).remove();
		});
		 
		$("#bt_save").click(function(){
		  if ($("#form_user_edit").valid()){
			// $('#loading_page').showLoading({'addClass': 'loading-indicator-bars'});	
			ps.post("./?mod_servicios/servicios_list",$("#form_user_edit").serializeArray(),function(data){
			//	alert(data.id);
				//$('#loading_page').hideLoading();	
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

function removeComponent(id){
 
	var conf=confirm("Desea eliminar este componente?");
	if (conf){
		$('#loading_page').showLoading({'addClass': 'loading-indicator-bars'});	
		$.post("./?mod_servicios/servicios_list",{remove_componet_submit:1,"id":id},function(data){
		//	alert(data);
			$('#loading_page').hideLoading();	
			if (data.error){
				alert(data.mensaje);	
			}else{
				alert(data.mensaje);
				$("#"+dialog_edit_serv).dialog("destroy");
				$("#"+dialog_edit_serv).remove();
				openDialogEditService(servicio_id); 
			}
		},"json");
	}
}
</script>
<div id="loading_page" class="fsPage" style="width:98%">
<h2>Servicios</h2>
<button type="button" class="positive" name="pbutton"  id="pbutton" onclick="openDialogNewService()" >
                        <img src="images/apply2.png" alt=""/> 
                        Agregar servicio
</button>
 

	<table width="100%" border="0" class="display" id="role_list" style="font-size:13px">
      <thead>
        <tr>
          <th>Descripcion</th>
          <th>Costo </th>
          <th>Cta Contable</th>
          <th>Precio Venta Local Necesidad</th>
          <th>Precio Venta Dolar Necesidad</th>
          <th>Precio Venta Local Pre-necesidad</th>
          <th>Precio Venta Dolar Pre-necesidad</th>
          <th>Promocion </th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
<?php
$SQL="SELECT * FROM `servicios` ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['serv_codigo'],$protect->getSessionID());
?>
        <tr>
          <td height="25"><?php echo $row['serv_descripcion']?></td>
          <td><?php echo $row['serv_costo']?></td>
          <td><?php echo $row['serv_cta_contable'];?></td>
          <td align="center" ><?php echo $row['serv_precio_venta_local_nec'];?></td>
          <td align="center" ><?php echo $row['serv_precio_venta_dolares_nec'];?></td>
          <td align="center" ><?php echo $row['serv_precio_venta_local_pre'];?></td>
          <td align="center" ><?php echo $row['serv_precio_venta_dolares_pre'];?></td>
          <td align="left" ><?php echo $row['serv_promocion']?></td>
          <td align="center" ><a href="#" onclick="openDialogEditService('<?php echo $encriptID;?>')"><img src="images/clipboard_edit.png"  /></a></td>
        </tr>
        <?php 
}
 ?>
      </tbody>
  </table>
</div>
<div id="content_dialog" ></div>
<?php SystemHtml::getInstance()->addModule("footer");?>