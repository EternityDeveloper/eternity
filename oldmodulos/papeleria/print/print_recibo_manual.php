<?php
if (!isset($protect)){
	exit;
}	


if (!isset($_REQUEST['id'])){
	exit;
}	


$recibo=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));

if (count($recibo)==0){
	exit;
}	  
///$bar_code_url=file_get_contents("http://".$_SERVER['SERVER_NAME'].$folder.'barcode.php?code='.$recibo->SERIE."-".$recibo->NO_DOCTO);
 	
 ?>
<style>
body{
	font-size:12px;	
}
</style>
<page format="100x210" orientation="L" backcolor="#FFFFFF" style="font: arial;">
<?php
foreach($recibo as $key =>$row){
	for($i=0;$i<=1;$i++){
 ?>
<table width="600" border="0" cellpadding="5" cellspacing="0" style="border: solid 1px #666;" >
  <tr>
    <td><table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td colspan="3" align="center" style="font-size:19px;"><strong>SERVICIOS MEMORIALES DOMINICANOS, S.R.L.</strong></td>
        </tr>
      <tr>
        <td width="500" valign="top"><table border="0" cellpadding="0" cellspacing="0" style="font-size:11px;" >
          <tr>
            <td align="center" >Oficina Comercial</td>
          </tr>
          <tr>
            <td width="450" align="center" style="font-size:9px">Av. 27 de Febrero No. 444 (Entre Av. Privada y Nuñez de Caceres),
              Santo Domingo Norte, Santo Domingo<br>
              * Telefono: 809.531.1091</td>
          </tr>
          <tr>
            <td></td>
          </tr>
          <tr>
            <td align="center">Parque Cementerio</td>
          </tr>
          <tr>
            <td align="left"  width="450" style="font-size:9px;"><p style="text-align:center;margin:0;padding:0px;">Ave. Jacobo Majluta, Santo Domingo Norte, R. D. * Tel. 809.683.2200</p></td>
          </tr>
        </table></td>
        <td width="100" align="right" valign="top" >&nbsp;</td>
        <td width="100" align="right" valign="top"><table width="150" border="0" align="right" cellpadding="0"  cellspacing="0">
          <tr>
            <td width="30"  style="font-size:12px"><strong>NO.</strong></td>
            <td width="154" align="left" style="font-size:22px"><?php echo $row;?></td>
            </tr>
          <tr>
            <td  style="font-size:12px"><strong>Fecha:</strong></td>
            <td style="font-size:12px">________________</td>
            </tr>
          <tr>
            <td colspan="2" >&nbsp;</td>
            </tr>
        </table></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td width="100" height="20" style="font-size:12px;border-bottom:#333 solid 1px;"><table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="60"><strong>Nombre:</strong></td>
          <td width="400">&nbsp;</td>
          <td width="50"><strong>Cedula/Pasaporte:</strong></td>
          <td width="50">&nbsp;</td>
        </tr>
    </table></td>
    </tr>
  <tr>
    <td width="100" height="20" style="font-size:12px;border-bottom:#333 solid 1px;"> <strong>Direccion:</strong></td>
    </tr>
  <tr>
    <td width="100" height="20" style="font-size:12px;border-bottom:#333 solid 1px;"><strong>Telefono:</strong></td>
    </tr>
  <tr>
    <td width="100" height="20"  style="font-size:12px;border-bottom:#333 solid 1px;"><table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="60"><strong>Monto:</strong></td>
          <td width="500">&nbsp;</td>
          <td width="50"><strong>RD$:</strong></td>
          <td width="50">&nbsp;</td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td width="100" height="20" style="font-size:12px;border-bottom:#333 solid 1px;"><strong>Concepto:</strong></td>
    </tr>
  <tr>
    <td width="100" height="20" style="font-size:12px;border-bottom:#333 solid 1px;"><strong>Observaciones:</strong></td>
    </tr>
  <tr>
    <td><table border="0" cellpadding="0" cellspacing="0" >
      <tr>
        <td align="center" valign="middle"><table border="0" cellspacing="0" cellpadding="0" style="border: solid 1px #666;font-size:9px;">
          <tr>
            <td  style="padding-left:2px;"	><table width="100" border="0" cellspacing="0" cellpadding="0">
              <tr >
                <td valign="middle" style="padding-top:2px;"><table width="70" border="0" align="left" cellpadding="0"  cellspacing="0" style="margin-right:2px;">
                  <tr>
                    <td width="15" height="15"><img src="images/unchecked_checkbox.png" width="20" height="20" /></td>
                    <td align="left" valign="middle" style="font-size:14px;font-size:12px;padding-right:5px;">Efectivo </td>
                  </tr>
                </table></td>
                <td valign="middle"><table width="70" border="0" align="left" cellpadding="0"  cellspacing="0"  style="margin-right:2px;">
                  <tr>
                    <td width="15" height="15"  ><img src="images/unchecked_checkbox.png" alt="" width="20" height="20" /></td>
                    <td align="left" valign="middle" style="font-size:14px;font-size:12px;padding-right:5px;">Depósito</td>
                  </tr>
                </table></td>
                <td valign="middle"><table width="60" border="0" align="left" cellpadding="0"  cellspacing="0">
                  <tr>
                    <td width="15" height="15" ><img src="images/unchecked_checkbox.png" alt="" width="20" height="20" /></td>
                    <td align="left" valign="middle" style="font-size:14px;font-size:12px;padding-right:5px;">Tarjeta</td>
                  </tr>
                </table></td>
                <td valign="middle">&nbsp;</td>
              </tr>
            </table></td>
          </tr>
          
          <tr >
            <td  style="padding-left:2px;font-size:12px;"><table width="60" border="0" align="left" cellpadding="0"  cellspacing="0">
              <tr>
                <td width="15" height="15" ><img src="images/unchecked_checkbox.png" alt="1" width="20" height="20" /></td>
                <td align="left" valign="middle" style="font-size:14px;font-size:12px;padding-right:5px;"><span style="font-size:14px;font-size:12px;">Cheque</span></td>
              </tr>
            </table></td>
          </tr>
          <tr >
            <td  style="padding-left:2px;font-size:12px;">Cuenta No._________________________</td>
          </tr>
          <tr>
            <td  style="padding-left:2px;font-size:12px;">Banco:_____________________________</td>
          </tr>
        </table></td>
        <td align="left" style="padding-left:10px;"><table width="250" border="0" cellspacing="0" cellpadding="0" style="border: solid 1px #666;">
          <tr >
            <td width="250"  style="padding:5px;font-size:11px;"><p style="text-align:justify">Si el monto recibido corresponde a un abono a inicial, el mismo es válido hasta por treinta (30) días a partir de la fecha del recibo para ser completado. Después de dicha fecha se pierde el derecho de reserva. No se realizarán devoluciones.</p></td>
          </tr>
        </table></td>
        <td align="left" valign="bottom" style="padding-left:20px;"><table width="100%" border="0" cellpadding="0" cellspacing="0" id="1" >
          <tr >
            <td  style="font-size:12px;border-bottom:solid 1px;">&nbsp;</td>
          </tr>
          <tr >
            <td width="200" align="center"  style="padding:5px;font-size:12px;">Recibido Conforme</td>
          </tr>
        </table></td>
        </tr>
    </table></td>
    </tr>
</table> 
<?php

	}
 } 
 
 ?>
</page>
<?php

    $content = ob_get_clean();
 
    require_once('class/lib/pdf/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', 0);
        $html2pdf->pdf->SetDisplayMode('fullpage');
	    $x=$html2pdf->pdf->addTTFfont("/fonts/dotmatri.ttf", '', '', 32); 
        $html2pdf->writeHTML($content,"");
        $html2pdf->Output('recibo_venta.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>