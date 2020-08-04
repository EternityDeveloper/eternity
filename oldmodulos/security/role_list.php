<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	


/*CARGA LA VISTA GENERAL DEL USUARIO*/
if (isset($_REQUEST['general_settings'])){
	include("view/general/user_general.php");
	exit;
}  


	SystemHtml::getInstance()->addTagStyle("css/smoothness/jquery.ui.combogrid.css");
	SystemHtml::getInstance()->addTagScript("script/jquery.ui.combogrid-1.6.3.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	
	SystemHtml::getInstance()->addTagScript("script/Class.js");
 	SystemHtml::getInstance()->addTagScriptByModule("Class.UserSettings.js");

	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");
	
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.mouse.js");
	SystemHtml::getInstance()->addTagScript("script/ui//jquery.ui.draggable.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.position.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.resizable.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.button.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.dialog.js");
	
	
	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css");

	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	SystemHtml::getInstance()->addModule("main/topmenu");

?>
<style type="text/css" title="currentStyle">	
.dataTables_wrapper {
	position: relative;
	min-height: 102px;
	clear: both;
	_height: 302px;
	zoom: 1; /* Feeling sorry for IE */
}
.dataTables_length{
	width:300px;
}	
.sizeUser{
	width:800px;
}

.linksA{
	
}
.edit_user_role{
}


.dtPermisos{
	
}

.validdate
{
    background:url('images/apply2.png') no-repeat right center;
}
.invalidate{
	background:url('images/agt_action_fail1.png') no-repeat right center;
}
</style>

<script>
var UID="";
var orole_list;
var gobal_table;

$(document).ready(function(){
	orole_list=$("#role_list").dataTable({
							"bFilter": false,
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
							
											
	    $("#pbutton").click(function(){
			openDialogNewRole();
		});		
													
	
});


var _isCharge=false;
var _request=null;
function openUserRoleList(request){
	delete gobla_table;
	_request=request;
	$.post("index.php",{"mod_security/component/roles_user_list":"","request":request},function(data){
		$("#div_users_list").html(data);
		$("#div_users_list").show();
		var is_loading=false;
		gobla_table=$("#user_list").dataTable({
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
					 			
		$(".edit_user_role").click(function(){
	  							
		});	
		
		
	});
}

function editUserRole(id){
    var user = new UserSettings('content_dialog');
    user.showEditWindow(id);
	user.addListener('user_change',function(){
		openUserRoleList(_request);
	});
 
}

function openPermisosRoleList(request){
	delete gobla_table;
	$.post("index.php",{"mod_security/component/roles_permisos_list":"","request":request},function(data){
		 
		$("#div_users_list").html(data);
		$("#div_users_list").show();
		
		gobla_table=$(".dtPermisos").dataTable({
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
 
function openDialogNewRole(){
	$("#dialog-role").html('');
	$.post("index.php",{"mod_security/role_add":""},function(data){
		$("#dialog-role").html(data);
	//	$("#dialog-role").dialog();
		$("#dialog-role").dialog({
			modal: true,
			width:400
		});
		
		
		$("#bt_save").click(function(){
		//	alert($("#proyectForm").serializeArray());
			if ($("#roles").val()!=""){	
				$.post("index.php?mod_security/role_add",{"roles":$("#roles").val(),"submit":"true"},function(data){				
					/* si hay un error que emita la alerta*/
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);	
						//$("#dialog-role").dialog();
						window.location.reload();		
					}
				},"json");
			}else{
				alert('Debe de llenar el campo de nombre!');	
			}

		});				
		
	});
}
  /*Guarda los permisos asignados*/
function _saveConfig(obj){
//alert($(obj).prop('checked'));
	$.get("index.php?mod_security/component/roles_permisos_list",
				{"id":obj.id,"checked":$(obj).prop('checked'),"submit":"true"},function(data){
		//	alert(data);				
		/* si hay un error que emita la alerta*/
		if (data.error){
			alert(data.mensaje);	
		}else{
			alert(data.mensaje);		
		}
	},"json");
}
function _reload(){
  window.location.reload();
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


function openUserRole(id){
	UID=id;
	openUserRoleList(UID);
}
  
  
  
function openDialogNewUser(){
	
	$.post("./?mod_security/component/roles_user_add",{"group_id":UID},function(data){
		var dialog=createNewDialog("Agregar usuario",data);

		$("#nombre_empleado").combogrid({
			url: './?mod_estructurac/list_view2&empleados_sin_usuario=1', 
			colModel: [ 
					 {'columnName':'nombre','width':'60','label':'Nombre'}
					],
			select: function( event, ui ) {
				$("#nombre_empleado").val( ui.item.nombre );
				$("#id_empleado").val( ui.item.value ); 
				return false;
			}
		});
		
		
		//usuario
		$("#form_user_edit").validate({
				rules: {
					id_empleado: {
						required: true 
					},
					tipo_usuario: {
						required: true 
					},
					usuario: {
						required: true,
						minlength: 3
					},
					password: {
						required: true,
						minlength: 7
					},
					new_password: {
						required: true,
						minlength: 7
					}
					
				},
				messages : {
					id_empleado : {
						required: "Este campo es obligatorio"
					},
					tipo_usuario : {
						required: "Este campo es obligatorio"
					},
					usuario : {
						required: "Este campo es obligatorio",
						minlength: "Debes de digitar un minimo de 3 caracteres"	
					},
					password : {
						required: "Este campo es obligatorio",
						minlength: "Debes de digitar un minimo de 7 caracteres"	
					},
					new_password : {
						required: "Este campo es obligatorio",
						minlength: "Debes de digitar un minimo de 7 caracteres"	
					}
					
				}
			
			});
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		
		var valid_user=false;
		$("#usuario").change(function(){
			$.get("./?mod_security/component/roles_user_add",{'validate_user':1,'user':$("#usuario").val()},function(data){
			 	if (data.userExist==1){
					alert('Usuario esta en uso favor utilizar otro!');
					$("#usuario").val('');	
					valid_user=false;
				}else{
					valid_user=true;	
				} 
			},"json");
		});
		
		$("#bt_save").click(function(){
		  if ($("#form_user_edit").valid() && (valid_user)){
			$.post("./?mod_security/component/roles_user_add",$("#form_user_edit").serializeArray(),function(data){
			//	alert(data);
			 	if (data.error){
					alert(data.mensaje);	
				}else{
					alert(data.mensaje);	
					$("#"+dialog).dialog("destroy");
					$("#"+dialog).remove();
					openUserRoleList(UID); //carga el listado de roles	
				}
			},"json");
		  }
		});
		
		
	});
	
}
</script>
	<div class="fsPage">
<table width="100%" border="1">
  <tr>
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td valign="top"><h2>Listado de Funciones </h2></td>
    <td valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td valign="top">   
                      <button type="button" class="positive" name="pbutton"  id="pbutton" onclick="openDialogNewRole()" >
                        <img src="images/apply2.png" alt=""/> 
                        Agregar nuevo
                      </button></td>
    <td valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td width="400" valign="top">


	<table border="0" class="display" id="role_list" style="font-size:13px">
      <thead>
        <tr>
          <th>Role</th>
          <th>&nbsp;</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
<?php
$SQL="SELECT * FROM roles";
$rs=mysql_query($SQL);
if (!$rs) {
     die('consulta con error'.mysql_error().'query'.$SQL);
} 
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
?>
        <tr>
          <td><?php echo $row['Role']?></td>
          <td align="center" ><a href="#" onclick="openUserRole('<?php echo $encriptID?>')"><img src="images/view.png" width="27" height="28" /></a></td>
          <td align="center" ><a href="#" onclick="openPermisosRoleList('<?php echo $encriptID?>')"><img src="images/keychain_access.png" width="27" height="28" /></a></td>
        </tr>
        <?php  
}
 ?>
      </tbody>
    </table>
</td>
    </td>
  </table>  
</div>	
<div id="div_users_list" class="fsPage sizeUser" style="margin-top:0px;display:none">

</div>
<div id="dialog-role" title="Agregar role" style="display:block;background:#FFF">
<div id="content_dialog" ></div>

<?php SystemHtml::getInstance()->addModule("footer");?>
