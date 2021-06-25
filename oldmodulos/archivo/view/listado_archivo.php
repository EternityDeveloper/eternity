<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	


	SystemHtml::getInstance()->includeClass("archivo","Archivo"); 
 
   /*QUERY PARA BUSQUEDA */
	if (isset($_REQUEST['x_search'])){ 
			
		$_archivo= new Archivo($protect->getDBLink(),$_REQUEST); 
		$result=$_archivo->getListContratos();
		 
		echo json_encode($result);
		
		exit;
		
	} 
	 
	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	SystemHtml::getInstance()->addTagScript("script/dataTables.bootstrap.js"); 
	 
	SystemHtml::getInstance()->addTagScript("script/Class.js");
	SystemHtml::getInstance()->addTagScriptByModule("class.Archivo.js"); 
	 
	
	SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");
	SystemHtml::getInstance()->addTagScript("script/jquery/jquery.hotkeys.js");
	
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Empresa.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Persona.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Direccion.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Telefono.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Email.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Referencia.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Contactos.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Referidos.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.ModuloPersona.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.beneficiario.js");
	 
	SystemHtml::getInstance()->addTagScript("script/Class.direcciones.js");
	SystemHtml::getInstance()->addTagScript("script/Class.phone.js");
	SystemHtml::getInstance()->addTagScript("script/Class.empresa.js");
	SystemHtml::getInstance()->addTagScript("script/Class.email.js");
	SystemHtml::getInstance()->addTagScript("script/Class.reference.js");
	SystemHtml::getInstance()->addTagScript("script/Class.contactos.js");
	SystemHtml::getInstance()->addTagScript("script/Class.AsesoresTree.js");
	SystemHtml::getInstance()->addTagScript("script/Class.Referidos.js");
	SystemHtml::getInstance()->addTagScript("script/personalData.js");
	SystemHtml::getInstance()->addTagScript("script/Class.reservas.js"); 
      
	 SystemHtml::getInstance()->addTagStyle("css/demo_table.css"); 
	 
	SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css"); 
	SystemHtml::getInstance()->addTagStyle("css/dataTables.bootstrap.css"); 
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
.fsPage{
	width:99%;
} 
.archimovil{
 background:#B9B9B9;
}
.archimovil td:hover {
  background:#CCC;
}
.archimovil:hover { 
  cursor:pointer;
  background:#CCC;
}

.archivo{
  -moz-box-shadow: -5px -5px #888;
  -webkit-box-shadow: -5px -5px #888;
  box-shadow: -5px -5px #888;
} 

.archivo_detalle td:hover {
  background-color:#D7D7D7;
}
.archivo_detalle{
  cursor:pointer;		
}

.archivo_detalle th {
    border: 1px solid black;
}
.archivo_detalle td {
    border: 1px solid black;
}

</style>
 
<script>
 
var _archivo;

$(function(){ 			
  	_archivo= new ArchivoMovil('content_dialog');
	_archivo.doViewList('contratos_list'); 
}); 
 
</script> 
<div  class="fsPage">
  <h2 style="margin:0px;color:#FFF;">Listado de Contratos</h2>
 	<table border="0"  class="table table-striped table-bordered" id="contratos_list" style="font-size:12px">
      <thead>
        <tr>
          <th  >Contrato</th>
          <th >Cliente</th>
          <th   >Fecha</th>
          <th>Empresa</th>
          <th >Estatus </th>
          <th >Ubicacion</th>
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