<?php
if (!isset($protect)){
	exit;
}

$rsx=$protect->getCaja();
 
?>
 
<form name="frm_general" id="frm_general" method="post" action=""> 
<table width="390" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
 
  <tr >
    <td align="right" ><strong> MOVIMIENTO:</strong></td>
    <td><select name="tipo_movimiento" id="tipo_movimiento" class="textfield_input required" style="width:200px;">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT tipo_movimiento.* FROM `tipo_movimiento` 
INNER JOIN `tipo_mov_caja` ON (`tipo_mov_caja`.`CAJA_TIPO_MOV_CAJA`=tipo_movimiento.TIPO_MOV)
WHERE  tipo_mov_caja.`CAJA_ID_CAJA`='".$rsx['ID_CAJA']."' ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
      <option value="<?php echo $encriptID?>" inf="<?php echo $row['TIPO_MOV']?>" ><?php echo $row['DESCRIPCION']?></option>
      <?php } ?>
    </select></td>
  </tr>
 
  <tr>
    <td colspan="2" align="center"><button type="button" class="greenButton" id="bt_caja_process" disabled="disabled">&nbsp;Proceder&nbsp;</button><button type="button" class="redButton" id="bt_caja_cancel">Cancelar</button></td>
  </tr> 
</table>
 </form>