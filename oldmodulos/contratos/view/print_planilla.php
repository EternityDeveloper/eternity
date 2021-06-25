<?php
if (!isset($protect)){
	exit;
}	
 
if (!isset($_REQUEST['id'])){
	echo "Error contrato no existe!";
	exit;
}	 

$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));

if (!isset($contrato->serie_contrato)){
	echo "Error contrato no existe!";
	exit;
}	

SystemHtml::getInstance()->includeClass("client","PersonalData");
SystemHtml::getInstance()->includeClass("contratos","Contratos");

$_asesor=new Asesores($protect->getDBLink());
$asesores=$_asesor->getComercialParentData($contrato->asesor);
 
$id_prospecto=System::getInstance()->Encrypt($contrato->prospecto);
$id_contrante=System::getInstance()->Encrypt($contrato->id_nit_cliente);
 
			
$person= new PersonalData($protect->getDBLink(),$_REQUEST);
$datos_p=$person->getClientData($contrato->id_nit_cliente);
$datos_p_telefono=$person->getPhone($contrato->id_nit_cliente);
$datos_p_direccion=$person->getAddress($contrato->id_nit_cliente);

$con= new Contratos($protect->getDBLink());
$representante=$con->getRepresentantes($contrato->serie_contrato,$contrato->no_contrato);

$product=$con->getDetalleProductsFromContrato($contrato->serie_contrato,$contrato->no_contrato);
$servicios=$con->getDetalleServicioFromContrato($contrato->serie_contrato,$contrato->no_contrato); 
//System::getInstance()->Decrypt($_REQUEST['serie_contrato']),System::getInstance()->Decrypt($_REQUEST['no_contrato'])
?>
<page backcolor="#FFFFFF" style="font: arial;">
<table width="750" border="0" align="center" cellpadding="5" cellspacing="0" style="width:800px;">
  <tr>
    <td><table width="750" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="250"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center"><img src="images/memorial.fw.png" width="222" height="75" /></td>
          </tr>
          <tr>
            <td style="font-size: 12px; text-align: center"><p><strong>Servicios Memoriales Dominicanos SMD SRL.<br />
              RNC 130-80999-8</strong></p></td>
          </tr>
        </table></td>
        <td width="250" align="center"><h1>Planilla de Solicitud</h1></td>
        <td width="250"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="120"><strong>Fecha:</strong></td>
            <td><?php echo $contrato->fecha_reiterado?></td>
          </tr>
          <tr>
            <td><strong>Recepcion</strong></td>
            <td><?php echo $contrato->fecha_ingreso?></td>
          </tr>
          <tr>
            <td><strong>No. Solicitud</strong></td>
            <td><?php echo $contrato->no_contrato?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td style="font-size:12px;"><strong>Direccion de Oficina y parque: Av. Jacobo Majluta, Santo Domingo Norte, Republica Dominicana</strong></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="25" align="center" bgcolor="#CCCCCC"><strong>INFORMACION GENERAL DEL TITULAR</strong></td>
  </tr>
  <tr>
    <td><strong>Nombre del Titular del Contrato</strong>: <?php echo $datos_p['nombre_completo']?></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><strong>Cedula de identidad del Titular</strong>:  <?php echo $datos_p['id_nit']?> </td>
        <td width="400">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      <?php foreach($datos_p_telefono as $key =>$phone){?>
        <td><strong>Telefono</strong> <?php echo $phone['tipo']?> :  <?php echo $phone['area']."-".$phone['numero']." Ext:".$phone['extencion']?> </td>
        <?php } ?>
      </tr>
    </table></td>
  </tr>
  <tr>
   <?php foreach($datos_p_direccion as $key =>$direccion){?>
    <td><strong>Direccion de <?php echo $direccion['tipo']?></strong>: <?php echo $direccion['provincia']." ".$direccion['ciudad']." ".$direccion['sector']." ".$direccion['avenida']." ".$direccion['calle']." ".$direccion['zona']." ".$direccion['departamento']." ".$direccion['manzana']." ".$direccion['numero']?></td>
     <?php } ?>
  </tr>
  <tr>
    <td height="25" align="center" bgcolor="#CCCCCC"><strong>INFORMACION DE LOS AUTORIZADOS</strong></td>
  </tr>
  <tr>
    <td>
 <?php 
 foreach($representante as $key =>$rep){
	 $datos_rep=$person->getClientData(System::getInstance()->Decrypt($rep['idnit']));  
 ?>   
    <table width="750" border="0" cellspacing="0" cellpadding="5" style="border:#F00 solid 1px;">
      <tr>
        <td width="503" height="15"><strong>Nombre y Apellido:</strong> <?php echo  $datos_rep['nombre_completo']?></td>
        <td width="257" height="15"><strong>Cédula:</strong> <?php echo  $datos_rep['id_nit']?></td>
      </tr>
      <tr>
        <td height="15"><strong>Parentesco del Titular:</strong> <?php echo  $rep['parentesco']?></td>
        <td height="15"><strong>Telefono:</strong>  <?php echo  $datos_rep['telefono']?></td>
      </tr>
      <tr>
        <td height="15" colspan="2"><strong>Dirección:</strong> <?php echo  strip_tags($datos_rep['direccion']);?></td>
        </tr>
    </table>
   <?php } ?> </td>
  </tr>
  <tr>
    <td height="25" align="center" bgcolor="#CCCCCC"><strong>INFORMACION DEL PLAN</strong></td>
  </tr>
  <tr>
    <td><table width="750" border="0" cellspacing="1" cellpadding="5">
      <tr>
        <td width="190" height="25" align="center" bgcolor="#F7F7F7"><strong>Plan</strong></td>
        <td width="190" align="center" bgcolor="#F7F7F7"><strong>Precio Base</strong></td>
        <td width="190" align="center" bgcolor="#F7F7F7"><strong>Cant. Productos/Servicios</strong></td>
        <td width="190" align="center" bgcolor="#F7F7F7"><strong>Descuento en Precio base</strong></td>
      </tr>
      <tr>
        <td height="25" align="center">0</td>
        <td align="center"><?php echo number_format($contrato->precio_lista,2);?></td>
        <td align="center"><?php echo $contrato->no_productos;?></td>
        <td align="center"> <?php echo number_format($contrato->descuento,2);?> </td>
      </tr>
      <tr>
        <td height="25" align="center" bgcolor="#F7F7F7"><strong>Precio Total</strong></td>
        <td align="center" bgcolor="#F7F7F7"><strong>Monto Capitalizado</strong></td>
        <td align="center" bgcolor="#F7F7F7"><strong>Monto de Inicial o Abono</strong></td>
        <td align="center" bgcolor="#F7F7F7"><strong>Descuento en Inicial o Abono</strong></td>
      </tr>
      <tr>
        <td height="25" align="center"><?php echo number_format($contrato->precio_neto,2);?></td>
        <td align="center">0</td>
        <td align="center"><?php echo number_format($contrato->enganche,2);?></td>
        <td align="center">0</td>
      </tr>
      <tr>
        <td height="25" align="center" bgcolor="#F7F7F7"><strong>Saldo a Financiar</strong></td>
        <td align="center" bgcolor="#F7F7F7"><strong>Interés de Financiamiento</strong></td>
        <td align="center" bgcolor="#F7F7F7"><strong>Sub-Total</strong></td>
        <td align="center" bgcolor="#F7F7F7"><strong>Pago Total</strong></td>
      </tr>
      <tr>
        <td height="25" align="center"><?php echo number_format($contrato->interes,2);?></td>
        <td align="center"><?php echo $contrato->porc_interes;?></td>
        <td align="center"><?php echo number_format($contrato->interes+$contrato->precio_neto,2);?></td>
        <td align="center"><?php echo number_format($contrato->interes+$contrato->precio_neto+$contrato->enganche,2);?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="750" border="0" cellspacing="0" cellpadding="8">
      <tr>
        <td height="25" align="center" bgcolor="#F7F7F7"><strong>Total de Cuotas</strong></td>
        <td align="center" bgcolor="#F7F7F7"><strong>Periodicidad</strong></td>
        <td align="center" bgcolor="#F7F7F7"><strong>Monto de la Cuota</strong></td>
      </tr>
      <tr>
        <td width="200" align="center"><?php echo $contrato->cuotas;?></td>
        <td width="200" align="center">0</td>
        <td width="200" align="center"><?php echo number_format($contrato->valor_cuota,2);?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="750" border="0" cellspacing="0" cellpadding="0"> 
       <?php if (count($product)>0){?>
        <tr>
        <td width="156" height="25" bgcolor="#F7F7F7"><strong>Parcela Asignada:</strong></td>
        <td width="634"><?php 		    
		foreach($product as $key =>$producto){
			echo $producto['id_jardin']."-".$producto['id_fases']."-".$producto['bloque']."-".$producto['lote']."   ";
		}
		?> </td>
          </tr>
         <?php } ?>
    
     <?php if (count($servicios)>0){?>
     <tr>
        <td width="156" height="25" bgcolor="#F7F7F7"><strong>Servicios:</strong></td>
        <td width="634"><?php 		    
		foreach($servicios as $key =>$srv){
			echo  $srv['serv_descripcion']."  ";
		}
		?> </td>
       </tr> 
         <?php } ?> 
    </table></td>
  </tr>
  <tr>
    <td><table width="750" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="25"><strong>Comentarios:</strong></td>
      </tr>
      <tr>
        <td><?php echo $contrato->observaciones?>&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="750" border="0" cellspacing="0" cellpadding="10">
      <tr>
        <td width="335"><strong>Procesado Por:</strong><br>
           <?php 
		   $proc=$person->getClientData($contrato->id_nit_reiterador);
		   echo $proc['nombre_completo']; ?></td>
        <td width="235"><strong>Asesor:</strong><br> 
          <?php echo $asesores[count($asesores)-1]['nombre']." ".$asesores[count($asesores)-1]['apellido'];?>
</td>
        <td width="160"><strong>Autorizado Por:</strong><br></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
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
        $html2pdf->writeHTML($content,"");
        $html2pdf->Output('recibo_venta_'.$pago->reporte_venta.'.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>
