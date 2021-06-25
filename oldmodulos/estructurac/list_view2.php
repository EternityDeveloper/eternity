<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	


if (validateField($_REQUEST,"ListAsesores")){
	SystemHtml::getInstance()->includeClass("estructurac","Asesores"); 
	$asesor=new Asesores($protect->getDBLink(),$_REQUEST);
 	echo json_encode($asesor->getAsesorList($_REQUEST['sSearch'])); 
	exit;
}

if (validateField($_REQUEST,"empleados_list")){
	SystemHtml::getInstance()->includeClass("estructurac","Asesores"); 
	$asesor=new Asesores($protect->getDBLink(),$_REQUEST);
 	echo json_encode($asesor->getListEmpleados()); 
	exit;
}

if (validateField($_REQUEST,"empleados_sin_usuario")){
	SystemHtml::getInstance()->includeClass("estructurac","Asesores"); 
	$asesor=new Asesores($protect->getDBLink(),$_REQUEST);
 	echo json_encode($asesor->getSinUusarioEmpleados());
	
	 
	exit;
}

if (validateField($_REQUEST,"listar_metas")){
 	include("metas/listar_metas.php");
	exit;
}


if (validateField($_REQUEST,"edit_metas") && validateField($_REQUEST,"id")){
 	include("metas/view_metas.php");
	exit;
}



 	SystemHtml::getInstance()->addTagScript("script/Class.js");   
	SystemHtml::getInstance()->addTagStyle("css/smoothness/jquery.ui.combogrid.css");
	SystemHtml::getInstance()->addTagScript("script/jquery.ui.combogrid-1.6.3.js"); 
	SystemHtml::getInstance()->addTagScript("script/bootstrap/js/bootstrap.min.js");
	
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

	SystemHtml::getInstance()->addTagStyle("css/showLoading.css");
	SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
		
	
	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css");


	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");

//css/smoothness/jquery-ui-1.10.1.custom.css
	SystemHtml::getInstance()->addTagStyle("css/smoothness/jquery.ui.combogrid.css");
	SystemHtml::getInstance()->addTagScript("script/jquery.ui.combogrid-1.6.3.js");
	
	SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");

	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	SystemHtml::getInstance()->addModule("main/topmenu");




function getRowDivicion($ID_Agente){
$data=array();
$SQL="SELECT  
	 CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido) AS nombre,
	 codigo_director,
	 (SELECT COUNT(codigo_director) AS TOTAL FROM `sys_gerentes_grupos` WHERE 
	 sys_gerentes_grupos.`codigo_director`=sys_directores_division.codigo_director AND `codigo_gerente`='". $ID_Agente."') AS total_gerente_grupo
	FROM `sys_directores_division` 
INNER JOIN `sys_divisiones` ON (sys_divisiones.`iddivision`=sys_directores_division.`iddivision`)
LEFT JOIN sys_personas ON (sys_personas.`id_nit`=sys_directores_division.`sys_personas_id_nit`)
WHERE sys_directores_division.`codigo_director` = '". $ID_Agente ."' and sys_directores_division.status='1'";
 
	$rs_division=mysql_query($SQL);
	while($row_division=mysql_fetch_assoc($rs_division)){
		$director_divicion_id=System::getInstance()->Encrypt($row_division['codigo_director']);
		$row_division['director_divicion_id_encript']=$director_divicion_id;
		array_push($data,$row_division);		
	}
	return $data;
}

function getRowGerentesGrupo($idgerente,$iddirectores){
	$data=array();
	 
$SQL="SELECT
	CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido) AS nombre,
	codigo_gerente_grupo,
	(SELECT COUNT(idgerente_grupo) AS total FROM `sys_asesor` 
		WHERE sys_asesor.`codigo_gerente_grupo`=gg.codigo_gerente_grupo) as TOTAL_ASESOR
 FROM `sys_gerentes_grupos` as gg
LEFT JOIN sys_personas ON (sys_personas.`id_nit`=gg.`id_nit`)
where gg.`codigo_gerente` = '".$idgerente."' and gg.codigo_director='".$iddirectores."' AND  gg.status='1' ";
 
	$rs_gg=mysql_query($SQL);
	while($row_gerente_g=mysql_fetch_assoc($rs_gg)){
		
		$idgerente_grupo=System::getInstance()->Encrypt($row_gerente_g['codigo_gerente_grupo']);
		$row_gerente_g['idgerente_grupo_encript']=$idgerente_grupo;
	 	
		
		array_push($data,$row_gerente_g);	
	}
	
	return $data;
}

function getRowAsesor($id_gerente_grupo){
	$data=array();
	$SQL="SELECT
		CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido) AS nombre,
		codigo_asesor  as asesor_id
		 FROM `sys_asesor`
		LEFT JOIN sys_personas ON (sys_personas.`id_nit`=sys_asesor.`id_nit`)
		WHERE 
		sys_asesor.`codigo_gerente_grupo`='".$id_gerente_grupo."' and  sys_asesor.status='1'"; 
	$rs_asesor=mysql_query($SQL);
	while($row_asesor=mysql_fetch_assoc($rs_asesor)){
		$id_asesor=System::getInstance()->Encrypt($row_asesor['asesor_id']);
		$row_asesor['id_asesor_encript']=$id_asesor;
		array_push($data,$row_asesor);	
	}
	
	return $data;
}



?> 
<script>
var gerente=null;
var director=null;
var gerente_grupo= null;

function agregar_gerente(){
	$.post("index.php",{"mod_estructurac/estruct_add":""},function(data){

		var dialog=createNewDialog("Agregar Gerente",data);
 	 
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		
 
		
		$("#bt_save").click(function(){
		//	alert($("#proyectForm").serializeArray());
			if ($("#id_gerente").val()!=""){	
				var data={
					"id_gerente":$("#id_gerente").val(),
					"id_empleado":$("#id_empleado").val(),
					"descripcion":$("#descripcion").val(),
					"submit":"true",
				}
				
				$.post("index.php?mod_estructurac/estruct_add",data,function(data){
				
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);	
						$("#dialog-global1").dialog();
						window.location.reload();		
					}
				},"json");
			}else{
				alert('Debe de llenar el campo de nombre!');	
			}

		});				
		
	});
}
function edit_gerente_general(){
	$('#content_dialog').showLoading({'addClass': 'loading-indicator-bars'});
	$.post("index.php",{"mod_estructurac/estruct_add":"",'id_gerente':gerente,"edit":1},function(data){
		$('#content_dialog').hideLoading();
		//createNewDialog("Editar gerente general",data);

		var dialog=createNewDialog("Editar gerente general",data);
 	 
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});		

		$("#bt_save").click(function(){
		//	alert($("#proyectForm").serializeArray());
			if ($("#id_gerente").val()!=""){	
				var data={
					"id_gerente":$("#id_gerente").val(),
					"id_empleado":$("#id_empleado").val(),
					"id_directores":$("#id_directores").val(),
					"id_division":$("#id_division").val(),			
					"edit":$("#edit").val(),		
					"descripcion":$("#descripcion").val(),	
					"submit":"true",
				}
				
				$.post("index.php?mod_estructurac/estruct_add",data,function(data){
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
function agregar_director_divicion(){
 	 
	
	$.post("index.php",{"mod_estructurac/directore_add":"",'id_gerente':gerente},function(data){
 
		var dialog=createNewDialog("Agregar director de divisiónn",data);
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});	
		
		$("#nombre_empleado").combogrid({
			url: './?mod_estructurac/list_view2&empleados_list=1', 
			colModel: [ 
					 {'columnName':'nombre','width':'60','label':'Nombre'}
					],
			select: function( event, ui ) {
				$( "#nombre_empleado" ).val( ui.item.nombre );
				$( "#id_empleado" ).val( ui.item.value );
				return false;
			}
		});
				
		$("#bt_save").click(function(){
		//	alert($("#proyectForm").serializeArray());
			if ($("#id_gerente").val()!=""){	
				var data={
					"id_gerente":$("#id_gerente").val(),
					"id_empleado":$("#id_empleado").val(),
					"id_directores":$("#id_directores").val(),
					"id_division":$("#id_division").val(),					
					"submit":"true",
				}
				
				$.post("index.php?mod_estructurac/directore_add",data,function(data){
					
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
function edit_director_divicion(){
	
	showLoading(1);
	$.post("index.php",{"mod_estructurac/directore_add":"",'id_gerente':gerente,'id_director':director,"edit":1},function(data){
		showLoading(0)
		var dialog=createNewDialog("Editar director de división",data);
	 
	 	 
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		
		$("#nombre_empleado").combogrid({
			url: './?mod_estructurac/list_view2&empleados_list=1', 
			colModel: [ 
					 {'columnName':'nombre','width':'60','label':'Nombre'}
					],
			select: function( event, ui ) {
				$( "#nombre_empleado" ).val( ui.item.nombre );
				$( "#id_empleado" ).val( ui.item.value );
				return false;
			}
		});		
		
		$("#form_pantallas").validate();
		$.validator.messages.required = "Campo obligatorio.";
		
		$("#bt_save").click(function(){
	
			if (($("#id_gerente").val()!="") && ($("#form_pantallas").valid())){	
				var data={
					"id_gerente":$("#id_gerente").val(),
					"id_empleado":$("#id_empleado").val(),
					"id_director":$("#id_director").val(),
					"id_division":$("#id_division").val(),			
					"edit":$("#edit").val(),		
					"descripcion":$("#descripcion").val(),	
					"submit":"true",
					"edit":$("#edit").val()
				}
				 
				showLoading(1);
				$.post("index.php?mod_estructurac/directore_add",data,function(data){
					showLoading(0);
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);	
						window.location.reload();		
					}  
				},"json");
			}else{
				alert('Debe de seleccionar un elemento de la lista!');	
			}

		});				
		
	});
}

function  showLoading(val){
	switch(val){
		case 0:
			$('#content_dialog').hideLoading();
		break;
		case 1:
			$('#content_dialog').showLoading({'addClass': 'loading-indicator-bars'});
		break;
	}
}

function agregar_gerentes_grupo(){
 	var GerenteGrupo = new Class();
	
	var gg= new GerenteGrupo();
	
	gg.post("index.php",{"mod_estructurac/subgerente_add":"",'id_gerente':gerente,'director_id':director},
	function(data){
 		var dialog=gg.doDialog("moda_agregar_gerente_venta","content_dialog",data);
  
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		$("#form_pantallas").validate();
		$.validator.messages.required = "Campo obligatorio.";

		$("#nombre_empleado").combogrid({
			url: './?mod_estructurac/list_view2&empleados_list=1', 
			colModel: [ 
					 {'columnName':'nombre','width':'60','label':'Nombre'}
					],
			select: function( event, ui ) {
				$( "#nombre_empleado" ).val( ui.item.nombre );
				$( "#id_empleado" ).val( ui.item.value );
				return false;
			}
		});	
		
		$("#bt_save").click(function(){
 				
			if (($("#id_gerente").val()!="") && ($("#form_pantallas").valid())){	
				var data={
					"id_gerente":$("#id_gerente").val(),
					"id_empleado":$("#id_empleado").val(),
					"id_gerente_grupo":$("#id_gerente_grupo").val(),
					"director_id":$("#director_id").val(),
					"id_grupos":$("#id_grupos").val(),	
					"submit":"true",
					"id_division":$("#id_division").val()
				}
				
				gg.post("index.php?mod_estructurac/subgerente_add",data,function(data){
					
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);	
 
						window.location.reload();		
					}
				},"json");
			}else{
				alert('Debe de seleccionar un elemento de la lista!');	
			}

		});				
		
	});
}
function edit_gerentes_grupo(){
	
	showLoading(1);
	$.post("index.php",{"mod_estructurac/subgerente_add":"",'id_gerente':gerente,'id_director':director,'id_gerente_grupo':gerente_grupo,"edit":1},function(data){
		showLoading(0)
		var dialog=createNewDialog("Editar gerentes de ventas y  grupos de ventas",data);
		
		$("#form_pantallas").validate();
		$.validator.messages.required = "Campo obligatorio.";
	 	 
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
		
		$("#nombre_empleado").combogrid({
			url: './?mod_estructurac/list_view2&empleados_list=1', 
			colModel: [ 
					 {'columnName':'nombre','width':'60','label':'Nombre'}
					],
			select: function( event, ui ) {
				$( "#nombre_empleado" ).val( ui.item.nombre );
				$( "#id_empleado" ).val( ui.item.value );
				return false;
			}
		});	
		
		$("#bt_save").click(function(){
	
			if (($("#id_gerente").val()!="") && ($("#form_pantallas").valid())){	
				var data={
					"id_gerente":$("#id_gerente").val(),
					"id_empleado":$("#id_empleado").val(),
					"id_director":$("#id_director").val(),
					"id_grupos":$("#id_grupos").val(),			
					"edit":$("#edit").val(),		
					"id_gerente_grupo":$("#id_gerente_grupo").val(),
					"descripcion":$("#descripcion").val(),	
					"submit":"true",
					"edit":$("#edit").val()
				}
				
				
				showLoading(1);
				$.get("index.php?mod_estructurac/subgerente_add",data,function(data){
					showLoading(0);
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);	
						window.location.reload();		
					}  
				},"json");
			}else{
				alert('Debe de seleccionar un elemento de la lista!');	
			}

		});				
		
	});
}

function agregar_asesor(){
 	var Asesor = new Class();
	
	var ase= new Asesor(); 
	ase.post("index.php",
			{"mod_estructurac/asesores_add":"",'id_gerente':gerente,
			'director_id':director,'gerente_grupo':gerente_grupo},
	function(data){
		var dialog=ase.doDialog("modal_agregar_asesor","content_dialog",data);
		//var dialog=createNewDialog("Agregar asesor",data);
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
				
		$("#form_pantallas").validate();
		$.validator.messages.required = "Campo obligatorio.";
	 	 		
		$("#nombre_empleado").combogrid({
			url: './?mod_estructurac/list_view2&empleados_list=1', 
			colModel: [ 
					 {'columnName':'nombre','width':'60','label':'Nombre'}
					],
			select: function( event, ui ) {
				$( "#nombre_empleado" ).val( ui.item.nombre );
				$( "#id_empleado" ).val( ui.item.value );
				return false;
			}
		});
			
		$("#bt_save").click(function(){

			if (($("#id_gerente").val()!="") && ($("#form_pantallas").valid())){	
				var data={
					"id_gerente":$("#id_gerente").val(),
					"id_empleado":$("#id_empleado").val(),
					"id_gerente_grupo":$("#id_gerente_grupo").val(),
					"director_id":$("#director_id").val(),					
					"submit":"true",
					"id_division":$("#id_division").val(),
					"id_grupos":$("#id_grupos").val()
				}
				
				ase.post("index.php?mod_estructurac/asesores_add",data,function(data){
				 
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);	
						$("#dialog-global4").dialog();
						window.location.reload();		
					}  
				},"json");
			}else{
				alert('Debe de seleccionar un elemento de la lista!');	
			}

		});				
		
	});
} 

function edit_asesor(id){
  	showLoading(1);
	
	$.post("index.php",{"mod_estructurac/asesores_add":"",'id_gerente':gerente,'id_director':director,'id_gerente_grupo':gerente_grupo,"edit":1,"id":id},function(data){
		showLoading(0);
		var dialog=createNewDialog("Editar asesor",data);
		$("#bt_cancel").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});

		$("#nombre_empleado").combogrid({
			url: './?mod_estructurac/list_view2&empleados_list=1', 
			colModel: [ 
					 {'columnName':'nombre','width':'60','label':'Nombre'}
					],
			select: function( event, ui ) {
				$( "#nombre_empleado" ).val( ui.item.nombre );
				$( "#id_empleado" ).val( ui.item.value );
				return false;
			}
		});

		$("#bt_remove").click(function(){
			/*Desactiva puesto de asesor*/
				var data={
					"id_gerente":$("#id_gerente").val(),
					"id_empleado":$("#id_empleado").val(),
					"id_gerente_grupo":$("#id_gerente_grupo").val(),
					"director_id":$("#director_id").val(),					
					"submit":"true",
					"id_division":$("#id_division").val(),
					"id_grupos":$("#id_grupos").val(),
					"id_asesor":$("#id_asesor").val(),
					"edit":3
				}
				
				$.post("index.php?mod_estructurac/asesores_add",data,function(data){
					 showLoading(0);
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);	
						
						window.location.reload();		
					} 
					 
				},"json");
			
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
		});
				
		$("#bt_save").click(function(){

			if ($("#id_gerente").val()!=""){	
				var data={
					"id_gerente":$("#id_gerente").val(),
					"id_empleado":$("#id_empleado").val(),
					"id_gerente_grupo":$("#id_gerente_grupo").val(),
					"director_id":$("#director_id").val(),					
					"submit":"true",
					"id_division":$("#id_division").val(),
					"id_grupos":$("#id_grupos").val(),
					"id_asesor":$("#id_asesor").val(),
					"edit":1
				}
				
				$.post("index.php?mod_estructurac/asesores_add",data,function(data){
					 showLoading(0);
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

function _remove_gerente(id){
	var data={
		"id_gerente":id,
		"remove":"true"
	}
	   if (confirm("Esta seguro de elminar este item?")){	

		   $.post("index.php?mod_estructurac/estruct_add",data,function(data){
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);	
						$("#dialog-global1").dialog();
						window.location.reload();		
					}
				},"json"); 
	   }
} 
function _remove_director_division(id){
	var data={
		"id_director":id,
		"remove":"true"
	}
	   if (confirm("Esta seguro de elminar este item?")){	

		   $.post("index.php?mod_estructurac/directore_add",data,function(data){
					if (data.error){
						alert(data.mensaje);	
					}else{
						alert(data.mensaje);	
						$("#dialog-global2").dialog();
						window.location.reload();		
					}
				},"json");  
	   }
}
function _remove_gerente_grupo(id){
	var data={
		"id_gerente_grupo":id,
		"remove":"true"
	}
   if (confirm("Esta seguro de elminar este item?")){	
	
	   $.get("index.php?mod_estructurac/subgerente_add",data,function(data){
				if (data.error){
					alert(data.mensaje);	
				}else{
					alert(data.mensaje);	
					$("#dialog-global3").dialog();
					window.location.reload();		
				}
			},"json"); 
   }
}
function _remove_asesor(id){
	var data={
		"id_asesor":id,
		"remove":"true"
	}
   if (confirm("Esta seguro de elminar este item?")){	
	   $.get("index.php?mod_estructurac/asesores_add",data,function(data){
				if (data.error){
					alert(data.mensaje);	
				}else{
					alert(data.mensaje);	
					$("#dialog-global4").dialog();
					window.location.reload();		
				}
			},"json"); 
   }
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


$(function(){
	$("#tree_estruct").jstree({
			//"plugins" : ["themes","html_data","dnd","ui","hotkeys","search"],
			"plugins" : ["themes","html_data","dnd","ui","types","search"],
			"core" : { "initially_open" : [ "top_main","gerentes_divicion" ]},
			"types" : {
				"valid_children" : [ "default" ],
				"types" : {
					"root" : {
						"icon" : { 
							"image" : "./images/1379792004_building.png" 
						},
						"valid_children" : [ "default" ],
						"max_depth" : 2,
						"hover_node" : true,
						"select_node" : true
					},					
					"ceo" : {
						"icon" : { 
							"image" : "./images/1379792434_administrator.png" 
						},
						"valid_children" : [ "subgerente" ],
						"max_depth" : 2,
						"hover_node" : true,
						"select_node" : true
					},
					"seller" : {
						"icon" : { 
							"image" : "./images/1379792259_Businessman.png" 
						},
						"valid_children" : [ "default" ],
						"max_depth" : 2,
						"hover_node" : true,
						"select_node" : true
					},
					"subgerente" : {
						"icon" : { 
							"image" : "./images/1379791605_ceo.png" 
						},
						"valid_children" : [ "default" ],
						"max_depth" : 2,
						"hover_node" : true,
						"select_node" : true
					},
					"director" : {
						"icon" : { 
							"image" : "./images/1379794630_administrator.png" 
						},
						"valid_children" : [ "default" ],
						"max_depth" : 2,
						"hover_node" : true,
						"select_node" : true
					}
				}				
			}
		})
		.bind("loaded.jstree", function (event, data) {
			// you get two params - event & data - check the core docs for a detailed description
		}).bind("dblclick.jstree", function (event) {
		  var node = $(event.target).closest("li").attr('id');
		  // var data = node.data("jstree");	
		   switch(node){
			case "top_main":		 
			break;
			case "gerentes":
				edit_gerente_general();	 
			break;
			case "gerentes_division":
				 edit_director_divicion();
			break;
			case "gerentes_grupo":
				 edit_gerentes_grupo();
			break;
			case "asesor":
				 var ids = $(event.target).closest("li").attr('ids');
				 edit_asesor(ids);
			break;
		   }
		}).bind("open_node.jstree", function (event, data) {
			//$("#alog").append(data.rslt.obj.attr("rel") +" "+"<br>");
			 selected(data);	
		}).bind("select_node.jstree", function (event, data) {
			//$("#alog").append(data.rslt.obj.attr("rel") +" "+"<br>");
			//alert(data.rslt.obj.attr("id"));
			selected(data);
		});
 
  $("#button").click(function () {
       $("#tree_estruct").jstree("search", $("#search").val());
   });
   
   $("#search").keypress(function (e) {
	   if (e.keyCode==13){
       	$("#tree_estruct").jstree("search", $("#search").val());
	   }
   });
 
function selected(data){
	switch(data.rslt.obj.attr("id")){
		case "top_main":
			$("#add_general").show();
			$("#add_gerente_divi").hide();
			$("#add_gerente_grupo").hide();
			$("#add_asesor_g").hide();
		break;
		case "gerentes":
			$("#add_general").hide();
			$("#add_gerente_grupo").hide();
			$("#add_asesor_g").hide();			
			$("#add_gerente_divi").show();
			gerente=data.rslt.obj.attr("ids");
		break;
		case "gerentes_division":
			$("#add_general").hide();
			$("#add_gerente_divi").hide();
			$("#add_asesor_g").hide();
			$("#add_gerente_grupo").show();
			gerente=data.rslt.obj.attr("gerente");
			director=data.rslt.obj.attr("ids");	
		break;
		case "gerentes_grupo":
			$("#add_general").hide();
			$("#add_gerente_divi").hide();
			$("#add_gerente_grupo").hide();
			$("#add_asesor_g").show();
			gerente=data.rslt.obj.attr("gerente");
			director=data.rslt.obj.attr("director");	
			gerente_grupo=data.rslt.obj.attr("ids");	
		break;
		case "asesor":
			$("#add_general").hide();
			$("#add_gerente_divi").hide();
			$("#add_gerente_grupo").hide();
			$("#add_asesor_g").hide();
		break;									
	}		 
}
 
});

</script>
<div  class="fsPage" style="overflow:auto;width:98%">
<h2>Estructura comercial</h2>
<div id="add_general" style="display:none;float:left">
	<button type="button" class="positive" name="add_g_general"  id="add_g_general" onclick="agregar_gerente()" >  <img src="images/apply2.png" alt=""/> Agregar gerente general </button>
</div>
<div id="add_gerente_divi" style="display:none;float:left">				  
  <button type="button" class="positive" name="add_g_d"  id="add_g_d"  onclick="agregar_director_divicion()" >
    <img src="images/apply2.png" alt=""/> 
    Agregar directores de division </button>

  <button type="button" class="positive" name="add_g_e"  id="add_g_e"  onclick="edit_gerente_general()" >
    <img src="images/apply2.png" alt=""/> 
    Editar gerente general</button>
    
</div>
<div id="add_gerente_grupo" style="display:none;float:left">						
          <button type="button" class="positive" name="add_g_grupo"  id="add_g_grupo" onclick="agregar_gerentes_grupo()" >
        <img src="images/apply2.png" alt=""/> 
        Agregar gerentes de ventas y  grupos de ventas</button>
        
  <button type="button" class="positive" name="add_g_e"  id="add_g_e"  onclick="edit_director_divicion()" >
    <img src="images/apply2.png" alt=""/> 
    Editar director de división</button>        
</div>
<div id="add_asesor_g" style="display:none;float:left">
            <button type="button" class="positive" name="add_asesor"  id="add_asesor"  onclick="agregar_asesor()">
            <img src="images/apply2.png" alt=""/> 
            Agregar asesor </button>
            
           <button type="button" class="positive" name="add_g_v_e"  id="add_g_v_e"  onclick="edit_gerentes_grupo()" >
    <img src="images/apply2.png" alt=""/> 
    Editar gerentes de ventas y  grupos de ventas</button>           
</div>
<div id="edit_asesor" style="display:none;float:left">
            <button type="button" class="positive" name="edit_asesor"  id="edit_asesor"  onclick="edit_asesor()">
            <img src="images/apply2.png" alt=""/> 
            Editar asesor </button> 
</div>
<div style="width:420px;">
    <input type="text" name="search" id="search" size="50" />
    <input type="button" name="button" id="button" value="Buscar" />
</div>
<div id="tree_estruct">
   
 <ul>
    <li id="top_main" rel="root" ids="0" >
  		<a href="#"><strong>Estructura comercial Jardines Memorial</strong></a>
 	
   <ul>
<?php 

$SQL="SELECT idgerentes,id_nit,CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido) AS nombre, 
	(SELECT COUNT(idgerentes) AS total FROM `sys_directores_division` WHERE `idgerentes` = sys_gerentes.idgerentes) as total_division
FROM `sys_gerentes` 
LEFT JOIN sys_personas ON (sys_personas.`id_nit`=sys_gerentes.`sys_personas_id_nit`)
WHERE  sys_gerentes.status='1' ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
//	print_r($row);
	$gerente_id=System::getInstance()->Encrypt($row['idgerentes']);
?> 
         <li id="gerentes" ids="<?php echo $gerente_id?>" rel="ceo">
             <a href="#"><?php echo $row['nombre']!=""?$row['nombre']:'No asignado'?>&nbsp;&nbsp;<strong>(Gerente General)</strong></a>     	
<?php
/*VERIFICO QUE ESTE GERENTE TENGA 1 O MAS DIVISIONES*/
if ($row['total_division']>0)
{
?>     	<ul>       
    
<?php 
	  $row_divi=getRowDivicion($row['idgerentes']);
	 // print_r($row_divi);
	  foreach($row_divi as $key => $row_division){
		 
?>
		<li id="gerentes_division"  ids="<?php echo $row_division['director_divicion_id_encript']?>"  gerente="<?php echo $gerente_id?>" rel="subgerente"> <a href="#"><?php echo $row_division['nombre']!=""?$row_division['nombre']:'No asignado'?>&nbsp;&nbsp;<strong>(Director de División)</strong> </a>
        
        
<?php
/*VERIFICO QUE ESTE GERENTE TENGA 1 O MAS DIVISIONES*/
if ($row_division['total_gerente_grupo']>0)
{
?>  
	<ul>      
<?php 

 $row_gerent_g=getRowGerentesGrupo($row['idgerentes'],$row_division['codigo_director']);
 foreach($row_gerent_g as $key => $row_gerente_g){
	 
?>
	<li id="gerentes_grupo" rel="director"  ids="<?php echo $row_gerente_g['idgerente_grupo_encript']?>" gerente="<?php echo $gerente_id?>" director="<?php echo $row_division['director_divicion_id_encript']?>" > <a href="#"><?php echo $row_gerente_g['nombre']!=""?$row_gerente_g['nombre']:'No asignado'?>&nbsp;&nbsp;<strong>(Gerente grupo de ventas)</strong> </a>
    
<?php
/*VERIFICO QUE ESTE GERENTE TENGA 1 O MAS DIVISIONES*/
if ($row_gerente_g['TOTAL_ASESOR']>0)
{
	
?>    
<ul>
<?php 

 $rs_ase=getRowAsesor($row_gerente_g['codigo_gerente_grupo']);
 
 foreach($rs_ase as $key => $row_asesor){
?>
<li  id="asesor" ids="<?php echo $row_asesor['id_asesor_encript']?>" rel="seller"> <a href="#"><?php echo $row_asesor['nombre']!=""?$row_asesor['nombre']:'No asignado'?>&nbsp;&nbsp;<strong>(Asesor de Familia)</strong> </a> </li>

<?php } ?>
</ul>
<?php } //END TOTAL_ASESOR?>    
    </li>    
<?php 
}// END While getRowGerentesGrupo
?> 
</ul>         
<?php 
}// END IF total_gerente_grupo
?>        
        
        </li>  
<?php } //END WHILE total_division?>
		</ul>
<?php } //END IF total_division ?>
 			

  		 </li>
<?php } ?>          
    
    </ul>
       
    </li>
    
  </ul>            
</div>   
</div>
<div id="content_dialog" ></div>

<div id="alog" class="fsPage"></div>

<div id="dialog-global1" title="" style="display:block;background:#FFF"></div>
<div id="dialog-global2" title="" style="display:block;background:#FFF"></div>
<div id="dialog-global3" title="" style="display:block;background:#FFF"></div>
<div id="dialog-global4" title="" style="display:block;background:#FFF"></div>
<?php SystemHtml::getInstance()->addModule("footer");?>