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


$disable_option=0;
if (isset($_REQUEST['disable_option'])){
	$disable_option=1;
}

$show_add_buttom=0;
if (isset($_REQUEST['show_add_buttom'])){
	$show_add_buttom=1;
}


$show_add_email=0;
if (isset($_REQUEST['show_add_email'])){
	$show_add_email=1;
}

$rand=0;
if (isset($_REQUEST['rand'])){
	$rand=$_REQUEST['rand'];
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

$data_email=array();
if ($addnew!=1){
	$data_email=$person->getEmails($client_id,$contact_id,$index);
	$data_email=$data_email[0];
}
?>
 <form method="post"  action="" id="<?php echo $comp?>"  name="<?php echo $comp?>" class="fsForm">
	  
	  <input type="hidden" name="form_email_submit" id="form_email_submit" value="1" />
	  <input type="hidden" id="id" name="id" size="20"  class="required" value="<?php echo $_REQUEST['client_id']?>" />
      <input type="hidden" id="contact_id" name="contact_id" size="20"  class="required" value="<?php echo $_REQUEST['contact_id']?>" />
      <input type="hidden" id="email_id" name="email_id" size="20"  class="required" value="<?php echo $_REQUEST['index']?>" />
<table width="100%" id="email_field_option[]">


	<tr valign="top">
		<th align="left" valign="middle" scope="row"><label>Tipo correo 
		    </label></th>
	  <td align="left" valign="top"><select name="email_tipo[]" id="email_tipo[]" class="required"  >
        <option value="">Seleccione</option>
        <?php 

$SQL="SELECT * FROM `sys_tipos_clasificacion` WHERE STATUS='A'  AND id_tipos_clasifica IN (2,10)";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_tipos_clasifica'],$protect->getSessionID());
?>
        <option value="<?php echo $encriptID?>" <?php echo $data_email['tipos_email']==$row['id_tipos_clasifica']?'selected="selected"':''?>><?php echo $row['descripcion']?></option>
        <?php } ?>
      </select></td>
	    <td align="left" valign="middle">&nbsp;</td>
	    <td colspan="3" align="left" valign="top">&nbsp;</td>
    </tr>
	<tr valign="top">
	  <td align="left" valign="middle"><strong>Direccion</strong></td>
	  <td colspan="3" align="left"><input name="email_direccion[]" type="text" class="required" id="email_direccion[]" value="<?php echo $data_email['direccion']?>" size="40" placeholder="Digite la direccion de email"   /></td>
    </tr>
	<tr valign="top">
	  <td align="left"><strong>Observacion:</strong></td>
	  <td colspan="5" align="left"><textarea name="email_descripcion[]" id="email_descripcion[]"  ><?php echo $data_email['observaciones']?></textarea></td>
  </tr>
  <tr valign="top" <?php echo $show_estatus==0?'style="display:none"':''?>  >
	  <th height="27" colspan="6" align="left" valign="middle" scope="row"><label class="fsLabel fsRequiredLabel" for="label">Estado<span>*</span></label>
	    <select name="email_estado[]" id="email_estado[]" class="required" >
           
          <?php 

$SQL="SELECT * FROM `sys_status` where id_status in (1,2) ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_status'],$protect->getSessionID());
?>
          <option value="<?php echo $encriptID?>" <?php echo $data_email['status']==$row['id_status']?'selected="selected"':''?>><?php echo $row['descripcion']?></option>
          <?php } ?>
        </select></th>
  </tr>
  <tr valign="top" <?php echo $show_estatus==0?'style="display:none"':''?>  >
    <th height="27" colspan="6" align="center" valign="middle" scope="row"><button type="button" id="save_email_bt_<?php echo $rand?>"  >Guardar </button><button type="button" id="cerrar_ventana4">Cancelar</button></th>
  </tr>
   <tr valign="top" <?php echo $show_add_email==0?'style="display:none"':'';?>>
     <th colspan="6" align="center" valign="middle"><button type="button" id="emails_save_<?php echo $comp?>" src="<?php echo $comp?>"  >Agregar email </button>&nbsp;<button   <?php echo $hide_remove==1?'style="display:none"':'';?> type="button" id="remove">Cancelar</button></th>
   </tr>
  	<tr valign="top">
	  <th colspan="6" align="left" valign="middle" scope="row"><hr/></th>
  </tr>
</table>
</form>