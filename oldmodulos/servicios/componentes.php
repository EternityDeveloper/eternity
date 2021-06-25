<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

 //print_r($_SERVER['DOCUMENT_ROOT']);
	/* AGREGAR UN COMPONENTE NUEVO */
	if (isset($_REQUEST['submit_component_add'])){
		if ($_REQUEST['submit_component_add']=="1"){
			
			$obj= new ObjectSQL();
			$obj->push($_POST);
			
			$retur=array("mensaje"=>"No se pudo completar la operacion!","error"=>true,"id"=>$obj->id_componente);
			
			$SQL="SELECT count(*) as total FROM `componentes` WHERE `id_componente`='".$obj->id_componente."'";
			$rs=mysql_query($SQL);
			$row=mysql_fetch_assoc($rs);
			
			if ($row['total']==0){
				/*ELIMINO LOS OBJECTOS QUE NO NECESITO*/
				unset($obj->submit_component_add);
				unset($obj->PHPSESSID); 
				$SQL=$obj->getSQL("insert","componentes");
			//	print_r($obj);
				mysql_query($SQL);
			
				$retur['mensaje']="Registro agregado correctamente!";
				$retur['error']=false;
				$retur['id']=$obj->id_componente;		
				
			}else{
				$retur['mensaje']="Error el componente existe!";
				$retur['error']=true;
				$retur['id']=0;	
			}
			
			
			//array("mensaje"=>,"error"=>false,"id"=>$obj->id_componente);
			echo json_encode($retur);
			exit;
		}
	}
 
 
 
	/* AGREGAR UNA IMAGEN A UN COMPONENTE */
	if (isset($_REQUEST['submit_component_upload_image'])){
		if ($_REQUEST['submit_component_upload_image']=="1"){
			SystemHtml::getInstance()->includeClass("servicios","Component");
			//print_r($_FILES);
			$comp= new Component($protect->getDBLink());
			$retur=$comp->uploadImage("imagen_upload",$_REQUEST['id']);
		//	$retur=array("mensaje"=>"Registro agregado correctamente!","error"=>false);
			echo json_encode($retur);
			exit;
		}
	} 

	/* AGREGAR UN COMPONENTE NUEVO */
	if (isset($_REQUEST['submit_component_edit'])){
		if ($_REQUEST['submit_component_edit']=="1"){
			
			$obj= new ObjectSQL();
			//$obj->push($_REQUEST); 
			$obj->descripcion_comp=$_REQUEST['descripcion_comp'];
			$obj->costos_comp=$_REQUEST['costos_comp'];
			$obj->cta_contable_comp=$_REQUEST['cta_contable_comp'];
			$obj->precio_venta_comp=$_REQUEST['precio_venta_comp'];
 			
			$retur=array("mensaje"=>"No se pudo completar la operacion!","error"=>true,"id"=>$_REQUEST['id_componente'] );
			
			$SQL="SELECT count(*) as total FROM `componentes` WHERE `id_componente`='".$_REQUEST['id_componente']."'";
			 
			$rs=mysql_query($SQL);
			$row=mysql_fetch_assoc($rs);
			
			if ($row['total']==1){
				/*ELIMINO LOS OBJECTOS QUE NO NECESITO*/
				unset($obj->submit_component_edit);
				unset($obj->PHPSESSID); 
				unset($obj->sub_componente_list_length);
				
				$SQL=$obj->getSQL("update","componentes"," where id_componente='". mysql_escape_string($_REQUEST['id_componente']) ."'");
				 
				mysql_query($SQL);
			
				$retur['mensaje']="Registro actualizado correctamente!";
				$retur['error']=false;
				$retur['id']=$obj->id_componente;		
				
			}else{
				$retur['mensaje']="Error el componente existe!";
				$retur['error']=true;
				$retur['id']=0;	
			}
			
			
			//array("mensaje"=>,"error"=>false,"id"=>$obj->id_componente);
			echo json_encode($retur);
			exit;
		}
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


function agregarDialogComp(){
	$('#page_loading').showLoading({'addClass': 'loading-indicator-bars'});	
	$.post("./?mod_servicios/component/componentes_add",function(data){
		$('#page_loading').hideLoading();	
		var dialog=createNewDialog("Agregar Componente",data);
		 
		 $("#form_user_edit").validate({
				rules: {
					id_componente: {
						required: true 
					},
					descripcion_comp: {
						required: true 
					},
					costos_comp: {
						required: true ,
						number: true
					},
					precio_venta_comp: {
						required: true,
						number: true
					},
					imagen_upload: {
						accept: "jpg,png,jpeg,gif"
					}
				},
				messages : {
					id_componente : {
						required: "Este campo es obligatorio"
					},
					descripcion_comp : {
						required: "Este campo es obligatorio" 
					},
					costos_comp : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					} ,
					precio_venta_comp : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},
					imagen_upload: "El archivo debe de ser una imagen"
					
				}
			});
			
			
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		 
		$("#bt_save").click(function(){
		  if ($("#form_user_edit").valid()){
			$('#page_loading').showLoading({'addClass': 'loading-indicator-bars'});	
			$.post("./?mod_servicios/componentes",$("#form_user_edit").serializeArray(),function(info){
			//	alert(info.id);
				if ($("#imagen_upload").val()!="" && (!info.error)){
					$.ajaxFileUpload
					(
						{
							url:'./?mod_servicios/componentes',
							secureuri:false,
							fileElementId:'imagen_upload',
							dataType: 'json',
							data:{submit_component_upload_image:'1', id:info.id},
							success: function (data, status)
							{
								
								$('#page_loading').hideLoading();	
								if (info.error){
									alert(info.mensaje);	
								}else{
									alert(info.mensaje);
									window.location.reload();	
								}
							},
							error: function (data, status, e)
							{
								$('#page_loading').hideLoading();
								alert(e);
							}
						}
					)
				}else{
					$('#page_loading').hideLoading();	
					if (info.error){
						alert(info.mensaje);	
					}else{
						alert(info.mensaje);
						window.location.reload();	
					}
				}				
			},"json");
			
			
		  }
		});
		
		
	});
	
}


var component_dialog;
function openDialogEditComponent(id){
	$('#page_loading').showLoading({'addClass': 'loading-indicator-bars'});	
	$.post("./?mod_servicios/component/componentes_edit",{'id':id},function(data){
		$('#page_loading').hideLoading();	
		var dialog=createNewDialog("Editar Componente",data,900);
		component_dialog=dialog;
		 
		$("#form_user_edit").validate({
				rules: {
					id_componente: {
						required: true 
					},
					descripcion_comp: {
						required: true 
					},
					costos_comp: {
						required: true ,
						number: true
					},
					precio_venta_comp: {
						required: true,
						number: true
					},
					imagen_upload: {
						accept: "jpg,png,jpeg,gif"
					}
				},
				messages : {
					id_componente : {
						required: "Este campo es obligatorio"
					},
					descripcion_comp : {
						required: "Este campo es obligatorio" 
					},
					costos_comp : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					} ,
					precio_venta_comp : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},
					imagen_upload: "El archivo debe de ser una imagen"
					
				}
			});
			
			
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		
		$("#sub_pbutton").click(function(){
			createDialogSubComponent(id);
		});
		 
		$("#bt_save").click(function(){
		  if ($("#form_user_edit").valid()){
			$('#page_loading').showLoading({'addClass': 'loading-indicator-bars'});	
			$.post("./?mod_servicios/componentes",$("#form_user_edit").serializeArray(),function(info){
 
 				if ($("#imagen_upload").val()!="" && (!info.error)){
					$.ajaxFileUpload
					(
						{
							url:'./?mod_servicios/componentes',
							secureuri:false,
							fileElementId:'imagen_upload',
							dataType: 'json',
							data:{submit_component_upload_image:'1', id:info.id},
							success: function (data, status)
							{
								
								$('#page_loading').hideLoading();	
								if (info.error){
									alert(info.mensaje);	
								}else{
									alert(info.mensaje);
									window.location.reload();	
								}
							},
							error: function (data, status, e)
							{
								$('#page_loading').hideLoading();
								alert(e);
							}
						}
					)
				}else{
					$('#page_loading').hideLoading();	
					if (info.error){
						alert(info.mensaje);	
					}else{
						alert(info.mensaje);
						window.location.reload();	
					}
				}				
			},"json");
			
			
		  }
		});
		
		/*CREO LA TABLA DEL SUB COMPONENTE*/
		createTable("sub_componente_list",{
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
		 
		
	});
	
}


/*ABRIR EL DIALOG DE CREAR SUBCOMPONENTES*/
function createDialogSubComponent(id){
	$('#page_loading').showLoading({'addClass': 'loading-indicator-bars'});	
	$.post("./?mod_servicios/component/subcomponent_add",{'id':id},function(data){
		$('#page_loading').hideLoading();	
		var dialog=createNewDialog("Agregar Sub-Componente",data);
		 
		 $("#form_add_subcomponent").validate({
				rules: {
					sub_subcomponente: {
						required: true 
					},
					sub_descripcion: {
						required: true 
					},
					sub_costos: {
						required: true ,
						number: true
					},
					sub_precio_venta: {
						required: true,
						number: true
					},
					imagen_upload: {
						accept: "jpg,png,jpeg,gif"
					}
				},
				messages : {
					sub_subcomponente : {
						required: "Este campo es obligatorio"
					},
					sub_descripcion : {
						required: "Este campo es obligatorio" 
					},
					sub_costos : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					} ,
					sub_precio_venta : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},
					imagen_upload: "El archivo debe de ser una imagen"
					
				}
			});
			
			
		$("#sub_bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		 
		$("#sub_bt_save").click(function(){
		  if ($("#form_add_subcomponent").valid()){
			$('#page_loading').showLoading({'addClass': 'loading-indicator-bars'});	
			$.post("./?mod_servicios/subcomponent",$("#form_add_subcomponent").serializeArray(),function(info){
			//	alert(info.id);
				//alert(info.mensaje);
				if ($("#imagen_upload").val()!="" && (!info.error)){
					$.ajaxFileUpload
					(
						{
							url:'./?mod_servicios/subcomponent',
							secureuri:false,
							fileElementId:'imagen_upload',
							dataType: 'json',
							data:{submit_subcomponent_upload_image:'1', id:info.id},
							success: function (data, status)
							{
								
								$('#page_loading').hideLoading();	
								if (info.error){
									alert(info.mensaje);	
								}else{
									alert(info.mensaje);
									window.location.reload();	
								}
							},
							error: function (data, status, e)
							{
								$('#page_loading').hideLoading();
								alert(e);
							}
						}
					)
				}else{
					$('#page_loading').hideLoading();	
					if (info.error){
						alert(info.mensaje);	
					}else{
						alert(info.mensaje);
						window.location.reload();	
					}
				}				
			},"json");
			
			
		  }
		});
		
		
	});
	
}



/*AGREGAR SUBCOMPONENTES AL COMPONENTE*/
function openDialogEditSubComponent(id){
	$('#page_loading').showLoading({'addClass': 'loading-indicator-bars'});	
	$.post("./?mod_servicios/component/subcomponent_edit",{'id':id},function(data){
		$('#page_loading').hideLoading();	
		var dialog=createNewDialog("Editar SubComponente",data);
		 
		$("#form_sub_component_edit").validate({
				rules: { 
					sub_descripcion: {
						required: true 
					},
					sub_costos: {
						required: true ,
						number: true
					},
					sub_precio_venta: {
						required: true,
						number: true
					},
					imagen_upload: {
						accept: "jpg,png,jpeg,gif"
					}
				},
				messages : {
 
					sub_descripcion : {
						required: "Este campo es obligatorio" 
					},
					sub_costos : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					} ,
					sub_precio_venta : {
						required: "Este campo es obligatorio",
						number: "Este campo es numerico"
					},
					imagen_upload: "El archivo debe de ser una imagen"
					
				}
			});
			
		$("#bt_cancel_sub_c").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		 
		$("#bt_save_sub_c").click(function(){
		  if ($("#form_sub_component_edit").valid()){
			$('#page_loading').showLoading({'addClass': 'loading-indicator-bars'});	
			$.post("./?mod_servicios/subcomponent",$("#form_sub_component_edit").serializeArray(),function(info){
	 			 
				if ($("#imagen_upload_component").val()!="" && (!info.error)){
					$.ajaxFileUpload
					(
						{
							url:'./?mod_servicios/subcomponent',
							secureuri:false,
							fileElementId:'imagen_upload_component',
							dataType: 'json',
							data:{submit_subcomponent_upload_image:'1', id:info.id},
							success: function (data, status)
							{
								
								$('#page_loading').hideLoading();	
			 
								if (info.error){
									alert(info.mensaje);	
								}else{
									alert(info.mensaje);
									$("#"+dialog).dialog("destroy");
									$("#"+dialog).remove();
									$("#"+component_dialog).dialog("destroy");
									$("#"+component_dialog).remove();
									 
									openDialogEditComponent(info.id_component);
								}
							},
							error: function (data, status, e)
							{
								$('#page_loading').hideLoading();
								alert(e);
							}
						}
					)
				}else{
					$('#page_loading').hideLoading();	
					if (info.error){
						alert(info.mensaje);	
					}else{
						alert(info.mensaje);
						$("#"+dialog).dialog("destroy");
						$("#"+dialog).remove();
						
						$("#"+component_dialog).dialog("destroy");
						$("#"+component_dialog).remove();
 
						openDialogEditComponent(info.id_component);
		 
					}
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
</script>
<div id="page_loading" class="fsPage" style="width:99%;float:left">
<h2>Componentes</h2>
<button type="button" class="positive" name="pbutton"  id="pbutton" onclick="agregarDialogComp()" >
                        <img src="images/apply2.png" alt=""/> 
                        Agregar componente
</button>
	<table border="0" class="display" id="role_list" style="font-size:13px">
      <thead>
        <tr>
          <th>Imagen</th>
          <th>Descripcion</th>
          <th>Costo </th>
          <th>Cta Contable</th>
          <th>Precio Venta</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
<?php
$SQL="SELECT * FROM `componentes` ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
        <tr>
          <td><img src="images/servicios/<?php echo $row['imagen']?>" width="51" height="49"  /></td>
          <td height="25"><?php echo $row['descripcion_comp']?></td>
          <td><?php echo number_format($row['costos_comp'],2)?></td>
          <td><?php echo $row['cta_contable_comp'];?></td>
          <td align="center" ><?php echo number_format($row['precio_venta_comp'],2);?></td>
          <td align="center" ><a href="#" onclick="openDialogEditComponent('<?php echo $encriptID;?>')"><img src="images/clipboard_edit.png"  /></a></td>
        </tr>
        <?php 
}
 ?>
      </tbody>
  </table>
</div>
<div id="content_dialog" ></div>
<?php SystemHtml::getInstance()->addModule("footer");?>