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
	
	
	
	include($_PATH."class/modulos/menu/class.TreeMenu.php");

	$tree=new TreeMenu();
	
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
.Estilo2 {color: #5D995B}
</style>

<script>
var orole_list;
var gobal_table;

var menu_main_id;
var menu_title;
var menu_tpye;
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
		openDialogNew();
	});	
	$("#pbuttons").click(function(){
		openDialogSubMenuNew();
	});	
	
	$("#pbutton_edit").click(function(){
		switch(menu_tpye){
			case "Submenu":
				openDialogEditSubmenu(menu_main_id);
			break;
			case "Menu":
				openDialogEditSubmenu(menu_main_id);
			break;
		}
	});	
		
	$("#tree_menu a").click(function () {
		menu_title=$(this).text();
		menu_main_id=this.id;
		menu_tpye=this.type;
		switch(this.type){
			case "Submenu":
				$("#pbutton").hide();
				$("#pbuttons").hide();
			break;
			case "Menu":
				$("#pbutton").show();
				$("#pbuttons").show();
			break;
		}
	});												

	$("#tree_menu").jstree({
			"plugins" : ["themes","html_data","ui","crrm","hotkeys"],
			"core" : { "initially_open" : [ "phtml_1" ] }
		})
		.bind("loaded.jstree", function (event, data) {
			// you get two params - event & data - check the core docs for a detailed description
		});
	
});

function openDialogSubMenuNew(){
	$("#dialog-global").html('');
	$.post("index.php",{"mod_security/menu_add_submenu":""},function(data){
		$("#dialog-global").attr("title","Agregar nuevo SubMenu ( menu"+menu_title+" )");
		$("#dialog-global").html(data);
		$("#dialog-global").dialog({
			modal: true,
			width:400
		});
		
		$("#bt_save").click(function(){
			if (($("#Pantalla").val()!="-1") && ($("#nombre").val()!="") && ($("#orden").val()!="")){	
				$.post("index.php?mod_security/menu_add_submenu",{"Pantalla":$("#Pantalla").val(),"nombre":$("#nombre").val(),"orden":$("#orden").val(),"main_menu":menu_main_id,"submit":"true"},function(data){
					/* si hay un error que emita la alerta*/
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);	
						//$("#dialog-global").dialog();
						window.location.reload();		
					}
				},"json");
			}else{
				alert('Debe de llenar el campo de nombre, posicion o seleccionar una pantalla!');	
			}

		});				
		
	});
}

  
function openDialogNew(){
	$("#dialog-global").html('');
	$.post("index.php",{"mod_security/menu_add":""},function(data){
		$("#dialog-global").attr("title","Agregar nuevo menu");
		$("#dialog-global").html(data);
		$("#dialog-global").dialog({
			modal: true,
			width:400
		});
		
		$("#bt_save").click(function(){
			if (($("#Pantalla").val()!="-1") && ($("#nombre").val()!="") && ($("#orden").val()!="")){	
				$.post("index.php?mod_security/menu_add",{"Pantalla":$("#Pantalla").val(),"nombre":$("#nombre").val(),"orden":$("#orden").val(),"submit":"true"},function(data){
					/* si hay un error que emita la alerta*/					
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);	
						window.location.reload();		
					}
				},"json");
			}else{
				alert('Debe de llenar el campo de nombre, posicion o seleccionar una pantalla!');	
			}

		});				
		
	});
}


function openDialogEditSubmenu(id){
	$("#dialog-global").html('');
	$.post("index.php",{"mod_security/menu_edit_submenu":"","request":id},function(data){
		$("#dialog-global").attr("title","Editar "+menu_tpye);
		$("#dialog-global").html(data);
		$("#dialog-global").dialog({
			modal: true,
			width:400
		});
		
		
		$("#bt_save").click(function(){
			if (($("#Pantalla").val()!="-1") && ($("#nombre").val()!="") && ($("#orden").val()!="")){	
				$.post("index.php?mod_security/menu_edit_submenu",{"Pantalla":$("#Pantalla").val(),"orden":$("#orden").val(),"URL":$("#URL").val(),"nombre":$("#nombre").val(),"request":$("#request").val(),"submit":"true"},function(data){
					/* si hay un error que emita la alerta*/
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);	
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
    <td valign="top"><h2>Listado de Menu del sistema </h2></td>
    <td valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td valign="top">   
                      <button type="button" class="positive" name="pbutton"  id="pbutton" >
                        <img src="images/apply2.png" alt=""/> 
                        Agregar nuevo menu                      </button>
					  
					  <button type="button" class="positive" name="pbuttons"  id="pbuttons" style="display:none" >
                        <img src="images/apply2.png" alt=""/> 
                        Agregar sub-menu                      </button>
						
						  <button type="button" class="positive" name="pbutton_edit"  id="pbutton_edit" >
                        <img src="images/apply2.png" alt=""/> 
                       	Editar	                     </button>
					  </td>
    <td valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td width="400" valign="top">

	<div id="tree_menu" class="demo">
	<?php
	 $tree->print_menu();
	?>
	</div>
	




	</td>
    </tr>
</table>
</div>
<div id="div_users_list" class="fsPage sizeUser" style="margin-top:0px;display:none">
</div>
<div id="dialog-global" title="Agregar nuevo menu" style="display:block;background:#FFF">

<?php SystemHtml::getInstance()->addModule("footer");?>