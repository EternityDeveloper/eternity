<?php
if (!isset($protect)){
	exit;
}

if (!validateField($_REQUEST,"id")){
	echo "Debe de seleccionar una caja!";
	exit;
}

$caja=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
 
/// print_r($caja);
?>
 
<form name="frm_caja" id="frm_caja" method="post" action=""> 
<table width="600" border="1" cellpadding="5"  class="table" style="border-spacing:8px;background:#FFF">
 
  <tr >
    <td align="right" ><strong>ID Caja:</strong></td>
    <td><input  name="CODIGO" type="text" disabled class="textfield " id="CODIGO" style="width:110px;padding-right:10px;" value="<?php echo $caja->id_caja?>" maxlength="5"></td>
  </tr>
  <tr >
    <td align="right" valign="top"><strong>Descripción:</strong></td>
    <td><textarea name="descripcion" class="textfield " id="descripcion" style="height:50px;"><?php echo $caja->descripcion?></textarea></td>
  </tr>
  <tr >
    <td align="right" ><strong>Cajero:</strong></td>
    <td><input  name="cajero" type="text" class="textfield " id="cajero" style="width:280px;padding-right:10px;" value="<?php echo $caja->cajero?>"></td>
  </tr>
  <tr >
    <td align="right"><strong>IP Equipo:</strong></td>
    <td><input  name="ip_caja" type="text" class="textfield " id="ip_caja" style="width:200px;padding-right:10px;" value="<?php echo $caja->ip?>"></td>
  </tr>
  <tr >
    <td align="right"><strong>Monto Incial:</strong></td>
    <td><input  name="monto_inicial" type="text" class="textfield " id="monto_inicial" style="width:200px;padding-right:10px;" value="<?php echo $caja->monto_inicial?>"></td>
  </tr>


  <tr>
    <td colspan="2"><table width="600" border="0" cellspacing="0" cellpadding="0" >
      <tr>
        <td width="300" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" >
          <tr>
            <td height="30" align="center"><strong>TIPOS DE MOVIMIENTOS PERMITIDOS</strong></td>
          </tr>
          <tr>
            <td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="table">
              <tr>
                <td>&nbsp;</td>
                <td><strong>Codigo</strong></td>
                <td><strong>Descripcion</strong></td>
              </tr>
              <?php

		$SQL="SELECT *,
 (SELECT COUNT(*) AS total FROM `tipo_mov_caja`
  WHERE `CAJA_TIPO_MOV_CAJA`=tipo_movimiento.TIPO_MOV AND `CAJA_ID_CAJA`='".$caja->id_caja."'  AND `estatus`=1 ) AS checked
FROM `tipo_movimiento` ";
 
		$rs=mysql_query($SQL); 
		while($row=mysql_fetch_assoc($rs)){
			$tipo_mv=System::getInstance()->Encrypt($row['TIPO_MOV']);  
?>
              <tr>
                <td width="10"><input type="checkbox" name="tipo_movimiento[]" id="tipo_movimiento[]" value="<?php echo $tipo_mv;?>" <?php echo ($row['checked']>0)?'checked':'';?> /></td>
                <td width="80"><?php echo $row['TIPO_MOV'];?></td>
                <td><?php echo $row['DESCRIPCION'];?></td>
              </tr>
              <?php 	} ?>
            </table></td>
          </tr>
        </table></td>
        <td width="300" valign="top" style="border-left:#CCC solid 1px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="30" align="center"><strong>TIPOS DE DOCUMENTOS PERMITIDOS</strong></td>
          </tr>
          <tr>
            <td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="table">
              <tr>
                <td>&nbsp;</td>
                <td><strong>Codigo</strong></td>
                <td><strong>Descripcion</strong></td>
              </tr>
              <?php

		$SQL="SELECT *,
 (SELECT COUNT(*) AS total FROM `tipo_documentos_caja`
  WHERE tipo_documentos_caja.`CAJA_TIPO_DOC`=tipo_documento.`TIPO_DOC` AND `CAJA_ID_CAJA`='".$caja->id_caja."'  AND `estatus`=1 ) AS checked
FROM `tipo_documento`";
 
		$rs=mysql_query($SQL); 
		while($row=mysql_fetch_assoc($rs)){
			$tipo_mv=System::getInstance()->Encrypt($row['TIPO_DOC']);  
?>
              <tr>
                <td width="10"><input type="checkbox" name="tipo_documentos[]" id="tipo_documentos[]" value="<?php echo $tipo_mv;?>" <?php echo ($row['checked']>0)?'checked':'';?> /></td>
                <td width="80"><?php echo $row['TIPO_DOC'];?></td>
                <td><?php echo $row['DOCUMENTO'];?></td>
              </tr>
              <?php 	} ?>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan="2"><input name="save_edit_caja" type="hidden" id="save_edit_caja" value="1">
      <input name="id_cajero" type="hidden" id="id_cajero" value="<?php echo System::getInstance()->Encrypt(json_encode(array("id_usuario"=>$caja->id_usuario)));?>"><input  name="id_caja" type="hidden" id="id_caja" value="<?php echo System::getInstance()->Encrypt($caja->id_caja);?>"  ></td>
  </tr>
 
  <tr>
    <td colspan="2" align="center"><button type="button" class="greenButton" id="bt_caja_add"> Guardar</button>
      <button type="button" class="redButton" id="bt_caja_cancel"> Cancel</button></td>
  </tr>
  <tr>
    <td colspan="2" align="center">&nbsp;</td>
  </tr> 
</table>
</form>
 
