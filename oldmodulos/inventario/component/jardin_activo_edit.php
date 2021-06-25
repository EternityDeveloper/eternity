<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}
 

if (!isset($_REQUEST['jardin'])){
	echo "Debe de seleccionar un jardin!";
	exit;
}

$dencryt=System::getInstance()->getEncrypt()->decrypt($_REQUEST['jardin'],$protect->getSessionID());
$data=json_decode($dencryt); 
//print_r($data);
 ?>
<style>
	.fsPage2{
		width:90%
	}
</style>
<form name="form_user_edit" id="form_user_edit" method="post" action="" class="fsForm  fsSingleColumn">
<div class="fsPage fsPage2" style="padding:10px 10px 10px 10px;margin:10px 10px 10px 10px;">
<table width="100%" border="1">
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td><strong>Jardin:</strong></td>
    <td><select name="jardin_list" id="jardin_list" disabled="disabled">
      <option value="<?php 
	//$row=array("nombre"=>"","id_nit"=>0);
   // echo System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
			
		?>">Seleccione</option>
      <?php 

$SQL="SELECT * FROM `jardines` ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
?>
      <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['id_jardin']==$data->id_jardin){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['jardin']?></option>
      <?php } ?>
      </select></td>
  </tr>
  <tr>
    <td><strong>Fase:</strong></td>
    <td id="m_fase"><select name="fase" id="fase" disabled="disabled">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT * FROM `fases` ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_fases'],$protect->getSessionID());
?>
      <option value="<?php echo $encriptID?>"  <?php if (count($data)>0){ 
						if ($row['id_fases']==$data->id_fases){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['fase']?></option>
<?php } ?>
    </select></td>
  </tr>
  <tr>
    <td><strong>Costo:</strong></td>
    <td>
 
    <input type="text" name="costo" id="costo" value="<?php echo $data->costo?>"></td>
  </tr>
  <tr>
    <td><strong>Cta contable:</strong></td>
    <td><input type="text" name="cta_contable" id="cta_contable"  value="<?php echo $data->cta_contable?>" /></td>
  </tr>
  <tr>
    <td><strong>Minimo abono a capital:</strong></td>
    <td><select name="minimo_abono_capital" id="minimo_abono_capital" class="valid">
      <option value="NINGUNO" <?php echo $data->minimo_abono_capital==''?'selected':''; ?>>NINGUNO</option>
      <option value="CUOTA" <?php echo $data->minimo_abono_capital=='CUOTA'?'selected="selected"':''; ?> >CUOTA</option>
      <option value="MONTO" <?php echo $data->minimo_abono_capital=='MONTO'?'selected="selected"':''; ?>>MONTO</option>
    </select></td>
  </tr>
  <tr id="monto_m_capital" <?php echo $data->minimo_abono_capital=='MONTO'?'':'style="display:none"'; ?> >
    <td><strong>Monto minimo abono a capital</strong></td>
    <td><input type="text" name="monto_minimo" id="monto_minimo" value="<?php echo $data->monto_m_abono_capital; ?>" /></td>
  </tr>
  <tr>
    <td colspan="2"><h2>Jardines</h2></td>
    </tr>
  <tr>
    <td><strong>Lista Precio  Local Necesidad</strong></td>
    <td><select name="precio_venta_local_nec" id="precio_venta_local_nec">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='LOCAL' ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['CODIGO_TP']);
?>
      <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['CODIGO_TP']==$data->precio_venta_local_nec){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['CODIGO_TP']?></option>
      <?php } ?>
      </select></td>
  </tr>
  <tr>
    <td><strong>Lista Precio Dolar Necesidad</strong></td>
    <td><select name="precio_venta_dolares_nec" id="precio_venta_dolares_nec">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='DOLARES' ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['CODIGO_TP']);
?>
      <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['CODIGO_TP']==$data->precio_venta_dolares_nec){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['CODIGO_TP']?></option>
      <?php } ?>
      </select></td>
  </tr>
  <tr>
    <td><strong>Lista Precio  Local Pre-necesidad</strong></td>
    <td><select name="precio_venta_local_pre" id="precio_venta_local_pre">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='LOCAL' ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['CODIGO_TP']);
?>
      <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['CODIGO_TP']==$data->precio_venta_local_pre){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['CODIGO_TP']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td><strong>Lista Precio Dolar Pre-necesidad</strong></td>
    <td><select name="precio_venta_dolares_pre" id="precio_venta_dolares_pre">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='DOLARES' ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['CODIGO_TP']);
?>
      <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['CODIGO_TP']==$data->precio_venta_dolares_pre){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['CODIGO_TP']?></option>
      <?php } ?>
      </select></td>
  </tr>
  <tr>
    <td colspan="2"><h2>Osario</h2></td>
  </tr>
  <tr>
    <td align="right"><strong>Osario Lista Precio Local Necesidad</strong></td>
    <td><select name="osario_precio_venta_local_nec" id="osario_precio_venta_local_nec">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='LOCAL' ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['CODIGO_TP']);
?>
      <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['CODIGO_TP']==$data->precio_osario_local_nec){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['CODIGO_TP']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td align="right"><strong>Osario Lista Precio Dolar Necesidad</strong></td>
    <td><select name="osario_precio_venta_dolares_nec" id="osario_precio_venta_dolares_nec">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='DOLARES' ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['CODIGO_TP']);
?>
      <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['CODIGO_TP']==$data->precio_osario_dolares_nec){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['CODIGO_TP']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td align="right"><strong>Osario Lista Precio  Local Pre-necesidad</strong></td>
    <td><select name="osario_precio_venta_local_pre" id="osario_precio_venta_local_pre">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='LOCAL' ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['CODIGO_TP']);
?>
      <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['CODIGO_TP']==$data->precio_osario_local_pre){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['CODIGO_TP']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td align="right"><strong>Osario Lista Precio Dolar Pre-necesidad</strong></td>
    <td><select name="osario_precio_venta_dolares_pre" id="osario_precio_venta_dolares_pre">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='DOLARES' ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['CODIGO_TP']);
?>
      <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['CODIGO_TP']==$data->precio_osario_dolares_pre){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['CODIGO_TP']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr  >
    <td align="right" valign="middle"><strong>Estatus:</strong></td>
    <td align="left"><select name="estado" id="estado" class="required">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT * FROM `sys_status` where id_status in (1,2)";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['id_status']);
?>
      <option value="<?php echo $encriptID?>"  <?php 
	  if (count($data)>0){ 
	 	 echo $data->id_status==$row['id_status']?'selected="selected"':'';
	  } ?>><?php echo $row['descripcion']?></option>
      <?php } ?>
      </select></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><input name="submit_jardin_activo_edit" type="hidden" id="submit_jardin_activo_edit" value="1" />
      <input name="jardin" type="hidden" id="jardin" value="<?php echo $_REQUEST['jardin'];?>" /></td>
  </tr>
  <tr>
    <td colspan="2" align="center">   
                      <button type="button" class="positive" id="bt_save">
                        <img src="images/apply2.png" alt=""/> 
                        Guardar</button>
                       <button type="button" class="positive" id="bt_cancel">
                        <img src="images/cross.png" alt=""/> 
                        Cancel</button>
                        <?php 
						if ($data->total<=0){
						?>
                            <button type="button" class="positive" id="bt_remove">
                            <img src="images/draft.png" width="16px" height="16px" alt=""/> 
                            Elminar</button>
                        <?php } ?>
                         
                  </td>
    </tr>
</table>
</div>
</form>