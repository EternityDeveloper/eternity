<?php

if (!isset($protect)){
	exit;
}

$add_edit_buttom=0;
if (isset($_REQUEST['add_edit_buttom'])){
	$add_edit_buttom=1;
}

$rand=0;
if (isset($_REQUEST['rand'])){
	$rand=$_REQUEST['rand'];
}

$hide_remove=0;
if (isset($_REQUEST['hide_remove'])){
	$hide_remove=1;
}

$show_estatus=0;
if (isset($_REQUEST['show_estatus'])){
	$show_estatus=1;
}

$disable_option=0;
if (isset($_REQUEST['disable_option'])){
	$disable_option=1;
}


$comp="";
if (isset($_REQUEST['comp'])){
	$comp=$_REQUEST['comp'];
}

SystemHtml::getInstance()->includeClass("client","PersonalData");

$index=System::getInstance()->getEncrypt()->decrypt($_REQUEST['index'],$protect->getSessionID()); // $_REQUEST['index'];

$client_id=$_REQUEST['client_id'];

$person= new PersonalData($protect->getDBLink());

$client_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['client_id'],$protect->getSessionID());

$contact_id=0;
if (isset($_REQUEST['contact_id'])){
	$contact_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['contact_id'],$protect->getSessionID());
}  
$addnew=0;
if (isset($_REQUEST['addnew'])){
	$addnew=$_REQUEST['addnew'];
} 
$data_phone=array();
if ($addnew!=1){
	$data_phone=$person->getPhone($client_id,$contact_id,$index);
	$data_phone=$data_phone[0];
}
/*
 onclick="savePhone('<?php echo $comp?>')"
*/

?>
<form method="post"  action="" id="<?php echo $comp?>"  name="<?php echo $comp?>"  class="fsForm">
      <input type="hidden" name="form_phone_submit" id="form_phone_submit" value="1" />
      <input type="hidden" id="id" name="id" size="20"  class="required" value="<?php echo $_REQUEST['client_id']?>" />
	<input type="hidden" id="contact_id" name="contact_id" size="20"  class="required" value="<?php echo $_REQUEST['contact_id']?>" />      
  <input type="hidden" id="phone_id" name="phone_id" size="20" value="<?php echo $_REQUEST['index']?>" />
<table width="100%" class="form-table"  id="test5" >
	<tr valign="top" id="test4">
		<th align="left" valign="middle" scope="row"><label>Tipo telefono:</label></th>
		<td colspan="5" align="left" valign="middle" id="dsafd"><select name="telefonos_tipo[]" id="telefonos_tipo[]" class="required" >
            <option value="">Seleccione</option>
            <?php 

$SQL="SELECT * FROM `sys_tipos_clasificacion` WHERE STATUS='A' AND id_tipos_clasifica IN (1,7,2,6)";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_tipos_clasifica'],$protect->getSessionID());
?>
            <option value="<?php echo $encriptID?>" <?php echo $data_phone['tipo_telefono']==$row['id_tipos_clasifica']?'selected="selected"':''?>><?php echo $row['descripcion']?></option>
            <?php } ?>
          </select></td>
    </tr>
	<tr valign="top"  >
	  <th align="left" valign="middle" scope="row">Codigo de Area:</th>
	  <td align="left" valign="middle"><input name="telefono_area[]" type="text" class="required" id="telefono_area[]" value="<?php echo $data_phone['area'];?>" size="6" maxlength="4" placeholder="ej: 809" /></td>
      <td align="right" valign="middle" ><strong>Numero:</strong></td>
      <td align="left" valign="middle"><input type="text" class="required" id="telefonos[]" name="telefonos[]" value="<?php echo $data_phone['numero'];?>" placeholder="Digite el telefono"  /></td>
      <td align="right" valign="middle"><strong>Extension:</strong></td>
      <td align="left" valign="middle"><input type="text"  id="telefono_extension[]" name="telefono_extension[]" value="<?php echo $data_phone['extencion'];?>" placeholder="Digite la extension"  /></td>
  </tr >
  
  <tr valign="top" <?php echo $add_edit_buttom==0?'style="display:none"':''?> >
	  <th colspan="6" align="center" valign="middle" scope="row" ><button type="button" id="save_phone" >Agregar telefono </button> &nbsp; <button type="button"  id="remove"  <?php echo $hide_remove==1?'style="display:none"':''?>>Cancelar</button></th>
  </tr>
  <tr valign="top" <?php echo $show_estatus==0?'style="display:none"':''?>  >
	  <th height="27" colspan="6" align="left" valign="middle" scope="row"><label class="fsLabel fsRequiredLabel" for="label">Estado<span>*</span></label>
	    <select name="telefono_estado[]" id="telefono_estado[]" class="required" >
           <?php 

$SQL="SELECT * FROM `sys_status` where id_status in (1,2)  ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_status'],$protect->getSessionID());
?>
          <option value="<?php echo $encriptID?>" <?php echo $data_phone['status_telefono']==$row['id_status']?'selected="selected"':''?>><?php echo $row['descripcion']?></option>
          <?php } ?>
        </select></th>
  </tr>
  <tr valign="top" <?php echo $show_estatus==0?'style="display:none"':''?>  >
    <th height="27" colspan="6" align="center" valign="middle" scope="row"><button type="button" id="save_phone_bt_<?php echo $rand;?>" >Guardar</button>&nbsp;<button type="button" id="cerrar_ventana3">Cancelar</button></th>
  </tr>
 
	<tr valign="top">
	  <th colspan="6" valign="middle" scope="row"><hr/></th>
  </tr>
</table>
</form>