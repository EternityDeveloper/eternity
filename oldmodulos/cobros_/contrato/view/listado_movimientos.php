<?php 
if (!isset($protect)){
	exit;	
}?>
<div class="fixed-table-container2">
    	<div style="background-color:#CCC;height:30px;"> </div>
        <div class="fixed-table-container-inner">
<table class="tb_detalle fsDivPage table-hover" id="tb_tipo_mov_"  border="1" style="font-size:12px;">
  <thead>
    <tr  >
      <td width="208" align="center"><div class="th-inner2"><strong>FECHA DE PAGO</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>TIPO MOVIMIENTO</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>NO. CUOTA</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>TIPO DE DOCUMENTO</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>SERIE DOCUMENTO</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>NO DOCUMENTO</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>TIPO DE CAMBIO</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>MONTO</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>CAJA</strong></div></td>
    </tr>
  </thead>
  <tbody>
    <?php
 
if (!isset($cache->movimientos)){
	$SQL="SELECT 
			*,
			caja.DESCRIPCION_CAJA AS CAJA ,
			`tipo_documento`.`DOCUMENTO`,
			tipo_movimiento.DESCRIPCION AS MOVIMIENTO
		FROM `movimiento_contrato` 
	INNER JOIN `caja` ON (caja.ID_CAJA=movimiento_contrato.ID_CAJA)
	INNER JOIN `movimiento_caja` ON  (movimiento_caja.`SERIE`=movimiento_contrato.`CAJA_SERIE` 
					AND movimiento_caja.`NO_DOCTO`=movimiento_contrato.`NO_DOCTO`)
	INNER JOIN `tipo_documento` ON (`tipo_documento`.TIPO_DOC=movimiento_contrato.TIPO_DOC)
	INNER JOIN `tipo_movimiento` ON (`tipo_movimiento`.TIPO_MOV=movimiento_contrato.TIPO_MOV)				
	WHERE 	movimiento_caja.TIPO_DOC IN ('RBC','ND','NC') AND 
			movimiento_caja.`NO_CONTRATO`='".$contrato->no_contrato."' AND
			movimiento_caja.`SERIE_CONTRATO`='".$contrato->serie_contrato."'
	ORDER BY  movimiento_caja.FECHA ";
	 
	
	$movimientos=array();
	$rs=mysql_query($SQL);
	while($row=mysql_fetch_assoc($rs)){   
		array_push($movimientos,$row);
	}	
	SystemCache::GI()->doPutCache("movimientos",$movimientos);
}else{
	$movimientos=(array)$cache->movimientos;	
 
}

$total_mov=0;
foreach($movimientos as $key=>$row){  
	$row=(array)$row;
	$total_mov=$total_mov+$row['TOTAL_MOV'];
?>
    <tr style="height:30px;">
      <td align="center"><?php echo $row['FECHA']?></td>
      <td align="center"><?php echo $row['MOVIMIENTO']?></td>
      <td align="center"><?php echo $row['NO_CUOTA']?></td>
      <td align="center"><?php echo $row['DOCUMENTO']?></td>
      <td align="center"><?php echo $row['CAJA_SERIE']?></td>
      <td align="center"><?php echo $row['NO_DOCTO']?></td>
      <td align="center"><?php echo $row['TIPO_CAMBIO']?></td>
      <td align="center"><?php echo number_format($row['TOTAL_MOV'],2)?></td>
      <td align="center"><?php echo $row['CAJA']?></td>
    </tr>
    <?php } ?>
  </tbody>
 <tfoot>
    <tr style="height:30px;">
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center"><?php echo number_format($total_mov,2);?>&nbsp;</td>
      <td align="center">&nbsp;</td>
    </tr> 
 </tfoot>
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
    <td>&nbsp;</td>
  </tr>
</table>     
 