<?php

if (!isset($protect)){
	exit;
}
 
SystemHtml::getInstance()->includeClass("client","PersonalData");
 
/*
$remove_advance_find: Esta opcion remueve el buscador rapido
$add_edit_buttom: muestra el boton de editar.
*/
$remove_advance_find=0;
if (isset($_REQUEST['remove_advance_find'])){
	$remove_advance_find=1;
}
$rand=0;

if (isset($_REQUEST['rand'])){
	$rand=$_REQUEST['rand'];
}

$remove_estatus_bt=0;
if (isset($_REQUEST['remove_estatus_bt'])){
	$remove_estatus_bt=1;
}

$remove_direccion_tipo=0;
if (isset($_REQUEST['remove_direccion_tipo'])){
	$remove_direccion_tipo=1;
}
 
$add_edit_buttom=0;
if (isset($_REQUEST['add_edit_buttom'])){
	$add_edit_buttom=1;
}

$comp="";
if (isset($_REQUEST['comp'])){
	$comp=$_REQUEST['comp'];
}


if (isset($_REQUEST['dispached'])){
	$dispached="saveAddress('".$comp."')";
	if ($_REQUEST['dispached']!=""){
		$dispached=$_REQUEST['dispached'];
	}
}

$itemFormSubmit="form_address_submit";
if (isset($_REQUEST['itemFormSubmit'])){
	if ($_REQUEST['itemFormSubmit']!=""){
		$itemFormSubmit=$_REQUEST['itemFormSubmit'];
	}
}


$createAddress="0";
if (isset($_REQUEST['createAddress'])){
	if ($_REQUEST['createAddress']!=""){
		$createAddress=$_REQUEST['createAddress'];
	}
}
 
$index=System::getInstance()->getEncrypt()->decrypt($_REQUEST['index'],$protect->getSessionID()); // $_REQUEST['index'];

$client_id=$_REQUEST['client_id'];

$person= new PersonalData($protect->getDBLink());

$client_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['client_id'],$protect->getSessionID());

 

if (isset($_REQUEST['contact_id'])){
	$contact_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['contact_id'],$protect->getSessionID());
}else{
	$contact_id=0;
}
$data_address=array();
if ($createAddress!="1"){
	$data_address=$person->getAddress($client_id,$contact_id,$index);
	//print_r($data_address);
	$data_address=$data_address[0];
}
 
 
?>
<form method="post"  action="" id="<?php echo $comp?>"  name="<?php echo $comp?>" class="fsForm fsPage" style="width:680px">

<input type="hidden" name="<?php echo $itemFormSubmit?>" id="<?php echo $itemFormSubmit?>" value="1" />
<input type="hidden" id="id" name="id" size="20"  class="required" value="<?php echo $_REQUEST['client_id']?>" />
<input type="hidden" id="contact_id" name="contact_id" size="20" value="<?php echo $_REQUEST['contact_id']?>" />
<input type="hidden" id="direccion_id" name="direccion_id" size="20" value="<?php echo $_REQUEST['index']?>" />
<input type="hidden" id="estatus_disable" name="estatus_disable" size="20" value="<?php echo System::getInstance()->Encrypt(2)?>" />
<table width="100%" border="0" cellpadding="0" cellspacing="0" id="master_direction" >
  <tr  >
    <td colspan="3"><label class="fsLabel fsRequiredLabel" for="label">Busqueda rapida por sector:</label><span class="fsLabel fsRequiredLabel">
      <input type="text" class="direcciones_faster" id="faster_search[]" name="faster_search[]" size="30" value=""  placeholder="Buscador rapido de direcciones" />
      </span>  <button id="faster_search_rm"  type="button" onclick="showAddress('address','faster_search_rm','<?php echo $comp?>')">Agregar de forma manual</button></td>
  </tr>
  <tr>
    <td colspan="3" id="address" <?php echo $remove_advance_find==0?'style="display:none"':''?>><table width="100%" border="0" cellpadding="5" cellspacing="5" >
      <tr>
        <td><label class="fsLabel fsRequiredLabel" for="label">Provincia<span>*</span></label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label">Municipio<span>*</span></label></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><select name="provincia_id[]" id="provincia_id[]" class="required" onchange="loadAddressField(this.value,'loadmunicipio','<?php echo $number?>','municipio_charge','<?php echo $comp ?>');" >
          <option value="">Seleccione</option>
          <?php 

$SQL="SELECT * FROM `sys_provincia` WHERE STATUS='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['idprovincia'],$protect->getSessionID());
?>
          <option value="<?php echo $encriptID?>" <?php echo $data_address['idprovincia']==$row['idprovincia']?'selected="selected"':''?>><?php echo $row['descripcion']?></option>
          <?php } ?>
        </select></td>
        <td colspan="2"  id="municipio_charge"><select name="municipio_id[]" id="municipio_id[]" class="required" onchange="loadAddressField(this.value,'loadciudad','<?php echo $number?>','ciudad_charge','<?php echo $_REQUEST['component'] ?>');" >
          <option value="">Seleccione</option>
          <?php 
		
		$SQL="SELECT * FROM `sys_municipio` WHERE status_2='A' and idprovincia='". mysql_escape_string($data_address['idprovincia']) ."'";
		//echo $SQL;
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			$encriptID=System::getInstance()->getEncrypt()->encrypt($row['idmunicipio'],$protect->getSessionID());
		?>
          <option value="<?php echo $encriptID?>" <?php echo $data_address['idmunicipio']==$row['idmunicipio']?'selected="selected"':''?>><?php echo $row['descripcion']?></option>
          <?php } ?>
        </select></td>
        </tr>
      <tr>
        <td><label class="fsLabel fsRequiredLabel" for="label">Ciudad<span>*</span></label></td>
        <td><span class="fsLabel fsRequiredLabel">Sector<span>*</span></span></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td id="ciudad_charge"><select name="cuidad_id[]" id="cuidad_id[]" class="required"  onchange="loadAddressField(this.value,'loadsector','<?php echo $number?>','sector_charge','<?php echo $_REQUEST['component'] ?>');" >
          <option value="">Seleccione</option>
          <?php 
		
		$SQL="SELECT * FROM `sys_ciudad` WHERE status_2='A' and idmunicipio='". mysql_escape_string($data_address['idmunicipio']) ."'";
		//echo $SQL;
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			$encriptID=System::getInstance()->getEncrypt()->encrypt($row['idciudad'],$protect->getSessionID());
		?>
          <option value="<?php echo $encriptID?>"  <?php echo $data_address['idciudad']==$row['idciudad']?'selected="selected"':''?>><?php echo $row['Descripcion']?></option>
          <?php } ?>
        </select></td>
        <td colspan="2"  id="sector_charge"><select name="sector_id[]" id="sector_id[]" class="required"  >
          <option value="">Seleccione</option>
          <option value="-2">Agregar nuevo</option>
          <?php 
		
		$SQL="SELECT * FROM `sys_sector` WHERE status='A' and idciudad='". mysql_escape_string($data_address['idciudad']) ."'";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			$encriptID=System::getInstance()->getEncrypt()->encrypt($row['idsector'],$protect->getSessionID());
		?>
          <option value="<?php echo $encriptID?>"  <?php echo $data_address['idsector']==$row['idsector']?'selected="selected"':''?>><?php echo $row['descripcion']?></option>
          <?php } ?>
        </select></td>
        </tr>
      <tr>
        <td><label class="fsLabel fsRequiredLabel" for="label">Avenida:<span>*</span></label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label">Calle:</label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label">Zona:</label></td>
      </tr>
      <tr>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="direccion_avenida[]" name="direccion_avenida[]" size="20" value="<?php echo $data_address['avenida']?>"  />
        </span></td>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="direccion_calle[]" name="direccion_calle[]" size="20" value="<?php echo $data_address['calle']?>" />
        </span></td>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="direccion_zona[]" name="direccion_zona[]" size="20" value="<?php echo $data_address['zona']?>"    />
        </span></td>
      </tr>
      <tr>
        <td><label class="fsLabel fsRequiredLabel" for="label">Numero:</label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label">Manzana:</label></td>
        <td><span class="fsLabel fsRequiredLabel">Departamento:</span></td>
      </tr>
      <tr>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="direccion_numero[]" name="direccion_numero[]" size="20" value="<?php echo $data_address['numero']?>"  />
        </span></td>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="direccion_manzana[]" name="direccion_manzana[]" size="20" value="<?php echo $data_address['manzana']?>" />
        </span></td>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="direccion_departamento[]" name="direccion_departamento[]" size="20" value="<?php echo $data_address['departamento']?>"  />
        </span></td>
      </tr>
      <tr>
        <td><label class="fsLabel fsRequiredLabel" for="label">Torre/ Residencial / Colonia / Condominio :</label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label">Referencia:</label></td>
        <td><label class="fsLabel fsRequiredLabel" for="label">Observaciones:</label></td>
      </tr>
      <tr>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="direccion_recidencial[]" name="direccion_recidencial[]" size="20" value="<?php echo $data_address['residencia_colonia_condominio']?>" />
        </span></td>
        <td><span class="fsLabel fsRequiredLabel">
          <input type="text" id="direccion_referencia[]" name="direccion_referencia[]" size="20" value="<?php echo $data_address['referencia']?>" />
        </span></td>
        <td><span class="fsLabel fsRequiredLabel">
          <textarea name="direccion_observacion[]" cols="20"  id="direccion_numero[]" ><?php echo $data_address['observaciones']?></textarea>
        </span></td>
      </tr>
      <tr <?php echo $remove_direccion_tipo==1?'style="display:none"':''?>>
        <td align="right"><label class="fsLabel fsRequiredLabel" for="label">Tipo de direccion <span>*</span></label></td>
        <td id="test"><select name="direccion_tipo[]" id="direccion_tipo[]" class="required" >
            <option value="">Seleccione</option>
            <?php 

$SQL="SELECT * FROM `sys_tipos_clasificacion` WHERE STATUS='A' AND id_tipos_clasifica IN (1,7)";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_tipos_clasifica'],$protect->getSessionID());
?>
            <option value="<?php echo $encriptID?>"  <?php echo $data_address['tipo_direccion']==$row['id_tipos_clasifica']?'selected="selected"':''?>><?php echo $row['descripcion']?></option>
            <?php } ?>
        </select></td>
        <td>&nbsp;</td>
      </tr>
      <tr  <?php echo $remove_estatus_bt==0?'style="display:none"':''?>>
        <td align="right"><label class="fsLabel fsRequiredLabel" for="label">Estado<span>*</span></label></td>
        <td>  <select name="estado[]" id="estado[]" class="required">
          <option value="0">Seleccione</option>
          <?php 

$SQL="SELECT * FROM `sys_status` where id_status in (1,2) ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_status'],$protect->getSessionID());
?>
          <option value="<?php echo $encriptID?>"  <?php echo $data_address['status']==$row['id_status']?'selected="selected"':''?>><?php echo $row['descripcion']?></option>
          <?php } ?>
        </select></td>
        <td>&nbsp;</td>
      </tr>
      <tr  <?php echo $remove_advance_find==0?'style="display:none"':''?>>
        <td>&nbsp;</td>
        <td></td>
        <td>&nbsp;</td>
      </tr>
      <tr  <?php echo $remove_advance_find==0?'style="display:none"':''?>>
        
        <td colspan="3" align="center"><button type="button" id="address_save_btn_<?php echo $rand;?>" onclick="<?php echo $dispached?>" >Guardar </button><button type="button" id="cerrar_ventana2" >Cancelar</button></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr   >
        <td colspan="3" align="center">&nbsp;</td>
      </tr>
      <tr  <?php echo $add_edit_buttom==0?'style="display:none"':''?>>
        <td colspan="3" align="center"><button type="button" id="address_save" onclick="<?php echo $dispached?>" >Guardar </button></td>
        </tr>
   <!--<a href="javascript:void(0);" id="remove" class="remCF">Cancelar</a>-->
    </table></td>
  </tr>
 <tr valign="top" <?php echo $remove_estatus_bt==1?'style="display:none"':'';?>>
	<th colspan="6" align="right" valign="middle"><button type="button" id="remove" class="remCF" onclick="javascript:void(0);">Cancelar</button></th>
  </tr>
</table>
 </form>