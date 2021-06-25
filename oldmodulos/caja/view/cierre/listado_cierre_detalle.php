<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
?><table border="0" class="table table-bordered table-striped table-hover"  style="font-size:13px;width:100%">
      <thead>
        <tr>
          <th>NO. FC.</th>
          <th>NO. RC</th>
          <th>DOCUMENTO</th>
          <th>NRO. CEDULA</th>
          <th>CLIENTE</th>
          <th>FORMA DE PAGO</th>
          <th>MONTO</th>
        </tr>
      </thead>
      <tbody>
        <?php
SystemHtml::getInstance()->includeClass("caja","Recibos");  
$recibos= new Recibos($protect->getDBLINK());   
 
$monto=0;
$cantidad_factura=0;
$listado= $cierre->listarDesgloseCierre($filter);
$cantidad_factura=count($listado);

foreach($listado as $key=>$row){ 
	$id=System::getInstance()->Encrypt(json_encode(array("SERIE"=> $row['SERIE'],"NO_DOCTO"=> $row['NO_DOCTO'])));
	$forma_pago=$recibos->getReciboFormaPago($row['SERIE'],$row['NO_DOCTO']);
	$monto=$monto+($row['MONTO_PAGO']);
?>
        <tr id="<?php echo $id;?>" >
          <td width="130"><?php echo $row['SERIE_FACTURA'].$row['NO_DOC_FACTURA'];?></td>
          <td width="130"><?php echo $row['SERIE']."-".$row['NO_DOCTO'];?></td>
          <td><?php echo strtoupper($row['TMOVIMIENTO']);?></td>
          <td><a href=".?mod_caja/delegate&operacion&determinate=1&id=<?php echo System::getInstance()->Encrypt(json_encode(array('id_nit'=>$row['id_nit'])));?>" target="_new" ><?php echo $row['ID_NIT'];?></a></td>
          <td width="250"><?php echo utf8_encode($row['nombre_cliente']);?></td>
          <td><table width="100%" border="0" cellspacing="0" cellpadding="0" style="cursor:pointer">
            <tr>
              <td width="150">TIPO</td>
              <td width="100">MONTO</td>
              <td width="100">REFERENCIA</td>
              </tr>
            <?php foreach($forma_pago as $key=>$fp_row){ ?>
            <tr id="<?php echo $id;?>" class="fpago_evnt">
              <td><?php echo $fp_row['descripcion_pago'];?></td>
              <td><?php echo number_format($fp_row['MONTO']*$fp_row['TIPO_CAMBIO'],2);?></td>
              <td><?php echo $fp_row['AUTORIZACION'];?></td>
            </tr>
            <?php } ?>
          </table></td>
          <td><?php echo number_format(($row['MONTO_PAGO']),2);?></td>
        </tr>
        <?php 
}
?>
        <tr  >
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td align="right"><strong>TOTAL FACTURA:</strong></td>
          <td><?php echo $cantidad_factura;?></td>
        </tr>
        <tr  >
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td align="right"><strong>TOTAL INGRESOS:</strong></td>
          <td><?php echo number_format($monto,2);?></td>
        </tr>
      </tbody>
    </table> 