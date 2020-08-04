<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}


 
 ?>
<form action="" method="post" enctype="multipart/form-data" name="form_user_edit" class="fsForm  fsSingleColumn" id="form_user_edit">
  <table width="100%" border="1" class="fsPage">
    <tr>
      <td align="right"><strong>Codigo:</strong></td>
      <td><input type="text" name="serv_codigo" id="serv_codigo"></td>
    </tr>
    <tr  >
      <td align="right"><strong>Empresa:</strong></td>
      <td><select name="EM_ID" id="EM_ID"  class="textfield "  style="height:30px;">
        <option value="">Seleccionar</option>
        <?php 
	 	 $SQL="SELECT EM_ID,`EM_NOMBRE`,`por_interes_local`,`por_interes_dolares`,`por_enganche`,`por_impuesto` FROM `empresa` ";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){  
	  ?>
        <option value="<?php echo System::getInstance()->Encrypt($row['EM_ID']);?>" <?php echo $info->EM_ID==$row['EM_ID']?'selected':''; ?>><?php echo $row['EM_NOMBRE'] ?></option>
        <?php } ?>
      </select></td>
    </tr>
    <tr>
      <td align="right"><strong>Descripción:</strong></td>
      <td><input type="text" name="serv_descripcion" id="serv_descripcion"></td>
    </tr>
    <tr>
      <td align="right"><strong>Costo:</strong></td>
      <td><input type="text" name="serv_costo" id="serv_costo"></td>
    </tr>
    <tr>
      <td align="right"><strong>Cta contable:</strong></td>
      <td><input type="text" name="serv_cta_contable" id="serv_cta_contable"></td>
    </tr>
    <tr>
      <td align="right"><strong>Lista Precio  Local</strong></td>
      <td><select name="serv_precio_venta_local" id="serv_precio_venta_local">
        <option value="">Seleccione</option>
        <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='LOCAL' ";
 
$rs=mysql_query($SQL);
while($rowx=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($rowx['CODIGO_TP']);
?>
        <option value="<?php echo $encriptID?>" <?php  
						if ($rowx['CODIGO_TP']==$row['serv_precio_venta_local']){ 
							echo 'selected="selected"';
						}
					 ?>><?php echo $rowx['CODIGO_TP']?></option>
        <?php } ?>
      </select></td>
    </tr>
    <tr>
      <td align="right"><strong>Lista Precio Dolar</strong></td>
      <td><select name="serv_precio_venta_dolares" id="serv_precio_venta_dolares">
        <option value="">Seleccione</option>
        <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='DOLARES' ";
 
$rs=mysql_query($SQL);
while($rowx=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($rowx['CODIGO_TP']);
?>
        <option value="<?php echo $encriptID?>" <?php  
						if ($rowx['CODIGO_TP']==$row['serv_precio_venta_dolares']){ 
							echo 'selected="selected"';
						}
					 ?>><?php echo $rowx['CODIGO_TP']?></option>
        <?php } ?>
      </select></td>
    </tr>
    <tr>
      <td align="right"><strong>Promocion:</strong></td>
      <td><input type="text" name="serv_promocion" id="serv_promocion"></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2"><input name="submit_services" type="hidden" id="submit_services" value="1" /></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><button type="button" class="positive" id="bt_save"> <img src="images/apply2.png" alt=""/> Guardar</button>
        <button type="button" class="positive" id="bt_cancel"> <img src="images/cross.png" alt=""/> Cancel</button></td>
    </tr>
  </table>
</form>