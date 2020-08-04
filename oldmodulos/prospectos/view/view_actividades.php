<?php
if (!isset($protect)){
	exit;
}

SystemHtml::getInstance()->includeClass("prospectos","Prospectos");
$prospecto= new Prospectos($protect->getDBLink(),$_REQUEST);

$prosp=json_decode(System::getInstance()->Decrypt($_REQUEST['prospecto_id']));
 
//print_r($prosp->correlativo);
$actividad=$prospecto->getLastActividad($prosp->id_nit,$prosp->correlativo);
 
?><table  width="100%" border="1">
        <tr>
          <td><h2 id="h_">L<span id="bt_add_seguir" title="Agregar más item al listado" class="positive"></span>istado de actividades</h2></td>
        </tr>
        <?php  if (count($actividad)<=1){?>
        <tr>
          <td><button type="button" class="orangeButton" id="bt_actividad_add">Registrar actividad</button>
            &nbsp;</td>
        </tr>
        <?php  } ?>
        <tr>
          <td><table id="tb_listado_actividad" width="100%" border="1" class="display">
            <thead>
              <tr>
                <td width="20%" align="center"><strong>Fecha Actividad</strong></td>
                <td width="12%" align="center"><strong>Actividad</strong></td>
                <td width="10%" align="center"><strong>Detalle</strong></td>
                <td width="7%" align="center"><strong>Hora</strong></td>
                <td width="8%"><strong>Lugar</strong></td>
                <td width="19%"><strong>Apoyo Gerente?</strong></td>
                <td width="24%">&nbsp;</td>
              </tr>
            </thead>
            <tbody>
<?php

$SQL="SELECT actividades.actividad as act_descripcion,
			tracking_prospecto.`fecha_inicio_cliente`,
			tracking_prospecto.`id_actividad`,
			tracking_prospecto.`hora`,
			tracking_prospecto.`lugar`,
			tracking_prospecto.`apoyo`,
			tracking_prospecto.detalle_actividad,
			tracking_prospecto.actividad_proxima,
			DATE_FORMAT(tracking_prospecto.fecha_proxima, '%d-%m-%Y') as fecha_proxima,
			DATEDIFF(CURDATE(),fecha_proxima) AS TIME_DIFERENCE
	FROM `tracking_prospecto`
INNER JOIN `actividades`  ON (`actividades`.`id_actividad`=tracking_prospecto.`id_actividad`)
WHERE tracking_prospecto.`correlativo`='".$prosp->correlativo."' and tracking_prospecto.id_nit='".$prosp->id_nit."'
ORDER BY tracking_prospecto.correlativo,tracking_prospecto.id DESC  ";
 
$rs=mysql_query($SQL);
$used=false;
while($row=mysql_fetch_assoc($rs)){	
	$id_actividad=System::getInstance()->Encrypt(json_encode($row));
?>
              <tr>
                <td align="center"><?php echo $row['fecha_proxima']?></td>
                <td align="center"><?php echo $row['act_descripcion']?></td>
                <td align="center"><?php echo $row['detalle_actividad']?></td>
                <td align="center"><?php echo $row['hora']?></td>
                <td align="center"><?php echo $row['lugar']?></td>
                <td align="center"><?php echo $row['apoyo']==1?'SI':'NO'?></td>
                <td><?php if((trim($row['actividad_proxima'])=="") && ($used==false) && ($row['id_actividad']!="CIE")){
					$used=true;
					?><button type="button" class="orangeButton" id="bt_close_actividad" value="<?php echo $id_actividad;?>">Cerrar activadad</button><?php } ?></td>
              </tr>
<?php } ?>
            </tbody>
          </table></td>
        </tr>
      </table>