<?php
if (!isset($protect)){
	exit;
}	

$producto=json_decode(System::getInstance()->Decrypt($_REQUEST['product'])); 

//print_r($producto);
?>
<style>
.fsPage{
	margin-bottom:10px;	
}
table.display2 {}
</style>
<div id="contrato_div"  >
 
  <table width="100%" border="0" cellpadding="0" cellspacing="0"  >
    <tr>
      <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="50%" valign="top"><h2 style="margin:0px;">PRODUCTO</h2></td>
          <td width="50%" valign="top" >&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="fsPage"  style="margin-left:0px;"	>
            <tr>
              <td><button type="button" class="orangeButton" id="bt_c_add_producto">&nbsp;Cambiar&nbsp;</button></td>
            </tr>
            <tr>
              <td id="display_product">
<table id="inv_simple_list"  class="display"  width="100%" >
  <thead>
  <tr>
    <th  style="width: 86px;">Jardin</th>
    <th  style="width: 67px;">Fase</th>
    <th  style="width: 95px;">Bloque</th>
    <th  style="width: 67px;">Lote</th>
    <th  style="width: 97px;">Estatus</th>
    <th  style="width: 129px;">Cavidades</th>
    <th  style="width: 102px;">Osarios</th>
  </tr>
</thead>
<tbody>  
  <tr>
    <td style="height:25px"><?php echo $producto->id_jardin?></td>
    <td><?php echo $producto->id_fases?></td>
    <td><?php echo $producto->bloque?></td>
    <td><?php echo $producto->lote?></td>
    <td><?php echo $producto->estatus?></td>
    <td><?php echo $producto->cavidades?></td>
    <td><?php echo $producto->osarios?></td>
  </tr>
</tbody>  
</table>
              </td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td valign="top">&nbsp;</td>
          <td valign="top" >&nbsp;</td>
        </tr>
        <tr>
          <td valign="top">&nbsp;</td>
          <td valign="top" >&nbsp;</td>
        </tr>
      </table></td>
      <td valign="top">&nbsp;</td>
    </tr>
 
    <tr>
      <td align="center" valign="top"><button type="button" class="greenButton" id="bt_c_save_product" disabled="disabled">&nbsp;Aplicar cambio&nbsp;</button>
        <button type="button" class="redButton" id="bt_produc_cancel">Cancelar</button>&nbsp;</td>
      <td align="center" valign="top">&nbsp;</td>
      </tr>
    <tr>
      <td align="center" valign="top" id="cuadro_producto" style="display:none">&nbsp;</td>
      <td align="center" valign="top" id="cuadro_producto" style="display:none">&nbsp;</td>
    </tr>
  </table>
 
</div>