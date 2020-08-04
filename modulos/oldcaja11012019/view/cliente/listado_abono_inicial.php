<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
 
if (validateField($_REQUEST,"id_nit") && validateField($_REQUEST,"moneda")){ 
	$id_nit=System::getInstance()->Decrypt($_REQUEST['id_nit']);
 
}else{
	exit;	
}


$moneda=$_REQUEST['moneda'];

SystemHtml::getInstance()->includeClass("caja","Caja"); 
$caja= new Caja($protect->getDBLINK());
$getitem=$caja->getItemListAbono();
 
$monto=0;
$monto_rd=0;
foreach($getitem as $key=>$val){
	$monto=$monto+$val->MONTO;
	$monto_rd=$monto_rd+$val->MONTO_RD;
}

?><table id="_detalle_abonos" width="100%" border="1" style="font-size:12px;border-spacing:1px;" class="tb_detalle fsDivPage">
  <thead>
    <tr  style="background-color:#CCC;height:30px;" >
      <td width="208" align="center">&nbsp;</td>
      <td width="208" align="center"><strong>FECHA DE PAGO</strong></td>
      <td width="276" align="center"><strong>TIPO MOVIMIENTO</strong></td>
      <td width="276" align="center"><strong>TIPO DE DOCUMENTO</strong></td>
      <td width="276" align="center"><strong>SERIE DOCUMENTO</strong></td>
      <td width="276" align="center"><strong>NO DOCUMENTO</strong></td>
      <td width="350" align="center"><strong>TIPO DE CAMBIO</strong></td>
      <td width="276" align="center"><strong>MONTO</strong></td>
      <td width="276" align="center"><strong>MONTO CAMBIO</strong></td>
      <td width="276" align="center"><strong>CAJA</strong></td>
    </tr>
  </thead>
  <tbody>
<?php
 
$rs=$caja->getListSaldosAfavor($id_nit,$moneda);

$getitem=$caja->getItemListAbono(); 
$comparer=array();
foreach($getitem as $key=>$row){
	$comparer[$row->SERIE.$row->NO_DOCTO]=$row;
}	 
		
foreach($rs as $key =>$row){
 	$enc=System::getInstance()->Encrypt(json_encode($row)); 	
 
?>
    <tr style="height:30px;" class="abono_persona">
      <td align="center" class="selectd_"><input type="checkbox" class="abnp_check" name="abp_check[]" id="abp_check[]"
       <?php if (array_key_exists($row['SERIE'].$row['NO_DOCTO'],$comparer)){ 	
		   		echo ' checked="checked"';
			}
			?> value="<?php echo $enc;?>"></td>
      <td align="center" class="selectd_"><?php echo $row['FECHA']?></td>
      <td align="center" class="selectd_"><?php echo $row['TMOVIMIENTO']?></td>
      <td align="center" class="selectd_"><?php echo $row['DOCUMENTO']?></td>
      <td align="center" class="selectd_"><?php echo $row['CAJA_SERIE']?></td>
      <td align="center" class="selectd_"><?php echo $row['CAJA_NO_DOCTO']?></td>
      <td align="center"><?php if ($row['SHOW_BOTTOM']){?><input type="text" name="<?php echo $enc;?>" id="<?php echo $enc;?>" class="tasa_cambio_css" style="width:50px;" value="<?php echo $row['TIPO_CAMBIO']?>" /><button type="button" class=" bt_agrega_fp orangeButton"   attr="<?php echo $enc;?>">Cambiar</button><?php }else{ ?><?php echo $row['TIPO_CAMBIO']?><?php } ?></td>
      <td align="center" class="selectd_"><?php echo number_format($row['MONTO'],2)?></td>
      <td align="center" class="selectd_"><?php echo number_format($row['MONTO_RD'],2)?></td>
      <td align="center" class="selectd_"><?php echo $row['CAJA']?></td>
    </tr>
    <?php } ?>
    <tr style="height: 30px; font-weight: bold;">
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">MONTO TOTAL</td>
      <td align="center" id="abi_monto_total"><strong><?php echo number_format($monto,2);?></strong></td>
      <td align="center" id="abi_monto_rd"><strong><?php echo number_format($monto_rd,2);?></strong></td>
      <td align="center">&nbsp;</td>
    </tr>
    <tr style="height: 30px; font-weight: bold;">
      <td colspan="10" align="center"><button type="button" class="redButton" id="bt_abni_cerrar">Cerrar</button></td>
    </tr>    
  </tbody>
</table>
