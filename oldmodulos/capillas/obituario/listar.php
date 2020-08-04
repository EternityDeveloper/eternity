<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
SystemHtml::getInstance()->includeClass("caja","ChequesDevuelto"); 

if (isset($_REQUEST['filter'])){ 	
	$cdv= new ChequesDevuelto($protect->getDBLink()); 
	echo json_encode($cdv->getListFormaPagoCheque($_REQUEST)); 
	exit;	
}  
if (isset($_REQUEST['doViewResponsable'])){ 	
	include("view_responsable.php");
	exit;	
} 

if (isset($_REQUEST['getListItemAdded'])){ 	
	include("listado_item.php");
	exit;	
} 


if (isset($_REQUEST['doPutOnView'])){ 
	$_DATA=$_REQUEST;
	if (validateField($_DATA,"item")){
		$cdv= new ChequesDevuelto($protect->getDBLink()); 
		$item=json_decode(System::getInstance()->Decrypt($_DATA['item'])); 
		echo json_encode($cdv->agregar_cheque($item)); 		 
	} 
	exit;	
} 

if (isset($_REQUEST['view_listado_cheque'])){  
	include("view_listado_cheque.php");
	exit;	
} 

 
SystemHtml::getInstance()->includeClass("caja","Recibos");  
 
SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js"); 
SystemHtml::getInstance()->addTagScript("script/Class.js");  
SystemHtml::getInstance()->addTagScript("script/jquery.timepicker.js");  


SystemHtml::getInstance()->addTagScriptByModule("class.Capillas.js");  
SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");

SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
//SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");

SystemHtml::getInstance()->addTagScriptByModule("class.FormaPago.js","caja");
SystemHtml::getInstance()->addTagScriptByModule("class.CFactura.js","caja");
  
	
SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");
//SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");
SystemHtml::getInstance()->addTagStyle("script/jquery.timepicker.css");
 
 
/*Cargo el Header*/
SystemHtml::getInstance()->addModule("header");
SystemHtml::getInstance()->addModule("header_logo");
/* cargo el modulo de top menu*/
SystemHtml::getInstance()->addModule("main/topmenu");
 
 
SystemHtml::getInstance()->includeClass("caja","Cierre"); 
$recibos= new Recibos($protect->getDBLINK());   


/*
	PONGO EN LIMPIO LA VARIABLE DE SESSION 
	PARA MANIPULACION DEL CARRITO DE CHEQUES 
	DEVUELTOS
*/
STCSession::GI()->setSubmit("put_cheques_devuelto",array());
  
 
?>
<script>
 
var _op; 
var _cp;
$(function(){ 	 
	_cp = new Capillas('content_dialog');
	$("#_registrar").click(function(){ 
		_cp.doRegistrar();	
	});
	$(".editar_ob").click(function(){
		_cp.doEditar($(this).attr('id'));		
	});	
	 
}); 

function toggle(id){
   $("#"+id).toggle();
}
</script>
 
<div style="width:98%">
  <h2 style="margin:0px;color:#FFF">LISTADO DE OBITUARIOS</h2>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="">
  <tr>
    <td><table width="100%" border="0"  >
      <tr>
        <td colspan="2" align="center">&nbsp;</td>
      </tr>
 
      <tr>
        <td colspan="2"><button class="btn btn-sm btn-primary pull-left m-t-n-xs" type="button" id="_registrar"><strong>Registrar</strong></button></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      </table></td>
    </tr>
  <tr>
    <td id="detalle_search"  ><table id="listado_recibo_tb" width="100%" border="0" class="table table-bordered table-striped table-hover"  style="font-size:13px;">
      <thead>
        <tr>
          <th valign="top">NOMBRE</th>
          <th valign="top">CAPILLAS</th>
          <th valign="top">FECHA/ HORA<br />
            EXPOSICION</th>
          <th valign="top">SALIDA DE <br />
            CAPILLAS</th>
          <th valign="top">HORAS <br />
            RESTANTE</th>
          <th valign="top"> INHUMACION</th>
          <th valign="top">CEMENTERIO</th>
          <th valign="top">VISITA A <br />
            LA RESIDENCIA</th>
          <th valign="top">LECTURA DE <br />
            LA PALABRA</th>
          <th valign="top">MISA EN<br />
CAPILLAS</th>
          <th>&nbsp;</th>
          </tr>
      </thead>
      <tbody>
<?php
 
$SQL=" SELECT capillas_obituario.* ,
	capillas_devices.`device_descripcion` AS nombre_capillas,
TIMESTAMPDIFF(HOUR,CURDATE(),DATE_ADD(CURDATE(),INTERVAL TIMESTAMPDIFF(HOUR,CONCAT(capillas_obituario.fecha_exposicion,' ',capillas_obituario.hora_exposicion),
	CONCAT(capillas_obituario.fecha_salida,' ',capillas_obituario.hora_salida)) HOUR)) AS RESTANTE	
FROM `capillas_obituario`
INNER JOIN `capillas_devices` ON (`capillas_devices`.id=capillas_obituario.`capillas_devices_id`)
 order by capillas_obituario.id desc "; 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){  
	$id=System::getInstance()->Encrypt($row['id']);  
?>
    <tr <? if ($row['RESTANTE']==2){?> style="background:#FF4646;color:#FFF" <?php } ?> >
      <td onclick="toggle('<?php echo $row['ID'];?>')"><?php echo $row['nombre_completo'];?></td>
      <td ><?php echo $row['nombre_capillas'];?></td>
      <td ><?php echo $row['fecha_exposicion'];?> - <?php echo $row['hora_exposicion'];?></td>      
      <td ><?php echo $row['fecha_salida'];?>- <?php echo $row['hora_salida'];?></td>
      <td ><?php echo $row['RESTANTE'];?></td>
      <td ><?php echo $row['detalle_inhumacion'];?></td>
      <td ><?php echo $row['cementerio_ihumacion'];?></td>
      <td ><?php echo $row['visita_residencia'];?></td>
      <td ><?php echo $row['lectura_palabra'];?></td>
      <td ><?php echo $row['misa_en_capillas'];?></td>
      <td ><a href="#" class="editar_ob"  id="<?php echo $id;?>" ><img src="images/subtract_from_cart.png" alt="" width="22" height="26" /></a></td>
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