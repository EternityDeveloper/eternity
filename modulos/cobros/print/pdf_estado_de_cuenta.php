<?php
if (!isset($protect)){
	exit;
}	


if (!isset($_REQUEST['id'])){
	exit;
}	

$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
if (!isset($contrato->serie_contrato)){
	exit;
}	


SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
SystemHtml::getInstance()->includeClass("client","PersonalData");
SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
SystemHtml::getInstance()->includeClass("caja","Caja"); 
SystemHtml::getInstance()->includeClass("estructurac","Asesores"); 
SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
SystemHtml::getInstance()->includeClass("client","PersonalData");
SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
SystemHtml::getInstance()->includeClass("caja","Caja"); 
SystemHtml::getInstance()->includeClass("estructurac","Asesores");


$con=new Contratos($protect->getDBLink()); 


$capita_interes=$con->getCapitalInteresCuotaFromContrato($contrato->serie_contrato,$contrato->no_contrato);	
 
$saldo_inicial=($capita_interes->capital_cancelado+$capita_interes->capital_pendiente+$capita_interes->interes_pagado+$capita_interes->interes_pendiente);

 

 
SystemCache::GI()->doCacheName("detalle_".$contrato->serie_contrato.$contrato->no_contrato); 
$cache=SystemCache::GI()->getCache();
 
$direccion=""; 
	
	$cobros= new Cobros($protect->getDBLINK()); 
	$cobros->session_restart();
	
	$caja= new Caja($protect->getDBLINK());
	$caja->session_restart(); 
	$caja->setObject($contrato);
	
	$con=new Contratos($protect->getDBLink()); 
	$person= new PersonalData($protect->getDBLink(),$_REQUEST);
 
	$cdata=$con->getInfoContrato($contrato->serie_contrato,$contrato->no_contrato);
	if (count($cdata)<=0){
		exit;	
	}	
	$ofi_moto=Cobros::getInstance()->getCobradorMotorizadoAreaC($cdata->serie_contrato,$cdata->no_contrato);
	$peron_data=$person->getClientData($cdata->id_nit_cliente);

	$addressData=$person->getAddress($cdata->id_nit_cliente);	

	foreach($addressData as $key=>$val){  
		$val=(array)$val; 
		$direccion=$val['provincia'].", ".$val['municipio'] .", ".$val['ciudad'] .", ".$val['sector'];
		$direccion.=trim($val['avenida'])!=""?",".$val['avenida']:'';
		$direccion.=trim($val['calle'])!=""?",".$val['calle']:'';
		$direccion.=trim($val['zona'])!=""?",".$val['zona']:'';
		$direccion.=trim($val['manzana'])!=""?",".$val['manzana']:'';
		$direccion.=trim($val['numero'])!=""?",".$val['numero']:'';
		$direccion.=trim($val['referencia'])!=""?",".$val['referencia']:'';
		$direccion.=trim($val['observaciones'])!=""?",".$val['observaciones']:''; 
		if ($val->tipo=="Cobro"){
			break;	
		}
	} 
	
	
	$listContract=$con->getContractListFromPerson($cdata->id_nit_cliente);	
	//$capita_interes=$con->getCapitalInteresCuotaFromContrato($contrato->serie_contrato,$contrato->no_contrato);	

	$ase= new Asesores($protect->getDBLINK());
	$asesor_data=$ase->getComercialParentData($cdata->codigo_asesor);
	 
 
$data=$con->getDetalleGeneralFromContrato($contrato->serie_contrato,$contrato->no_contrato);
$tasa_cambio=$caja->getTasaActual($cdata->tipo_moneda);	

$product=$con->getDetalleProductsFromContrato($contrato->serie_contrato,$contrato->no_contrato);
$servicios=$con->getDetalleServicioFromContrato($contrato->serie_contrato,$contrato->no_contrato);


$addressData=$person->getAddress($cdata->id_nit_cliente);
$phoneData=$person->getPhone($cdata->id_nit_cliente);
  
$phone="";
foreach($phoneData as $key=>$val){   
	$val=(array)$val;
	$phone.=$val['area'].$val['numero']; 
	if ($val->tipo==2){
		$phone.=" Ext.".$val['extencion'];
	}
	$phone.=", ";  
}
$phone=substr($phone,0, strlen($phone)-2); 

	$id_nit=System::getInstance()->Encrypt($cdata->id_nit_cliente);


  
?>
<page  orientation="P" backcolor="#FFFFFF" style="font: arial;">
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="600" border="0" cellpadding="0" cellspacing="0" style="font-size:9px">
      <tr>
        <td height="20" align="center" bgcolor="#CCCCCC" style="font-size: 9px; font-weight: bold;">NUMERO DE DOCUMENTO</td>
        <td height="20" align="left"  style="font-size:9px"><?php echo $peron_data['id_nit'];?></td>
      </tr>
      <tr>
        <td width="100" height="20" align="center" bgcolor="#CCCCCC" style="font-size: 9px; font-weight: bold;"><span style="font-size:9px;"><strong>CLIENTE:</strong></span></td>
        <td width="100" height="20" align="left"  style="font-size:9px"><?php echo utf8_encode($peron_data['primer_nombre']." ".$peron_data['segundo_nombre']." ".$peron_data['primer_apellido']." ".$peron_data['segundo_apellido']);?></td>
        </tr>
      <tr>
        <td width="100" height="20" align="center"  bgcolor="#CCCCCC" ><span style="font-size: 9px; font-weight: bold;"><strong>DIRECCION COBRO:</strong></span></td>
        <td align="left"><?php echo $direccion;?></td>
      </tr>
      <tr>
        <td width="100" height="20" align="center"  bgcolor="#CCCCCC" ><span style="font-size: 9px; font-weight: bold;"><strong>TELEFONO:</strong></span></td>
        <td align="left"><?php echo $phone;?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="800" border="0" cellpadding="0" cellspacing="0" style="font-size:9px">
      <tr>
        <td width="100" height="20" align="center" bgcolor="#CCCCCC" style="font-size: 9px; font-weight: bold;"><span style="font-size: 9px">PRODUCTO</span></td>
        <td width="150" height="20" align="center" bgcolor="#CCCCCC" style="font-size:9px"><span style="font-size:9px;"><strong>NRO. CONTRATO</strong></span></td>
        <td width="120" align="center" bgcolor="#CCCCCC" style="font-size:9px"><span style="font-size:9px;"><strong> INICIAL</strong></span></td>
        <td width="100" align="center" bgcolor="#CCCCCC" style="font-size:9px"><span style="font-size:9px;"><strong>PAGO MINIMIO</strong></span></td>
        <td width="100" height="20" align="center" bgcolor="#CCCCCC" style="font-size:9px"><span style="font-size:9px;"><strong>SALDO INICIAL</strong></span></td>
        <td width="100" height="20" align="center" bgcolor="#CCCCCC" style="font-size:9px"><span style="font-size:9px;"><strong>SALDO VENCIDO</strong></span></td>
      </tr>
      <tr>
        <td align="center"><?php echo  $cdata->producto?>&nbsp;</td>
        <td align="center"><span style="margin:0px;font-size:9px"><?php echo $contrato->serie_contrato ." ".$contrato->no_contrato;?></span></td>
        <td align="center"><span style="margin:0px;font-size:9px"><?php echo number_format($capita_interes->INICIAL,2);?></span></td>
        <td align="center"><span style="margin:0px;font-size:9px"><?php echo number_format($cdata->valor_cuota,2);?></span></td>
        <td align="center"><span style="margin:0px;font-size:9px"><?php echo number_format($saldo_inicial,2);?></span></td>
        <td align="center">&nbsp;</td>
      </tr>
    </table>
      <br>
      <table border="0" style="font-size:9px;">
        <thead>
          <tr  >
            <td width="80" height="20" align="center" bgcolor="#CCCCCC"><strong>FECHA</strong></td>
            <td width="80" height="20" align="center" bgcolor="#CCCCCC"><strong>TRANSACCION</strong></td>
            <td width="80" height="20" align="center" bgcolor="#CCCCCC"><strong> RECIBO CAJA</strong></td>
            <td width="80" height="20" align="center" bgcolor="#CCCCCC"><strong>CAPITAL</strong></td>
            <td width="80" height="20" align="center" bgcolor="#CCCCCC"><strong>INTERES</strong></td>
            <td width="80" height="20" align="center" bgcolor="#CCCCCC"><strong>CARGO</strong></td>
            <td width="80" height="20" align="center" bgcolor="#CCCCCC"><strong>ABONO</strong></td>
            <td width="80" height="20" align="center" bgcolor="#CCCCCC"><strong>SALDO</strong></td>
          </tr>
        </thead>
        <tbody>
          <?php

$SQL="SELECT 
				movimiento_contrato.*,
				caja.DESCRIPCION_CAJA AS CAJA ,
				`tipo_documento`.`DOCUMENTO`,
				tipo_movimiento.DESCRIPCION AS MOVIMIENTO,
				CONCAT(movimiento_caja.SERIE,' ',movimiento_caja.NO_DOCTO) AS RECIBO
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
 			AND movimiento_caja.anulado='N' 
		order by  movimiento_caja.FECHA asc  ";
  
$rs=mysql_query($SQL);
$estado_de_cuenta=array(); 
$interes=0;
$capital=0;
$total_mov=0;
while($row=mysql_fetch_assoc($rs)){   
	//$row['CAPITAL_PAG']=$row['CAPITAL_PAG']+$row['TOT_ABONOS'];
	if ($row['TIPO_MOV']!="INI"){ 
		if ($row['TIPO_MOV']=="ABO"){ 
			$row['CAPITAL_PAG']=$row['TOT_ABONOS'];
		}
		$row['TOTAL_MOV']=$row['CAPITAL_PAG']+$row['INTERESES_PAG'];
	} 
	if (($row['ID_CAMBIOS_FINANCIEROS']>0) && ($row['TIPO_MOV']!="INI")){
		$row['TOTAL_MOV']=$row['CAPITAL_PAG'];
		$row['INTERESES_PAG']=0;
	}
        if ($row['TIPO_MOV']=="CTI"){
                $row['TOTAL_MOV']=$row['MONTO_DOC'];
                $row['INTERESES_PAG']=0;
           }
	
	if ($row['ANULADO']=="S"){ 
		$cp=(array)$row;
		$cp['ANULADO']="N";
		$row['FECHA']=substr($cp['ANULADO_DATE'],0,10);
		array_push($estado_de_cuenta,$cp); 
	} 
 
	$interes=$interes+$row['INTERESES_PAG'];
	$capital=$capital+$row['CAPITAL_PAG']; 
	$total_mov=$total_mov+$row['TOTAL_MOV'];
	array_push($estado_de_cuenta,$row); 
}	 

 
usort($estado_de_cuenta, 'cb'); 
 
function cb($a, $b) {
    return strtotime($a['FECHA']) - strtotime($b['FECHA']);
}



foreach($estado_de_cuenta as $key=>$row){  
	$row=(array)$row;

	if (($row['ANULADO']=="S") || ($row['TIPO_MOV']=="NC")){
		$saldo_inicial=$saldo_inicial+$row['TOTAL_MOV'];
		$debito = $row['TOTAL_MOV'];
		$credito = 0;
	}else{
 		$saldo_inicial=$saldo_inicial-$row['TOTAL_MOV']; 
		$credito= $row['TOTAL_MOV'];	
		$debito=0;	
	}
 
/*	
	if ($row['ANULADO']=="S"){
		$saldo_inicial=$saldo_inicial+$row['TOTAL_MOV'];
		$credito= $row['TOTAL_MOV'];
		$debito=0;
	}else{
 		$saldo_inicial=$saldo_inicial-$row['TOTAL_MOV']; 
		$debito= $row['TOTAL_MOV'];	
		$credito=0;	
	}*/
?>
          <tr  >
            <td height="30" align="center"><?php echo $row['FECHA']?></td>
            <td align="center"><?php echo $row['ANULADO']=="S"?'Anulado '.$row['MOVIMIENTO']:$row['MOVIMIENTO'];?></td>
            <td align="center"><?php echo $row['RECIBO'];?></td>
            <td align="center"><?php echo number_format($row['CAPITAL_PAG']+$row['TOT_ABONOS'],2);?></td>
            <td align="center"><?php echo number_format($row['INTERESES_PAG'],2)?></td>
            <td align="center"><?php echo number_format($debito,2)  ?></td>
            <td align="center"><?php echo number_format($credito,2); ?></td>
            <td align="center"><?php echo number_format($saldo_inicial,2)?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><table width="300" cellpadding="2" style="border-spacing:0px;font-size:9px;">
          <tr  >
            <td width="97" height="20" bgcolor="#CCCCCC" style="padding-right:10px;"><strong>RESUMEN</strong></td>
            <td width="47" align="right" bgcolor="#CCCCCC" style="padding-right:10px;"><strong>TOTAL</strong></td>
            <td width="65" height="20" align="right" bgcolor="#CCCCCC" style="padding-right:10px;"><strong>CANCELADO</strong></td>
            <td width="63" height="20" align="right" bgcolor="#CCCCCC" style="padding-right:10px;"><strong>PENDIENTE</strong></td>
            </tr>
          <tr >
            <td width="97"><strong>CAPITAL </strong></td>
            <td align="right"><?php echo  number_format($capita_interes->capital_cancelado+$capita_interes->capital_pendiente,2)?></td>
            <td align="right"><?php 
 							echo number_format($capita_interes->capital_cancelado,2);							
							?></td>
            <td align="right"><?php 
 							echo number_format($capita_interes->capital_pendiente,2);							
							?></td>
            </tr>
          <tr >
            <td><strong>INTERESES </strong></td>
            <td align="right"><?php echo  number_format($capita_interes->interes_pagado+$capita_interes->interes_pendiente,2)?></td>
            <td align="right"><?php echo  number_format($capita_interes->interes_pagado,2)?></td>
            <td align="right"><?php echo  number_format($capita_interes->interes_pendiente,2)?></td>
            </tr>
          <tr >
            <td><strong>CUOTAS DE MANT.</strong></td>
            <td align="right">0</td>
            <td align="right">0</td>
            <td align="right">0</td>
            </tr>
          <tr >
            <td><strong>TOTALES</strong></td>
            <td align="right"><?php echo  number_format($capita_interes->capital_cancelado+$capita_interes->capital_pendiente+$capita_interes->interes_pagado+$capita_interes->interes_pendiente,2)?></td>
            <td align="right"><?php echo  number_format($capita_interes->capital_cancelado+$capita_interes->interes_pagado,2)?></td>
            <td align="right"><?php echo  number_format($capita_interes->capital_pendiente+$capita_interes->interes_pendiente,2)?></td>
            </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
</page>
<?php 
exit;
    $content = ob_get_clean();
 
    require_once('class/lib/pdf/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', 0);
        $html2pdf->pdf->SetDisplayMode('fullpage');
	    $x=$html2pdf->pdf->addTTFfont("/fonts/dotmatri.ttf", '', '', 32);
		$html2pdf->pdf->setFont('dotmatri');
        $html2pdf->writeHTML($content,"");
        $html2pdf->Output('recibo_venta_'.$pago->reporte_venta.'.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>
