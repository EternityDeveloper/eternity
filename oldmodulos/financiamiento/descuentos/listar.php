<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	



if (isset($_REQUEST['add_descuento'])){
	include("view/add_descuento.php");
	exit;
}
if (isset($_REQUEST['edit_descuento'])){
	include("view/edit_descuento.php");
	exit;
}
if (isset($_REQUEST['edit_plan'])){
	include("view/edit_plan_view.php");
	exit;
}
 

SystemHtml::getInstance()->includeClass("financiamiento/descuentos","Descuentos");


if (isset($_REQUEST['form_submit_desc'])){ 
	$descuento= new Descuentos($protect->getDBLink(),$_REQUEST);
	if ($_REQUEST['events']=="create"){
		echo json_encode($descuento->addDescuento($_REQUEST));
	}
	if ($_REQUEST['events']=="edit"){
		echo json_encode($descuento->editDescuento($_REQUEST));
	}
	
	exit;
} 
 

/*QUERY PARA BUSQUEDA */
if (isset($_REQUEST['x_search'])){
	$descuento= new Descuentos($protect->getDBLink(),$_REQUEST);
	echo json_encode($descuento->getListadoDescuento($_REQUEST['sSearch']));
	exit;
	
}
/*QUERY PARA BUSQUEDA */
if (isset($_REQUEST['validate_code'])){
	$descuento= new Descuentos($protect->getDBLink(),$_REQUEST);
	echo json_encode(array("exist"=>$descuento->existCodigo($_REQUEST['codigo'])));
	exit;
	
}


	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	 
	SystemHtml::getInstance()->addTagScript("script/Class.js");
	SystemHtml::getInstance()->addTagScriptByModule("Class.descuento.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");
 
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");
 
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");
	
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
<style>
.dataTables_filter{
	width:80%;	
	margin-top:0px;
	margin-left:10px;
}
.plan_detalle{
	display:none;	
}
.plan_moneda{ display:none;}
.enganche{
	list-style-type:none;
	border:0;
	padding:0;	
}
.enganche li {
	float:left;
	padding:5px 5px;	
}


 
</style>
<script>
 
var _descuento;

$(function(){
 						
	//$('<button id="refresh"  class="greenButton">Buscar</button>').appendTo('div.dataTables_filter'); 
	//$('<button id="button" class="greenButton">Nuevo</button>').appendTo('div.dataTables_filter'); 
  	_descuento= new Descuento('content_dialog','planes_list');
	_descuento.createTable(); 
	_descuento.putCreateButton("desc_refresh");
		
});
 
</script>
 
<div  class="fsPage" style="width:99%">
<h2>DESCUENTOS</h2> 
<button id="desc_refresh"  class="greenButton">Crear descuento</button>
 	<table width="100%" border="0" class="display" id="planes_list" style="font-size:13px">
      <thead>
        <tr>
          <th>Codigo</th>
          <th>Descripcion</th>
          <th>Monto</th>
          <th>Porcentaje</th>
          <th>ingresado %</th>
          <th>ingresado monto</th>
          <th>autorizacion</th>
          <th>Negocios </th>
          <th>Moneda</th>
          <th>Prioridad</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>

      </tbody>
  </table>
</div>
<div id="content_dialog" ></div>
<?php SystemHtml::getInstance()->addModule("footer");?>