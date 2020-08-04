<?php
if (!isset($protect)){
	exit;
}

if (!isset($_REQUEST['contrato'])){
	if (!isset($_REQUEST['id_nit'])){
		exit;
	}
}

$id_nit=System::getInstance()->Decrypt($_REQUEST['id_nit']);
$ct=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));
$reserva=json_decode(System::getInstance()->Decrypt($_REQUEST['reserva']));


if (!isset($reserva->no_reserva)){
	if (!isset($ct->serie_contrato)){
		if ($id_nit==""){
			exit;
		}
	}
	
}

if (isset($reserva->no_reserva)){
	$cn=array(
		"reserva"=>1,
		"id_reserva"=>$reserva->id_reserva,
		"no_reserva"=>$reserva->no_reserva,			
	);	
}


if (isset($ct->serie_contrato)){
	$cn=array(
		"contrato"=>1,
		"id_nit"=>$id_nit,
		"serie_contrato"=>$ct->serie_contrato,
		"no_contrato"=>$ct->no_contrato,			
	);	
}
if ($id_nit!=""){
	$cn=array(
		"cliente"=>1,
		"id_nit"=>$id_nit
	);	
}

 
SystemHtml::getInstance()->includeClass("caja","Caja"); 
SystemHtml::getInstance()->includeClass("caja","Recibos"); 
$cj= new Caja($protect->getDBLINK());    
//$avico=$cobro->getAvisoCobroData($ct->serie_contrato,$ct->no_contrato);  
$recibos=$cj->getListadoReciboSinFacturar($cn);
 

 
?><table border="0" class="table table-bordered table-striped table-hover"  style="font-size:13px">
  <thead>
    <tr>
      <th><input class="select_all_cu" type="checkbox" name="checkbox2" id="checkbox2" /></th>
      <th>No. Recibo</th>
      <th>Contrato</th>
      <th>Descripcion</th>
      <th>Items a Cobrar</th>
      <th>Monto</th>
      <th>Monto RD$</th>
      <th>Fecha </th>
      <th width="170">Oficial</th>
      <th>Motorizado</th>
      <th>Accion</th>
    </tr>
  </thead>
  <tbody>
<?php
 
 
foreach($recibos as $key =>$row){  
	$solicitud=System::getInstance()->Encrypt(json_encode($row));
 
?>
    <tr style="cursor:pointer" id="<?php echo $solicitud;?>" class="no_recibo_fact" ref="<?php echo $row['SERIE'].$row['NO_DOCTO'];?>">
      <td><input class="listado_rc_" type="checkbox" name="checkbox" id="checkbox" value="<?php echo $solicitud;?>">
      <label for="checkbox"></label></td>
      <td><?php echo $row['SERIE']." ".$row['NO_DOCTO'];?></td>
      <td><?php echo $row['SERIE_CONTRATO']." ".$row['NO_CONTRATO'];?></td>
      <td><?php echo $row['tmovimiento'];?></td>
      <td><?php echo $row['TOTAL_CUOTAS'];?></td>
      <td><?php echo number_format($row['MONTO_TOTAL'],2);?></td>
      <td><?php echo number_format($row['MONTO_LOCAL'],2);?></td>
      <td><?php echo $row['FECHA_REQUERIMIENTO'];?></td>
      <td><?php echo utf8_encode($row['oficial']);?></td>
      <td><?php echo utf8_encode($row['motorizado']);?></td>
      <td> <?php if ($protect->getIfAccessPageById(160)){ ?>
	  <?php if ($row['SERIE']!='ND'){?><button type="button" class="recibo_remove orangeButton" value="<?php echo $solicitud;?>">	Eliminar</button><?php } ?><?php } ?></td>
    </tr>
    <tr  style="display:none"  id="<?php echo $row['SERIE'].$row['NO_DOCTO'];?>">
      <td colspan="11"><table id="list_formas_pagos2" width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped table-hover">
        <thead>
          <tr>
            <td  style="font-size:14px;"><strong>Forma de pago</strong></td>
            <td  style="font-size:14px;"><strong>Banco</strong></td>
            <!--    <td><strong>Abono reserva</strong></td>-->
            <td style="font-size:14px;"><strong>Autorización</strong></td>
            <td style="font-size:14px;"><strong>Tipo de cambio</strong></td>
            <td style="font-size:14px;"><strong>Monto</strong></td>
            <td style="font-size:14px;"><strong>Monto en RD$</strong></td>
            </tr>
        </thead>
        <tbody id="t_body_fpago2"><?php 
$recibos=new Recibos($protect->getDBLINK());
$forma_pago=$recibos->getReciboFormaPago($row['SERIE'],$row['NO_DOCTO'],$row['TIPO_DOC']);
$monto_a_pagar=0;
foreach($forma_pago as $key=>$val){
	$monto_a_pagar=$monto_a_pagar+($val['TIPO_CAMBIO']*$val['MONTO']);
?>
          <tr>
            <td><?php echo $val['descripcion_pago']?></td>
            <td  ><?php echo $val['ban_descripcion']?></td>
            <td><?php echo $val['AUTORIZACION']?></td>
            <td align="center"><?php echo $val['TIPO_CAMBIO']?></td>
            <td><?php echo number_format($val['MONTO'],2);?></td>
            <td><?php echo number_format($val['MONTO']*$val['TIPO_CAMBIO'],2);?></td>
            </tr>
          <?php 
 } ?>
        </tbody>
        <tfoot>
          <tr >
            <td colspan="5" align="right"  style="font-size:14px;"><strong>TOTAL PAGO:&nbsp;</strong></td>
            <td  style="font-size:14px;" ><span class="badge alert-danger">
              <?php  echo number_format($monto_a_pagar,2);?>
            </span></td>
            </tr>
        </tfoot>
      </table></td>
    </tr>
<?php 
}
 ?>
  </tbody>
</table>
<?php
$data=ob_get_clean();
$info=array(
			"html"=>utf8_encode($data),
			"monto_a_pagar"=>$monto_abonar,
			"total_recibo"=>count($recibos)
		);
echo json_encode($info);
?>
