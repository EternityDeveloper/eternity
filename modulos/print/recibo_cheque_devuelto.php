<?php
if (!isset($protect)){
	exit;
}	


if (!isset($_REQUEST['id'])){
	exit;
}	


$cd=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
 
if (!isset($cd->cd_id)){
	exit;
}	


SystemHtml::getInstance()->includeClass("caja","ChequesDevuelto");
SystemHtml::getInstance()->includeClass("contratos","Contratos");
SystemHtml::getInstance()->includeClass("client","PersonalData");
 
$ct= new Contratos($protect->getDBLink());	
	

$person= new PersonalData($protect->getDBLink());	
 
	
 

//$client_data=$person->getClientData($recibo->ID_NIT);
$SQL="SELECT cd.*,
(SELECT CONCAT(reg.`primer_nombre`,' ',reg.`segundo_nombre`,
' ',reg.`primer_apellido`,' ',reg.segundo_apellido) FROM sys_personas AS reg
 WHERE reg.id_nit=cd.REGISTRADO_POR) AS registrado_por 
FROM `caja_cheque_devuelto` AS cd 	 	
WHERE  id='".$cd->cd_id."'"; 
 
$rs=mysql_query($SQL);
$row=mysql_fetch_assoc($rs);
 


 
?> 
<page >
<table width="335" border="0" align="center" cellpadding="5" cellspacing="0" >
  <tr>
    <td width="325"><table width="900" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td colspan="2" align="right" style="font-size:22px;">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" align="right" style="font-size:22px;">Cheque Devuelto: CD <?php echo $row['ID'];?></td>
        </tr>
      <tr>
        <td width="550" valign="top"><table border="0" cellspacing="0" cellpadding="0" style="font-size:11px;" >
          <tr>
            <td ><span style="font-size:22px;"><strong>SERVICIOS MEMORIALES DOMINICANOS, S.R.L.</strong></span></td>
          </tr>
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
 
          </table></td>
        <td width="150" valign="top"><table width="200" border="0"  cellspacing="0" cellpadding="0">
          <tr>
            <td width="70"  style="font-size:12px"><strong>FECHA:</strong></td>
            <td width="100" style="font-size:12px"><?php echo $row['FECHA_REGISTRO'];?></td>
          </tr>
     
          </table></td>
        </tr>
      <tr>
        <td valign="top">&nbsp;</td>
        <td valign="top">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td valign="top">COMENTARIOS: <?php echo $row['COMENTARIO']?></td>
  </tr>
  <tr>
    <td height="800" valign="top" style="border:#333 solid 1px;padding:0px;margin:0px;">
  
    <table width="910" style="border:#333 solid 1px;">
      <thead>
        <tr>
          <th width="250" align="left" bgcolor="#999999">CLIENTE</th>
          <th width="110" bgcolor="#999999">CONTRATO</th>
          <th width="130" align="left" bgcolor="#999999">NUMERO FACTURA</th>
          <th width="110" bgcolor="#999999">DETALLE</th>
          <th width="100" bgcolor="#999999">MONTO</th>
          </tr>
      </thead>
      <tbody>
        <?php
 $monto_total=0;
$SQL="SELECT *,
(SELECT CONCAT(reg.`primer_nombre`,' ',reg.`segundo_nombre`,
' ',reg.`primer_apellido`,' ',reg.segundo_apellido) FROM sys_personas AS reg
 WHERE reg.id_nit=caja_cheque_devuelto_detalle.id_nit_cliente) AS cliente 
  FROM `caja_cheque_devuelto_detalle` WHERE `id_caja_cheque_devuelto`='".$cd->cd_id."'"; 
$rXs=mysql_query($SQL);
while($rowx=mysql_fetch_assoc($rXs)){ 
 $contrato=System::getInstance()->Encrypt(json_encode(array("serie_contrato"=>$rowx['serie_contrato'],"no_contrato"=>$rowx['no_contrato'],"id_nit"=>$rowx['id_nit_cliente'])));
 	$monto_total=$monto_total+$rowx['monto'];
	
	
$addressData=$person->getAddress($rowx['id_nit_cliente']);
$phoneData=$person->getPhone($rowx['id_nit_cliente']);
	
$direccion_cobro="";
$direccion_residencia=""; 
foreach($addressData as $key=>$val){   
	$direccion=$val['ciudad'] .", ".$val['sector']."<br>";
	$direccion.=trim($val['avenida'])!=""?",".$val['avenida']:'';
	$direccion.=trim($val['calle'])!=""?",".$val['calle']:'';
	$direccion.=trim($val['zona'])!=""?",".$val['zona'] :'';
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
?>
        <tr>
          <td><table width="300" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><?php echo $rowx['cliente'];?>&nbsp;</td>
            </tr>
            <tr>
              <td><span><?php echo $direccion_cobro;?></span></td>
            </tr>
            <tr>
              <td><?php echo $phone;?></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
          </table></td>
          <td valign="top"><?php echo $rowx['serie_contrato']." ".$rowx['no_contrato'];?></td>
          <td valign="top"><?php echo $rowx['serie']." ".$rowx['no_docto'];?></td>
          <td valign="top">CHEQUE DEVUELTO</td>
          <td valign="top"><?php echo number_format($rowx['monto'],2);?></td>
          </tr>
        <?php  
}
 ?>
      </tbody>
    </table>
  
    </td>
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
