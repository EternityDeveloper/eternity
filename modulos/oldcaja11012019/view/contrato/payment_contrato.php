<?php 
if (!isset($protect)){
	exit;	
}
SystemHtml::getInstance()->includeClass("contratos","Contratos"); 

$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
 
//print_r($contrato);
$ct= new Contratos(UserAccess::getInstance()->getDBLink());
$info=$ct->getInfoContrato($contrato->serie_contrato,$contrato->no_contrato);
 
//print_r($info->estatus);

$filter_tipo_mov="";
/*	
En caso que el contrato seleccionado este en estatus 13 o pendiente de verificar
entonces filtrar los tipos de movimientos para que sea un pago de incial
*/
if ($info->estatus=="13"){
	$filter_tipo_mov=" WHERE no_movimiento='1' ";
}else{
	
}
//not in('1')
$filter_tipo_mov=" WHERE no_movimiento in (11,2,8,9) ";


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

$SQL="SELECT * FROM `tipo_movimiento` ".$filter_tipo_mov;
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
              <option value="<?php echo $encriptID?>" inf="<?php echo $row['no_movimiento']?>" ><?php echo $row['descripcion']?></option>
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
            <td><strong>Motorizado</strong></td>
            </tr>
          <tr>
            <td><input type="text" name="motorizado" id="motorizado"  class="textfield_input"/></td>
            </tr>
          </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
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
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td id="forma_pago_view">&nbsp;</td>
  </tr>
  <tr>
    <td align="center"><table width="150" border="0" cellspacing="5" cellpadding="1"  >
      <tr>
        <td align="center"><strong>MONTO CUOTA</strong></td>
      </tr>
      <tr>
        <td align="center"><strong><?php echo number_format($info->cuota,2);?></strong></td>
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