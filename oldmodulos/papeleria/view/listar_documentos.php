<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

 
 
SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js"); 
SystemHtml::getInstance()->addTagScript("script/Class.js");  
 
SystemHtml::getInstance()->addTagScriptByModule("class.Papeleria.js");
 

SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");

SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");
  
	
SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");
SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");
SystemHtml::getInstance()->addTagStyle("css/select2-bootstrap.css");
 
 
/*Cargo el Header*/
SystemHtml::getInstance()->addModule("header");
SystemHtml::getInstance()->addModule("header_logo");
/* cargo el modulo de top menu*/
SystemHtml::getInstance()->addModule("main/topmenu");
 
 
SystemHtml::getInstance()->includeClass("papeleria","Recibos"); 
$pap= new Recibos($protect->getDBLINK());   
 
?>
<script>
 
var _op; 
$(function(){ 				
  	_op= new Papeleria('content_dialog'); 
	_op.doListadoDocumento();
}); 
 
</script>
 
<div  id="caja_view" style="width:90%;">
  <h2 style="margin:0px;color:#FFF">Listado de Documentos</h2>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="">
  <tr>
    <td><table width="200%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><button type="button" id="crear_" class="btn btn-primary">CREAR DOCUMENTO</button></td>
        </tr>
      <tr>
        <td>&nbsp;</td>
        </tr>
    </table></td>
    </tr>
  <tr>
    <td id="detalle_search"  ><table border="0" class="table table-bordered table-striped table-hover"  style="font-size:13px;">
      <thead>
        <tr>
          <th><span >NOMBRE</span></th>
          <th>CREADO POR</th>
          <th>&nbsp;</th>
          </tr>
      </thead>
      <tbody>
        <?php
 
$listado= $pap->getListadoDocumento();
foreach($listado as $key=>$row){ 
	unset($row['TEXTO']);
	$id=System::getInstance()->Encrypt(json_encode($row)); 
?>
    <tr  >
      <td><?php echo $row['NOMBRE_DOC'];?></td>
      <td><?php echo $row['CREADO_POR'];?></td>
      <td><button type="button" class="orangeButton doct_view" value="<?php echo $id;?>">Editar</button></td>
      </tr>
        <?php 
}
 ?>
      </tbody>
    </table></td>
    </tr>
  <tr>
    <td id="detalle_search2" style="padding-top:10px;">&nbsp;</td>
  </tr>
  <tr>
    <td align="center" id="detalle_search4" >&nbsp;</td>
  </tr>
  </table>



</div>
<div id="content_dialog" ></div>