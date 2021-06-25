<?php 
if (!isset($protect)){
	exit;	
}

if (isset($_REQUESTP['id'])){
	exit;
}
$solicitud=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
if (!isset($solicitud->id_planilla_gestion)){
	exit;	
}

SystemHtml::getInstance()->includeClass("client","PersonalData");  

SystemHtml::getInstance()->includeClass("contratos","Contratos");  
$con=new Contratos($protect->getDBLink()); 
$cdata=$con->getInfoContrato($solicitud->serie_contrato,$solicitud->no_contrato);

$person= new PersonalData($protect->getDBLink());
$data_p=$person->getClientData($cdata->id_nit_cliente);
 
	$addressData=$person->getAddress($cdata->id_nit_cliente);
	$phoneData=$person->getPhone($cdata->id_nit_cliente);
		

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
 
?>
<page>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
  </table>
  <table border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="175">&nbsp;</td>
      <td width="400" align="center" style="font-size:18px;font-weight: bold;"><b>Planilla de </b><br>
      <b>Solicitud de Gestion</b></td>
      <td width="180"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="58%"  style="background:#CCC"><span style="font-weight:bold;font-size:9pt;background:#CCC;" ><b>Contrato</b></span></td>
          <td width="42%" style="background:#CCC"><?php echo $solicitud->serie_contrato?> <?php echo $solicitud->no_contrato?></td>
        </tr>
        <tr>
          <td><strong>Fecha Solicitud</strong></td>
          <td><?php echo $solicitud->fecha_c?></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <table  cellpadding="0" cellspacing="0" style="border:dotted 1px #000077;border-right:dotted 1px #000077;">
    <tr>
      <td width="700" height="20" align="center" bgcolor="#D1D1D1"><strong>TIPO DE SOLICITUD</strong></td>
    </tr>
    <tr>
      <td><table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="300" valign="top"  style="border-right:dotted 1px #000077;">[<?php echo $solicitud->idtipogestion=="ABCAP"?'X':' ';?>] Abono a capital restante del contrato<br>
[ ] Cambio de monto de cuota o plazo<br>
[ ] Cambio de plan de financiamiento<br>
[ ] Separación de Parcelas en distintos contratos<br>
[ ] Mejora de Producto<br>
[ ] Acuerdo de Pago</td>
          <td width="440" valign="top">[ ] Disminución de parcelas del contrato<br>
[ ] Incremento de parcelas del contrato<br>
[ ] Cambio de titularidad, pagador o actualización de Datos<br>
[ ] Cambio de Ubicación de la(s) parcela(s) o Jardín del contrato<br>
[ ] Cambio de Autorizados<br>
[ ] Otro _______________</td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="20" align="center" bgcolor="#D1D1D1"><strong>DATOS DEL PLAN DE FINANCIAMIENTO</strong></td>
    </tr>
    <tr>
      <td align="center"><table border="0" cellspacing="0" cellpadding="0" align="left" width="744">
        <tr>
          <td width="347"  style="border-right:dotted 1px #000077;">Plan actual_____________________</td>
          <td width="397" height="22">Plan Solicitado_____________________</td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="20" align="center" bgcolor="#D1D1D1"><strong>DATOS MONETARIOS DEL  CONTRATO</strong></td>
    </tr>
    <tr>
      <td><table border="0" cellspacing="0" cellpadding="0" width="744">
        <tr>
          <td width="262" rowspan="2" style="border-right:dotted 1px #000077;"><table width="100%" border="0" cellspacing="0" cellpadding="0" >
            <tr >
              <td width="110">Monto saldado</td>
              <td width="150" style="border-bottom:dotted 1px #000077;">$<?php echo number_format($solicitud->monto_saldo,2);?></td>
            </tr>
            <tr>
              <td width="110">Monto    a Abonar</td>
              <td style="border-bottom:dotted 1px #000077;">$<?php echo number_format($solicitud->monto_abonar,2);?></td>
            </tr>
            <tr>
              <td width="110">Nuevo    Saldo</td>
              <td style="border-bottom:dotted 1px #000077;">$<?php echo number_format($solicitud->precio_neto,2);?></td>
            </tr>
          </table></td>
          <td width="198">Cambiar Plazo</td>
          <td width="284" rowspan="2" valign="top"  style="border-left:dotted 1px #000077;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="140">Nuevo Plazo </td>
              <td width="140" style="border-bottom:dotted 1px #000077;"><?php echo number_format($solicitud->cuotas);?></td>
            </tr>
            <tr>
              <td width="140">Nueva Cuota</td>
              <td style="border-bottom:dotted 1px #000077;">$<?php echo number_format($solicitud->valor_cuota,2);?></td>
            </tr>
            <tr>
              <td width="140">Nueva    cant. Productos </td>
              <td style="border-bottom:dotted 1px #000077;"><?php echo $cdata->no_productos;?></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td width="198">[<?php if ($solicitud->cuotas<$cdata->cuotas){echo "X";}else{echo " ";}?>] Disminuir<br>
            [<?php if ($solicitud->cuotas>$cdata->cuotas){echo "X";}else{echo " ";}?>] Incrementar</td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="20" align="center" bgcolor="#D1D1D1"><strong>ACTUALIZACION DE DATOS DEL PAGADOR (O AUTORIZADO  1)</strong></td>
    </tr>
    <tr>
      <td><table width="740" border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="150"  style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;" ><strong>Cedula</strong></td>
          <td width="600"  style="border-bottom:dotted 1px #000077;" ><?php echo $cdata->id_nit_cliente?></td>
        </tr>
        <tr>
          <td  style="border-right:dotted 1px #000077;border-bottom:dotted 1px #000077;" ><strong>Apellidos y Nombres</strong></td>
          <td  style="border-bottom:dotted 1px #000077;" ><?php echo $data_p['nombre_completo'];?></td>
        </tr>
        <tr>
          <td style="border-right:dotted 1px #000077;border-bottom:dotted 1px #000077;" ><strong>Dirección de Cobro</strong></td>
          <td  style="border-bottom:dotted 1px #000077;" ><?php echo $direccion;?></td>
        </tr>
        <tr>
          <td style="border-right:dotted 1px #000077;border-bottom:dotted 1px #000077;" ><strong>Teléfonos</strong></td>
          <td  style="border-bottom:dotted 1px #000077;" ><?php echo $phone;?></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="20" align="center" bgcolor="#D1D1D1"><strong>ACTUALIZACION DE DATOS DEL TITULAR (O AUTORIZADO  2)</strong></td>
    </tr>
    <tr>
      <td ><table width="740" border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="150"  style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;" ><strong>Cedula</strong></td>
          <td width="600"  style="border-bottom:dotted 1px #000077;" >&nbsp;</td>
        </tr>
        <tr>
          <td  style="border-right:dotted 1px #000077;border-bottom:dotted 1px #000077;" ><strong>Apellidos y Nombres</strong></td>
          <td  style="border-bottom:dotted 1px #000077;" >&nbsp;</td>
        </tr>
        <tr>
          <td style="border-right:dotted 1px #000077;border-bottom:dotted 1px #000077;" ><strong>Dirección de Cobro</strong></td>
          <td  style="border-bottom:dotted 1px #000077;" >&nbsp;</td>
        </tr>
        <tr>
          <td style="border-right:dotted 1px #000077;border-bottom:dotted 1px #000077;" ><strong>Teléfonos</strong></td>
          <td  style="border-bottom:dotted 1px #000077;" >&nbsp;</td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="20" align="center" bgcolor="#D1D1D1"><strong>PRODUCTOS ASOCIADOS  AL CONTRATO</strong></td>
    </tr>
    <tr>
      <td align="center"><table border="0" cellspacing="0" cellpadding="0" align="left">
        <tr >
          <td width="245" style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;"><p><strong>PRODUCTO ACTUAL</strong></p></td>
          <td width="245" style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;"><p><strong>PRODUCTO SOLICITADO</strong></p></td>
          <td width="245" style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;"><p align="center"><strong>COMENTARIOS</strong></p></td>
        </tr>
        <tr >
          <td height="25" style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;">&nbsp;</td>
          <td style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;">&nbsp;</td>
          <td style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;">&nbsp;</td>
        </tr>
        <tr >
          <td height="25" style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;">&nbsp;</td>
          <td style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;">&nbsp;</td>
          <td style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;">&nbsp;</td>
        </tr>
        <tr >
          <td height="25" style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;">&nbsp;</td>
          <td style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;">&nbsp;</td>
          <td style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;">&nbsp;</td>
        </tr>
        <tr >
          <td height="25" style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;">&nbsp;</td>
          <td style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;">&nbsp;</td>
          <td style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;">&nbsp;</td>
        </tr>
        <tr >
          <td height="25" style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;">&nbsp;</td>
          <td style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;">&nbsp;</td>
          <td style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;">&nbsp;</td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="20" align="center" bgcolor="#D1D1D1"><strong>OBSERVACIONES</strong></td>
    </tr>
    <tr>
      <td height="80" align="left" valign="top"><?php echo $solicitud->descrip_general;?></td>
    </tr>
    <tr>
      <td height="20" align="center" bgcolor="#D1D1D1"><strong>FIRMAS DE ACEPTACION  CONFORME</strong></td>
    </tr>
    <tr>
      <td align="center"><table border="0" cellspacing="0" cellpadding="0" align="left">
        <tr >
          <td >&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <td >&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <td width="185" >_______________________</td>
          <td width="185">_______________________</td>
          <td width="185">_______________________</td>
          <td width="185">_______________________</td>
        </tr>
        <tr >
          <td height="25" align="center"><strong>Titular del Contrato</strong></td>
          <td align="center" ><strong>Atencion al Cliente</strong></td>
          <td align="center"><strong>Aprobado Por</strong></td>
          <td align="center"><strong>Procesado Por</strong></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td align="center">&nbsp;</td>
    </tr>
    <tr>
      <td align="center">&nbsp;</td>
    </tr>
 
  </table> 
</page> 
<?php

     $content = ob_get_clean();
 
    require_once('class/lib/pdf/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'letter', 'fr');
		//DOTMATRI.TTF
        $html2pdf->pdf->SetDisplayMode('fullpage'); 
	    $x=$html2pdf->pdf->addTTFfont(dirname(__FILE__)."/dotmatri.ttf", 'TrueTypeUnicode', '', 32); 
        $html2pdf->writeHTML($content,"");
        $html2pdf->Output("test"."-".date("d-m-Y").'.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>