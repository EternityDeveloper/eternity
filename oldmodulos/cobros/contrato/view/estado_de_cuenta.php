<?php 
if (!isset($protect)){
	exit;	
}

function cb($a,$b){
	return strtotime($a['FECHA'])-strtotime($b['FECHA']);
}

$saldo_inicial=$capita_interes->capital_cancelado+$capita_interes->capital_pendiente+$capita_interes->interes_pagado+$capita_interes->interes_pendiente;


$SaldoVencido=$con->getSaldoVencido($contrato->serie_contrato,$contrato->no_contrato);;
 
?>
<table width="700" border="0" cellspacing="0" cellpadding="0" class="tb_detalle fsDivPage">
  <tr>
    <td align="center"><strong>EMPRESA</strong></td>
    <td align="center"><strong>NRO. CONTRATO</strong></td>
    <td align="center"><strong>MONTO CAPITALIZADO</strong></td>
    <td align="center"><strong>SALDO INICIAL</strong></td>
    <td align="center"><strong>SALDO VENCIDO</strong></td>
    <td align="center">&nbsp;</td>
    <td align="center"><strong>REPARAR</strong></td>
  </tr>
  <tr>
    <td align="center">&nbsp;</td>
    <td align="center"><span style="margin:0px;"><?php echo $cdata->serie_contrato ." ".$cdata->no_contrato;?></span></td>
    <td align="center"><span style="margin:0px;"><?php echo  number_format($cdata->monto_capitalizado,2);?></span></td>
    <td align="center"><span style="margin:0px;"><?php echo number_format($saldo_inicial,2);?></span></td>
    <td align="center"><span class="day_restantes"><?php echo number_format($SaldoVencido,2);?></span></td>
    <td align="center"><a href="./?mod_cobros/delegate&amp;print_estado_de_cuenta&amp;id=<?php echo $_REQUEST['id'];?>" target="new_"><img src="images/preferences_desktop_printer.png" alt="" width="22" height="26" /></a></td>
    <td align="center"><a href="#repar" id="repar" onclick="reparar_('<?php echo $_REQUEST['id'];?>');"><img src="images/settings.png" alt="" width="24" height="24" /></a></td>
  </tr>
</table><br>

<div class="fixed-table-container2">
  <div style="background-color:#CCC;height:30px;"> </div>
        <div class="fixed-table-container-inner">
<table class="tb_detalle fsDivPage table-hover" id="tb_tipo_mov_"  border="1" style="font-size:12px;">
  <thead>
    <tr  >
      <td width="276" align="center"><div class="th-inner2"><strong>FECHA</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>TRANSACCION</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>NO. NCF</strong></div></td>
      <td width="276" align="center"><div class="th-inner2" ><strong>TIPO DE DOCUMENTO</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>CAPITAL</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>INTERES</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>DEBITO</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>CREDITO</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>SALDO</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>CAJA</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>OPCIONES</strong></div></td>
    </tr>
  </thead>
  <tbody>
    <?php



if (!isset($cache->estado_de_cuenta)){

	$SQL="SELECT 
					movimiento_contrato.*,
					movimiento_caja.*,
					caja.DESCRIPCION_CAJA AS CAJA ,
					`tipo_documento`.`DOCUMENTO`,
					tipo_movimiento.DESCRIPCION AS MOVIMIENTO
				FROM `movimiento_contrato` 
			LEFT JOIN `caja` ON (caja.ID_CAJA=movimiento_contrato.ID_CAJA)
			INNER JOIN `movimiento_caja` ON  (movimiento_caja.`SERIE`=movimiento_contrato.`CAJA_SERIE` 
							AND movimiento_caja.`NO_DOCTO`=movimiento_contrato.`NO_DOCTO`) 
			INNER JOIN `tipo_documento` ON (`tipo_documento`.TIPO_DOC=movimiento_contrato.TIPO_DOC)
			INNER JOIN `tipo_movimiento` ON (`tipo_movimiento`.TIPO_MOV=movimiento_contrato.TIPO_MOV)				
			WHERE  
				 movimiento_caja.TIPO_DOC IN ('RBC','NC','ND','RCA')  AND  
				movimiento_caja.`NO_CONTRATO`='".$contrato->no_contrato."' AND 
				movimiento_caja.`SERIE_CONTRATO`='".$contrato->serie_contrato."'  
			order by  movimiento_caja.FECHA ASC ";
		 
			 
 /*
 			INNER JOIN `movimiento_factura` ON  (movimiento_caja.`SERIE`=movimiento_factura.`CAJA_SERIE` 
							AND movimiento_caja.`NO_DOCTO`=movimiento_factura.`CAJA_NO_DOCTO`)	
 */
 
	$rs=mysql_query($SQL);
	$estado_de_cuenta=array();
	while($row=mysql_fetch_assoc($rs)){   
		if ($row['ANULADO']=="S"){ 
			$cp=(array)$row;
			$cp['ANULADO']="N";
			$row['FECHA']=substr($cp['ANULADO_DATE'],0,10);
			array_push($estado_de_cuenta,$cp); 
		} 	
		array_push($estado_de_cuenta,$row);
	}	
	
	usort($estado_de_cuenta, 'cb');  

	
	SystemCache::GI()->doPutCache("estado_de_cuenta",$estado_de_cuenta);
}else{
	$estado_de_cuenta=(array)$cache->estado_de_cuenta;	 
}  
foreach($estado_de_cuenta as $key=>$row){  
	$row=(array)$row;
	$encriptID=System::getInstance()->Encrypt(json_encode($row)); 
 
	if (($row['ANULADO']=="S") || ($row['TIPO_DOC']=="NC")){
		$saldo_inicial=$saldo_inicial+($row['MONTO_DOC']+$row['TOT_ABONOS']);
		$credito= $row['MONTO_DOC'];
		$debito=0;
	}else{
 		$saldo_inicial=$saldo_inicial-($row['MONTO_DOC']+$row['TOT_ABONOS']); 
		$debito= ($row['MONTO_DOC']+$row['TOT_ABONOS']);	
		$credito=0;	
	}
 
?>
    <tr style="height:30px;">
      <td height="30" align="center"><?php echo $row['FECHA']?></td>
      <td align="center"><?php echo $row['ANULADO']=="S"?'Anulado '.$row['MOVIMIENTO']:$row['MOVIMIENTO'];?></td>
      <td align="center"><?php echo $row['SERIE_FACTURA'].$row['NO_DOC_FACTURA'];?></td>
      <td align="center"><?php echo $row['DOCUMENTO']?></td>
      <td align="center"><?php echo number_format($row['CAPITAL_PAG'],2);?></td>
      <td align="center"><?php echo number_format($row['INTERESES_PAG'],2)?></td>
      <td align="center"><?php echo number_format($debito,2)  ?></td>
      <td align="center"><?php echo number_format($credito,2); ?></td>
      <td align="center"><?php echo number_format($saldo_inicial,2)?></td>
      <td align="center"><?php echo $row['CAJA']?></td>
      <td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><a href="./?mod_caja/delegate&amp;recibo_factura&id=<?php echo $encriptID;?>" target="new"	 ><img src="images/preferences_desktop_printer.png" alt="" width="22" height="26" /></a></td>
          <td> <a href="#"  onclick="print_('<?php echo $encriptID;?>')"><img src="images/preferences_desktop_printer.png" alt="" width="22" height="26" /></a></td>
          <td>  <a href="#"  onclick="sendmail_('<?php echo $encriptID;?>')"><img src="images/mail2_send.png" alt="" width="32" height="32" /></a></td> 
        </tr>
      </table>             
    </tr>
<?php } ?>
  </tbody>
 
</table>
  </div>
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="400" border="1" style="border-spacing:0px;font-size:12px;" class="tb_detalle fsDivPage">
      <tr  >
        <td width="170"><strong>RESUMEN</strong></td>
        <td width="60" align="right"><strong>CANCELADO</strong></td>
        <td width="54" align="right"><strong>PENDIENTE</strong></td>
        <td width="50" align="right"><strong>TOTAL</strong></td>
      </tr>
      <tr >
        <td><strong>CAPITAL </strong></td>
        <td align="right"><?php 
 							echo number_format($capita_interes->capital_cancelado,2);							
							?></td>
        <td align="right"><?php 
 							echo number_format($capita_interes->capital_pendiente,2);							
							?></td>
        <td align="right"><?php echo  number_format($capita_interes->capital_cancelado+$capita_interes->capital_pendiente,2)?></td>
      </tr>
      <tr >
        <td><strong>INTERESES </strong></td>
        <td align="right"><?php echo  number_format($capita_interes->interes_pagado,2)?></td>
        <td align="right"><?php echo  number_format($capita_interes->interes_pendiente,2)?></td>
        <td align="right"><?php echo  number_format($capita_interes->interes_pagado+$capita_interes->interes_pendiente,2)?></td>
      </tr>
      <tr >
        <td><strong>CUOTAS DE MANT.</strong></td>
        <td align="right">0</td>
        <td align="right">0</td>
        <td align="right">0</td>
      </tr>
      <tr >
        <td><strong>TOTALES</strong></td>
        <td align="right"><?php echo  number_format($capita_interes->capital_cancelado+$capita_interes->interes_pagado,2)?></td>
        <td align="right"><?php echo  number_format($capita_interes->capital_pendiente+$capita_interes->interes_pendiente,2)?></td>
        <td align="right"><?php echo  number_format($capita_interes->capital_cancelado+$capita_interes->capital_pendiente+$capita_interes->interes_pagado+$capita_interes->interes_pendiente,2)?></td>
      </tr>
    </table></td>
  </tr>
</table>     
 