<?php ob_start();

if (!isset($protect)){
	echo "Security error!";
	exit;
}
  
$data=json_decode(System::getInstance()->Decrypt($_REQUEST['reserva']));
$pago=json_decode(System::getInstance()->Decrypt($_REQUEST['pago']));
 
if (count($data)<=0){
	echo json_encode(array("error"=>"Error parser JSON"));
	exit;
}

 
if (count($pago)<=0){
	echo json_encode(array("error"=>"Error parser JSON"));
	exit;
}

$user_data=$protect->getUserData();
/*print_r($data);
print_r($pago);

print_r($user_data);*/
?>
<page format="100x210" orientation="L" backcolor="#FFFFFF" style="font: arial;">
<table width="820" border="0" cellpadding="0" cellspacing="0"  style="margin-left:15px;margin-top:20px;">
  <tr>
    <td><table width="820" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="250" align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center"><img src="images/memorial.fw.png" width="222" height="75" /></td>
          </tr>
          <tr>
            <td style="font-size:9px;text-align:center"><p>Servicios Memoriales Dominicanos SMD SRL.<br />
            RNC 130-80999-8</p></td>
          </tr>
        </table></td>
        <td width="250" align="center" style="font-size:16px;"><p><strong>REPORTE DE VENTA</strong></p></td>
        <td width="250" align="right" valign="top"><table width="100" border="0" align="right" cellpadding="0" cellspacing="0">
          <tr>
            <td align="right"><strong>No.</strong></td>
            <td><?php echo $pago->reporte_venta?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
 
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="12%"><strong>Solicitud No.:</strong></td>
        <td width="100"  style="border-bottom:#000 solid 1px;">&nbsp;<?php echo $pago->no_reserva?>&nbsp;</td>
        <td width="15%"><strong>&nbsp;Nombre Cliente</strong>:</td>
        <td width="455"  style="border-bottom:#000 solid 1px;">&nbsp;<?php echo $data->nombre_cliente;?>&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="820" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="17%"><strong>Nombre del asesor:</strong></td>
        <td width="300"  style="border-bottom:#000 solid 1px;">&nbsp;<?php echo $data->nombre_asesor?></td>
        <td width="10%"   ><strong>Supervisor</strong>:</td>
        <td width="250"  style="border-bottom:#000 solid 1px;">&nbsp;</td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="820" border="0" cellspacing="0" cellpadding="0" style="margin-top:5px;">
      <tr>
        <td width="6%"><strong>Fecha:</strong></td>
        <td width="100"  style="border-bottom:#000 solid 1px;">&nbsp;<?php echo $pago->fecha?>&nbsp;</td>
        <td width="5%"><strong>Hora:</strong></td>
        <td width="80"  style="border-bottom:#000 solid 1px;">&nbsp;<?php echo $pago->hora?>&nbsp;</td>
        <td width="17%"  ><strong>Dinero Reportado $</strong>:</td>
        <td width="120"  style="border-bottom:#000 solid 1px;">&nbsp;<?php echo number_format($pago->monto,2);?></td>
        <td width="8%"   ><strong>Modeda:</strong></td>
        <td width="31%"  ><table width="150" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="30px;" style="border:#000 solid 2px;">&nbsp;</td>
                <td>&nbsp;<strong>RD$&nbsp;&nbsp;</strong></td>
              </tr>
            </table></td>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="30px;" style="border:#000 solid 2px;">&nbsp;</td>
                <td>&nbsp;<strong>US$</strong></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="9%"><strong>Concepto</strong>:</td>
        <td width="91%" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><table width="100" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="30px;" style="border:#000 solid 2px;">&nbsp;</td>
                <td>&nbsp;<strong>Incial&nbsp;&nbsp;</strong></td>
              </tr>
            </table></td>
            <td><table width="160" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="30px;" style="border:#000 solid 2px;">&nbsp;</td>
                <td>&nbsp;<strong>Anticipo del incial</strong></td>
              </tr>
            </table></td>
            <td><table width="160" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="30px;" style="border:#000 solid 2px;">&nbsp;</td>
                <td>&nbsp;<strong>Abono A Capital</strong></td>
              </tr>
            </table></td>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="30px;" style="border:#000 solid 2px;">&nbsp;</td>
                <td width="150">&nbsp;<strong>Otros, Forma de Pago:</strong></td>
                <td width="40" style="border-bottom:#000 solid 1px;">&nbsp;&nbsp;&nbsp;&nbsp;</td>
              </tr>
            </table></td>
            </tr>
        </table>
        
        </td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="820" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50%" align="center"><table width="300" border="0" cellspacing="0" cellpadding="0" style="margin-left:50px;">
          <tr>
            <td width="300" align="center"  style="border-bottom:#000 solid 1px;">&nbsp;</td>
          </tr>
          <tr>
            <td align="center">Recibido Conforme</td>
          </tr>
        </table></td>
        <td align="center"  ><table width="300" border="0" cellspacing="0" cellpadding="0"  style="margin-left:50px;">
          <tr>
            <td width="300" align="center"  style="border-bottom:#000 solid 1px;"><?php echo $user_data['Nombres']." ". $user_data['Apellidos']; ?></td>
          </tr>
          <tr>
            <td align="center">Cajero(a)</td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
</page>
<?php


     $content = ob_get_clean();
 //../../../../
    require_once('class/lib/pdf/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', 0);
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->writeHTML($content,"");
        $html2pdf->Output('recibo_venta_'.$pago->reporte_venta.'.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>
