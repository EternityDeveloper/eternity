<?php
if (!isset($protect)){
	exit;
}	


$rt=STCSession::GI()->getSubmit("doPrint");
 
 
SystemHtml::getInstance()->includeClass("caja","Caja");
SystemHtml::getInstance()->includeClass("caja","Recibos");
SystemHtml::getInstance()->includeClass("client","PersonalData");

SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva"); 
	

function getAddress($serie_contrato,$no_contrato){
	$SQL="SELECT direccion FROM `sys_direccion_temp` WHERE serie_contrato='".$serie_contrato."' 
		AND no_contrato='".$no_contrato."'   ";
	$rs=mysql_query($SQL);
	 
	$direccion="";
	while($row=mysql_fetch_object($rs)){
		$direccion=$row->direccion;
	}
	return $direccion;
}

$caja= new Caja($protect->getDBLink());	
$rcb=new Recibos($protect->getDBLink());
	
$person= new PersonalData($protect->getDBLink());	
 
foreach($rt as $k_  => $recibo){
	 
	$direccion_referencia=getAddress($recibo->serie_contrato,$recibo->no_contrato);
 
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
	
	$bar_code_url=file_get_contents("http://".$_SERVER['SERVER_NAME']."/".$folder.'barcode.php?code='.$recibo->NO_CODIGO_BARRA);
 
	$client_data=$person->getClientData($recibo->ID_NIT);	
	
	$moto=$person->getClientData($recibo->ID_NIT_MOTORIZADO);
	$ofici=$person->getClientData($recibo->ID_NIT_OFICIAL);	
	  
	$rcb->doMarcarComoImpreso($recibo->SERIE,$recibo->NO_DOCTO); 	
	
	 
 for($i=0;$i<3;$i++){		
?>
<page format="70x300" orientation="P" backcolor="#FFFFFF" style="font: arial;">
<table border="0" cellpadding="0" cellspacing="0" >
  <tr>
    <td  ><table border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="250" style="font-size:16px;"><strong>SERVICIOS MEMORIALES DOMINICANOS, S.R.L.</strong></td>
        </tr>
      <tr>
        <td width="100" valign="top"><table  border="0" cellpadding="0" cellspacing="0" style="font-size:12px;" >
          <tr>
            <td style="font-size:12px;">Av. 27 de Febrero No. 444</td>
          </tr>
          <tr>
            <td style="font-size:12px;">(Entre Av. Privada y Nuñez de Caceres),</td>
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
    </table></td>
  </tr>
</table>
<table  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table border="0"  cellspacing="0" cellpadding="0">
      <tr>
        <td width="20"  style="font-size:12px"><strong>FECHA:</strong></td>
        <td style="font-size:12px"><?php echo $recibo->FECHA_DOC;?></td>
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
      <?php } ?>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><strong>OFICIAL:</strong></td>
  </tr>
  <tr>
    <td width="250"><?php echo $ofici['nombre_completo']?></td>
  </tr>
</table>
<table  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><strong>MOTORIZADO:</strong></td>
    </tr>
      <tr>
        <td><span style="font-size:12px"><?php echo $moto['nombre_completo']?></span></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><strong>CLIENTE:</strong></td>
      </tr>
      <tr>
        <td><?php echo $client_data['nombre_completo']?> - <?php echo $client_data['id_nit'];?></td>
      </tr>
      <tr>
        <td><strong>DIRECCION DE COBRO:</strong></td>
      </tr>
      <tr>
        <td width="250"><?php echo utf8_encode($direccion_cobro);?></td>
      </tr>
      <tr>
        <td><strong>DIRECCION REFERENCIA</strong></td>
      </tr>
      <tr>
        <td><?php echo $direccion_referencia;?></td>
      </tr>
      <tr>
        <td><strong>OBSERVACIONES</strong></td>
      </tr>
      <tr>
        <td><?php echo $recibo->OBSERVACIONES?>&nbsp;</td>
      </tr>
      <tr>
        <td><strong>TELEFONO</strong></td>
      </tr>
      <tr>
        <td><span style="font-size:12px;"><?php echo $phone;?></span></td>
      </tr>
  </table>
<table border="0" cellpadding="5" cellspacing="0" >
  <tr>
    <td  ><table border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="100" valign="top"><table width="250" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td style="font-size:12px;border-bottom:#000 solid 1px;"><strong>DESCRIPCION</strong></td>
            <td style="font-size:12px;border-bottom:#000 solid 1px;"><strong>MONTO</strong></td>
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
            <td width="130"   style="font-size:12px;" ><?php echo $DESCRIPCION;?></td>
            <td width="80"  style="font-size:12px;">RD$ <?php echo number_format($row['MONTO']*$row['TIPO_CAMBIO'],2);?></td>
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
            <td width="80">&nbsp;</td>
            <td><table border="0"  cellpadding="0" cellspacing="0" >
              <tr>
                <td  style="font-size:12px;" ><strong>Sub-Total:</strong></td>
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
      <tr>
        <td valign="top"><table border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td width="70">&nbsp;</td>
            <td><img src="<?php echo $bar_code_url;?>" alt="" /></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
</page>
<?php
 }
}  
 
    $content = ob_get_clean();
    require_once('class/lib/pdf/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', 0);
   //     $html2pdf->pdf->SetDisplayMode('fullpage');
	   // $x=$html2pdf->pdf->addTTFfont("/fonts/dotmatri.ttf", '', '', 32);
		$html2pdf->pdf->setFont('dotmatri');
        $html2pdf->writeHTML($content,"");	
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
