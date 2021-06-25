<?php
if (!isset($protect)){
	exit;
}


$remove_submit_bt=true;

if (isset($_REQUEST['config']['show_submit_bt'])){
	if ($_REQUEST['config']['show_submit_bt']=="false"){
		$remove_submit_bt=false;
	}
}
$show_activity=true;
if (isset($_REQUEST['config']['show_activity'])){
	if ($_REQUEST['config']['show_activity']=="false"){
		$show_activity=false;
	}
}

$show_form=true;
if (isset($_REQUEST['config']['show_form'])){
	if ($_REQUEST['config']['show_form']=="false"){
		$show_form=false;
	}
}



?>
<?php if ($show_form){ ?>
<form name="frm_new_actividad" id="frm_new_actividad" method="post" action="">
<?php } ?>
<table width="390" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
<?php
if ($show_activity){
?>
  <tr class="all_opction">
    <td align="right"><strong>Actividad:</strong></td>
    <td><select name="actividad" id="actividad">
      <option value="">Seleccionar</option>
      <?php 
	 	 $SQL="SELECT * FROM `actividades` where id_actividad in ('CITA','LLA','PRE','VSP') order by order_ ";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
	  ?>
      <option value="<?php echo $row['id_actividad']?>" <?php echo $row['id_actividad']==$data->id_actividad?'selected':'' ?>><?php echo $row['actividad'] ?></option>
      <?php } ?>
      </select></td>
  </tr>
<?php } ?> 
  <tr class="all_opction" style="display:none">
    <td align="right" ><strong>Fecha de <br>
      contacto:</strong></td>
    <td><input  name="fecha_contacto" type="text" class="textfield" style="cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;width:110px;padding-right:10px;" id="fecha_contacto" readonly></td>
  </tr>
  <tr class="all_opction">
    <td align="right" valign="top"><strong>Detalle <br>
      actividad:</strong></td>
    <td><textarea name="descripcion" class="textfield " id="descripcion" style="height:50px;"></textarea></td>
  </tr>
  <tr class="all_opction">
    <td align="right" ><strong>Fecha proximo<br>
      contacto:</strong></td>
    <td><input  name="fecha" type="text" class="textfield " style="cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;width:110px;padding-right:10px;" id="fecha" readonly></td>
  </tr>
  <tr  class="fields_hidden all_opction">
    <td align="right"><strong>Hora:</strong></td>
    <td><input type="text" class="textfield "  name="hora" id="hora" style="width:110px;"></td>
  </tr>
  <tr  class="fields_hidden all_opction">
    <td align="right" valign="top"><strong>Lugar:</strong></td>
    <td><textarea name="lugar" class="textfield " id="lugar" style="height:50px;"></textarea></td>
  </tr>
  <tr class="all_opction">
    <td align="right"><strong>Necesita Apoyo <br />
      de gerente?:</strong></td>
    <td><input name="is_apoyo" type="checkbox" id="is_apoyo" value="1" /></td>
  </tr>
  <tr class="pilar_actividad" style="display:none">
    <td align="right"><strong>Fuente de cierre<br />
      (Pilar)?:</strong></td>
    <td><select name="tipos_prospectos" id="tipos_prospectos"  class="textfield "  style="height:30px;">
      <option value="">Seleccionar</option>
      <?php 
	 	 $SQL="SELECT * FROM `tipos_pilares` WHERE estatus IN (1,2)";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
	  ?>
      <option value="<?php echo System::getInstance()->Encrypt(json_encode($row));?>" ><?php echo $row['dscrip_tipopilar'] ?></option>
      <?php } ?>
    </select></td>
  </tr>


  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
    <?php if ($remove_submit_bt){?>
  <tr>
    <td colspan="2" align="center"><button type="button" class="greenButton" id="bt_actividad_save"> Guardar</button>
      <button type="button" class="redButton" id="bt_actividad_cancel"> Cancel</button></td>
  </tr>
    <?php } ?>
</table>

<?php
if ($show_form){
?></form>
<?php } ?>
