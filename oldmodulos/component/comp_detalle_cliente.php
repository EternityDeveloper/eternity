<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	


SystemHtml::getInstance()->includeClass("client","PersonalData");
			
$nameForm=$_REQUEST['form'];

$client_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id'],$protect->getSessionID());

$person= new PersonalData($protect->getDBLink());

/* VERIFICO SI EL CLIENTE EXISTE  */
if (!$person->existClient($client_id)){
	header("location:index.php?mod_client/client_list");
	exit;
}

$data_p=$person->getClientData($client_id);

$_permisos=$protect->getPermisosByPage(System::getInstance()->getCurrentModulo());
//print_r($data_p);
?>
<form method="post"  action="" id="<?php echo $nameForm?>"  name="<?php echo $nameForm?>" class="fsForm">
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
      <option value="<?php echo $encriptID?>" <?php echo $data_p['id_documento']==$row['id_documento']?'selected':''?>><?php echo $row['descripcion']?></option>
      <?php } ?>
    </select></td>
    <td><span class="fsLabel fsRequiredLabel">
      <input type="hidden" id="id" name="id" size="20"  class="required" value="<?php echo $_REQUEST['id']?>" />
      <input type="text" id="numero_documento_text" name="numero_documento_text" size="20"  class="required" value="<?php echo $data_p['numero_documento']?>" disabled="disabled" />
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
    <td><label class="fsLabel fsRequiredLabel" for="firstname">Segundo nombre<span>*</span></label></td>
    <td><label class="fsLabel fsRequiredLabel" for="firstname">Tercer nombre</label></td>
  </tr>
  <tr>
    <td><input type="text" id="primer_nombre" name="primer_nombre" size="20"  class="required" value="<?php echo $data_p['primer_nombre']; ?>" /></td>
    <td><input type="text" id="segundo_nombre" name="segundo_nombre" size="20" value="<?php echo $data_p['segundo_nombre']; ?>"  /></td>
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
    <td><label class="fsLabel fsRequiredLabel" for="firstname">Fecha nacimiento<span>*</span></label></td>
    <td><label class="fsLabel fsRequiredLabel" for="firstname">Lugar de nacimiento<span>*</span></label></td>
    <td><label class="fsLabel fsRequiredLabel" for="label">Estado civil <span>*</span></label></td>
  </tr>
  <tr>
    <td><input type="text" id="fecha_nacimiento" name="fecha_nacimiento" size="20" value="<?php echo $data_p['fecha_nacimiento']; ?>" class="required" /></td>
    <td><select name="lugar_nacimiento" id="lugar_nacimiento" class="required">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT * FROM `paises` ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['idPaises'],$protect->getSessionID());
?>
      <option <?php echo $data_p['lugar_nacimiento']==$row['Pais']?'selected':''?>><?php echo $row['Pais']?></option>
      <?php } ?>
    </select> 
    </td>
    <td><select name="id_estado_civil" id="id_estado_civil" class="required">
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
    <td ><label class="fsLabel fsRequiredLabel" for="label">Profesi&oacute;n y oficio <span>*</span></label></td>
    <td ><label class="fsLabel fsRequiredLabel" for="label">Tipo Cliente <span>*</span></label></td>
    <td><label class="fsLabel fsRequiredLabel" for="label">Religion<span>*</span></label></td>
  </tr>
  <tr>
    <td ><select name="id_profecion" id="id_profecion" >
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT * FROM `sys_profeciones` WHERE status='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_profecion'],$protect->getSessionID());
?>
      <option value="<?php echo $encriptID?>" <?php echo $data_p['id_profecion']==$row['id_profecion']?'selected':''?> ><?php echo ($row['descripcion'])?></option>
      <?php } ?>
    </select></td>
    <td ><select name="sys_clasificacion_persona" id="sys_clasificacion_persona" class="required">
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
    <td><select name="id_religion" id="id_religion" class="required">
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
    <td><label class="fsLabel fsRequiredLabel" for="firstname">Numero de hijos <span>*</span></label></td>
    <td><label class="fsLabel fsRequiredLabel" for="label">Cliente Local/Extrangero<span>*</span></label></td>
    <td><label class="fsLabel fsRequiredLabel" for="label">Nacionalidad<span>*</span></label></td>
  </tr>
  <tr>
    <td><input type="text" id="numero_hijos" name="numero_hijos" size="20" value="<?php echo $data_p['numero_hijos']; ?>" class="required" /></td>
    <td><p>
      <label>
        <input type="radio" name="idtipo_cliente" value="<?php echo System::getInstance()->getEncrypt()->encrypt("1",$protect->getSessionID());?>" id="idtipo_cliente" <?php echo $data_p['id_clase']=="1"?'checked="checked"':''?>   />
        Local</label>
      <label>
        <input type="radio" name="idtipo_cliente" value="<?php echo System::getInstance()->getEncrypt()->encrypt("2",$protect->getSessionID());?>" id="idtipo_cliente"  <?php echo $data_p['id_clase']=="2"?'checked="checked"':''?>  />
        Extranjero</label>
    </p></td>
    <td><select name="id_nacionalidad" id="id_nacionalidad" class="required">
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
  </tr>
  <tr>
    <td><strong>Cuenta bancaria</strong></td>
    <td align="center">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><input type="text" id="cuenta_bancaria" name="cuenta_bancaria" size="20" value="<?php echo $data_p['cuenta_bancaria']; ?>" class="required" /></td>
    <td align="center">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <?php if (trim($_permisos['Cambios'])=="1"){ ?>
  <tr>
    <td colspan="3" align="center"><button type="button" id="update_personal" >Actualizar información</button></td>
  </tr>
  <?php } ?>
</table>
</form> 