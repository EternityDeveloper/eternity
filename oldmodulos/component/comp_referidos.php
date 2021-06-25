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

$rand=0;
if (isset($_REQUEST['rand'])){
	$rand=$_REQUEST['rand'];
}

SystemHtml::getInstance()->includeClass("client","PersonalData");

$index=System::getInstance()->getEncrypt()->decrypt($_REQUEST['index'],$protect->getSessionID()); // $_REQUEST['index'];

$client_id=$_REQUEST['client_id'];

$person= new PersonalData($protect->getDBLink());

$client_id=System::getInstance()->getEncrypt()->decrypt($_REQUEST['client_id'],$protect->getSessionID());

$data_ref=array();

$addnew=0;
if (isset($_REQUEST['addnew'])){
	$addnew=$_REQUEST['addnew'];
} 

if ($addnew!=1){
	$data_ref=$person->getPersonalRefefencia($client_id,$index);
	$data_ref=$data_ref[0];
}

 
?>
<form method="post"  action="" id="<?php echo $comp?>"  name="<?php echo $comp?>" class="fsForm">
<input type="hidden" name="form_referidos_submit" id="form_referencia_submit" value="1" />
<input type="hidden" id="id" name="id" size="20"   value="<?php echo $_REQUEST['client_id']?>" />
<table width="600" align="center" id="referencia_fields_option[]">

	<tr valign="top">
	  <th align="left" valign="middle" scope="row"><strong>Primer nombre:</strong></th>
	  <td align="left" valign="top"><input name="nombre1" type="text"  id="nombre1" value="<?php echo $data_ref['nombre1']?>" size="20" placeholder="Primer nombre"    /></td>
	  <td align="left" valign="middle"><strong>Segundo nombre:</strong></td>
	  <td align="left" valign="top"><input name="nombre2" type="text"  id="nombre2" value="<?php echo $data_ref['nombre2']?>" size="20" placeholder="Segundo nombre"    /></td>
    </tr>
	<tr valign="top">
	  <th align="left" valign="middle" scope="row">Primer Apellido</th>
	  <td align="left" valign="top"><input name="apellido1" type="text"  id="apellido1" value="<?php echo $data_ref['apellido1']?>" size="20" placeholder="Primer Apellido"    /></td>
	  <td align="left" valign="middle"><strong>Segundo Apellido:</strong></td>
	  <td align="left" valign="top"><input name="apellido2" type="text"  id="apellido2" value="<?php echo $data_ref['apellido2']?>" size="20" placeholder="Segundo Apellido"    /></td>
    </tr>
	<tr valign="top">
	  <th align="left" valign="middle" scope="row">Telefono:</th>
	  <td><input type="text"  id="telefono" name="telefono" value="<?php echo $data_ref['telefono']?>" placeholder="Digite el telefono"    /></td>
	  <th align="left" valign="middle" scope="row">Telefono Movil:</th>
	  <td><input type="text"  id="movil" name="movil" value="<?php echo $data_ref['movil']?>" placeholder="Digite el telefono movil"    /></td>
	</tr>
	<tr valign="top">
	  <th align="left" valign="middle" scope="row">Descripcion:</th>
	  <td valign="top"><textarea name="descripcion" id="descripcion"   ><?php echo $data_ref['descripcion']?></textarea></td>
	  <td valign="top">&nbsp;</td>
	  <td valign="top">&nbsp;</td>
    <tr valign="top"  >
      <th height="27" colspan="6" align="left" valign="middle" scope="row"><label>Tipo referencia:
        <select name="referencia_tipo" id="referencia_tipo" class="required"   >
          <option value="">Seleccione</option>
          <?php 

$SQL="SELECT * FROM `sys_tipos_clasificacion` WHERE STATUS='A'  AND id_tipos_clasifica IN (10,2,9)";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_tipos_clasifica'],$protect->getSessionID());
?>
          <option value="<?php echo $encriptID?>" <?php echo $data_ref['tipo_refencia']==$row['id_tipos_clasifica']?'selected="selected"':''?>><?php echo $row['descripcion']?></option>
          <?php } ?>
        </select>
      </label></th>
    </tr>
    <tr valign="top" <?php echo $show_estatus==0?'style="display:none"':''?>  >
	  <th height="27" colspan="6" align="left" valign="middle" scope="row"><label class="fsLabel fsRequiredLabel" for="label">Estado<span>*</span></label>
	    <select name="ref_estado" id="ref_estado" class="required" >
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
    <th height="27" colspan="6" align="center" valign="middle" scope="row"><button type="button" id="bt_save_ref_<?php echo $rand;?>">Guardar</button><button type="button" id="cerrar_ventana5">Cancelar</button></th>
  </tr>
    <tr valign="top" <?php echo $show_add_ref==0?'style="display:none"':'';?>>
     <th colspan="6" align="center" valign="middle"><button type="button" id="bt_add_referido">Agregar referido </button>&nbsp;<button type="button"  id="remove" <?php echo $hide_remove==1?'style="display:none"':'';?>>Cancelar</button></th>
   </tr> 
 
 
	<tr valign="top">
	  <th colspan="4" align="left" valign="middle" scope="row"><hr/></th>
  </tr> 
</table>

</form>