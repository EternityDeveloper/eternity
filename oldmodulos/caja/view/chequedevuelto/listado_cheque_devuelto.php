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
 
SystemHtml::getInstance()->addTagScriptByModule("class.CierreCaja.js");
SystemHtml::getInstance()->addTagScriptByModule("class.ChequesDevueltos.js");  
SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");

SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
//SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");

SystemHtml::getInstance()->addTagScriptByModule("class.FormaPago.js","caja");
SystemHtml::getInstance()->addTagScriptByModule("class.CFactura.js","caja");
  
	
SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");
//SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");
//SystemHtml::getInstance()->addTagStyle("css/select2-bootstrap.css");
 
 
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
  

$day_from 	=	isset($_REQUEST['day_from'])?date("Y-m-d", strtotime($_REQUEST['day_from'])):date("d-m-Y");
$day_to		=	isset($_REQUEST['day_to'])?date("Y-m-d", strtotime($_REQUEST['day_to'])):date("d-m-Y");	
  

?>
<script>
 
var _op; 
var _cd;
$(function(){ 	 
	_cd = new ChequesDevueltos('content_dialog');
	$("#_registrar").click(function(){ 
		_cd.doRegistrar();	
	});
	
	$("._posponer").click(function(){
		_cd.doReponer($(this).attr("id"));		
	});
	
}); 

function toggle(id){
   $("#"+id).toggle();
}
</script>
 
<div  class="row  border-bottom white-bg dashboard-header">
  <h2 style="margin:0px;">LISTADO DE CHEQUES DEVUELTOS</h2>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="">
  <tr>
    <td><table width="100%" border="0"  >
      <tr>
        <td colspan="2" align="center">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" align="center"><form method="post" >
          <table width="400" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td><strong>FECHA</strong></td>
              <td><input type="text" name="day_from" id="day_from" class="form-control textfield"   value="<?php echo date("d-m-Y", strtotime($day_from))?>" /></td>
              <td><input type="text" name="day_to" id="day_to" class="form-control textfield"  value="<?php echo date("d-m-Y", strtotime($day_to))?>" /></td>
              <td><button type="submit" class="greenButton" id="filtrar">Filtrar</button></td>
              </tr>
            </table>
          </form></td>
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
          <th>NUMERO CHEQUE</th>
          <th>MONTO</th>
          <th>FECHA REGISTRO</th>
          <th>EJECUTOR</th>
          <th>REPONER</th>
          <th>&nbsp;</th>
          </tr>
      </thead>
      <tbody>
<?php
$filter=array("day_from"=>$day_from,"day_to"=>$day_to,"action"=>"filter_range_date");

$SQL="SELECT cd.*,
(SELECT CONCAT(reg.`primer_nombre`,' ',reg.`segundo_nombre`,
' ',reg.`primer_apellido`,' ',reg.segundo_apellido) FROM sys_personas AS reg
 WHERE reg.id_nit=cd.REGISTRADO_POR) AS registrado_por 
FROM `caja_cheque_devuelto` AS cd 	 	
WHERE cd.ESTATUS='POR_REPONER' "; 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){  
	$id=System::getInstance()->Encrypt(json_encode(array('cd_id'=>$row['ID'])));  
?>
    <tr style="cursor:pointer" >
      <td onclick="toggle('<?php echo  "doc_".$row['ID'];?>')"><a href="../listado_recibos/.?mod_cobros/delegate&amp;contrato_view&amp;id=<?php echo $id;?>" target="_new" ><?php echo $row['NUMERO_CHEQUE'];?></a></td>
      <td onclick="toggle('<?php echo  "doc_".$row['ID'];?>')"><?php echo number_format($row['MONTO'],2);?></td>
      <td onclick="toggle('<?php echo  "doc_".$row['ID'];?>')"><?php echo $row['FECHA_REGISTRO'];?></td>
      <td onclick="toggle('<?php echo  "doc_".$row['ID'];?>')"><?php echo $row['registrado_por'];?></td>
      <td ><input type="button" id="<?php  echo $id;?>" class="_posponer btn btn-primary" value="Reposición"/></td>
      <td ><a href="./?mod_caja/delegate&amp;doPrintReciboChequeDevuelto&id=<?php echo $id;?>" target="dsa" ><img src="images/preferences_desktop_printer.png" alt="" width="22" height="26" /></a></td>
      </tr>
    <tr   >
      <td colspan="6" style="display:none" id="<?php echo "doc_".$row['ID']?>"><table   width="100%" border="0" class="table table-bordered table-striped table-hover"  style="font-size:13px;">
        <thead>
          <tr>
            <th>CONTRATO</th>
            <th>NUMERO FACTURA</th>
            <th>MONTO</th>
            <th>CLIENTE</th>
            </tr>
        </thead>
        <tbody>
          <?php
 $monto_total=0;
$SQL="SELECT *,
(SELECT CONCAT(reg.`primer_nombre`,' ',reg.`segundo_nombre`,
' ',reg.`primer_apellido`,' ',reg.segundo_apellido) FROM sys_personas AS reg
 WHERE reg.id_nit=caja_cheque_devuelto_detalle.id_nit_cliente) AS cliente 
  FROM `caja_cheque_devuelto_detalle` WHERE `id_caja_cheque_devuelto`='".$row['ID']."'"; 
$rXs=mysql_query($SQL);
while($rowx=mysql_fetch_assoc($rXs)){ 
 $contrato=System::getInstance()->Encrypt(json_encode(array("serie_contrato"=>$rowx['serie_contrato'],"no_contrato"=>$rowx['no_contrato'],"id_nit"=>$rowx['id_nit_cliente'])));
 	$monto_total=$monto_total+$rowx['monto'];
?>
          <tr>
            <td><a href="./?mod_cobros/delegate&amp;contrato_view&amp;id=<?php echo $contrato;?>" target="_new" ><?php echo $rowx['serie_contrato']." ".$rowx['no_contrato'];?></a></td>
            <td><?php echo $rowx['serie']." ".$rowx['no_docto'];?></td>
            <td><?php echo number_format($rowx['monto'],2);?></td>
            <td><?php echo $rowx['cliente'];?></td>
            </tr>
          <?php  
}
 ?>            
          <tr>
            <td colspan="2" align="right"><strong>MONTO TOTAL:</strong></td>
            <td><strong><?php echo number_format($monto_total,2);?></strong></td>
            <td>&nbsp;</td>
          </tr>
  

        </tbody>
      </table></td>
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