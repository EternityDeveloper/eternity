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
SystemHtml::getInstance()->includeClass("caja","Recibos");
$caja= new Caja($protect->getDBLink());	
$rcb=new Recibos($protect->getDBLink());
	
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

$rcb->doMarcarComoImpreso($recibo->SERIE,$recibo->NO_DOCTO); 

$bar_code_url="";//file_get_contents("http://".$_SERVER['SERVER_NAME']."/".$folder.'barcode.php?code='.$recibo->SERIE."-".$recibo->NO_DOCTO);

$client_data=$person->getClientData($recibo->ID_NIT);
 ?>
<page format="80x180" orientation="P" backcolor="#FFFFFF" style="font: arial;">
<style>
@font-face {
    font-family: "Dot Matrix";
    src: url(dotmatri.ttf) format("truetype");
}
.table{
	font-family: "Dot Matrix";	
}
</style>
<div style="width:200px;">
<table width="200" border="0" cellpadding="5" cellspacing="0" >
  <tr>
    <td width="325"><table width="200" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td style="font-size:14px;"><strong>SERVICIOS MEMORIALES DOMINICANOS, S.R.L.</strong></td>
        </tr>
      <tr>
        <td valign="top" style="font-size:12px;"><table width="200" border="0" cellpadding="0" cellspacing="0" style="font-size:16px;" >
          <tr>
            <td style="font-size:12px;">Av. 27 de Febrero No. 444</td>
          </tr>
          <tr>
            <td style="font-size:12px;">(Entre Av. Privada y Nu??ez de Caceres),</td>
          </tr>
          <tr>
            <td style="font-size:12px;">Mirador Norte, Santo Domingo</td>
          </tr>
          <tr>
            <td></td>
          </tr>
          <tr>
            <td style="font-size:12px;">Telefono: 809.683.2200</td>
          </tr>
          <tr>
            <td style="font-size:12px;">Republica Dominicana</td>
          </tr>
          <tr>
            <td style="font-size:12px;">RNC 130-81999-8</td>
          </tr>
          <?php if (trim($recibo->TIPO_DOC_FACTURA)!=""){?>
          <tr>
            <td style="font-size:12px;">NCF: <?php echo $recibo->SERIE_FACTURA.$recibo->NO_DOC_FACTURA; ?></td>
          </tr>
          <?php } ?>
        </table></td>
        </tr>
      <tr>
        <td valign="top">&nbsp;</td>
        </tr>
      <tr>
        <td valign="top"><table width="200" border="0"  cellspacing="0" cellpadding="0">
          <tr>
            <td width="70"  style="font-size:12px"><strong>FECHA:</strong></td>
            <td width="100" style="font-size:12px"><?php echo $recibo->FECHA_DOC;?></td>
          </tr>
          <tr>
            <td style="font-size:12px"><strong>RECIBO:</strong></td>
            <td style="font-size:12px"><?php echo $recibo->SERIE."-".$recibo->NO_DOCTO;?></td>
          </tr>
          <?php if ($recibo->NO_CONTRATO>0){?>
          <tr>
            <td style="font-size:12px"><strong>CONTRATO:</strong></td>
            <td style="font-size:12px"><?php echo $recibo->SERIE_CONTRATO." ".$recibo->NO_CONTRATO;?></td>
          </tr>
          <tr>
            <td style="font-size:12px"><strong>CAJERO:</strong></td>
            <td style="font-size:12px"><?php echo $recibo->id_usuario;?></td>
          </tr>
          <?php } ?>
        </table></td>
        </tr>
      <tr>
        <td valign="top">&nbsp;</td>
      </tr>
      <tr>
        <td valign="top"><span style="font-size:12px"><strong>CLIENTE:</strong></span></td>
        </tr>
      <tr>
        <td valign="top"><span style="font-size:12px"><?php echo utf8_encode($client_data['nombre_completo'])?> - <?php echo $client_data['id_nit'];?></span></td>
        </tr>
      <tr>
        <td valign="top"><strong><span style="font-size: 12px">DIRECCION DE COBRO:</span></strong></td>
        </tr>
      <tr>
        <td valign="top"><?php echo utf8_encode($direccion_cobro);?></td>
      </tr>
      <tr>
        <td valign="top"><span style="font-size: 12px"><strong>TELEFONO:</strong></span></td>
      </tr>
      <tr>
        <td valign="top"><span style="font-size: 12px"> <span style="font-size:12px;"><?php echo $phone;?></span></span></td>
      </tr>
      <tr>
        <td valign="top">&nbsp;</td>
      </tr>
      <tr>
        <td valign="top"><table width="200" border="0" cellspacing="0" cellpadding="0">
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
		
		?>
          <tr>
            <td width="200"  style="font-size:12px;" ><?php echo $DESCRIPCION;?></td>
            <td  style="font-size:12px;">RD$ <?php echo number_format($row['MONTO']*$row['TIPO_CAMBIO'],2);?></td>
          </tr>
          <?php } ?>
        </table></td>
      </tr>
      <tr>
        <td valign="top">&nbsp;</td>
      </tr>
      <tr>
        <td valign="top"><strong>FORMA DE PAGO</strong></td>
      </tr>
      <tr>
        <td valign="top"><table border="0" cellpadding="0" cellspacing="0" style="cursor:pointer">
          <tr>
            <td width="120">TIPO</td>
            <td width="100">MONTO</td>
            <td width="50">REF.</td>
          </tr>
          <?php 
		  SystemHtml::getInstance()->includeClass("caja","Recibos");  
		  $recibos= new Recibos($protect->getDBLINK());  
		  $forma_pago=$recibos->getReciboFormaPago($recibo->SERIE,$recibo->NO_DOCTO);	
//		  echo $recibo->SERIE."-".$recibo->NO_DOCTO;	  
		  foreach($forma_pago as $key=>$fp_row){ ?>
          <tr id="<?php echo $id;?>" class="fpago_evnt">
            <td><?php echo $fp_row['descripcion_pago'];?></td>
            <td><?php echo number_format($fp_row['MONTO']*$fp_row['TIPO_CAMBIO'],2);?></td>
            <td><?php echo $fp_row['AUTORIZACION'];?></td>
          </tr>
          <tr class="fpago_evnt">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <?php } ?>
        </table></td>
      </tr>
      <tr>
        <td valign="top">&nbsp;</td>
      </tr>
      <tr>
        <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="120">&nbsp;</td>
            <td><table width="200" border="0" align="left" cellpadding="0" cellspacing="0" >
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
      
    </table></td>
  </tr>
</table>
</div>
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
		$html2pdf->writeHTML($content,"");
	//	$html2pdf->writeHTML($content,"");		
	//	$html2pdf->pdf->AddPage();
     //   $html2pdf->writeHTML($content,"");
	  //  $html2pdf->writeHTML($content,"");		
        $html2pdf->Output('recibo_venta_'.$pago->reporte_venta.'.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>
