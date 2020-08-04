<?php 
if (!isset($protect)){
	exit;	
} 


if (!isset($_REQUEST['id'])){
	exit;	
} 


$id=System::getInstance()->Decrypt($_REQUEST['id']);
 
 
$SQL=" SELECT capillas_obituario.* ,
	capillas_devices.`device_descripcion` AS nombre_capillas,
TIMESTAMPDIFF(HOUR,CURDATE(),DATE_ADD(CURDATE(),INTERVAL TIMESTAMPDIFF(HOUR,CONCAT(capillas_obituario.fecha_exposicion,' ',capillas_obituario.hora_exposicion),
	CONCAT(capillas_obituario.fecha_salida,' ',capillas_obituario.hora_salida)) HOUR)) AS RESTANTE	
FROM `capillas_obituario`
INNER JOIN `capillas_devices` ON (`capillas_devices`.id=capillas_obituario.`capillas_devices_id`)
where capillas_obituario.id='".$id."'
 order by capillas_obituario.id desc "; 
$rs=mysql_query($SQL);
$row=mysql_fetch_assoc($rs);
 
 
?>
<div class="modal fade" id="modal_editar_obituario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:730px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="title_template">AGREGAR OBITUARIOS</h4>
      </div>
      <div class="modal-body">
 <?php
 /*
 if (!$protect->getIfAccessPageById(180)){ 
	echo "No tiene permiso para realizar esta operacion!";
	exit;
  } */
 ?>    
 
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td id="items_cheque2"><table width="100%" border="0" cellspacing="5" cellpadding="5">
              <tr  >
                <td>Capillas:</td>
                <td><select name="capillas_obi" id="capillas_obi" class="form-control required" style="width:390px;" >
                  <option value="">Seleccione</option>
                  <?php 

$SQL="SELECT * FROM `capillas_devices`  WHERE `tipo_entrada_salida`='FUERA' AND `categoria_pantalla`='DISPLAY' ";
$rs=mysql_query($SQL);
while($rowx=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($rowx));
?>
                  <option value="<?php echo $encriptID?>" <?php if ($row['capillas_devices_id']==$rowx['id']){?> selected <?php } ?> ><?php echo $rowx['device_descripcion']?></option>
                  <?php } ?>
                </select></td>
              </tr>
              <tr>
                <td>Estatus</td>
                <td><select name="estatus" id="estatus" class="form-control required" style="width:390px;" >
                  <option value="">Seleccione</option>
                  <?php 

$SQL="SELECT id_status,descripcion FROM `sys_status` WHERE id_status IN (1,2)";
$rs=mysql_query($SQL);
while($rowx=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($rowx['id_status']);
?>
                  <option value="<?php echo $encriptID?>" <?php if ($rowx['id_status']==$row['estatus']){?> selected <?php } ?> ><?php echo $rowx['descripcion']?></option>
                  <?php } ?>
                </select></td>
              </tr>
              <tr>
                <td>Nombre completo:</td>
                <td><input type="text" name="nombre_completo_obi" id="nombre_completo_obi" class="form-control" value="<?php echo $row['nombre_completo'];?>"  ></td>
              </tr>
              <tr>
                <td>Fecha velacion:</td>
                <td><input type="text" name="fecha_velacion_obi" id="fecha_velacion_obi" class="form-control" style="width:200px;" value="<?php echo $row['fecha_exposicion'];?>" ></td>
              </tr>
              <tr>
                <td>Hora exposicion:</td>
                <td><input type="text" name="hora_exposicion_obi" id="hora_exposicion_obi" class="form-control" style="width:100px;" value="<?php echo $row['hora_exposicion'];?>" ></td>
              </tr>
              <tr>
                <td>Fecha salida</td>
                <td><input type="text" name="fecha_salida_obi" id="fecha_salida_obi" class="form-control" style="width:200px;" value="<?php echo $row['fecha_salida'];?>"></td>
              </tr>
              <tr>
                <td>Hora salida:</td>
                <td><input type="text" name="hora_salida_obi" id="hora_salida_obi" class="form-control" style="width:100px;" value="<?php echo $row['hora_salida'];?>" ></td>
              </tr>
              <tr>
                <td>Detalle inhumacion</td>
                <td><label for="detalle_inhumacion"></label>
                  <input type="text" name="detalle_inhumacion" id="detalle_inhumacion" class="form-control" value="<?php echo $row['detalle_inhumacion'];?>" ></td>
              </tr>
              <tr>
                <td>Cementerio (Inhumacion):</td>
                <td><input type="text" name="cementerio_obi" id="cementerio_obi" class="form-control" value="<?php echo $row['cementerio_ihumacion'];?>"  ></td>
              </tr>
              <tr>
                <td>Visita a la residencia:</td>
                <td><input type="text" name="visita_a_la_residencia_obi" id="visita_a_la_residencia_obi" class="form-control" value="<?php echo $row['visita_residencia'];?>" ></td>
              </tr>
              <tr>
                <td>Lectura de la palabra:</td>
                <td><input type="text" name="lectura_palabra_obi" id="lectura_palabra_obi" class="form-control" value="<?php echo $row['lectura_palabra'];?>" ></td>
              </tr>
              <tr>
                <td>Misa en capillas:</td>
                <td><input type="text" name="misa_en_capillas" id="misa_en_capillas" class="form-control" value="<?php echo $row['misa_en_capillas'];?>" ></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
          </tr>
          <tr>
            <td align="center"><button type="button" class="greenButton" id="bt_registro_cheque">&nbsp;Agregar</button>
              <button type="button" class="redButton" id="bt_chk_cancel">Cancelar</button>
              &nbsp;</td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
          </tr>
        </table>
  
      </div>
       
    </div>
  </div>
</div>