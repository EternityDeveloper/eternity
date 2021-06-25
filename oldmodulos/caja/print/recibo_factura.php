<?php
if (!isset($protect)){
	exit;
}	


if (!isset($_REQUEST['id'])){
	exit;
}	


$recibo=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));

if (!isset($recibo->SERIE)){
	exit;
}	


SystemHtml::getInstance()->includeClass("caja","Caja");
SystemHtml::getInstance()->includeClass("contratos","Contratos");


$caja= new Caja($protect->getDBLink());	

$ct= new Contratos($protect->getDBLink());	
	
SystemHtml::getInstance()->includeClass("client","PersonalData");
$person= new PersonalData($protect->getDBLink());	

SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva"); 
 
$addressData=$person->getAddress($recibo->ID_NIT);
$phoneData=$person->getPhone($recibo->ID_NIT);
	
$direccion_cobro="";
$direccion_residencia=""; 
foreach($addressData as $key=>$val){   
	$direccion=$val['provincia'].", ".$val['ciudad'] .", ".$val['sector'];
	$direccion.=trim($val['avenida'])!=""?",".$val['avenida']:'';
	$direccion.=trim($val['calle'])!=""?",".$val['calle']:'';
	$direccion.=trim($val['zona'])!=""?",".$val['zona']:'';
	$direccion.=trim($val['manzana'])!=""?",".$val['manzana']:'';
	$direccion.=trim($val['numero'])!=""?",".$val['numero']:'';
	$direccion.=trim($val['referencia'])!=""?",".$val['referencia']:'';
	$direccion.=trim($val['observaciones'])!=""?",".$val['observaciones']:'';
	if ($val['tipo']=="Cobro"){
		$direccion_cobro=$direccion;
	}
	if ($val['id_direcciones']==$recibo->id_direcciones){
		$direccion_cobro=$direccion;
	}
	if ($val['tipo']=="Residencia"){
		$direccion_residencia=$direccion;
	}	
} 

if ($direccion_cobro==""){
	$direccion_cobro=$direccion_residencia;	
}


if ($direccion_residencia==""){
	$direccion_residencia=$direccion_cobro;	
}

$phone="";
foreach($phoneData as $key=>$val){   
	$phone.=$val['area'].$val['numero']; 
	if ($val->tipo==2){
		$phone.=" Ext.".$val['extencion'];
	}
	$phone.=", ";  
}
$phone=substr($phone,0, strlen($phone)-2); 
 
$mc_listado=$caja->getDetalleRecibo($recibo->SERIE,$recibo->NO_DOCTO,$recibo->TIPO_DOC);

$sp=explode("/",$_SERVER['SCRIPT_NAME']);
if (count($sp)>2){
	$folder=$sp[1]."/";
}

 
///$bar_code_url=file_get_contents("http://".$_SERVER['SERVER_NAME'].$folder.'barcode.php?code='.$recibo->SERIE."-".$recibo->NO_DOCTO);

$client_data=$person->getClientData($recibo->ID_NIT);

 
?>
<style>

	@font-face {
		  font-family: "DotShort";
		  src: url(modulos/caja/print/dotmatri.ttf);
		  font-weight:bold;
	}

body{
	font-family:"DotShort",system;
	font-size:18pt;
	line-height:1.6;
}

</style>

<page format="100x210" orientation="L" backcolor="#FFFFFF" style="font: arial;">
<table width="335" border="0" align="center" cellpadding="5" cellspacing="0" >
  <tr>
    <td><table width="900" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td colspan="2" style="font-size:22px;"><strong>SERVICIOS MEMORIALES DOMINICANOS, S.R.L.</strong></td>
        </tr>
      <tr>
        <td width="550" valign="top"><table border="0" cellspacing="0" cellpadding="0" style="font-size:11px;" >
          <tr>
            <td >Av. 27 de Febrero No. 444 (Entre Av. Privada y Nuñez de Caceres), Mirador Norte, Santo Domingo</td>
            </tr>
          <tr>
            <td></td>
            </tr>
          <tr>
            <td>Telefono: 809.683.2200</td>
            </tr>
          <tr>
            <td>Republica Dominicana</td>
            </tr>
          <tr>
            <td>RNC 130-81999-8</td>
            </tr>
          <?php if (trim($recibo->TIPO_DOC_FACTURA)!=""){?>
          <tr>
            <td>NCF: <?php echo $recibo->SERIE_FACTURA.$recibo->NO_DOC_FACTURA; ?></td>
            </tr>
          <?php } ?>
          </table></td>
        <td width="150" valign="top"><table width="200" border="0"  cellspacing="0" cellpadding="0">
          <tr>
            <td width="70"  style="font-size:12px"><strong>FECHA:</strong></td>
            <td width="100" style="font-size:12px"><?php echo $recibo->FECHA_DOC;?></td>
            </tr>
          <tr>
            <td style="font-size:12px"><strong>RECIBO:</strong></td>
            <td style="font-size:12px"><?php echo $recibo->SERIE."-".$recibo->NO_DOCTO;?></td>
            </tr>
          <?php if ($recibo->NO_CONTRATO>0){?>
<!--          <tr>
            <td style="font-size:12px"><strong>CONTRATO:</strong></td>
            <td style="font-size:12px"><?php echo $recibo->SERIE_CONTRATO."-".$recibo->NO_CONTRATO;?></td>
            </tr>-->
          <tr>
            <td style="font-size:12px"><strong>OFICIAL:</strong></td>
            <td style="font-size:12px"><?php echo $recibo->id_usuario;?></td>
            </tr>
          <?php } ?>
          </table></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td style="font-size:12px"><strong>NOMBRE DE CLIENTE:</strong> <?php echo $client_data['id_nit'];?> - <?php echo utf8_encode($client_data['nombre_completo'])?></td>
  </tr>
  <tr>
    <td width="325" height="20" style="font-size:12px;" ><table width="900" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="400"><strong><span style="font-size: 12px">DIRECCION DE RECIDENCIA:</span></strong></td>
          <td><strong><span style="font-size: 12px">DIRECCION DE COBRO:</span></strong></td>
        </tr>
        <tr>
          <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="350"><?php echo utf8_encode($direccion_residencia);?>&nbsp;</td>
            </tr>
          </table></td>
          <td width="340" ><?php echo utf8_encode($direccion_cobro);?></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td><span style="font-size: 12px"><strong>TELEFONO:</strong> <span style="font-size:12px;"><?php echo $phone;?></span></span></td>
  </tr>
  <tr>
    <td height="60" valign="top"><table width="900" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td style="font-size:12px;border-bottom:#000 solid 1px;"><strong>DESCRIPCION</strong></td>
        <td width="100" style="font-size:12px;border-bottom:#000 solid 1px;"><strong>PRECIO</strong></td>
        </tr>
      <?php 
	$monto_total=0; 
 	foreach($mc_listado as $key =>$row){
 		$_reserva=new Reserva($protect->getDBLink());
		$r_data=array();
		$monto_total=$monto_total+($row['MONTO']*$row['TIPO_CAMBIO']);
		$DESCRIPCION=strtoupper($row['MOV']);
		if ($row['NO_RESERVA']>0){
			$r_data=$_reserva->getDataReserva($row['NO_RESERVA']);
			foreach($r_data['res'] as $rk=>$rRow){
				$ubicacion=$ubicacion.$rRow['jardin'].",";
			}
			$ubicacion=substr($ubicacion,0,strlen($ubicacion)-1);
			$DESCRIPCION="PAGO ".strtoupper($row['MOV'])." RESERVA NO.".$row['NO_RESERVA']." (".$ubicacion.")";
		}	
		$ctn=$ct->getInfoContrato($row['SERIE_CONTRATO'],$row['NO_CONTRATO']);
 
		?>     
      <tr>
        <td width="670"  style="font-size:12px;" ><?php echo utf8_encode($DESCRIPCION);?> (<?php echo $ctn->producto; ?>) (<?php echo $row['SERIE_CONTRATO']." ".$row['NO_CONTRATO'];?>)</td>
        <td  style="font-size:12px;">RD$ <?php echo number_format($row['MONTO']*$row['TIPO_CAMBIO'],2);?></td>
        </tr>
      <?php } ?>     
    </table></td>
  </tr>
  <tr>
    <td><table width="900" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="380" valign="bottom"><!--<img src="<?php echo $bar_code_url;?>">--></td>
        <td width="380" align="right"><table width="250" border="0" align="right" cellpadding="0" cellspacing="0">
          <tr>
            <td width="80"  style="font-size:12px;" ><strong>Sub-Total:</strong></td>
            <td  style="font-size:12px;" >&nbsp;<?php echo number_format($monto_total,2);?></td>
            </tr>
          <tr>
            <td  style="font-size:12px;" ><strong>Descuento:</strong></td>
            <td  style="font-size:12px;" >&nbsp;<?php echo number_format(0,2);?></td>
            </tr>
          <tr>
            <td  style="font-size:12px;" ><strong>ITBIS:</strong></td>
            <td  style="font-size:12px;" >&nbsp;&nbsp;0.00</td>
            </tr>
          <tr>
            <td  style="font-size:12px;" ><strong>Total General :</strong></td>
            <td  style="font-size:12px;" >&nbsp;<?php echo number_format($monto_total,2);?></td>
            </tr>
          </table></td>
        </tr>
    </table></td>
  </tr>
</table>
</page>
<?php 
    $content = ob_get_clean();
    require_once('class/lib/pdf/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', 0);
        $html2pdf->pdf->SetDisplayMode('fullpage');
	   // $html2pdf->pdf->addTTFfont("fonts/dotmatri.ttf", '', '', 32);
	   //$html2pdf->pdf->addTTFfont("fonts/dotmatri.ttf", '', '', 32);
		//$html2pdf->pdf->setFont('DotShort');
	//	$html2pdf->pdf->AddTTFFont('fonts/dotmatri.ttf');
	//	$html2pdf->addFont('DotShort','', 'domatri.php');
//		$html2pdf->pdf->setDefaultFont('DotMatrix');
        $html2pdf->writeHTML($content,"");
        $html2pdf->Output('recibo_venta_'.$pago->reporte_venta.'.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>
