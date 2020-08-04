<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

if (isset($_REQUEST['view_detalle_cierre'])){
	include("cierre_caja.php");
	exit;	
}
 

if (isset($_REQUEST['view_detalle'])){
	include("view_cierre_detalle.php");
	exit;	
}
  
SystemHtml::getInstance()->includeClass("caja","Recibos");  
 
SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js"); 
SystemHtml::getInstance()->addTagScript("script/Class.js");  
 
SystemHtml::getInstance()->addTagScriptByModule("class.CierreCaja.js");
SystemHtml::getInstance()->addTagScriptByModule("class.ChequesDevueltos.js"); 

SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");

SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");
 
SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
	
SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");
SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");
SystemHtml::getInstance()->addTagStyle("css/select2-bootstrap.css");
 
 
/*Cargo el Header*/
SystemHtml::getInstance()->addModule("header");
SystemHtml::getInstance()->addModule("header_logo");
/* cargo el modulo de top menu*/
SystemHtml::getInstance()->addModule("main/topmenu");
 
 
SystemHtml::getInstance()->includeClass("caja","Cierre"); 
$recibos= new Recibos($protect->getDBLINK());   
  

$day_from 	=	isset($_REQUEST['day_from'])?date("Y-m-d", strtotime($_REQUEST['day_from'])):date("d-m-Y");
$day_to		=	isset($_REQUEST['day_to'])?date("Y-m-d", strtotime($_REQUEST['day_to'])):date("d-m-Y");	
  
?>
<script>
 
var _op; 
var _cd;
$(function(){ 				
  	_op= new CierreCaja('content_dialog'); 
	_op.doListadoRecibo();
	
	_cd = new ChequesDevueltos('content_dialog');
	
 }); 
 
function anular(id){
	_op.doAnularReciboCaja(id);
} 
function asignar_recibo(id){
	_op.doAsignarRecibo(id);
} 

function chequeDevuelto(id){
	_cd.doRegistrarCD(id);
} 
</script>
 
<div  id="caja_view" style="width:100%;">
  <h2 style="margin:0px;color:#FFF;">LISTADO DE RECIBOS</h2>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="">
  <tr>
    <td><table width="100%" border="0" class="header_day">
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
        <td colspan="2" align="center">&nbsp;</td>
      </tr>
      </table></td>
    </tr>
  <tr>
    <td id="detalle_search"  ><table id="listado_recibo_tb" width="100%" border="0" class="table table-bordered table-striped table-hover"  style="font-size:13px;">
      <thead>
        <tr>
          <th>CONTRATO</th>
          <th> MOVIMIENTO</th>
          <th>SERIE DOCT.</th>
          <th>NO. DOCT.</th>
          <th>NO. FACTURA</th>
          <th>MONEDA</th>
          <th><span >FECHA</span></th>
          <th>CAJA</th>
          <th>CED</th>
          <th>NOMBRE CLIENTE</th>
          <th>MONTO</th>
          <th>&nbsp;</th>
          <th>FORMA DE PAGO</th>
          <th>EJECUTOR</th>
          <th>&nbsp;</th>
          </tr>
      </thead>
      <tbody>
<?php
$filter=array("day_from"=>$day_from,"day_to"=>$day_to,"action"=>"filter_range_date");
 
$listado= $recibos->getListadoRecibo($filter);
 
foreach($listado as $key=>$row){  
	$id=System::getInstance()->Encrypt(json_encode($row));
	$forma_pago=$recibos->getReciboFormaPago($row['SERIE'],$row['NO_DOCTO']);
?>
    <tr <?php echo $row['ANULADO']=="S"?'style="color:red"':''?> id="<?php echo $id;?>" class="listado_recibo" >
      <td><?php echo $row['SERIE_CONTRATO']." ".$row['NO_CONTRATO'];?></td>
      <td><?php echo $row['TMOVIMIENTO'];?></td>
      <td><?php echo $row['SERIE'];?></td>
      <td><?php echo $row['NO_DOCTO'];?></td>
      <td><?php echo $row['SERIE_FACTURA'].$row['NO_DOC_FACTURA'];?></td>
      <td><?php echo $row['TIPO_MONEDA'];?></td>
      <td><?php echo $row['FECHA'];?></td>
      <td><?php echo $row['DESCRIPCION_CAJA'];?></td>
      <td><a href=".?mod_caja/delegate&operacion&determinate=1&id=<?php echo System::getInstance()->Encrypt(json_encode(array('id_nit'=>$row['id_nit'])));?>" target="_new" ><?php echo $row['ID_NIT'];?></a></td>
      <td><?php echo $row['nombre_cliente'];?></td>
      <td><?php echo number_format($row['MONTO']*$row['TIPO_CAMBIO'],2);?></td>
      <td><a href="#"  onclick="asignar_recibo('<?php echo $id;?>')" class="anular" >ASIGNAR RECIBO</a></td>
      <td><table width="100%" border="0" cellspacing="0" cellpadding="0"  style="cursor:pointer">
        <tr>
          <td>TIPO</td>
          <td>MONTO</td>
          <td>REFERENCIA</td>
          <td>&nbsp;</td>
          </tr>
        <?php foreach($forma_pago as $key=>$fp_row){ 
				$id_fp=System::getInstance()->Encrypt(json_encode($fp_row));
			?>
        <tr>
          <td><?php echo $fp_row['descripcion_pago'];?></td>
          <td><?php echo number_format($fp_row['MONTO']*$row['TIPO_CAMBIO'],2);?></td>
          <td><?php echo $fp_row['AUTORIZACION'];?></td>
          <td><?php if ($fp_row['forpago']=="CK"){?><img src="images/bank_check.png" width="32" height="32" style="cursor:pointer" onclick="chequeDevuelto('<?php echo $id_fp;?>');" /> <?php } ?></td>
          </tr>
        <?php } ?>
      </table></td>
      <td><?php echo $row['EJECUTOR'];?></td>
      <td>
        <?php if (($row['ANULADO']=="N") ){?>
        <a href="#"  onClick="anular('<?php echo $id;?>')" class="anular" >ANULAR</a>
        <?php  } ?>
        
        
      </td>
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