<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	



	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");

	
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.mouse.js");
	SystemHtml::getInstance()->addTagScript("script/ui//jquery.ui.draggable.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.position.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.resizable.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.button.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.dialog.js");
	SystemHtml::getInstance()->addTagStyle("css/base/jquery.ui.all.css");

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
</style>

<script>
var orole_list;
var gobal_table;

$(document).ready(function(){
	orole_list=$("#role_list").dataTable({
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
							
											
	    $("#pbutton").click(function(){
			openDialogNewPantalla();
		});		
													
	
});



  
function openDialogNewPantalla(){
	$("#dialog-global").html('');
	$.post("index.php",{"mod_security/pantalla_add":""},function(data){
		$("#dialog-global").attr("title","Agregar nueva Pantalla");
		$("#dialog-global").html(data);
		$("#dialog-global").dialog({
			modal: true,
			width:400
		});
		
		
		$("#bt_save").click(function(){
		//	alert($("#proyectForm").serializeArray());
			if ($("#Pantalla").val()!=""){	
				$.post("index.php?mod_security/pantalla_add",{"Pantalla":$("#Pantalla").val(),"URL":$("#URL").val(),"submit":"true"},function(data){
					/* si hay un error que emita la alerta*/
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);	
						$("#dialog-global").dialog();
						window.location.reload();		
					}
				},"json");
			}else{
				alert('Debe de llenar el campo de nombre!');	
			}

		});				
		
	});
}


function openDialogEditPantalla(id){
	$("#dialog-global").html('');
	$.post("index.php",{"mod_security/pantalla_edit":"","request":id},function(data){
		$("#dialog-global").attr("title","Editar Pantalla");
		$("#dialog-global").html(data);
		$("#dialog-global").dialog({
			modal: true,
			width:400
		});
		
		
		$("#bt_save").click(function(){
			if ($("#Pantalla").val()!=""){	
				$.post("index.php?mod_security/pantalla_edit",{"Pantalla":$("#Pantalla").val(),"URL":$("#URL").val(),"request":$("#request").val(),"submit":"true"},function(data){
					/* si hay un error que emita la alerta*/
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);	
						$("#dialog-global").dialog();
						window.location.reload();		
					}
				},"json");
			}else{
				alert('Debe de llenar el campo de nombre!');	
			}

		});				
		
	});
}
  function _reload(){
	  window.location.reload();
  }
</script>
	<div class="fsPage">
<table width="100%" border="1">
  <tr>
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td valign="top"><h2>Listado de Pantallas del sistema</h2></td>
    <td valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td valign="top">   
                      <button type="button" class="positive" name="pbutton"  id="pbutton" onclick="openDialogNewPantalla()" >
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
          <th>Id</th>
          <th>Pantalla</th>
          <th>Modulo</th>
          <th>&nbsp;</th>
          </tr>
      </thead>
      <tbody>
<?php
$SQL="SELECT * FROM pantallas ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
?>
        <tr>
          <td><?php echo $row['id_pantalla']?></td>
          <td><?php echo $row['Pantalla']?></td>
          <td align="center" ><?php echo $row['URL']?></td>
          <td align="center" ><a href="#" onclick="openDialogEditPantalla('<?php echo $encriptID?>')"><img src="images/subtract_from_cart.png" width="27" height="28" /></a></td>
          </tr>
        <?php  
}
 ?>
      </tbody>
    </table>
  </tr> 
</table>
</div>	 
<div id="div_users_list" class="fsPage sizeUser" style="margin-top:0px;display:none">
</div>
<div id="dialog-global" title="Agregar nueva Pantalla" style="display:block;background:#FFF">

<?php SystemHtml::getInstance()->addModule("footer");?>
