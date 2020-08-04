<?php  
 
 
if (isset($info['no_reserva'])){
	SystemHtml::getInstance()->includeClass("caja","Caja");
	SystemHtml::getInstance()->includeClass("client","PersonalData");
	SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva"); 
	 
//	$trans=json_decode(System::getInstance()->Decrypt($_REQUEST['pago'])); 
	
	$caja= new Caja($db_link);	 
	$person= new PersonalData($db_link);	
	
	$oficial=$person->getClientData($info['oficial_nit']);
 
	
	$_reserva=new Reserva($db_link);
 
	$r_data=$_reserva->getDataReserva($info['no_reserva']);
 
	//$personData=$person->getClientData($r_data['id_nit']);
	$addressData=$person->getAddress($info['person_nit']);
	$phoneData=$person->getPhone($info['person_nit']);
		 
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

 	//$pago_cuota=$caja->getPagoDeUnaReserva($r_data['id_nit'],$r_data['no_reserva'],$trans->SERIE,$trans->NO_DOCTO); 
 
 	//print_r($pago_cuota);
 
?>
<page format="100x210" orientation="L" backcolor="#FFFFFF" style="font: arial;">

<table width="700" border="0" align="center" cellpadding="5" cellspacing="0" >
  <tr>
    <td ><table width="700" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="500" bgcolor="#00CC66" style="color:#FFF;padding-left:10px;"><strong>NOTIFICACION DESCUENTO APLICADO (<?php echo $info['titulo_header']?>)</strong></td>
        <td><table width="200" border="0"  cellspacing="0" cellpadding="0">
          <tr>
            <td width="70"  style="font-size:12px"><strong>FECHA:</strong></td>
            <td style="font-size:12px"><?php echo date("d/m/Y");?></td>
          </tr>
          <tr>
            <td style="font-size:12px"><strong>RESERVA:</strong></td>
            <td style="font-size:12px"><?php echo $r_data['no_reserva'];?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td style="font-size:12px"><strong>NOMBRE DE CLIENTE:</strong> <?php echo $r_data['id_nit'];?> - <?php echo $r_data['nombre_cliente']?></td>
  </tr>
  <tr>
    <td style="font-size:12px;" ><table width="700" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><?php echo utf8_encode($direccion);?></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td><span style="font-size: 12px"><strong>TELEFONO:</strong> <span style="font-size:10px;"><?php echo $phone;?></span></span></td>
  </tr>
  <tr>
    <td valign="top"><table width="700" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td style="font-size:12px;border-bottom:#000 solid 1px;"><strong>DESCRIPCION</strong></td>
        <td width="100" style="font-size:12px;border-bottom:#000 solid 1px;"><strong>PRECIO</strong></td>
        </tr>
      <?php 
 	foreach($r_data['res'] as $key =>$val){
 ?>     
      <tr>
        <td width="670"  style="font-size:12px;" >JARDIN <?php echo $val['jardin'];?></td>
        <td  style="font-size:12px;">RD$ <?php echo number_format($info['monto_pagado'],2);?></td>
        </tr>
      <?php } ?>     
      </table></td>
  </tr>
  <tr>
    <td><table width="700" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="380"><table width="400" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="100"  style="font-size:12px;" ><strong>APLICADO POR</strong>:</td>
            <td  style="font-size:12px;" ><?php echo $oficial['primer_nombre']." ".$oficial['segundo_nombre']." ".$oficial['primer_apellido']." ".$oficial['segundo_apellido']?></td>
          </tr>
        </table></td>
        <td width="380" align="right"><table width="250" border="0" align="right" cellpadding="0" cellspacing="0">
          <tr>
            <td width="80"  style="font-size:12px;" ><strong>Sub-Total:</strong></td>
            <td  style="font-size:12px;" >&nbsp;<?php echo number_format($info['monto_pagado'],2);?></td>
          </tr>
          <tr>
            <td  style="font-size:12px;" ><strong>Descuento:</strong></td>
            <td bgcolor="#FF0000"  style="font-size:12px;color:#FFF;" >&nbsp;<strong><?php echo number_format($info['monto_descuento'],2);?></strong></td>
          </tr>
          <tr>
            <td  style="font-size:12px;" ><strong>TOTAL GENERAL :</strong></td>
            <td  style="font-size:12px;" >&nbsp;<?php echo number_format($info['monto_total'],2);?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td  style="font-size:12px;" >&nbsp;</td>
  </tr>
</table>
</page>
<?php } ?>