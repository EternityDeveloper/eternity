<?php
if (!isset($protect)){
	exit;
}	

if (isset($_REQUEST['reserva'])){
	 
	$trans=json_decode(System::getInstance()->Decrypt($_REQUEST['pago']));
	  
	SystemHtml::getInstance()->includeClass("caja","Caja");
	$caja= new Caja($protect->getDBLink());	
		
	SystemHtml::getInstance()->includeClass("client","PersonalData");
	$person= new PersonalData($protect->getDBLink());	
	
	SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva"); 
	$_reserva=new Reserva($protect->getDBLink());
	//$data=$caja->searchByReserva($_REQUEST['field']);
	$reserva=System::getInstance()->Decrypt($_REQUEST['reserva']);
	$r_data=$_reserva->getDataReserva($reserva);
//	print_r($r_data);

	 
	//$personData=$person->getClientData($r_data['id_nit']);
	$addressData=$person->getAddress($r_data['id_nit']);
	$phoneData=$person->getPhone($r_data['id_nit']);
		

	$direccion="";
	foreach($addressData as $key=>$val){  
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
	
	
	$phone="";
	foreach($phoneData as $key=>$val){   
		$phone.=$val['area'].$val['numero']; 
		if ($val->tipo==2){
			$phone.=" Ext.".$val['extencion'];
		}
		$phone.=", ";  
	}
	$phone=substr($phone,0, strlen($phone)-2); 

 	$pago_cuota=$caja->getPagoDeUnaReserva($r_data['id_nit'],$r_data['no_reserva'],$trans->SERIE,$trans->NO_DOCTO); 
 
 
} 

 echo "fdsa";
exit; 
?>
<page format="100x210" orientation="L" backcolor="#FFFFFF" style="font: arial;">

<table width="900" border="0" align="center" cellpadding="5" cellspacing="0" >
  <tr>
    <td><table width="900" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td colspan="2" style="font-size:22px;"><strong>SERVICIOS MEMORIALES DOMINICANOS, S.R.L.</strong></td>
      </tr>
      <tr>
        <td width="650" valign="top"><span style="font-size:14px;">Av. 27 de Febrero No. 444 (Entre Av. Privada y Nuñez de Caceres)</span><br>
          Santo Domingo Norte, Santo Domingo<br>
          Telefono 809.683.2200<br>
          Republica Dominicana<br>
          RNC 130-81999-8</td>
        <td width="150" valign="top"><table width="200" border="0"  cellspacing="0" cellpadding="0">
          <tr>
            <td width="70"  style="font-size:12px"><strong>FECHA:</strong></td>
            <td style="font-size:12px"><?php echo $pago_cuota['fecha'];?></td>
          </tr>
          <tr>
            <td style="font-size:12px"><strong>RESERVA:</strong></td>
            <td style="font-size:12px"><?php echo $r_data['no_reserva'];?></td>
          </tr>
          <tr>
            <td  style="font-size:12px">&nbsp;</td>
            <td style="font-size:12px">&nbsp;</td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td style="font-size:12px"><strong>NOMBRE DE CLIENTE:</strong> <?php echo $r_data['id_nit'];?> - <?php echo $r_data['nombre_cliente']?></td>
  </tr>
  <tr>
    <td width="325" height="20px;" style="font-size:12px;" ><table width="900" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><?php echo utf8_encode($direccion);?></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td><span style="font-size: 12px"><strong>TELEFONO:</strong> <span style="font-size:10px;"><?php echo $phone;?></span></span></td>
  </tr>
 
  <tr>
    <td><span style="font-size:12px;"><strong>COMENTARIO</strong></span></td>
  </tr>
  <tr>
    <td >&nbsp;</td>
  </tr>
  <tr>
    <td height="60" valign="top"><table width="900" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td style="font-size:12px;border-bottom:#000 solid 1px;"><strong>DESCRIPCION</strong></td>
        <td width="100" style="font-size:12px;border-bottom:#000 solid 1px;"><strong>PRECIO</strong></td>
      </tr>
 <?php 
 	foreach($r_data['res'] as $key =>$val){
 ?>     
      <tr>
        <td width="670"  style="font-size:12px;" ><?php echo $val['jardin'];?> (<?php echo utf8_encode($pago_cuota['desc_tipo_movimiento']);?>)</td>
        <td  style="font-size:12px;">RD$ <?php echo number_format($pago_cuota['MONTO'],2);?></td>
      </tr>
 <?php } ?>     
    </table></td>
  </tr>
  <tr>
    <td><table width="900" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="380"><table width="300" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="60"  style="font-size:12px;" >CHEQUE No:</td>
            <td  style="font-size:12px;" >&nbsp;</td>
          </tr>
          <tr>
            <td  style="font-size:12px;" >BANCO:</td>
            <td  style="font-size:12px;" >&nbsp;</td>
          </tr>
          <tr>
            <td  style="font-size:12px;" >CANCELADO EL:</td>
            <td  style="font-size:12px;" ><strong>__/__&nbsp;/__&nbsp;&nbsp;</strong></td>
          </tr>
          <tr>
            <td  style="font-size:12px;" >EFECTIVO:</td>
            <td  style="font-size:12px;" >()</td>
          </tr>
        </table></td>
        <td width="380" align="right"><table width="250" border="0" align="right" cellpadding="0" cellspacing="0">
          <tr>
            <td width="80"  style="font-size:12px;" ><strong>Sub-Total:</strong></td>
            <td  style="font-size:12px;" >&nbsp;<?php echo number_format($pago_cuota['MONTO'],2);?></td>
          </tr>
          <tr>
            <td  style="font-size:12px;" ><strong>Descuento:</strong></td>
            <td  style="font-size:12px;" >&nbsp;<?php echo number_format($pago_cuota['DESCUENTO'],2);?></td>
          </tr>
          <tr>
            <td  style="font-size:12px;" ><strong>ITBIS:</strong></td>
            <td  style="font-size:12px;" >&nbsp;&nbsp;0.00</td>
          </tr>
          <tr>
            <td  style="font-size:12px;" ><strong>Total General :</strong></td>
            <td  style="font-size:12px;" >&nbsp;<?php echo number_format($pago_cuota['MONTO'],2);?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td  style="font-size:12px;" ><strong>RECIBE NOMBRE Y FIRMA:</strong> _____________________________________________</td>
  </tr>
</table>
</page>
<?php
 
     $content = ob_get_clean();
 
    require_once('class/lib/pdf/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', 0);
		//DOTMATRI.TTF
        $html2pdf->pdf->SetDisplayMode('fullpage');
		//dotmatri.ttf
 
	    $x=$html2pdf->pdf->addTTFfont(dirname(__FILE__)."/dotmatri.ttf", 'TrueTypeUnicode', '', 32);

        $html2pdf->writeHTML($content,"");
        $html2pdf->Output('recibo_venta_'.$pago->reporte_venta.'.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>
