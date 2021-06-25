<?php

	//print_r($_REQUEST);
	if (!isset($_REQUEST['search']))
	{	
		exit;
	}
	
	
	$id=trim($_REQUEST['search']);
	$sp=explode(" ",$id);
 	 
	
	$SQL="SELECT CLI_CODIGO
			,CLI_DESCRIPCION
			,CLI_DIRECCION
			,CLI_TLF_1
			,CLI_TLF_2
			,CLI_TLF_3
			,CTT_COD_TD
			,CTT_CODIGO
			,CONVERT(varchar,CTT_FECHA, 103)  as CTT_FECHA
			,DEU_FECHA
			,DEU_FECHA_VENCE
			,DEU_MONTO
			,CASE WHEN DEU_CANC_TOTAL = 1 THEN 0 ELSE DEU_INTERES_FINANCIAMIENTO+DEU_INTERES_FINANCIAMIENTO_2 END  as INTERES
			,CASE WHEN DEU_CANC_TOTAL = 1 THEN 0 ELSE DEU_INTERES_FINANCIAMIENTO_1 END  as GA
			,DEU_COD_TIPO_DOCUMENTO
			,DEU_NUMERO
			,DEU_NRO_CUOTA
			,DEU_INTERES
			,DEU_ALICUOTA
			,DEU_FECHA_CANC
			,DEU_SERIE
			,DEU_COD_SERVICIO
			,PRO_DESCRIPCION
			,CTT_OMITIR_INTERES
			,DEU_GASTO_COB+DEU_GASTO_ADM+DEU_GASTO_MNJ as GASTOS
			,cli_observaciones
			,CTT_INICIAL
			,CTT_COD_TIPO_DOCUMENTO
			,CTT_NUMERO
			,PLN_COD_SERVICIO
			,CTT_ANULADO
			,DEU_REFINANCIADA
			,CTT_CAPITALIZA
			,DEU_ADICIONAL
			,PLN_CODIGO
			,CTT_FECHA_1_CUOTA
			,CTT_PARCELAS
			,DEU_DSCTO_PROM
			FROM PRO_CONTRATOS_DEUDAS, PRO_CONTRATOS, MAE_CC_CLIENTES, MAE_MATERIALES, PRO_PLANES_VENTA
			WHERE CTT_COD_TD  = DEU_COD_TD
			  AND CTT_CODIGO  = DEU_COD_CONTRATO
			  AND CLI_CODIGO  = CTT_COD_CLIENTE
			  AND PLN_CODIGO  = CTT_COD_PLAN
			  AND PRO_CODIGO  = DEU_COD_SERVICIO 
			  AND CTT_COD_TD   ='".$sp[0]."'
			  AND CTT_CODIGO='".$sp[1]."' 
			  AND CTT_RECIBO  =0
			  AND PLN_COD_SERVICIO = DEU_COD_SERVICIO
			  AND DEU_REFINANCIADA = 0
			  AND CTT_ANULADO =0
			ORDER BY CTT_COD_TD,CTT_CODIGO,DEU_COD_SERVICIO,DEU_FECHA_VENCE "; 
 
		
	$rsx=mssql_query($SQL,$ms_db);  
	$cli=mssql_fetch_assoc($rsx);
	
 

function getReciboOfPayment($ms_db,$TIPO_DOC,$NRO_DOC){
	$SQL="SELECT 
			VTA_COD_TIPO_DOCUMENTO, 
			VTA_NUMERO_OFICIAL, 
			VTA_FECHA_CANC, 
			VTA_TIPO_DOC_APLICA, 
			VTA_NRO_DOC_APLICA
		FROM MAE_CC_VENTAS 
		WHERE VTA_ANULADO = 0 AND 
			VTA_TIPO_DOC_APLICA = '".$TIPO_DOC."' AND 
			VTA_NRO_DOC_APLICA = '".$NRO_DOC."' AND 
			VTA_COD_TIPO_DOCUMENTO <> 'AC' ";
	$rsx=mssql_query($SQL,$ms_db);  
	return mssql_fetch_assoc($rsx);			
}



?>
<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" media="screen" />
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0px;font-size:13px;background:#FFF"  >
  <tr>
    <td><strong>Estado de Cuenta</strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><strong>Servicios Memoriales Dominicanos S.R.L</strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><strong>Periodo hasta el <?php echo  date("d/m/Y "); ?></strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><table width="800" border="0" cellspacing="0" cellpadding="0" style="border-spacing:0px;font-size:12px;"  >
      <tr>
        <td width="103"><strong>CLIENTE:</strong></td>
        <td width="444"><?php echo $cli['CLI_DESCRIPCION'];?></td>
        <td width="64" align="right"><strong>CODIGO:</strong>&nbsp;</td>
        <td width="189"><?php echo $cli['CLI_CODIGO'];?></td>
      </tr>
      <tr>
        <td><strong>DIRECCION:</strong></td>
        <td colspan="3"><?php echo $cli['CLI_DIRECCION'];?></td>
        </tr>
      <tr>
        <td><strong>COBRO EN:</strong></td>
        <td colspan="3"><?php echo $cli['cli_observaciones'];?></td>
        </tr>
      <tr>
        <td><strong>TELEFONO:</strong></td>
        <td colspan="3"><?php echo $cli['CLI_TLF_1']." / ".$cli['CLI_TLF_2']." / ".$cli['CLI_TLF_3'];?></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><strong>DETALLE DE CUOTAS</strong></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><table  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td  style="width:320px"><strong>CONTRATO</strong>: <?php echo $cli['CTT_COD_TD']." ".$cli['CTT_CODIGO'];?></td>
        <td style="width:320px"><strong>FECHA DE VENTA:</strong><?php echo $cli['CTT_FECHA'];?></td>
        <td style="width:320px"><strong>PRODUCTOS:</strong> <?php echo $cli['CTT_PARCELAS'];?></td>
        </tr>
    </table>
   <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-hover" >
 	 <thead>
      <tr style="background-color:#999;height:20px;">
        <td bgcolor="#999999">No.</td>
        <td bgcolor="#999999">Vence</td>
        <td bgcolor="#999999">Capital</td>
        <td bgcolor="#999999">Int.Fncmto.</td>
        <td bgcolor="#999999">Gasto Adm.</td>
        <td bgcolor="#999999">Gastos</td>
        <td bgcolor="#999999">Cuota</td>
        <td bgcolor="#999999">Dscto</td>
        <td bgcolor="#999999">SALDO</td>
        <td bgcolor="#999999">Dcmto</td>
        <td bgcolor="#999999">F. Canc.</td>
      </tr>
      </thead>
    <tbody>
      <tr>
        <td colspan="2"><?php echo $cli['PRO_DESCRIPCION']?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      
      <tr>
        <td colspan="2">MONTO CAPITALIZADO: <br /></td>
        <td>0.00</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2">INICIAL:</td>
        <td><?php echo number_format($cli['CTT_INICIAL'],2);?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
<?php

$SQL="SELECT CLI_CODIGO
			,CLI_DESCRIPCION
			,CLI_DIRECCION
			,CLI_TLF_1
			,CLI_TLF_2
			,CLI_TLF_3
			,CTT_COD_TD
			,CTT_CODIGO
			,CTT_FECHA
			,CONVERT(varchar,DEU_FECHA, 103) as DEU_FECHA
			,DEU_FECHA_VENCE
			,DEU_MONTO
			,CASE WHEN DEU_CANC_TOTAL = 1 THEN 0 ELSE DEU_INTERES_FINANCIAMIENTO+DEU_INTERES_FINANCIAMIENTO_2 END  as INTERES
			,CASE WHEN DEU_CANC_TOTAL = 1 THEN 0 ELSE DEU_INTERES_FINANCIAMIENTO_1 END  as GA
			,DEU_COD_TIPO_DOCUMENTO
			,DEU_NUMERO
			,DEU_NRO_CUOTA
			,DEU_INTERES
			,DEU_ALICUOTA
			,CONVERT(varchar,DEU_FECHA_CANC, 103)  AS DEU_FECHA_CANC
			,DEU_SERIE
			,DEU_COD_SERVICIO
			,PRO_DESCRIPCION
			,CTT_OMITIR_INTERES
			,DEU_GASTO_COB+DEU_GASTO_ADM+DEU_GASTO_MNJ as GASTOS
			,cli_observaciones
			,CTT_INICIAL
			,CTT_COD_TIPO_DOCUMENTO
			,CTT_NUMERO
			,PLN_COD_SERVICIO
			,CTT_ANULADO
			,DEU_REFINANCIADA
			,CTT_CAPITALIZA
			,DEU_ADICIONAL
			,PLN_CODIGO
			,CTT_FECHA_1_CUOTA
			,CTT_PARCELAS
			,DEU_DSCTO_PROM
			FROM PRO_CONTRATOS_DEUDAS, PRO_CONTRATOS, MAE_CC_CLIENTES, MAE_MATERIALES, PRO_PLANES_VENTA
			WHERE CTT_COD_TD  = DEU_COD_TD
			  AND CTT_CODIGO  = DEU_COD_CONTRATO
			  AND CLI_CODIGO  = CTT_COD_CLIENTE
			  AND PLN_CODIGO  = CTT_COD_PLAN
			  AND PRO_CODIGO  = DEU_COD_SERVICIO 
			  AND CTT_COD_TD   ='".$sp[0]."'
			  AND CTT_CODIGO='".$sp[1]."' 
			  AND CTT_RECIBO  =0
			  AND PLN_COD_SERVICIO = DEU_COD_SERVICIO
			  AND DEU_REFINANCIADA = 0
			  AND CTT_ANULADO =0
			ORDER BY CTT_COD_TD,CTT_CODIGO,DEU_COD_SERVICIO,DEU_FECHA_VENCE "; 	
		
	$rsx=mssql_query($SQL,$ms_db);  
	$INTERES_F=0;
	$CUOTA=0;
	$SALDO=0;
	$CAPITAL_C=$cli['CTT_INICIAL'];
	$CAPITAL_P=0;
	$INTERES_C=0;
	$INTERES_P=0;
	while($row=mssql_fetch_assoc($rsx)){
		$detalle=getReciboOfPayment($ms_db,$row['DEU_COD_TIPO_DOCUMENTO'],$row['DEU_NUMERO']);  
		$INTERES_F=$INTERES_F+$row['INTERES'];
		$CUOTA=$CUOTA + ($row['DEU_MONTO']+$row['INTERES']);
		$SALDO=$SALDO + (count($detalle)>1?0:($row['DEU_MONTO']+$row['INTERES']));
		
		if (count($detalle)>1){
			$CAPITAL_C=$CAPITAL_C+$row['DEU_MONTO'];
			$INTERES_C=$INTERES_C+$row['INTERES'];
		}else{
			$CAPITAL_P=$CAPITAL_P+$row['DEU_MONTO'];
			$INTERES_P=$INTERES_P+$row['INTERES'];
		}
?>
      <tr>
        <td><?php echo "Cuota ".$row['DEU_NRO_CUOTA'];?></td>
        <td height="16"><?php echo $row['DEU_FECHA'];?></td>
        <td><?php echo number_format($row['DEU_MONTO'],2);?></td>
        <td><?php echo $row['INTERES'];?></td>
        <td><?php echo number_format($row['GA'],2);?></td>
        <td><?php echo number_format($row['GASTOS'],2);?></td>
        <td><?php echo number_format(($row['DEU_MONTO']+$row['INTERES']),2);?></td>
        <td><?php echo number_format($row['DEU_DSCTO_PROM'],2);?></td>
        <td><?php echo count($detalle)>1?'0.00':number_format(($row['DEU_MONTO']+$row['INTERES']),2);?></td>
        <td><?php echo $detalle['VTA_TIPO_DOC_APLICA']." ". $detalle['VTA_NRO_DOC_APLICA'];?></td>
        <td><?php echo $row['DEU_FECHA_CANC'];?></td>
      </tr>
<?php } ?>
</tbody>
<tfoot>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><?php echo number_format($INTERES_F,2);?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><?php echo number_format($CUOTA,2);?></td>
        <td>&nbsp;</td>
        <td><?php echo number_format($SALDO,2);?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
  </tfoot>    
    </table></td>
  </tr>
  <tr>
    <td colspan="2"><table width="350" border="1" style="border-spacing:0px;font-size:12px;width:300px;"  class="table table-hover"   >
    <thead>
      <tr  style="background-color:#999;height:20px;">
        <td width="80"><strong>RESUMEN</strong></td>
        <td width="60" align="right"><strong>CANCELADO</strong></td>
        <td width="54" align="right"><strong>PENDIENTE</strong></td>
        <td width="50" align="right"><strong>TOTAL</strong></td>
      </tr>
     </thead>
     <tbody> 
      <tr >
        <td><strong>CAPITAL</strong></td>
        <td align="right"><?php echo number_format($CAPITAL_C,2);?></td>
        <td align="right"><?php echo number_format($CAPITAL_P,2);?></td>
        <td align="right"><?php echo number_format($CAPITAL_C+$CAPITAL_P,2);?></td>
      </tr>
      <tr >
        <td><strong>INTERESES</strong></td>
        <td align="right"><?php echo  number_format($INTERES_C,2);?></td>
        <td align="right"><?php echo  number_format($INTERES_P,2);?></td>
        <td align="right"><?php echo number_format($INTERES_C+$INTERES_P,2);?></td>
      </tr>
      <tr >
        <td><strong>GASTOS</strong></td>
        <td align="right">0</td>
        <td align="right">0</td>
        <td align="right">0</td>
      </tr>
      </tbody>
      <tfoot>
      <tr >
        <td><strong>TOTALES</strong></td>
        <td align="right"><?php echo number_format($CAPITAL_C+$INTERES_C,2);?></td>
        <td align="right"><?php echo number_format($CAPITAL_P+$INTERES_P,2);?></td>
        <td align="right"><?php echo number_format($CAPITAL_C+$CAPITAL_P+$INTERES_C+$INTERES_P,2);?></td>
      </tr>
      </tfoot>
    </table></td>
  </tr>
</table>
