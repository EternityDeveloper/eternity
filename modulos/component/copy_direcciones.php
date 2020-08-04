<?php

if (!isset($protect)){
	exit;
}

if (!isset($_REQUEST['address_number'])){
	exit;
}

$number=$_REQUEST['address_number'];

?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" id="master_direction_<?php echo $number; ?>" >
  <tr id="faster_search_rm_<?php echo $number; ?>">
    <td colspan="3"><label class="fsLabel fsRequiredLabel" for="label">Busqueda rapida por sector:</label><span class="fsLabel fsRequiredLabel">
      <input type="text" id="faster_search_<?php echo $number; ?>" name="faster_search_<?php echo $number; ?>" size="30" value="" class="required" />
      </span>  <button type="button" onclick="showAddressView('address_<?php echo $number; ?>','faster_search_rm_<?php echo $number; ?>')">Agregar de forma manual</button></td>
  </tr>
  <tr>
    <td colspan="3" id="address_<?php echo $number; ?>" style="display:none"><table width="100%" border="0" cellpadding="5" cellspacing="5" >
      <tr>
        <td><label class="fsLabel fsRequiredLabel" for="label">Tipo de direccion <span>*</span></label></td>
        <td><select name="sys_tipos_clasificacion_<?php echo $number; ?>" id="sys_tipos_clasificacion_<?php echo $number; ?>" class="required">
          <option value="">Seleccione</option>
          <?php 

$SQL="SELECT * FROM `sys_tipos_clasificacion` WHERE STATUS='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_tipos_clasifica'],$protect->getSessionID());
?>
          <option value="<?php echo $encriptID?>"><?php echo $row['descripcion']?></option>
          <?php } ?>
        </select></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><label class="fsLabel fsRequiredLabel" for="label">Provincia<span>*</span></label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label">Municipio<span>*</span></label></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><select name="provincia_id_<?php echo $number; ?>" id="provincia_id_<?php echo $number; ?>" class="required" onchange="loadAddressComponent(this.value,'loadmunicipio','<?php echo $number?>','municipio_charge_<?php echo $number?>');">
          <option value="">Seleccione</option>
          <?php 

$SQL="SELECT * FROM `sys_provincia` WHERE STATUS='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['idprovincia'],$protect->getSessionID());
?>
          <option value="<?php echo $encriptID?>"><?php echo $row['descripcion']?></option>
          <?php } ?>
        </select></td>
        <td colspan="2"  id="municipio_charge_<?php echo $number; ?>"><select name="select4" id="select4" class="required">
          <option value="">Seleccione</option>
        </select></td>
        </tr>
      <tr>
        <td><label class="fsLabel fsRequiredLabel" for="label">Ciudad<span>*</span></label></td>
        <td><span class="fsLabel fsRequiredLabel">Sector<span>*</span></span></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td id="ciudad_charge_<?php echo $number; ?>"><select name="select5" id="select5" class="required">
          <option value="">Seleccione</option>
        </select></td>
        <td colspan="2"  id="sector_charge_<?php echo $number; ?>"><select name="select2" id="select2" class="required">
            <option value="">Seleccione</option>
        </select></td>
        </tr>
      <tr>
        <td><label class="fsLabel fsRequiredLabel" for="label">Avenida:<span>*</span></label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label">Calle:<span>*</span></label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label">Zona:<span>*</span></label></td>
      </tr>
      <tr>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="avenida_<?php echo $number; ?>" name="avenida_<?php echo $number; ?>" size="20" value="" class="required" />
        </span></td>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="calle_<?php echo $number; ?>" name="calle_<?php echo $number; ?>" size="20" value="" class="required" />
        </span></td>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="zona_<?php echo $number; ?>" name="zona_<?php echo $number; ?>" size="20" value="" class="required" />
        </span></td>
      </tr>
      <tr>
        <td><label class="fsLabel fsRequiredLabel" for="label">Numero:<span>*</span></label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label">Manzana:<span>*</span></label></td>
        <td><span class="fsLabel fsRequiredLabel">Departamento:<span>*</span></span></td>
      </tr>
      <tr>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="numero_<?php echo $number; ?>" name="numero_<?php echo $number; ?>" size="20" value="" class="required" />
        </span></td>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="manzana_<?php echo $number; ?>" name="manzana_<?php echo $number; ?>" size="20" value="" class="required" />
        </span></td>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="departamento_<?php echo $number; ?>" name="departamento_<?php echo $number; ?>" size="20" value="" class="required" />
        </span></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan="3"><hr/></td>
  </tr>
</table>
