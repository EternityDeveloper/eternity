<?php
if (!isset($protect)){
	exit;
}
if (!isset($_REQUEST['id'])){ exit;}
$row=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));

if (!isset($row->idtipogestion)){ exit;}
 

SystemHtml::getInstance()->includeClass("client","PersonalData"); 
$person= new PersonalData($protect->getDBLink());

$escalamiento1="";
$escalamiento2="";
if (trim($row->escalamiento1)!=""){
	$dt=$person->getClientData($row->escalamiento1);
	$escalamiento1=$dt['nombre_completo']; 
}
if (trim($row->escalamiento2)!=""){
	$dt=$person->getClientData($row->escalamiento2);
	$escalamiento2=$dt['nombre_completo'];  
}
 
?> 
<form name="frm_cobro" id="frm_cobro" method="post" action="">
  <table width="600" border="0" cellspacing="0" cellpadding="0">
    <tr>
    <td><table width="390" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
      <tr >
        <td align="right" ><strong>ID Tipo Gestion:</strong></td>
        <td><input  name="idtipogestion" type="text" class="textfield " id="idtipogestion" style="width:110px;padding-right:10px;" maxlength="5" value="<?php echo $row->idtipogestion?>" readonly></td>
      </tr>
      <tr >
        <td align="right" valign="top"><strong>Descripción:</strong></td>
        <td><textarea name="gestion" class="textfield " id="gestion" style="height:50px;"><?php echo $row->gestion?></textarea></td>
      </tr>
      <tr >
        <td align="right" ><strong>Genera Actividad:</strong></td>
        <td><input name="genera_actividad" type="checkbox" id="genera_actividad"  value="1"  <?php echo $row->genera_actividad==1?'checked':'';?>></td>
      </tr>
      <tr >
        <td align="right"><strong>Tiempo Maximo:</strong></td>
        <td><input  name="Tiempo_max" type="text" class="textfield " style="width:200px;padding-right:10px;" id="Tiempo_max" value="<?php echo $row->Tiempo_max?>"></td>
      </tr>
      <tr >
        <td align="right"><strong>Escalamiento 1:</strong></td>
        <td><input  name="escalamiento1" type="text" class="textfield " style="width:200px;padding-right:10px;" id="escalamiento1" value="<?php echo $escalamiento1;?>"></td>
      </tr>
      <tr >
        <td align="right"><strong>Escalamiento 2:</strong></td>
        <td><input  name="escalamiento2" type="text" class="textfield " style="width:200px;padding-right:10px;" id="escalamiento2"  value="<?php echo $escalamiento2;?>"></td>
      </tr>
      <tr>
        <td colspan="2"><input name="save_new_caja" type="hidden" id="save_new_caja" value="1">
          <input name="escalamiento1_code" type="hidden" id="escalamiento1_code" value="<?php echo System::getInstance()->Encrypt($row->escalamiento1);?>">
          <input name="escalamiento2_code" type="hidden" id="escalamiento2_code" value="<?php echo System::getInstance()->Encrypt($row->escalamiento2);?>">
          <input name="id_gestion" type="hidden" id="id_gestion" value="<?php echo $_REQUEST['id'];?>" /></td>
      </tr>
      <tr>
        <td colspan="2" align="center"><button type="button" class="greenButton" id="bt_cobro_add">Guardar</button>
          <button type="button" class="redButton" id="bt_caja_cancel"> Cancel</button></td>
      </tr>
    </table></td>
    <td valign="top" bgcolor="#FFFFFF"  class="fsPage" style="background:#FFF"><table width="100%" border="0"  >
      <tr>
        <td width="150%"><h2> Listado de Actividades </h2></td>
      </tr>
      <tr>
        <td><button type="button" class="positive" name="abutton"  id="abutton" > <img src="images/apply2.png" alt=""/>Agregar</button></td>
      </tr>
      <tr>
        <td><table border="0" class="display" id="list_actividad" style="font-size:13px">
          <thead>
            <tr>
              <th>Descripcion </th>
              <th>Actividad Interna</th>
              <th>Tiempo Maximo</th>
              <th>Escalamiento 1</th>
              <th>Escalamiento 2</th>
              <th>&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            <?php
			
$escalamiento1="";
$escalamiento2="";
			
$SQL="SELECT * FROM tipo_actividades WHERE `idtipogestion`='".$row->idtipogestion."' ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
	
	if (trim($row['escalamiento1'])!=""){
		$dt=$person->getClientData($row['escalamiento1']);
		$escalamiento1=$dt['nombre_completo']; 
	}
	if (trim($row['escalamiento2'])!=""){
		$dt=$person->getClientData($row['escalamiento2']);
		$escalamiento2=$dt['nombre_completo'];  
	}		
?>
            <tr>
              <td height="25"><?php echo $row['actividad']?></td>
              <td><?php echo $row['act_int_ext']?></td>
              <td><?php echo $row['tiempo_max']?></td>
              <td><?php echo $escalamiento1?></td>
              <td><?php echo $escalamiento2?></td>
              <td align="center" ><a href="#" class="edit_list_actividad" id="<?php echo $encriptID;?>"><img src="images/clipboard_edit.png" alt=""  /></a></td>
            </tr>
            <?php 
}
 ?>
          </tbody>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
 </form>
 
