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
SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");

SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");

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
  

$day_from 	=	isset($_REQUEST['day_from'])?date("Y-m-d", strtotime($_REQUEST['day_from'])):date("d-m-Y");
$day_to		=	isset($_REQUEST['day_to'])?date("Y-m-d", strtotime($_REQUEST['day_to'])):date("d-m-Y");	
  
?>
<script>
 
var _op; 
var _cd;
$(function(){ 	 
	_cd = new ChequesDevueltos('content_dialog');
	$("._posponer").click(function(){ 
		_cd.doReponer($(this).attr("id"),$(this).attr("id_nit"));	
	});
}); 
</script>
 
<div  id="caja_view" style="width:100%;">
  <h2 style="margin:0px;color:#FFF;">LISTADO DE CHEQUES DEVUELTOS</h2>
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
          <th> DOCUMENTO</th>
          <th><span >FECHA</span></th>
          <th>IDENTIFICACION</th>
          <th>NOMBRE CLIENTE</th>
          <th>MONTO</th>
          <th>FECHA REGISTRO</th>
          <th>EJECUTOR</th>
          <th>REPONER</th>
          </tr>
      </thead>
      <tbody>
<?php
$filter=array("day_from"=>$day_from,"day_to"=>$day_to,"action"=>"filter_range_date");

$SQL="SELECT cd.*,
(SELECT CONCAT(reg.`primer_nombre`,' ',reg.`segundo_nombre`,
' ',reg.`primer_apellido`,' ',reg.segundo_apellido) FROM sys_personas AS reg
 WHERE reg.id_nit=cd.REGISTRADO_POR) AS registrado_por,
(SELECT CONCAT(cli.`primer_nombre`,' ',cli.`segundo_nombre`,
' ',cli.`primer_apellido`,' ',cli.segundo_apellido)  FROM sys_personas AS cli WHERE cli.id_nit=cd.ID_NIT) AS cliente 
FROM `caja_cheque_devuelto` AS cd 	 	
WHERE cd.ESTATUS='POR_REPONER' "; 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){ 
//	$id=System::getInstance()->Encrypt(json_encode($row)); 
$id=System::getInstance()->Encrypt(json_encode(array('cd_id'=>$row['ID'],'id_nit'=>$row['ID_NIT'],"serie_contrato"=>$row['SERIE_CONTRATO'],"no_contrato"=>$row['NO_CONTRATO'])));
 

//$contrato=System::getInstance()->Encrypt(json_encode(array("serie_contrato"=>$row['SERIE_CONTRATO'],"no_contrato"=>$row['NO_CONTRATO'])));
?>
    <tr>
      <td><a href=".?mod_cobros/delegate&contrato_view&id=<?php echo $id;?>" target="_new" ><?php echo $row['SERIE_CONTRATO']." ".$row['NO_CONTRATO'];?></a></td>
      <td><?php echo $row['SERIE'];?>-<?php echo $row['NO_DOCTO'];?></td>
      <td><?php echo $row['FECHA'];?></td>
      <td><?php echo $row['ID_NIT'];?></td>
      <td><?php echo $row['cliente'];?></td>
      <td><?php echo number_format($row['MONTO']*$row['TIPO_CAMBIO'],2);?></td>
      <td><?php echo $row['FECHA_REGISTRO'];?></td>
      <td><?php echo $row['registrado_por'];?></td>
      <td><input type="button" id="<?php  echo $id;?>" id_nit="<?php echo System::getInstance()->Encrypt($row['ID_NIT']);?>"  class="_posponer btn btn-primary" value="Reposición"/></td>
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