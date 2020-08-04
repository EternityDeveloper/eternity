<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
 
if (validateField($_REQUEST,"id_nit")){ 
	$id_nit=System::getInstance()->Decrypt($_REQUEST['id_nit']);
 
}else{
	exit;	
}

SystemHtml::getInstance()->includeClass("caja","Caja"); 
$caja= new Caja($protect->getDBLINK());
 $getitem=$caja->getItemListAbono();
$monto=0;

foreach($getitem as $key=>$val){
	$monto=$monto+$val->MONTO;
}

?><table width="100%" border="1" class="tb_detalle fsDivPage" style="font-size:12px;border-spacing:1px;">
  <thead>
    <tr  style="background-color:#CCC;height:30px;" >
      <td align="center"><strong>TIPO MOVIMIENTO</strong></td>
      <td align="center"><strong>MONTO</strong></td>
    </tr>
  </thead>
  <tbody>
    <?php
 
 /*
$SQL="SELECT 
	*,
	caja.DESCRIPCION_CAJA AS CAJA ,
	`tipo_documento`.`DOCUMENTO`
	FROM `movimiento_caja` 
INNER JOIN `caja` ON (caja.ID_CAJA=movimiento_caja.ID_CAJA) 
INNER JOIN `tipo_documento` ON (`tipo_documento`.TIPO_DOC=movimiento_caja.TIPO_DOC)
WHERE movimiento_caja.id_nit='".$id_nit."' ";
  
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){*/
//	$contrato=array("serie_contrato"=>$row['serie_contrato'],"no_contrato"=>$row['no_contrato']);
foreach($getitem as $key=>$row){
	$enc=System::getInstance()->Encrypt(json_encode($row)); 	
	 
 //if (array_key_exists($enc,$getitem)){
?>
    <tr style="height:30px;">
      <td align="center"><?php echo $row->TIPO_MOV?></td>
      <td align="center"><?php echo number_format($row->MONTO,2)?></td>
    </tr>
    <?php // }
} ?>
    <tr style="height: 30px; font-weight: bold;">
      <td align="center">MONTO TOTAL</td>
      <td align="center"><strong><?php echo number_format($monto,2);?></strong></td>
    </tr>
       
  </tbody>
</table>