<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
 
if (isset($_REQUEST['add_tabla_precio'])){
	include("view/add_tabla_precio_view.php");
	exit;
}
if (isset($_REQUEST['edit_tabla_precio'])){
	include("view/edit_tabla_precio_view.php");
	exit;
}
if (isset($_REQUEST['edit_plan'])){
	include("view/edit_plan_view.php");
	exit;
}

if (isset($_REQUEST['show_plan_group'])){
	include("view/view_plan_group.php");
	exit;
}

if (isset($_REQUEST['plan_custom_list_show'])){
	include("view/list_plan_finan_custom.php");
	exit;
} 

if (isset($_REQUEST['pl_inters_comision'])){
	include("view/listar_plazo_interes_comision.php");
	exit;	
}

SystemHtml::getInstance()->includeClass("financiamiento","PlanFinanciamiento");

if (isset($_REQUEST['financiamiento_submit'])){ 
	$plan= new PlanFinanciamiento($protect->getDBLink(),$_REQUEST);
	if ($_REQUEST['type_form']=="add"){
		echo $plan->createTablaPrecio();
	}
	if ($_REQUEST['type_form']=="edit"){
		echo $plan->updateTablaPrecio();
	}	
	exit;
}

if (isset($_REQUEST['disable_plan'])){ 
	$plan= new PlanFinanciamiento($protect->getDBLink(),$_REQUEST);
	echo $plan->removePlan();
	exit;
} 
 

/*QUERY PARA BUSQUEDA */
if (isset($_REQUEST['x_search'])){
	$plan= new PlanFinanciamiento($protect->getDBLink(),$_REQUEST);
	echo $plan->getListTablaPrecio();
	exit;
	
}


	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	 
	SystemHtml::getInstance()->addTagScript("script/Class.js");
	SystemHtml::getInstance()->addTagScriptByModule("Class.financiamiento.js");
	
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
 
var _financiamiento;

$(function(){
 						
	//$('<button id="refresh"  class="greenButton">Buscar</button>').appendTo('div.dataTables_filter'); 
	//$('<button id="button" class="greenButton">Nuevo</button>').appendTo('div.dataTables_filter'); 
  	_financiamiento= new PlanesFinanciamiento('content_dialog','planes_list');
	_financiamiento.createTable();
 	_financiamiento.putCreateButton("pro_refresh");
		
});
 
</script>
 
<div id="inventario_page" class="fsPage">
<h2>Planes de Financiamiento</h2>
<button id="pro_refresh"  class="greenButton">Crear tabla de precio</button>
 	<table border="0" class="display" id="planes_list" style="font-size:13px">
      <thead>
        <tr>
          <th>Codigo</th>
          <th>Precio</th>
          <th>Impuesto</th>
          <th>% Impuesto</th>
          <th>Capital</th>
          <th>Moneda</th>
          <th>% Interes</th>
          <th>Enganche </th>
          <th>&nbsp;</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>

      </tbody>
  </table>
</div>
<div id="content_dialog" ></div>
<?php SystemHtml::getInstance()->addModule("footer");?>