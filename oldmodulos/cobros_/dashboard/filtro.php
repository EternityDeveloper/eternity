<?php
if (!isset($protect)){
	exit;
}


$_REQUEST=$_REQUEST['filtro_busqueda'];

//print_r($_REQUEST);

$estatus=json_decode(System::getInstance()->Decrypt($_REQUEST['estatus']));
$id_status=0;
if (isset($estatus->id_status)){
	$id_status=$estatus->id_status;
}

$f_pago=json_decode(System::getInstance()->Decrypt($_REQUEST['forma_de_pago']));
$forpago=0;
if (isset($f_pago->forpago)){
	$forpago=$f_pago->forpago;
}

$a_cobro=json_decode(System::getInstance()->Decrypt($_REQUEST['area_de_cobro']));
$area_cobro=-1;
if (isset($a_cobro->codigo_sector)){
	$area_cobro=$a_cobro->codigo_sector;
}
 

$emp=json_decode(System::getInstance()->Decrypt($_REQUEST['empresa']));
$empresa=-1;
if (isset($emp->EM_ID)){
	$empresa=$emp->EM_ID;
}
  
?> 
<table width="500" border="0" cellspacing="0" cellpadding="0" class="fsPage fsDivPage">
  <tr>
    <td>
    <form name="frm_actividad_" id="frm_actividad_" method="post" action="">
<table width="500" border="1" cellpadding="5"  style="border-spacing:8px;">
      <tr >
        <td align="right" ><strong>CONTRATO:</strong></td>
        <td><input  name="f_contrato" type="text" class="textfield textfieldsize required" style="width:150px;padding-right:10px;" id="f_contrato" value="<?php echo isset($_REQUEST['contrato'])?$_REQUEST['contrato']:''?>" /></td>
      </tr>
      <tr >
        <td align="right"><strong>ESTATUS:</strong></td>
        <td><select name="f_estatus" id="f_estatus" class="textfield_input required" style="width:200px;">
          <option value="">Seleccione</option>
            <?php  
	$SQL="SELECT id_status,descripcion FROM `sys_status` WHERE id_status IN (1,2)";
	$rs=mysql_query($SQL);
	while($row=mysql_fetch_assoc($rs)){
		$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
            <option value="<?php echo $encriptID?>" <?php echo $id_status==$row['id_status']?'selected':''?>  ><?php echo $row['descripcion']?></option>
            <?php } ?>
        </select></td>
      </tr>
      <tr >
        <td align="right" valign="middle"><strong>NOMBRES/APLLIDO:</strong></td>
        <td><input  name="f_nombre_apellido" type="text" class="textfield textfieldsize required" style="width:200px;padding-right:10px;" id="f_nombre_apellido"  value="<?php echo isset($_REQUEST['nombre_apellido'])?$_REQUEST['nombre_apellido']:''?>" /></td>
      </tr>
      <tr >
        <td align="right" ><strong>FECHA CONTRATO:</strong></td>
        <td><p>
          <input  name="f_fecha_contrato" type="text" class="_calendar_t textfield textfieldsize required" style="cursor:pointer;width:80px;padding-right:10px;" id="f_fecha_contrato" value="<?php echo isset($_REQUEST['fecha_contrato'])?$_REQUEST['fecha_contrato']:''?>"  />
        </p></td>
      </tr>
      <tr >
        <td align="right" valign="middle" ><strong>FECHA PAGO:</strong></td>
        <td><input  name="f_fecha_pago" type="text" class="_calendar_t textfield textfieldsize required" style="cursor:pointer;width:80px;padding-right:10px;" id="f_fecha_pago" value="<?php echo isset($_REQUEST['fecha_pago'])?$_REQUEST['fecha_pago']:''?>"/></td>
      </tr>
      <tr >
        <td align="right" valign="middle" ><strong>NO. CUOTA</strong>:</td>
        <td><input  name="f_n_cuota" type="text" class="textfield textfieldsize required" style="cursor:pointer;width:80px;padding-right:10px;" id="f_n_cuota" value="<?php echo isset($_REQUEST['no_cuota'])?$_REQUEST['no_cuota']:''?>"/></td>
      </tr>
      <tr >
        <td align="right" valign="middle" ><strong>FECHA ULTIMO PAGO</strong>:</td>
        <td><input  name="f_ultimo_pago" type="text" class="_calendar_t textfield textfieldsize required" style="cursor:pointer;width:80px;padding-right:10px;" id="f_ultimo_pago" value="<?php echo isset($_REQUEST['fecha_ultimo_pago'])?$_REQUEST['fecha_ultimo_pago']:''?>"  /></td>
      </tr>
      <tr >
        <td align="right" valign="middle" ><strong>MONTO A COBRAR</strong>:</td>
        <td><input  name="f_monto_a_cobrar" type="text" class="textfield textfieldsize required" style="width:150px;padding-right:10px;" id="f_monto_a_cobrar"  value="<?php echo isset($_REQUEST['monto_a_cobrar'])?$_REQUEST['monto_a_cobrar']:''?>"/></td>
      </tr>
      <tr >
        <td align="right" valign="middle" ><strong>FORMA DE PAGO</strong>:</td>
        <td><select name="f_forma_pago" id="f_forma_pago" class="textfield_input required" style="width:200px;">
          <option value="">Seleccione</option>
          <?php 

$SQL="SELECT forpago,`descripcion_pago` FROM `formas_pago` ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
          <option value="<?php echo $encriptID?>" <?php echo $forpago==$row['forpago']?'selected':''?> ><?php echo $row['descripcion_pago']?></option>
          <?php } ?>
        </select></td>
      </tr>
      <tr >
        <td align="right" valign="middle" ><strong>AREA DE COBRO</strong>:</td>
        <td><select name="f_area_de_cobro" id="f_area_de_cobro" class="textfield_input required" style="width:200px;">
          <option value="">Seleccione</option>
          <?php 

$SQL="SELECT codigo_sector FROM `areas_d_cobro` GROUP BY `codigo_sector` ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
          <option value="<?php echo $encriptID?>" <?php echo $area_cobro==$row['codigo_sector']?'selected':''?> ><?php echo $row['codigo_sector']?></option>
          <?php } ?>
        </select></td>
      </tr>
      <tr >
        <td align="right" valign="middle" ><strong>EMPRESA</strong>:</td>
        <td><select name="f_empresa" id="f_empresa" class="textfield_input required" style="width:200px;">
          <option value="">Seleccione</option>
          <?php 

$SQL=" SELECT `EM_ID`,`EM_NOMBRE` FROM `empresa` WHERE `estatus`=1 ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
          <option value="<?php echo $encriptID?>"  <?php echo $empresa==$row['EM_ID']?'selected':''?>><?php echo $row['EM_NOMBRE']?></option>
          <?php } ?>
        </select></td>
      </tr>
 

    </table>
    </form></td>
  </tr>
 
  <tr>
        <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="center"><button type="button" class="greenButton" id="filtro_applicar">Aplicar</button>
      <button type="button" class="redButton" id="act_cancel"> Cancel</button></td>
  </tr>
  <tr>
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
</table>
 
