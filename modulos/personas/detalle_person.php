<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	


?>
<form method="post"  action="" id="client_form" class="fsForm">
        <table width="100%" border="0" cellpadding="5" cellspacing="5" >
          <tr>
            <td><label class="fsLabel fsRequiredLabel" for="label">Tipo de documento<span>*</span></label></td>
            <td><label class="fsLabel fsRequiredLabel" for="label">Numero de documento<span>*</span></label></td>
            <td><label class="fsLabel fsRequiredLabel" for="label">Genero<span>*</span></label></td>
          </tr>
          <tr>
            <td><input type="hidden" name="form_submit" id="form_submit" value="1" />
              <select name="id_documento" id="id_documento" class="required">
                <option value="">Seleccione</option>
                <?php 

$SQL="SELECT * FROM `sys_documentos_identidad` WHERE STATUS='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_documento'],$protect->getSessionID());
?>
                <option value="<?php echo $encriptID?>"><?php echo $row['descripcion']?></option>
                <?php } ?>
              </select></td>
            <td><span class="fsLabel fsRequiredLabel">
              <input type="text" id="numero_documento" name="numero_documento" size="20" value="" class="required" />
            </span></td>
            <td><p>
              <label>
                <input type="radio" name="id_genero" value="<?php echo System::getInstance()->getEncrypt()->encrypt("1",$protect->getSessionID());?>" id="id_genero"   />
                Masculino</label>
              <label>
                <input type="radio" name="id_genero" value="<?php echo System::getInstance()->getEncrypt()->encrypt("2",$protect->getSessionID());?>" id="id_genero"   />
                Femenino</label>
            </p></td>
          </tr>
          <tr>
            <td><label class="fsLabel fsRequiredLabel" for="firstname">Primer nombre<span>*</span></label></td>
            <td><label class="fsLabel fsRequiredLabel" for="firstname">Segundo nombre</label></td>
            <td><label class="fsLabel fsRequiredLabel" for="firstname">Tercer nombre</label></td>
          </tr>
          <tr>
            <td><input type="text" id="primer_nombre" name="primer_nombre" size="20" value="" class="required" /></td>
            <td><input type="text" id="segundo_nombre" name="segundo_nombre" size="20" value="" /></td>
            <td><input type="text" id="tercer_nombre" name="tercer_nombre" size="20" value="" /></td>
          </tr>
          <tr>
            <td><label class="fsLabel fsRequiredLabel" for="firstname">Primer apellido <span>*</span></label></td>
            <td><label class="fsLabel fsRequiredLabel" for="firstname">Segundo apellido</label></td>
            <td><label class="fsLabel fsRequiredLabel" for="firstname">Apellido de casada </label></td>
          </tr>
          <tr>
            <td><input type="text" id="primer_apellido" name="primer_apellido" size="20" value="" class="required" /></td>
            <td><input type="text" id="segundo_apellido" name="segundo_apellido" size="20" value=""></td>
            <td><input type="text" id="apellido_conyuge" name="apellido_conyuge" size="20" value=""></td>
          </tr>
          <tr>
            <td><label class="fsLabel fsRequiredLabel" for="firstname">Fecha nacimiento<span>*</span></label></td>
            <td><label class="fsLabel fsRequiredLabel" for="firstname">Lugar de nacimiento<span>*</span></label></td>
            <td><label class="fsLabel fsRequiredLabel" for="label">Estado civil</label></td>
          </tr>
          <tr>
            <td><input type="text" id="fecha_nacimiento" name="fecha_nacimiento" size="20" value="" class="required" placeholder="Dia - Mes - A&ntilde;o" /></td>
            <td><input type="text" id="lugar_nacimiento" name="lugar_nacimiento" size="20" value="" class="required" /></td>
            <td><select name="id_estado_civil" id="id_estado_civil" >
              <option value="">Seleccione</option>
              <?php 

$SQL="SELECT * FROM `sys_estado_civil` WHERE status_2='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_estado_civil'],$protect->getSessionID());
?>
              <option value="<?php echo $encriptID?>"><?php echo $row['nombre']?></option>
              <?php } ?>
            </select></td>
          </tr>
          <tr>
            <td ><label class="fsLabel fsRequiredLabel" for="label">Profesi&oacute;n y oficio</label></td>
            <td ><label class="fsLabel fsRequiredLabel" for="label">Tipo Cliente</label></td>
            <td><label class="fsLabel fsRequiredLabel" for="label">Religion</label></td>
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
              <option value="<?php echo $encriptID?>"><?php echo ($row['descripcion']);?></option>
              <?php } ?>
            </select></td>
            <td ><select name="sys_clasificacion_persona" id="sys_clasificacion_persona" >
              <option value="">Seleccione</option>
              <?php 

$SQL="SELECT * FROM `sys_clasificacion_persona` WHERE status='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_clasificacion'],$protect->getSessionID());
?>
              <option value="<?php echo $encriptID?>"><?php echo $row['descripcion']?></option>
              <?php } ?>
            </select></td>
            <td><select name="id_religion" id="id_religion" >
              <option value="">Seleccione</option>
              <?php 

$SQL="SELECT * FROM `sys_religiones` WHERE status='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_religion'],$protect->getSessionID());
?>
              <option value="<?php echo $encriptID?>"><?php echo $row['descripcion']?></option>
              <?php } ?>
            </select></td>
          </tr>
          <tr>
            <td><label class="fsLabel fsRequiredLabel" for="firstname">Numero de hijos</label></td>
            <td><label class="fsLabel fsRequiredLabel" for="label">Cliente Local/Extrangero<span>*</span></label></td>
            <td><label class="fsLabel fsRequiredLabel" for="label">Nacionalidad<span>*</span></label></td>
          </tr>
          <tr>
            <td><input type="text" id="numero_hijos" name="numero_hijos" size="20" value="" /></td>
            <td><p>
              <label>
                <input type="radio" name="idtipo_cliente" value="<?php echo System::getInstance()->getEncrypt()->encrypt("1",$protect->getSessionID());?>" id="idtipo_cliente"   />
                Local</label>
              <label>
                <input type="radio" name="idtipo_cliente" value="<?php echo System::getInstance()->getEncrypt()->encrypt("2",$protect->getSessionID());?>" id="idtipo_cliente"  />
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
              <option value="<?php echo $encriptID?>"><?php echo ($row['Descripcion'])?></option>
              <?php } ?>
            </select></td>
          </tr>
          <tr>
            <td colspan="3" align="center">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="3" align="center"><button type="button" id="procesar" >&nbsp;&nbsp;Guardar&nbsp;&nbsp;</button></td>
          </tr>
        </table>
      </form>