<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}
 

if ($_REQUEST['edit']=="1"){
	$data=array();
	$dencryt=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id'],$protect->getSessionID());
	$data=json_decode($dencryt);	
 
} 
 
?>
<form name="form_user_edit" id="form_user_edit" method="post" action="" class="fsForm  fsSingleColumn">
<table width="300" border="1" class="fsPage">
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td align="right"><strong>Codigo:</strong></td>
    <td><input name="id_jardin_code" type="text" <?php if (count($data)>0){echo ' disabled="disabled" ';}?>id="id_jardin_code" value="<?php if (count($data)>0){echo $data->id_jardin;}?>"></td>
  </tr>
  <tr>
    <td align="right"><strong>Jardin:</strong></td>
    <td><input type="text" name="jardin" id="jardin" value="<?php if (count($data)>0){echo $data->jardin;}?>" /></td>
  </tr>
  <tr >
    <td align="right" valign="middle"><strong>Estatus:</strong></td>
    <td align="left"><select name="estado" id="estado" class="required">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT * FROM `sys_status` where id_status in (1,2)";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['id_status']);
?>
      <option value="<?php echo $encriptID?>"  <?php 
	  if (count($data)>0){ 
	 	 echo $data->estatus==$row['id_status']?'selected="selected"':'';
	  } ?>><?php echo $row['descripcion']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td colspan="2"><input name="submit_nomenclatura_jardin" type="hidden" id="submit_nomenclatura_jardin" value="1" />
      <input type="hidden" name="edit" id="edit" value="<?php echo $_REQUEST['edit']?>">
      <input name="id_jardin" type="hidden" id="id_jardin" value="<?php if (count($data)>0){echo $data->id_jardin;}?>" /></td>
  </tr>
  <tr>
    <td colspan="2" align="center">   
                      <button type="button" class="positive" id="bt_save">
                        <img src="images/apply2.png" alt=""/> 
                        Guardar</button>
                       <button type="button" class="positive" id="bt_cancel">
                        <img src="images/cross.png" alt=""/> 
                        Cancel</button>  
        </td>
    </tr>
</table>
</form>