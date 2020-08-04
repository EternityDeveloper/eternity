<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	


SystemHtml::getInstance()->includeClass("client","PersonalData");
			
$nameForm=$_REQUEST['form'];

$client_id=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));

if (isset($client_id->id_nit)){
	$client_id=$client_id->id_nit;
}else{
	$client_id=System::getInstance()->Decrypt($_REQUEST['id']);	
} 

$person= new PersonalData($protect->getDBLink());




/* VERIFICO SI EL CLIENTE EXISTE  */
if (!$person->existClient($client_id)){
	echo "ID no existe!";
	//header("location:index.php?mod_client/client_list");
	exit;
}

$data_p=$person->getClientData($client_id); 
$_permisos=$protect->getPermisosByPage("mod_client/client_edit");
 
?>
<form method="post"  action="" id="client_form"  name="client_form" class="fsForm">
<table width="100%" border="0" cellpadding="5" cellspacing="5" >
  <td><input type="hidden" name="form_submit" id="form_submit" value="1" />
    <label class="fsLabel fsRequiredLabel" for="label">Tipo de documento<span>*</span></label></td>
    <td ><label class="fsLabel fsRequiredLabel" for="label">Numero de documento<span>*</span></label></td>
    <td><label class="fsLabel fsRequiredLabel" for="label">Genero<span>*</span></label></td>
  </tr>
  <tr>
    <td><select name="id_documento" id="id_documento" class="required" disabled="disabled">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT * FROM `sys_documentos_identidad` WHERE STATUS='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_documento'],$protect->getSessionID());
?>
      <option value="<?php echo $encriptID?>" <?php echo $data_p['id_documento']==$row['id_documento']?'selected':''?>><?php echo trim($row['descripcion']);?></option>
      <?php } ?>
    </select></td>
    <td><span class="fsLabel fsRequiredLabel">
      <input type="hidden" id="id" name="id" size="20"  class="required" value="<?php echo $_REQUEST['id']?>" />
      <input type="text" id="numero_documento" name="numero_documento" size="20"  class="required" value="<?php echo $data_p['numero_documento']?>" disabled="disabled" />
    </span></td>
    <td><p>
      <label>
        <input type="radio" name="id_genero" value="<?php echo System::getInstance()->getEncrypt()->encrypt("1",$protect->getSessionID());?>" id="id_genero"  <?php echo $data_p['id_genero']=="1"?'checked="checked"':''?> />
        Masculino</label>
      <label>
        <input type="radio" name="id_genero" value="<?php echo System::getInstance()->getEncrypt()->encrypt("2",$protect->getSessionID());?>" id="id_genero"    <?php echo $data_p['id_genero']=="2"?'checked="checked"':''?>/>
        Femenino</label>
    </p></td>
  </tr>
  <tr>
    <td><label class="fsLabel fsRequiredLabel" for="firstname">Primer nombre<span>*</span></label></td>
    <td><label class="fsLabel fsRequiredLabel" for="firstname">Segundo nombre</label></td>
    <td><label class="fsLabel fsRequiredLabel" for="firstname">Tercer nombre</label></td>
  </tr>
  <tr>
    <td><input type="text" id="primer_nombre" name="primer_nombre" size="20"  class="required" value="<?php echo $data_p['primer_nombre']; ?>" /></td>
    <td><input type="text" id="segundo_nombre" name="segundo_nombre" size="20" value="<?php echo $data_p['segundo_nombre']; ?>" /></td>
    <td><input type="text" id="tercer_nombre" name="tercer_nombre" size="20" value="<?php echo $data_p['tercer_nombre']; ?>" /></td>
  </tr>
  <tr>
    <td><label class="fsLabel fsRequiredLabel" for="firstname">Primer apellido <span>*</span></label></td>
    <td><label class="fsLabel fsRequiredLabel" for="firstname">Segundo apellido</label></td>
    <td><label class="fsLabel fsRequiredLabel" for="firstname">Apellido de casamiento</label></td>
  </tr>
  <tr>
    <td><input type="text" id="primer_apellido" name="primer_apellido" size="20" value="<?php echo $data_p['primer_apellido']; ?>" class="required" /></td>
    <td><input type="text" id="segundo_apellido" name="segundo_apellido" size="20" value="<?php echo $data_p['segundo_apellido']; ?>" /></td>
    <td><input type="text" id="apellido_conyuge" name="apellido_conyuge" size="20" value="<?php echo $data_p['apellido_conyuge']; ?>"  /></td>
  </tr>
  <tr>
    <td><label class="fsLabel fsRequiredLabel" for="firstname">Fecha nacimiento</label></td>
    <td><label class="fsLabel fsRequiredLabel" for="firstname">Lugar de nacimiento</label></td>
    <td><label class="fsLabel fsRequiredLabel" for="label">Estado civil</label></td>
  </tr>
  <tr>
    <td><input type="text" id="fecha_nacimiento" name="fecha_nacimiento" size="20" value="<?php echo $data_p['fecha_nacimiento']; ?>" /></td>
    <td><select name="lugar_nacimiento" id="lugar_nacimiento" >
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT * FROM `paises` ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['idPaises'],$protect->getSessionID());
?>
      <option <?php echo $data_p['lugar_nacimiento']==$row['Pais']?'selected':''?>><?php echo $row['Pais']?></option>
      <?php } ?>
    </select></td>
    <td><select name="id_estado_civil" id="id_estado_civil">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT * FROM `sys_estado_civil` WHERE status_2='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_estado_civil'],$protect->getSessionID());
?>
      <option value="<?php echo $encriptID?>" <?php echo $data_p['id_estado_civil']==$row['id_estado_civil']?'selected':''?> ><?php echo $row['nombre']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td ><label class="fsLabel fsRequiredLabel" for="label">Profesi&oacute;n y oficio</label></td>
    <td ><label class="fsLabel fsRequiredLabel" id="tipo_clte" for="label" >Tipo Cliente</label></td>
    <td><label class="fsLabel fsRequiredLabel" for="label">Religion</label></td>
  </tr>
  <tr>
    <td ><select name="id_profecion" id="id_profecion">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT * FROM `sys_profeciones` WHERE status='A'  ORDER BY descripcion ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_profecion'],$protect->getSessionID());
?>
      <option value="<?php echo $encriptID?>" <?php echo $data_p['id_profecion']==$row['id_profecion']?'selected':''?> ><?php echo ($row['descripcion'])?></option>
      <?php } ?>
    </select></td>
    <td ><select name="sys_clasificacion_persona" id="sys_clasificacion_persona">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT * FROM `sys_clasificacion_persona` WHERE status='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_clasificacion'],$protect->getSessionID());
?>
      <option value="<?php echo $encriptID?>" <?php echo $data_p['id_clasificacion']==$row['id_clasificacion']?'selected':''?>><?php echo $row['descripcion']?></option>
      <?php } ?>
    </select></td>
    <td><select name="id_religion" id="id_religion">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT * FROM `sys_religiones` WHERE status='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_religion'],$protect->getSessionID());
?>
      <option value="<?php echo $encriptID?>" <?php echo $data_p['id_religion']==$row['id_religion']?'selected':''?>><?php echo $row['descripcion']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td><label class="fsLabel fsRequiredLabel" for="firstname">Numero de hijos</label></td>
    <td><label class="fsLabel fsRequiredLabel" for="label">Nacionalidad</label></td>
    <td><label class="fsLabel fsRequiredLabel" for="label">Factura fiscal</label></td>
    </tr>
  <tr>
    <td><input type="text" id="numero_hijos" name="numero_hijos" size="20" value="<?php echo $data_p['numero_hijos']; ?>" /></td>
    <td><select name="id_nacionalidad" id="id_nacionalidad">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT * FROM `sys_nacionalidad` WHERE status='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_nacionalidad'],$protect->getSessionID());
?>
      <option value="<?php echo $encriptID?>" <?php echo $data_p['id_nacionalidad']==$row['id_nacionalidad']?'selected':''?>><?php echo $row['Descripcion']?></option>
      <?php } ?>
    </select></td>
    <td><input name="factura_fiscal" type="checkbox" id="factura_fiscal" value="1"  <?php echo $data_p['factura_fiscal']==1?'checked="checked"':''?>  /></td>
    </tr>
  <tr class="dt_parentesco" style="display:none">
    <td><strong>Parentesco</strong></td>
    <td align="center">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr class="dt_parentesco"  style="display:none" >
    <td><select name="parentesco" id="parentesco" class="required">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT * FROM `tipos_parentescos` WHERE estatus=1 ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['id_parentesco']);
?>
      <option <?php echo $data_p['id_parentesco']==$row['id_parentesco']?'selected':''?> value="<?php echo $encriptID;?>"><?php echo $row['parentesco']?></option>
      <?php } ?>
    </select></td>
    <td align="center">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <?php if (trim($_permisos['Cambios'])=="1"){ ?>
  <tr>
    <td colspan="3" align="center"><button type="button" id="procesar" >Actualizar informacion</button></td>
  </tr>
  <?php } ?>
</table>
</form> 