<?php
if (!isset($protect)){
	exit;
}

if (validateField($_REQUEST,'paramt')){
	$obj = new ObjectSQL();
	unset($_REQUEST['mod_probando/hola']);
	unset($_REQUEST['paramt']);
	unset($_REQUEST['__atuvc']);
	unset($_REQUEST['jquery-ui-theme']);
	unset($_REQUEST['PHPSESSID']);
	//print_r($_REQUEST);
	$obj->push($_REQUEST);
	$obj->setTable('xxx');
	//echo $obj->toSQL('insert');
	echo json_encode(array('valid'=>1,"mesnaje"=>"Objeto insertado"));
	
	exit;	
}


if (validateField($_REQUEST,'loadview')){
	include("view/formulario.php");
	exit;
}


SystemHtml::getInstance()->addTagScript("script/Class.js");
SystemHtml::getInstance()->addTagScriptByModule("Class.hola.js");

SystemHtml::getInstance()->addModule("header");
SystemHtml::getInstance()->addModule("header_logo");
/* cargo el modulo de top menu*/
SystemHtml::getInstance()->addModule("main/topmenu");
 

?>
<script>
var hl
	$(function(){
		hl= new Hola('container');	
		hl.loadview();
		hl.addListener("modulo_cargado",function(data){
			this.procesar();
		})
	});
</script>
<input type="submit" name="button2" id="button2" value="Cargar vista" />
<div id="contenido">

<div>

<div id="container"></div>
<?php SystemHtml::getInstance()->addModule("footer");?>