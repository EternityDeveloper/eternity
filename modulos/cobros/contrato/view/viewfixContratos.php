<?php 
if (!isset($protect)){
	exit;	
}

if (!isset($_REQUEST['id'])){
	exit;
} 

$id_contrato=$_REQUEST['id'];
$contrato=json_decode(System::getInstance()->Decrypt($id_contrato));
$id_nit=$contrato->id_nit;


SystemCache::GI()->doCacheName("detalle_".$contrato->serie_contrato.$contrato->no_contrato); 
$cache=SystemCache::GI()->getCache();
 
$direccion="";

if (isset($contrato->serie_contrato)){
	SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
	SystemHtml::getInstance()->includeClass("contratos","EstadoContrato"); 
	
	SystemHtml::getInstance()->includeClass("client","PersonalData");
	SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	SystemHtml::getInstance()->includeClass("estructurac","Asesores"); 
	
	/*Estados financieros entre otros*/
	$EF=new EstadoContrato($protect->getDBLink()); 
	
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
	$capita_interes=$con->getCapitalInteresCuotaFromContrato($contrato->serie_contrato,$contrato->no_contrato);	

	$ase= new Asesores($protect->getDBLINK());
	$asesor_data=$ase->getComercialParentData($cdata->codigo_asesor);
	 
	
}else{
	exit;	
}

 
//$data=$con->getDetalleGeneralFromContrato($contrato->serie_contrato,$contrato->no_contrato);
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

 
$saldo_inicial=$capita_interes->capital_cancelado+$capita_interes->capital_pendiente+$capita_interes->interes_pagado+$capita_interes->interes_pendiente;

$INICIAL=$EF->getMontosInicial($contrato->serie_contrato,$contrato->no_contrato);
 
	
?>
<div class="modal fade" id="DetalleImprimir" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:1200px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">AJUSTAR CONTRATO</h4>
      </div>
      <div class="modal-body">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tb_detalle fsDivPage">
  <tr>
    <td align="center"><strong> CONTRATO</strong></td>
    <td align="center"><strong>MONEDA</strong></td>
    <td align="center"><strong>CUOTAS</strong></td>
    <td align="center"><strong>% INTERES</strong></td>
    <td align="center"><strong>MONTO INTERES</strong></td>
    <td align="center"><strong>MONTO CAPITALIZADO</strong></td>
    <td align="center"><strong> INICIAL</strong></td>
    <td align="center"><strong> INICIAL RD$</strong></td>
    <td align="center"><strong>COMPROMISO</strong></td>
    <td align="center">&nbsp;</td>
    </tr>
  <tr>
    <td align="center"><span style="margin:0px;"><?php echo $cdata->serie_contrato ." ".$cdata->no_contrato;?></span></td>
    <td align="center"><span style="margin:0px;"><?php echo  $cdata->tipo_moneda;?></span></td>
    <td align="center"><span style="margin:0px;"><?php echo  $cdata->cuotas;?></span></td>
    <td align="center"><span style="margin:0px;"><?php echo number_format($cdata->porc_interes,2);?></span></td>
    <td align="center"><span style="margin:0px;"><?php echo number_format($cdata->interes,2);?></span></td>
    <td align="center"><span style="margin:0px;"><?php echo  number_format($cdata->monto_capitalizado,2);?></span></td>
    <td align="center"><span style="margin:0px;"><?php echo number_format($INICIAL['MONTO'],2);?></span></td>
    <td align="center"><span style="margin:0px;"><?php echo number_format($INICIAL['MONTO_RD'],2);?></span></td>
    <td align="center"><span style="margin:0px;"><?php echo  number_format($cdata->valor_cuota,2);?></span></td>
    <td align="center"><input type="submit" name="ajustar_contrato" id="ajustar_contrato" class="btn btn-primary" value="Ajustar" contrato="<?php echo $_REQUEST['id'];?>" id_nit="<?php echo System::getInstance()->Encrypt($cdata->id_nit_cliente);?>" /></td>
    </tr>
  <tr>
    <td colspan="4" align="center"><input type="submit" name="mode_edit" id="mode_edit" class="btn btn-primary" value="Activar Modo Edicion" contrato="<?php echo $_REQUEST['id'];?>" id_nit="<?php echo System::getInstance()->Encrypt($cdata->id_nit_cliente);?>" /></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><input type="submit" name="cambiar_inicial" id="cambiar_inicial" class="btn btn-primary" value="Cambiar" contrato="<?php echo $_REQUEST['id'];?>" id_nit="<?php echo System::getInstance()->Encrypt($cdata->id_nit_cliente);?>" /></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
</table>
<br>

<div class="fixed-table-container2">
  <div style="background-color:#CCC;height:30px;"> </div>
        <div class="fixed-table-container-inner">
<table class="tb_detalle fsDivPage table-hover" id="tb_tipo_mov_"  border="1" style="font-size:12px;">
  <thead>
    <tr  >
      <td width="276" align="center"><div class="th-inner2"><strong>FECHA</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>TRANSACCION</strong></div></td>
      <td width="276" align="center"><div class="th-inner2" ><strong> DOCUMENTO</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>CAPITAL</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>INTERES</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>DEBITO</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>CREDITO</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>SALDO</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>CAJA</strong></div></td>
      <td width="276" align="center"><div class="th-inner2"><strong>MONTO RD$</strong></div></td>
    </tr>
  </thead>
  <tbody>
    <?php

	$SQL="SELECT 
					*,
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
				 movimiento_caja.TIPO_DOC IN ('RBC','NC','ND','RCA','NDA','NCA')  AND  
				movimiento_caja.`NO_CONTRATO`='".$contrato->no_contrato."' AND 
				movimiento_caja.`SERIE_CONTRATO`='".$contrato->serie_contrato."'   
			order by  movimiento_caja.FECHA desc ";
  
	$rs=mysql_query($SQL);
	$estado_de_cuenta=array();
	while($row=mysql_fetch_assoc($rs)){   
		array_push($estado_de_cuenta,$row);
	}	
 
foreach($estado_de_cuenta as $key=>$row){  
	$row=(array)$row;
	$encriptID=System::getInstance()->Encrypt(json_encode($row)); 
	
	if ($row['ANULADO']=="S"){
		$saldo_inicial=$saldo_inicial+$row['TOTAL_MOV'];
		$credito= $row['TOTAL_MOV'];
		$debito=0;
	}else{
 		$saldo_inicial=$saldo_inicial-$row['TOTAL_MOV']; 
		$debito= $row['TOTAL_MOV'];	
		$credito=0;	
	}
?>
    <tr style="height:30px;">
      <td height="30" align="center"><?php echo $row['FECHA']?></td>
      <td align="center"><?php echo $row['MOVIMIENTO']?></td>
      <td align="center"><?php echo $row['DOCUMENTO']?></td>
      <td align="center"><?php echo number_format($row['CAPITAL_PAG']+$row['TOT_ABONOS'],2);?></td>
      <td align="center"><?php echo number_format($row['INTERESES_PAG'],2)?></td>
      <td align="center"><?php echo number_format($debito,2)  ?></td>
      <td align="center"><?php echo number_format($credito,2); ?></td>
      <td align="center"><?php echo number_format($saldo_inicial,2)?></td>
      <td align="center"><?php echo $row['CAJA']?></td>
      <td align="center">0</tr>
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
    <td><input type="submit" name="eliminar_movimiento" id="eliminar_movimiento" class="btn btn-primary" value="Eliminar movimientos" contrato="<?php echo $_REQUEST['id'];?>" id_nit="<?php echo System::getInstance()->Encrypt($cdata->id_nit_cliente);?>" /></td>
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
        <td align="right"><?php echo  number_format($capita_interes->capital_total,2)?></td>
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
        <td align="right"><?php echo  number_format($capita_interes->capital_total+$capita_interes->interes_pagado+$capita_interes->interes_pendiente,2)?></td>
      </tr>
    </table></td>
  </tr>
</table>     
       
      
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button> 
      </div>
    </div>
  </div>
</div>