<?php 
if (!isset($protect)){
	exit;	
}
//$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['person'])); 
 
 

$filter_tipo_mov="";
 


?>
<style>
.pay_item{
	float:right;
	margin-right:10px;
	cursor:pointer;
}
 
</style>
<div class="fsPage">
<form method="post"  action="" id="caja_payment"  name="caja_payment" class="fsForm">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="25%"><table width="150" border="0" cellspacing="5" cellpadding="1">
          <tr>
            <td><strong>Tipo Movimiento</strong></td>
          </tr>
          <tr>
            <td><select name="tipo_movimiento" id="tipo_movimiento" class="textfield_input required" style="width:200px;">
        <option value="">Seleccione</option>
        <?php 

$SQL="SELECT * FROM `tipo_movimiento` WHERE no_movimiento='1'  ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
        <option value="<?php echo $encriptID?>" inf="<?php echo $row['no_movimiento']?>" selected="selected" ><?php echo $row['descripcion']?></option>
        <?php } ?>
        </select>
            </select></td>
          </tr>
        </table></td>
        <td width="25%"><table width="150" border="0" cellspacing="5" cellpadding="1">
          <tr>
            <td><strong>Tipo de documento</strong></td>
          </tr>
          <tr>
            <td><select name="tipo_documento" id="tipo_documento" class="textfield_input required" style="width:200px;">
              <option value="">Seleccione</option>
              <?php 

$SQL="SELECT * FROM `tipo_documento`";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
              <option value="<?php echo $encriptID?>" ><?php echo ($row['DESCRIPCION_TIPO_DOC'])?></option>
              <?php } ?>
            </select></td>
          </tr>
        </table></td>
        <td width="25%"><table width="150" border="0" cellspacing="5" cellpadding="1">
          <tr>
            <td><strong>Empresa</strong></td>
          </tr>
          <tr>
            <td><select name="EM_ID" id="EM_ID" class="textfield_input required" style="width:200px;">
              <option value="">Seleccione</option>
              <?php 

$SQL="SELECT EM_NOMBRE,EM_ID FROM `empresa` where estatus=1 ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
              <option value="<?php echo $encriptID?>" ><?php echo ($row['EM_NOMBRE'])?></option>
              <?php } ?>
            </select></td>
          </tr>
        </table></td>
        </tr>
      <tr>
        <td><table width="150" border="0" cellspacing="5" cellpadding="1">
          <tr>
            <td><strong>Serie. Documento</strong></td>
          </tr>
          <tr>
            <td><input type="text" name="serie_documento" id="serie_documento"  class="textfield_input required"/></td>
          </tr>
        </table></td>
        <td><table width="150" border="0" cellspacing="5" cellpadding="1">
          <tr>
            <td><strong>No. Documento</strong></td>
          </tr>
          <tr>
            <td><input type="text" name="no_documento" id="no_documento"  class="textfield_input required"/></td>
          </tr>
        </table></td>
        <td>&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr style="display:none">
        <td colspan="4"><h2 style="cursor:pointer" id="factura_mov_label">FACTURA<span class="pay_item ui-button-icon-primary ui-icon ui-icon-triangle-1-s"></span></h2></td>
        </tr>
      <tr id="mov_caja_factura" style="display:none">
        <td><table width="150" border="0" cellspacing="5" cellpadding="1">
          <tr>
            <td><strong>Serie factura</strong></td>
          </tr>
          <tr>
            <td><input type="text" name="serie_factura" id="serie_factura"  class="textfield_input required"/></td>
          </tr>
        </table></td>
        <td ><table width="150" border="0" cellspacing="5" cellpadding="1">
          <tr>
            <td><strong>No. Factura</strong></td>
          </tr>
          <tr>
            <td><input type="text" name="no_factura" id="no_factura" class="textfield_input required" /></td>
          </tr>
        </table></td>
        <td><table width="150" border="0" cellspacing="5" cellpadding="1">
          <tr>
            <td><strong>Fecha factura</strong></td>
          </tr>
          <tr>
            <td><input type="text" name="fecha_factura" id="fecha_factura"  class="textfield_input required"/></td>
          </tr>
        </table></td>
        <td>&nbsp;</td>
      </tr>
    </table></td>
  </tr>
   
  <tr>
    <td><h2>FORMA DE PAGO</h2></td>
  </tr>
  <tr>
    <td><table width="400" border="0" cellspacing="5" cellpadding="5">
      <tr>
        <td align="right"><strong>Forma de pago:</strong></td>
        <td><select name="forma_pago" id="forma_pago" class="textfield_input required" style="width:200px;" >
            <option value="">Seleccione</option>
        <?php 

$SQL="SELECT * FROM `formas_pago`";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	 
?>
        <option value="<?php echo $row['forpago']?>" ><?php echo $row['descripcion_pago']?></option>
        <?php } ?>
        </select></td>
      </tr>
      <tr  class="tipo_trans_tarjeta" style="display:none">
        <td align="right"><strong>Banco:</strong></td>
        <td><select name="banco" id="banco" class="textfield_input required" style="width:200px;" >
          <option value="">Seleccione</option>
          <?php 

$SQL="SELECT * FROM `bancos`";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
          <option value="<?php echo $encriptID?>" ><?php echo $row['ban_descripcion']?></option>
          <?php } ?>
          </select></td>
      </tr>
      <tr>
        <td align="right"><strong>Monto a pagar:</strong></td>
        <td><input type="text" name="monto" id="monto"  class="textfield_input required" /></td>
      </tr>
      <tr  class="tipo_trans_tarjeta" style="display:none">
        <td align="right"><strong>Autorización:</strong></td>
        <td><input type="text" name="autorizacion" id="autorizacion"  class="textfield_input required"/></td>
      </tr>
      <tr  >
        <td align="right"><strong>Tipo de cambio:</strong></td>
        <td><input type="text" name="tipo_cambio" id="tipo_cambio" value="1"  class="textfield_input required"/></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><h2>OBSERVACION</h2></td>
  </tr>
  <tr>
    <td align="center"><textarea name="observacion" id="observacion" cols="45" rows="5" style="width:95%" class="textfield_input"></textarea></td>
  </tr>
  <tr>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td align="center"><button type="button" class="greenButton" id="bt_caja_process">&nbsp;Realizar pago&nbsp;</button>
        <button type="button" class="redButton" id="bt_caja_cancel">Cancelar</button>&nbsp;</td>
  </tr>
  <tr>
    <td align="center">&nbsp;</td>
  </tr>
</table>
</form>
</div>