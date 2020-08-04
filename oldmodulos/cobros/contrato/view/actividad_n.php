<?php
if (!isset($protect)){
	exit;
}

SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
    
$gestion=json_decode(System::getInstance()->Decrypt($_REQUEST['gestion'])); 
$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 

$ofi_moto=Cobros::getInstance()->getCobradorMotorizadoAreaC($contrato->serie_contrato,$contrato->no_contrato);
 
?> <table width="100%" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle">
  <tr style="background:#CCCCCC">
    <td align="center"  ><strong>TAREAS</strong><strong></strong></td>
  </tr>
<?php 		
$field=array(); 	
$SQL="SELECT * FROM tipo_actividades WHERE `idtipogestion`='AVICO' ";
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
if ($row['idtipoact']=="AVICO"){
	
?>
  <tr>
    <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="codigo_act"  inc="<?php echo $i;?>" id="<?php echo System::getInstance()->Encrypt($ofi_moto['nitoficial']);  ?>" code="<?php echo System::getInstance()->Encrypt($row['idtipoact']);?>"><strong><?php echo $row['actividad']?></strong></td>
      </tr>
      <tr>
        <td><?php include("detalle_cuota.php");?></td>
      </tr>
      <tr>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="0" >
          <tr>
             
            <td align="center"><strong>EJECUTOR</strong>
              <?php 
	
	if ($row['asignar_actividad_a']=="ELEGIR"){
	?>
              <?php echo $ofi_moto['nombre_oficial']; ?>
              <input name="act_responsabe_id_<?php echo $i;?>" id="act_responsabe_id_<?php echo $i;?>" />
              <?php }else{ echo "N/A";} ?></td>
            <td align="center"><input  name="act_dia_realizacion_<?php echo $i;?>" type="text" class="d_calendar required" style="width:120px;padding-right:10px;cursor:pointer;background:url(images/calendar.png) no-repeat;;background-position:95% 50%;" id="act_dia_realizacion_<?php echo $i;?>"  inc="<?php echo $i;?>"  /></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
<?php }else{ ?>
 
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="0" cellpadding="0"  >
         
          <tr>
            <td colspan="2" inc="<?php echo $i;?>" class="codigo_act" id="<?php echo System::getInstance()->Encrypt($ofi_moto['nitmotorizado']);  ?>"  code="<?php echo System::getInstance()->Encrypt($row['idtipoact']);?>">&nbsp;<strong><?php echo $row['actividad']?></strong></td>
            </tr>
          <tr>
            <td>
              &nbsp;<strong>EJECUTOR</strong>
              <?php 
            
            if ($row['asignar_actividad_a']=="ELEGIR"){
            ?> 
             <?php echo $ofi_moto['nombre_motorizado']; ?>
              <input  name="act_responsabe_id_<?php echo $i;?>" id="act_responsabe_id_<?php echo $i;?>" />
              <?php }else{ echo "N/A";} ?> </td>
            <td align="center"><input  name="act_dia_realizacion_<?php echo $i;?>" type="text" class="d_calendar required" style="width:120px;padding-right:10px;cursor:pointer;background:url(images/calendar.png) no-repeat;;background-position:95% 50%;" id="act_dia_realizacion_<?php echo $i;?>"  inc="<?php echo $i;?>" /></td>
          </tr>
         
        </table>    
    </td> 
  </tr>
 
<?php } ?> 
  <?php
  
  
$i++;

 } ?>
</table>