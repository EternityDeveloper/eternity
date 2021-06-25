<?php

if (!isset($protect)){
	exit;
}

$hide_remove=0;

if (isset($_REQUEST['hide_remove'])){
	$hide_remove=$_REQUEST['hide_remove'];
}

$show_estatus=0;

if (isset($_REQUEST['show_estatus'])){
	$show_estatus=$_REQUEST['show_estatus'];
}

$show_add_ref=0;

if (isset($_REQUEST['show_add_ref'])){
	$show_add_ref=$_REQUEST['show_add_ref'];
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

$data_ref=array();


$rand=0;
if (isset($_REQUEST['rand'])){
	$rand=$_REQUEST['rand'];
} 

$addnew=0;
if (isset($_REQUEST['addnew'])){
	$addnew=$_REQUEST['addnew'];
} 

if ($addnew!=1){
	$data_ref=$person->getPersonalRef($client_id,$index);
	$data_ref=$data_ref[0];
}

 
?>
<form method="post"  action="" id="<?php echo $comp?>"  name="<?php echo $comp?>" class="fsForm">
<input type="hidden" name="form_referencia_submit" id="form_referencia_submit" value="1" />
<input type="hidden" id="id" name="id" size="20"  class="required" value="<?php echo $_REQUEST['client_id']?>" />
<input type="hidden" id="refer_id" name="refer_id" size="20"  class="required" value="<?php echo $_REQUEST['index']?>" />
<table width="100%" id="referencia_fields_option[]">

	<tr valign="top">
		<th align="left" valign="middle" scope="row"><label>Tipo referencia 
		    </label></th>
	  <td align="left" valign="top"><select name="referencia_tipo[]" id="referencia_tipo[]" class="required"   >
        <option value="">Seleccione</option>
        <?php 

$SQL="SELECT * FROM `sys_tipos_clasificacion` WHERE STATUS='A'  AND id_tipos_clasifica IN (10,2,9)";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_tipos_clasifica'],$protect->getSessionID());
?>
        <option value="<?php echo $encriptID?>" <?php echo $data_ref['tipo_refencia']==$row['id_tipos_clasifica']?'selected="selected"':''?>><?php echo $row['descripcion']?></option>
        <?php } ?>
      </select></td>
	    <td align="left" valign="middle"><strong>Nombre completo:</strong></td>
	    <td align="left" valign="top"><input name="referencia_nombre[]" type="text" class="required" id="referencia_nombre[]" value="<?php echo $data_ref['nombre_completo']?>" size="40" placeholder="Digite el nombre completo"    /></td>
  </tr>
	<tr valign="top">
	  <th align="left" valign="middle" scope="row">Telefono:</th>
	  <td valign="top"><input type="text" class="required" id="referencia_telefono_one[]" name="referencia_telefono_one[]" value="<?php echo $data_ref['telefono']?>" placeholder="Digite el telefono"    /></td>
	  <td valign="middle"><strong>Otro Telefono:</strong> </td>
	  <td valign="top"><input type="text" class="" id="referencia_telefono_two[]" name="referencia_telefono_two[]" value="<?php echo $data_ref['telefono_2']?>" placeholder="Digite el telefono"   /></td>
  </tr>
	<tr valign="top">
	  <th align="left" valign="middle" scope="row">Descripcion:</th>
	  <td valign="top"><textarea name="referencia_descripcion[]" id="referencia_descripcion[]"   ><?php echo $data_ref['observaciones']?></textarea></td>
	  <td valign="top">&nbsp;</td>
	  <td valign="top">&nbsp;</td>
  <tr valign="top" <?php echo $show_estatus==0?'style="display:none"':''?>  >
	  <th height="27" colspan="6" align="left" valign="middle" scope="row"><label class="fsLabel fsRequiredLabel" for="label">Estado<span>*</span></label>
	    <select name="ref_estado[]" id="ref_estado[]" class="required" >
           <?php 

$SQL="SELECT * FROM `sys_status` where id_status in (1,2) ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_status'],$protect->getSessionID());
?>
          <option value="<?php echo $encriptID?>" <?php echo $data_ref['status']==$row['id_status']?'selected="selected"':''?>><?php echo $row['descripcion']?></option>
          <?php } ?>
        </select></th>
  </tr>
  <tr valign="top" <?php echo $show_estatus==0?'style="display:none"':''?>  >
    <th height="27" colspan="6" align="center" valign="middle" scope="row"><button type="button" id="save_referencia_<?php echo $rand;?>">Guardar</button><button type="button" id="cerrar_ventana5">Cancelar</button></th>
  </tr>
    <tr valign="top" <?php echo $show_add_ref==0?'style="display:none"':'';?>>
     <th colspan="6" align="center" valign="middle"><button type="button" id="add_reference">Agregar referencia </button>&nbsp;<button type="button"  id="remove" <?php echo $hide_remove==1?'style="display:none"':'';?>>Cancelar</button></th>
   </tr> 
 
 
	<tr valign="top">
	  <th colspan="4" align="left" valign="middle" scope="row"><hr/></th>
  </tr> 
</table>

</form>