<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
 
if (!isset($_REQUEST['id'])){
	echo "No existe el acta!";
	exit;	
}


SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");   
SystemHtml::getInstance()->addTagScript("script/Class.js"); 
      
SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");
SystemHtml::getInstance()->addTagStyle("css/select2-bootstrap.css");
SystemHtml::getInstance()->addTagStyle("css/select2.css");
SystemHtml::getInstance()->addTagScriptByModule("class.cActas.js","cobros/actas"); 
SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");

/*Cargo el Header*/
SystemHtml::getInstance()->addModule("header");
SystemHtml::getInstance()->addModule("header_logo");

SystemHtml::getInstance()->includeClass("cobros/actas","Actas");    
$actas=new Actas($protect->getDBLink());

$actas->restart();

$acta=json_decode(System::getInstance()->Decrypt($_REQUEST['id'])); 	


$listado=$actas->getListadoDetalle($acta); 
 
?> 
<script> 

var _actas= new cActas("content_dialog");
 
$(function(){ 
	_actas.doViewListadoActa('<?php echo $_REQUEST['id'];?>');
});

 

 
</script>
<div id="inventario_page" class="fsPage" style="width:98%">
  <h2 style="color:#FFF;margin-top:0px;">DETALLE ACTA <?php echo $acta->idacta;?></h2>
 <table width="100%" border="0" cellspacing="0" cellpadding="0">
 
  <tr >
    <td  ><strong>TOTAL CONTRATOS:</strong>&nbsp;<?php echo count($listado);?></td>
  </tr>
 </table>

<table id="view_listado_acta" width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-hover" style="font-size:9px">
  <thead>
    <tr style="background:#CCC">
      <td><strong>TIPO</strong></td>
      <td><strong>CONTRATO</strong></td>
      <td align="center"><strong>CLIENTE</strong></td>
      <td align="center"><strong>FECHA VENTA</strong></td>
      <td align="center"><strong>PRODUCTO</strong></td>
      <td align="center"><strong>PRECIO NETO</strong></td>
      <td align="center"><strong>PRECIO N.COBRADO</strong></td>
      <td align="center"><strong>UBICACION</strong></td>
      <td align="center"><strong>CUOTAS</strong></td>
      <td align="center"><strong>PAG.</strong></td>
      <td align="center"><strong>MORA</strong></td>
      <td align="center"><strong>MTO.MORA</strong></td>
      <td align="center"><strong>OFICIAL COBROS</strong></td>
      <td align="center"><strong>ASESOR</strong></td>
      <td align="center"><strong>GTE. VENTAS</strong></td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      </tr>
  </thead>
  <tbody>
<?php   
 
 
foreach($listado as $key =>$row){ 
 
	$data=array("serie_contrato"=>$row['serie_contrato'],"no_contrato"=>$row['no_contrato'],"id_nit"=>$row['id_nit']);
	$id=System::getInstance()->Encrypt(json_encode($data));
 
?>
    <tr  >
      <td align="center"  ><?php echo $row['tipo']; ?></td>
      <td  ><a href="?mod_cobros/delegate&contrato_view&id=<?php echo $id;?>" target="new"><?php echo $row['serie_contrato']." ".$row['no_contrato'];?></a></td>
      <td align="center"  ><?php echo $row['nombre_completo'];?></td>
      <td align="center"  ><?php echo $row['fecha_venta']; ?></td>
      <td align="center" ><?php echo $row['producto_total'];?></td>
      <td align="center"  ><?php echo number_format($row['precio_neto'],2);?></td>
      <td align="center"  ><?php echo number_format($row['monto_no_cobrado'],2);?></td>
      <td align="center" ><?php echo $row['PROPIEDAD'];?></td>
      <td align="center" ><?php echo $row['cuotas'];?></td>
      <td align="center"  ><?php echo $row['cuotas_pagas'];?></td>
      <td align="center"  ><?php echo $row['CUOTAS_EN_ATRASO'];?></td>
      <td align="center"  ><?php echo number_format($row['MONTO_PENDIENTE_ATRASO'],2);?></td>
      <td align="center"  ><?php echo $row['OFICIAL'];?></td>
      <td align="center" ><?php echo $row['ASESOR'];?></td>
      <td align="center" ><?php echo $row['GERENTEV'];?></td>
      <td align="center"  ><a href="?mod_cobros/delegate&contrato_view&id=<?php echo $id;?>" target="new"><img src="images/document_preview.png" width="24" height="24"></a></td>
      <td align="center"  ><?php if ($acta->id_status!=27){?><a href="#" onclick="remover_item('<?php echo $id;?>')"><img src="images/cross.png" alt="" width="24" height="24"></a><?php } ?></td>
      </tr>
    <?php } ?>
  </tbody>
  <tfoot>
  </tfoot>
</table>
 
</div>

<div id="content_dialog" ></div>