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



$new_c=0;
if (isset($_REQUEST['new_c'])){
	$new_c=1;
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

 

$person= new PersonalData($protect->getDBLink());

$client_id=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));

if (isset($client_id->id_nit)){
	$client_id=$client_id->id_nit;
}else{
	$client_id=System::getInstance()->Decrypt($_REQUEST['id']);	
}

 

$data_contact=array();
if ($new_c!=1){
	$data_contact=$person->getContactList($client_id,$index);
	$data_contact=$data_contact[0];
}



//$contact_id=System::getInstance()->getEncrypt()->encrypt($data_contact[$index]['id_contactos'],$protect->getSessionID());
$contact_id=$_REQUEST['index'];

?>
 
<div class="st_contactos">
  <ul>
    <li><a href="#tabs-contact-1">Datos contacto</a></li>
    <li><a href="#tabs-contact-2">Direccion</a></li>
    <li><a href="#tabs-contact-3">Telefono </a></li>
    <li><a href="#tabs-contact-4">Email</a></li>
  </ul> 
<div id="tabs-contact-1">
 
<form method="post"  action="" id="<?php echo $comp?>"  name="<?php echo $comp?>" class="fsForm">

<input type="hidden" name="form_contactos_submit" id="form_contactos_submit" value="1" />
<input type="hidden" id="id" name="id" size="20"  class="required" value="<?php echo $_REQUEST['client_id']?>" />
<input type="hidden" id="contact_id" name="contact_id" size="20"  class="required" value="<?php echo $_REQUEST['index']?>" />
<table width="100%" id="contactos_fields_option[]">
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
	<tr valign="top">
		<th align="left" valign="middle" scope="row"><label>Tipo contacto</label></th>
	  <td align="left" valign="top"><select name="contacto_tipo[]" id="contacto_tipo[]" class="required"   <?php echo $disable_option==1?'disabled="disabled"':''?>>
        <option value="">Seleccione</option>
        <?php 

$SQL="SELECT * FROM `sys_tipos_clasificacion` WHERE STATUS='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_tipos_clasifica'],$protect->getSessionID());
?>
        <option value="<?php echo $encriptID?>" <?php echo $data_contact['idtipo_contacto']==$row['id_tipos_clasifica']?'selected="selected"':''?> ><?php echo $row['descripcion']?></option>
        <?php } ?>
      </select></td>
	    <td align="left" valign="middle">&nbsp;</td>
	    <td align="left" valign="top">&nbsp;</td>
  </tr>
	<tr valign="top">
	  <td align="left" valign="middle"><strong>Nombre:</strong></td>
	  <td align="left"><input name="contactos_nombre[]" type="text" class="required" id="contactos_nombre[]" value="<?php echo $data_contact['Nombres']?>" size="40" placeholder="Digite el nombre"   <?php echo $disable_option==1?'disabled="disabled"':''?> /></td>
	  <td valign="middle"><strong>Apellido:</strong> </td>
	  <td valign="top"><input type="text" class="" id="contactos_apellido[]" name="contactos_apellido[]" value="<?php echo $data_contact['Apellidos']?>" placeholder="Digite el apellido"   <?php echo $disable_option==1?'disabled="disabled"':''?>/></td>
  </tr>
 
  <tr valign="top" <?php echo $show_estatus==0?'style="display:none"':''?>  >
	  <th height="27" colspan="6" align="left" valign="middle" scope="row"><label class="fsLabel fsRequiredLabel" for="label">Estado<span>*</span></label>
	    <select name="contacto_estado[]" id="contacto_estado[]" class="required" >
          <option value="">Seleccione</option>
          <?php 

$SQL="SELECT * FROM `sys_status` where id_status in (1,2) ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_status'],$protect->getSessionID());
?>
          <option value="<?php echo $encriptID?>"  <?php echo $data_contact['estatus']==$row['id_status']?'selected="selected"':''?>><?php echo $row['descripcion']?></option>
          <?php } ?>
        </select></th>
  </tr>
  <tr valign="top" <?php echo $show_estatus==0?'style="display:none"':''?>  >
    <th height="27" colspan="6" align="center" valign="middle" scope="row"><button type="button" id="cerrar_ventana">Cancelar</button></th>
  </tr>
    <tr valign="top" <?php echo $show_add_ref==0?'style="display:none"':'';?>>
     
     <th colspan="6" align="center" valign="middle">
     <button type="button" id="bt_contact_save">Agregar </button>&nbsp;&nbsp;
     <button type="button" id="remove"  <?php echo $hide_remove==1?'style="display:none"':'';?>>Cancelar</button></th>

</table>
</form>
 
</div> 
 <div id="tabs-contact-2">
 <button type="button" onclick="addNewContactAddress('address_<?php echo $contact_id?>')">Agregar direccion</button>
 <div id="address_<?php echo $contact_id?>">

 </div>
 
</div>
<div id="tabs-contact-3">
 <button type="button" onclick="addNewContactPhone('phone_<?php echo $contact_id?>')">Agregar telefono</button>
 <div id="phone_<?php echo $contact_id?>">

 </div> 
</div>
 <div id="tabs-contact-4">
 <button type="button" onclick="addNewContactEmail('email_<?php echo $contact_id?>')">Agregar Email</button>
 <div id="email_<?php echo $contact_id?>">

 </div> 
</div>

<!--DIV FINAL-->
</div>
 
