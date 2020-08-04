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

$rs=$caja->getListAbonoSinInicial($id_nit);
if (count($rs)>0){
?>
<strong>SELECCIONE LOS ABONOS QUE DESEA APLICAR AL INICIAL</strong>
<table id="_detalle_abonos" width="100%" border="1" style="font-size:12px;border-spacing:1px;" class="tb_detalle fsDivPage">
  <thead>
    <tr  style="background-color:#CCC;height:30px;" >
      <td width="208" align="center">&nbsp;</td>
      <td width="208" align="center"><strong>FECHA DE PAGO</strong></td>
      <td width="276" align="center"><strong>SERIE DOCUMENTO</strong></td>
      <td width="276" align="center"><strong>NO DOCUMENTO</strong></td>
      <td width="276" align="center"><strong>MONTO</strong></td>
      <td width="276" align="center"><strong>CAJA</strong></td>
    </tr>
  </thead>
  <tbody>
    <?php
 


foreach($rs as $key =>$row){
//	$contrato=array("serie_contrato"=>$row['serie_contrato'],"no_contrato"=>$row['no_contrato']);
	$enc=System::getInstance()->Encrypt(json_encode($row)); 	
 
?>
    <tr style="height:30px;" class="abono_persona">
      <td align="center"><input type="checkbox" class="abnp_check" name="abp_check[]" id="abp_check[]" <?php if (array_key_exists($enc,$getitem)){echo ' checked="checked"';}?> value="<?php echo $enc;?>"></td>
      <td align="center"><?php echo $row['FECHA']?></td>
      <td align="center"><?php echo $row['SERIE']?></td>
      <td align="center"><?php echo $row['NO_DOCTO']?></td>
      <td align="center"><?php echo number_format($row['MONTO'],2)?></td>
      <td align="center"><?php echo $row['CAJA']?></td>
    </tr>
    <?php } ?>    
  </tbody>
</table>
<?php } ?>    
