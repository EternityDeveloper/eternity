<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}
 
if (!isset($_REQUEST['id'])){
	echo "Debe seleccionar un componente!";
	exit;
}

$data=json_decode(System::getInstance()->Request("id"));

if (!isset($data->id_componente)){
	echo "Debe seleccionar un componente!";
	exit;
}
 
 ?>
<form action="" method="post" enctype="multipart/form-data" name="form_user_edit"  id="form_user_edit">
  <table width="100%" border="0" cellpadding="0" cellspacing="0"  >
    <tr>
      <td width="50%"   valign="top"><table width="400" border="0" class="fsPage" >
        <tr>
          <td width="160" align="right"><strong>ID Componente:</strong></td>
          <td width="180"><input name="component" type="text" disabled="disabled" id="component" value="<?php echo $data->id_componente?>" />
            <input name="id_componente" type="hidden" id="id_componente" value="<?php echo $data->id_componente?>" /></td>
        </tr>
        <tr>
          <td align="right"><strong>Descripción:</strong></td>
          <td><input type="text" name="descripcion_comp" id="descripcion_comp"  value="<?php echo $data->descripcion_comp?>" /></td>
        </tr>
        <tr>
          <td align="right"><strong>Costo:</strong></td>
          <td><input type="text" name="costos_comp" id="costos_comp"  value="<?php echo $data->costos_comp?>" /></td>
        </tr>
        <tr>
          <td align="right"><strong>Cta contable:</strong></td>
          <td><input type="text" name="cta_contable_comp" id="cta_contable_comp"  value="<?php echo $data->cta_contable_comp?>" /></td>
        </tr>
        <tr>
          <td align="right"><strong>Precio venta:</strong></td>
          <td><input type="text" name="precio_venta_comp" id="precio_venta_comp"  value="<?php echo $data->precio_venta_comp?>" /></td>
        </tr>
        <tr>
          <td align="right"><strong>Foto:</strong></td>
          <td><img src="images/servicios/<?php echo $data->imagen?>" width="164" height="104"  />
            <input type="file" name="imagen_upload" id="imagen_upload"  style="width:150px;" align=""  /></td>
        </tr>
        <tr>
          <td colspan="2" >&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2"><input name="submit_component_edit" type="hidden" id="submit_component_edit" value="1" /></td>
        </tr>
        <tr>
          <td colspan="2" align="center"><button type="button" class="positive" id="bt_save"> <img src="images/apply2.png" alt=""/> Guardar</button>
            <button type="button" class="positive" id="bt_cancel"> <img src="images/cross.png" alt=""/> Cancel</button></td>
        </tr>
      </table></td>
      <td width="40%" align="left" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="fsPage">
          <tr>
            <td><h2>Sub-Componentes</h2></td>
          </tr>
          <tr>
            <td> 
<button type="button" class="positive" name="sub_pbutton"  id="sub_pbutton" >
                        <img src="images/apply2.png" alt=""/> 
                        Agregar Sub-componente
</button></td>
          </tr>
          <tr>
            <td><table width="100%" border="0"  class="display" id="sub_componente_list" style="font-size:13px">
              <thead>
                <tr>
                  <th>Imagen</th>
                  <th>Descripcion</th>
                  <th>Costo </th>
                  <th>Cta Contable</th>
                  <th>Precio Venta</th>
                  <th>&nbsp;</th>
                </tr>
              </thead>
              <tbody>
                <?php
$SQL="SELECT * FROM `subcomponentes` WHERE `id_componente`='". mysql_escape_string($data->id_componente) ."'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$row['data_component']=$_REQUEST['id'];
	$encriptID=System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
?>
                <tr>
                  <td><img src="images/servicios/<?php echo $row['imagen']?>" width="51" height="49"  /></td>
                  <td height="25"><?php echo $row['sub_descripcion']?></td>
                  <td><?php echo number_format($row['sub_costos'],2)?></td>
                  <td><?php echo $row['sub_cta_contable'];?></td>
                  <td align="center" ><?php echo number_format($row['sub_precio_venta'],2);?></td>
                  <td align="center" ><a href="#" onclick="openDialogEditSubComponent('<?php echo $encriptID;?>')"><img src="images/clipboard_edit.png"  /></a></td>
                </tr>
                <?php 

}
 ?>
              </tbody>
            </table></td>
          </tr>
      </table></td>
    </tr>
  </table>
</form>