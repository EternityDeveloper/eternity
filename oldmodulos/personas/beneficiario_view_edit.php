<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

$type_person="";



$data=$_REQUEST['data'];


if ($_REQUEST['update']=="1"){
	SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
	$_contratos=new Contratos($protect->getDBLink(),$_REQUEST);
	echo json_encode($_contratos->updateBeneficiarioMenor($_REQUEST));
 
	exit;	
}

?>
<form method="post"  action="" id="client_form" class="fsForm">
        <table width="400" border="0" cellpadding="5" cellspacing="5" >
     
          <tr>
            <td><strong>
              <label class="fsLabel fsRequiredLabel" for="firstname">Primer nombre<span>*</span></label>
            </strong></td>
            <td><strong>
              <label class="fsLabel fsRequiredLabel" for="firstname">Segundo nombre</label>
            </strong></td>
          </tr>
          <tr>
            <td><input type="text" id="primer_nombre" name="primer_nombre" size="20" value="<?php echo $data['primer_nombre']?>" class="required" /></td>
            <td><input type="text" id="segundo_nombre" name="segundo_nombre" size="20" value="<?php echo $data['segundo_nombre']?>" /></td>
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
            <td><input type="text" id="primer_apellido" name="primer_apellido" size="20" value="<?php echo $data['primer_apellido']?>" class="required" /></td>
            <td><input type="text" id="segundo_apellido" name="segundo_apellido" size="20" value="<?php echo $data['segundo_apellido']?>" /></td>
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
            <td><input type="text" id="fecha_nacimiento" name="fecha_nacimiento" size="20" value="<?php echo $data['fecha_nacimiento']?>"  placeholder="Dia - Mes - Año" /></td>
            <td><select name="lugar_nacimiento" id="lugar_nacimiento" class="required">
              <option value="">Seleccione</option>
              <?php 

$SQL="SELECT * FROM `paises` ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['idPaises']);
?>
              <option <?php echo $data['lugar_nacimiento']==$row['Pais']?'selected':''?>><?php echo $row['Pais']?></option>
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
              <option <?php echo $data['parentesco_id']==$encriptID?'selected':''?> value="<?php echo $encriptID;?>"><?php echo $row['parentesco']?></option>
              <?php } ?>
            </select></td>
            <td>&nbsp;</td>
        
          </tr>
          <tr>
            <td colspan="3" align="center">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="3" align="center"><button type="button" id="procesar" >&nbsp;&nbsp;Guardar&nbsp;&nbsp;</button>&nbsp;<button type="button" id="procesar_cancel" >&nbsp;&nbsp;Cancelar&nbsp;&nbsp;</button></td>
          </tr>
        </table>
      </form>