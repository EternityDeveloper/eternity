<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}



 
$data=array();
if ($_REQUEST['edit']=="1"){
	$dencryt=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id'],$protect->getSessionID());
	$data=json_decode($dencryt);	
//	print_r($data);
} 

?>
<form name="form_interfase" id="form_interfase" method="post" action="" class="fsForm  fsSingleColumn">
<table width="300" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td align="right"><strong>ID Reserva:</strong></td>
    <td><input name="id_reserva" type="text" class="textfield textfieldsize" id="id_reserva" value="<?php if (count($data)>0){echo $data->id_reserva;}?>" <?php if (count($data)>0){echo "disabled";}?> maxlength="5" ></td>
  </tr>
  <tr>
    <td align="right"><strong>Descripcion:</strong></td>
    <td>
      
      <input type="text" class="textfield textfieldsize"  name="reserva_descrip" id="reserva_descrip" value="<?php if (count($data)>0){echo $data->reserva_descrip;}?>"></td>
  </tr>
  <tr>
    <td align="right"><strong>Horas:</strong></td>
    <td><input type="text" class="textfield textfieldsize"  name="horas" id="horas" value="<?php if (count($data)>0){echo $data->horas;}?>"></td>
  </tr>
  <tr>
    <td align="right"><strong>Gerencia:</strong></td>
    <td><input type="checkbox"  name="gerencia" id="gerencia" value="1"
    		 <?php if (count($data)>0){
					if ($data->gerencia=="1"){
						echo "checked='checked'";
					}
				}?>></td>
  </tr>
  <tr>
    <td align="right"><strong>Abono:</strong></td>
    <td><input type="checkbox"  name="abono" id="abono" value="1" <?php if (count($data)>0){
					if ($data->abono=="1"){
						echo "checked='checked'";
					}
				}?>></td>
  </tr>
<?php if ($_REQUEST['edit']=="1"){?>  
  <tr>
    <td align="right"><strong>Estatus:</strong></td>
    <td><select name="estatus" id="estatus">
      <option value="0">Seleccionar</option>
      <?php 
	 	 $SQL="SELECT * FROM `sys_status` WHERE id_status IN (1,2)";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
	  ?>
     	 <option value="<?php echo $row['id_status']?>" <?php echo $row['id_status']==$data->estatus?'selected':'' ?>><?php echo $row['descripcion'] ?></option>
      <?php } ?>
    </select></td>
  </tr>
<?php } ?>  
  <tr>
    <td colspan="2"><input name="submit_tipo_reserva" type="hidden" id="submit_tipo_reserva" value="1" />
      <input type="hidden"  name="edit" id="edit" value="<?php echo $_REQUEST['edit']?>">&nbsp;
      <input name="tipo_reserva" type="hidden" id="tipo_reserva" value="1">
      <input name="id" type="hidden" id="id" value="<?php echo $_REQUEST['id']?>"></td>
  </tr>
  <tr>
    <td colspan="2" align="center">   
                      <button type="button" class="greenButton" id="bt_save">
                        Guardar</button>
                       <button type="button" class="redButton" id="bt_cancel">
                        Cancel</button>  
        </td>
    </tr>
</table>
</form>