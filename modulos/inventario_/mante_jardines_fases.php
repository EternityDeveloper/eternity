<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	



if (isset($_REQUEST['submit_nomenclatura_fase'])){
	
	$retur=array("mensaje"=>"Nomenclatura agregada","error"=>false);
 	
	if ($_REQUEST['edit']=="0"){
		$jardines = new ObjectSQL();
		$jardines->id_fases=$_REQUEST['id_fases'];
		$jardines->fase=$_REQUEST['fase'];
		$jardines->estatus=System::getInstance()->Decrypt($_REQUEST['estado']);
		$SQL=$jardines->getSQL("insert","fases");
		mysql_query($SQL);
		
	}else if ($_REQUEST['edit']=="1"){
		$retur['mensaje']="Nomenclatura actualizada";
		
		$fase = new ObjectSQL();
	//	$jardines->id_fases=$_REQUEST['id_fases'];
		$fase->fase=$_REQUEST['fase'];
		$fase->estatus=System::getInstance()->Decrypt($_REQUEST['estado']);
		$SQL=$fase->getSQL("update","fases"," where id_fases='".$_REQUEST['id_fases']."'");
		mysql_query($SQL);
 
	}
	echo json_encode($retur);
	
	exit;	
 
}


if (isset($_REQUEST['submit_nomenclatura_jardin'])){
	
	$retur=array("mensaje"=>"Nomenclatura agregada","error"=>false);
 	
	if ($_REQUEST['edit']=="0"){
		$jardines = new ObjectSQL();
		$jardines->id_jardin=$_REQUEST['id_jardin_code'];
		$jardines->jardin=$_REQUEST['jardin'];
		$jardines->estatus=System::getInstance()->Decrypt($_REQUEST['estado']);
		$SQL=$jardines->getSQL("insert","jardines");
		mysql_query($SQL);
		
		//echo json_encode($retur);
	}else if ($_REQUEST['edit']=="1"){
		$retur['mensaje']="Nomenclatura actualizada";
		
		$jardines = new ObjectSQL();
		//$jardines->id_jardin=$_REQUEST['id_jardin'];
		$jardines->jardin=$_REQUEST['jardin'];
		$jardines->estatus=System::getInstance()->Decrypt($_REQUEST['estado']);
		$SQL=$jardines->getSQL("update","jardines"," where id_jardin='".$_REQUEST['id_jardin']."'");
		mysql_query($SQL);

		
	}
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
							
							
	$("#fase_list").dataTable({
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

function addNFase(){
	$('#jardines_page').showLoading({'addClass': 'loading-indicator-bars'});	
	$.post("./?mod_inventario/component/nomenclatura_fase",{'edit':"0"},function(data){
		$('#jardines_page').hideLoading();	
		var dialog=createNewDialog("Agregar Nomenclatura",data);
		 
		 $("#form_user_edit").validate({
				rules: {
					fase: {
						required: true ,
						minlength: 2
					} ,
					id_fases: {
						required: true ,
						minlength: 2
					},
					estado :  {
						required: true 
					}  
				},
				messages : {
					fase : {
						required: "Este campo es obligatorio",
						minlength: "Debes de digitar un minimo de 2 caracteres"
					},
					id_fases : {
						required: "Este campo es obligatorio",
						minlength: "Debes de digitar un minimo de 2 caracteres"
					} ,
					estado :  {
						required: "Este campo es obligatorio",
					}
					 
				}
			
			});
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
 
		$("#bt_save").click(function(){
			
		 	if ($("#form_user_edit").valid()){	
			 	$('#jardines_page').showLoading({'addClass': 'loading-indicator-bars'});
				$.post("./?mod_inventario/mante_jardines_fases",$("#form_user_edit").serializeArray(),function(data){
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
function editNFase(id){
	$('#jardines_page').showLoading({'addClass': 'loading-indicator-bars'});	
	$.post("./?mod_inventario/component/nomenclatura_fase",{'edit':"1","id":id},function(data){
		$('#jardines_page').hideLoading();	
		var dialog=createNewDialog("Agregar Nomenclatura",data);
		 
		 $("#form_user_edit").validate({
				rules: {
					jardin: {
						required: true ,
						minlength: 2
					}  
				},
				messages : {
					jardin : {
						required: "Este campo es obligatorio",
						minlength: "Debes de digitar un minimo de 2 caracteres"
					}  ,
					estado : {
						required: "Este campo es obligatorio"
					}
				}
			
			});
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
 
		$("#bt_save").click(function(){
			
		 	if ($("#form_user_edit").valid()){	
			 	$('#jardines_page').showLoading({'addClass': 'loading-indicator-bars'});
				$.post("./?mod_inventario/mante_jardines_fases",$("#form_user_edit").serializeArray(),function(data){
					$('#jardines_page').hideLoading();	
					//alert(data);
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
function addNJardin(){
	$('#jardines_page').showLoading({'addClass': 'loading-indicator-bars'});	
	$.post("./?mod_inventario/component/nomenclatura_jardin",{'edit':"0"},function(data){
		$('#jardines_page').hideLoading();	
		var dialog=createNewDialog("Agregar Nomenclatura",data);
		 
		 $("#form_user_edit").validate({
				rules: {
					jardin: {
						required: true ,
						minlength: 2
					} ,
					id_jardin: {
						required: true ,
						minlength: 2
					} ,
					estado :  {
						required: true 
					},
				   id_jardin_code: {
						required: true ,
						minlength: 2
					}
				},
				messages : {
					jardin : {
						required: "Este campo es obligatorio",
						minlength: "Debes de digitar un minimo de 2 caracteres"
					},
					id_jardin : {
						required: "Este campo es obligatorio",
						minlength: "Debes de digitar un minimo de 2 caracteres"
					},
					estado :  {
						required: "Este campo es obligatorio"
					},
					id_jardin_code : {
						required: "Este campo es obligatorio",
						minlength: "Debes de digitar un minimo de 2 caracteres"
					}  
					 
				}
			
			});
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
 
		$("#bt_save").click(function(){
			
		 	if ($("#form_user_edit").valid()){	
			 	$('#jardines_page').showLoading({'addClass': 'loading-indicator-bars'});
				$.post("./?mod_inventario/mante_jardines_fases",$("#form_user_edit").serializeArray(),function(data){
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

function editNJardin(id){
	$('#jardines_page').showLoading({'addClass': 'loading-indicator-bars'});	
	$.post("./?mod_inventario/component/nomenclatura_jardin",{'edit':"1","id":id},function(data){
		$('#jardines_page').hideLoading();	
		var dialog=createNewDialog("Agregar Nomenclatura",data);
		 
		 $("#form_user_edit").validate({
				rules: {
					jardin: {
						required: true ,
						minlength: 2
					}  
				},
				messages : {
					jardin : {
						required: "Este campo es obligatorio",
						minlength: "Debes de digitar un minimo de 2 caracteres"
					}    ,
					estado : {
						required: "Este campo es obligatorio"
					}
				}
			
			});
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
 
		$("#bt_save").click(function(){
			
		 	if ($("#form_user_edit").valid()){	
			 	$('#jardines_page').showLoading({'addClass': 'loading-indicator-bars'});
				$.post("./?mod_inventario/mante_jardines_fases",$("#form_user_edit").serializeArray(),function(data){
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
 

<div class="fsPage" style="width:98%" id="jardines_page">
  <table width="100%" border="1">
    <tr>
      <td width="50%" valign="top"><table width="100%" border="0">
        <tr>
          <td width="150%"><h2> Jardines </h2></td>
        </tr>
        <tr>
          <td><button type="button" class="positive" name="pbutton"  id="pbutton" onClick="addNJardin()" > <img src="images/apply2.png" alt=""/>Agregar Nomenclatura</button></td>
        </tr>
        <tr>
          <td><table border="0" class="display" id="role_list" style="font-size:13px">
            <thead>
              <tr>
                <th>Jardin</th>
                <th>&nbsp;</th>
              </tr>
            </thead>
            <tbody>
              <?php
$SQL="SELECT * FROM jardines ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
?>
              <tr>
                <td height="25"><?php echo $row['jardin']?></td>
                <td align="center" ><a href="#" onClick="editNJardin('<?php echo $encriptID;?>')"><img src="images/clipboard_edit.png"  /></a></td>
              </tr>
              <?php 
}
 ?>
            </tbody>
          </table></td>
        </tr>
      </table></td>
      <td width="50%" valign="top"><table width="100%" border="0">
        <tr>
          <td width="150%"><h2>  Fases</h2></td>
        </tr>
        <tr>
          <td><button type="button" class="positive" name="pbutton"  id="pbutton2" onClick="addNFase()" > <img src="images/apply2.png" alt=""/>Agregar Nomenclatura</button></td>
        </tr>
        <tr>
          <td><table border="0" class="display" id="fase_list" style="font-size:13px">
            <thead>
              <tr>
                <th>Fase </th>
                <th>&nbsp;</th>
              </tr>
            </thead>
            <tbody>
              <?php
$SQL="SELECT * FROM fases ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
?>
              <tr>
                <td height="25"><?php echo $row['fase']?></td>
                <td align="center" ><a href="#" onClick="editNFase('<?php echo $encriptID;?>')"><img src="images/clipboard_edit.png"  /></a></td>
              </tr>
              <?php 
}
 ?>
            </tbody>
          </table></td>
        </tr>
      </table></td>
    </tr>
  </table>
</div>

<div id="content_dialog" ></div>

<?php SystemHtml::getInstance()->addModule("footer");?>