<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

$type_person="";

if (isset($_REQUEST['type_person'])){
	$type_person=	$_REQUEST['type_person'];
}

?>
<form method="post"  action="" id="client_form" class="fsForm">
        <table width="400" border="0" cellpadding="5" cellspacing="5" >
  <?php if ($type_person=="MAYOR"){?>      
          <tr>
          
            <td><strong>
              <label class="fsLabel fsRequiredLabel" for="label">Tipo de documento<span>*</span></label>
            </strong></td>
            <td><strong>
              <label class="fsLabel fsRequiredLabel" for="label">Numero de documento<span>*</span></label>
            </strong></td>
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
          </tr>
<?php  } ?>        
          <tr>
            <td><strong>
              <label class="fsLabel fsRequiredLabel" for="firstname">Primer nombre<span>*</span></label>
            </strong></td>
            <td><strong>
              <label class="fsLabel fsRequiredLabel" for="firstname">Segundo nombre</label>
            </strong></td>
          </tr>
          <tr>
            <td><input type="text" id="primer_nombre" name="primer_nombre" size="20" value="" class="required" /></td>
            <td><input type="text" id="segundo_nombre" name="segundo_nombre" size="20" value="" /></td>
          </tr>
          <tr>
            <td><strong>
              <label class="fsLabel fsRequiredLabel" for="firstname">Primer apellido <span>*</span></label>
            </strong></td>
            <td><strong>
              <label class="fsLabel fsRequiredLabel" for="firstname">Segundo apellido</label>
            </strong></td>
          </tr>
          <tr>
            <td><input type="text" id="primer_apellido" name="primer_apellido" size="20" value="" class="required" /></td>
            <td><input type="text" id="segundo_apellido" name="segundo_apellido" size="20" value="" /></td>
          </tr>
          <tr>
            <td><strong>
              <label class="fsLabel fsRequiredLabel" for="firstname">Fecha nacimiento</label>
            </strong></td>
            <td><strong>
              <label class="fsLabel fsRequiredLabel" for="firstname">Lugar de nacimiento</label>
            </strong></td>
          </tr>
          <tr>
            <td><input type="text" id="fecha_nacimiento" name="fecha_nacimiento" size="20" value=""  placeholder="Dia - Mes - Año" /></td>
            <td><select name="lugar_nacimiento" id="lugar_nacimiento" class="required">
              <option value="">Seleccione</option>
              <?php 

$SQL="SELECT * FROM `paises` ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['idPaises']);
?>
              <option <?php echo $data_p['lugar_nacimiento']==$row['Pais']?'selected':''?>><?php echo $row['Pais']?></option>
              <?php } ?>
            </select></td>
          </tr>
          <tr>
            <td><strong>Parentesco</strong></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
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
            <td>&nbsp;</td>
        
          </tr>
          <tr>
            <td colspan="3" align="center">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="3" align="center"><button type="button" id="procesar" >&nbsp;&nbsp;Guardar&nbsp;&nbsp;</button>&nbsp;&nbsp;<button type="button" id="procesar_cancel" >&nbsp;&nbsp;Cancelar&nbsp;&nbsp;</button></td>
          </tr>
        </table>
      </form>