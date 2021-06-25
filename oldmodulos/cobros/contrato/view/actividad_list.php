<?php
if (!isset($protect)){
	exit;
}
if (!isset($_REQUEST['gestion'])){
	exit;
} 

$gestion=json_decode(System::getInstance()->Decrypt($_REQUEST['gestion'])); 

if (!isset($gestion->idtipogestion)){
	exit;
}


?> <table width="100%" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle">
  <tr style="background:#CCCCCC">
    <td align="center"  ><strong>Actividad</strong></td>
    <td align="center"  ><strong>Responsable</strong></td>
    <td align="center"  ><strong>Dia realización</strong></td>
  </tr>
<?php 		
$field=array(); 	
$SQL="SELECT * FROM tipo_actividades WHERE `idtipogestion`='".$gestion->idtipogestion."' ";
$rs=mysql_query($SQL);
$i=0;
while($row=mysql_fetch_assoc($rs)){ 
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
 	$info=array(
				"nomen"=>$i,
				"actividad"=>$encriptID,
				"tipo"=>$row['idtipoact']
				);
	array_push($field,$info);
?>
  <tr>
    <td align="center"><?php echo $row['actividad']?></td>
    <td align="center"><?php 
	
	if ($row['asignar_actividad_a']=="ELEGIR"){
	?><input  name="act_responsable_<?php echo $i;?>" type="text" class="textfield textfieldsize required" style="width:200px;padding-right:10px;" id="act_responsable_<?php echo $i;?>" />
    <input type="hidden" name="act_responsabe_id_<?php echo $i;?>" id="act_responsabe_id_<?php echo $i;?>" />
   <?php }else{ echo "N/A";} ?> </td>
    <td align="center"><input  name="act_dia_realizacion_<?php echo $i;?>" type="text" class="textfield textfieldsize required" style="width:85px;padding-right:10px;cursor:pointer;background:url(images/calendar.png) no-repeat;;background-position:95% 50%;" id="act_dia_realizacion_<?php echo $i;?>" /></td>
  </tr>
<?php
$i++;

 } ?>
</table>
<?php
 
echo json_encode(array("html"=>ob_get_clean(),"field"=>$field));

?>