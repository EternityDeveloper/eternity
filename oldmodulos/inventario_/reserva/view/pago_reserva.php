<?php
if (!isset($protect)){
	echo "Security error!";
	exit;
}


?><style type="text/css">
.fp_efectivo {		display:none;
}
.fp_tipo_reserva {		display:none;		
}
.fp_transferencia {		display:none;	
}
.fsPage{ width:100%;
min-height:300px;
}
</style>
<form name="form_reserva_forma_pago" id="form_reserva_forma_pago" method="post">

<div class="fsPage">
<table width="100%" border="1" >
  <tr>
    <td colspan="2"><h2>Pago</h2></td>
  </tr>
  <tr >
    <td width="40%" align="right"><strong>Forma de pago:</strong></td>
    <td><select name="forma_pago" id="forma_pago" class="required">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT * FROM `formas_pago`   ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
?>
      <option  <?php echo $forma_pago->forma_pago==$row['forpago']?'selected':''?>  value="<?php echo $row['forpago'];?>" ><?php echo $row['descripcion_pago']?></option>
      <?php } ?>
      </select></td>
  </tr>
  <tr class="fp_efectivo">
    <td align="right"><strong>Serie recibo:</strong></td>
    <td><input name="serie_recibo" type="text" id="serie_recibo" value="" class="required"/></td>
  </tr>
  <tr  class="fp_efectivo">
    <td align="right"><strong>No. Recibo:</strong></td>
    <td><input name="no_recibo" type="text" id="no_recibo" value="" /></td>
  </tr>
  <tr  class="fp_efectivo">
    <td align="right"><strong>Monto:</strong></td>
    <td><input name="monto" type="text" id="monto" value="" /></td>
  </tr>
  <tr  class="fp_efectivo">
    <td align="right"><strong>Reporte de venta:</strong></td>
    <td><input name="reporte_venta" type="text" id="reporte_venta" value="" /></td>
  </tr>
  <tr  class="fp_efectivo">
    <td align="right"><strong>Tipo de cambio:</strong></td>
    <td><input name="tipo_cambio" type="text" id="tipo_cambio" value="" /></td>
  </tr>
  <tr class="fp_transferencia">
    <td align="right"><strong>No. Documento:</strong></td>
    <td><input name="no_documento" type="text" id="no_documento" value="" /></td>
  </tr>
  <tr  class="fp_transferencia">
    <td align="right"><strong>Aprobacion:</strong></td>
    <td><input name="aprobacion" type="text" id="aprobacion" value="" /></td>
  </tr>
  <tr  class="fp_transferencia">
    <td align="right"><strong>Banco:</strong></td>
    <td><select name="banco" id="banco">
      <option value="">Seleccione</option>
      <?php 

		$SQL="SELECT * FROM `bancos` ";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
?>
      <option value="<?php echo $row['ban_id'];?>" ><?php echo $row['ban_descripcion']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr  >
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr  >
    <td colspan="2" align="center"><button type="button" class="orangeButton" id="bt_rs_pago_save">Realizar abono</button>&nbsp;<button type="button" class="redButton" id="bt_pago_cancel" style="padding:3px 6px;">Cancelar</button></td>
    </tr>
  <tr  >
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
</table>
</div>
</form>