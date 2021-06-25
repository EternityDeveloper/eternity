<?php
if (!isset($protect)){
	exit;
}	

$data=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
 
?>
<div class="fsPage">
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td valign="top">
      <form name="form_prospecto" id="form_prospecto" method="post" action="" class="fsForm  fsSingleColumn">

      <table border="1" cellpadding="5"  style="border-spacing:10px;width:450px">
        <tr>
          <td colspan="2" align="center"><h2>Informacion&nbsp;</h2></td>
        </tr>
        <tr>
          <td align="right"><strong>Prospecto:</strong></td>
          <td width="70%"><?php echo $data->nombre_completo?>&nbsp;</td>
        </tr>
        <tr>
          <td align="right"><strong>Tipo Pilar:</strong></td>
          <td><?php echo $data->dscrip_tipopilar?> (<?php echo $data->pilar_inicial?>)</td>
        </tr>
        <tr >
          <td align="right" valign="top"><strong>Observacion:</strong></td>
          <td align="left"><?php echo $data->observaciones?></td>
        </tr>
        <tr >
          <td align="right"><strong>Fecha inicio:</strong></td>
          <td align="left"><?php echo $data->fecha_inicio?></td>
        </tr>
        <tr >
          <td align="right"><strong>Fecha fin:</strong></td>
          <td align="left"><?php echo $data->fecha_fin?></td>
        </tr>
        <tr >
          <td width="30%" align="right"><strong>Estado:</strong></td>
          <td align="left"><?php echo $data->estatus?></td>
        </tr>
        <tr >
          <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="center">&nbsp;</td>
            </tr>
            <tr>
              <td align="center"><h2>Preguntas</h2>
                &nbsp;</td>
            </tr>
            <tr>
              <td><table width="100%" border="0" cellspacing="5" cellpadding="2">
                <?php 
	$SQL="SELECT * FROM `detalle_prospecto`
  INNER JOIN `detalle_tipos_pilar` ON (`detalle_tipos_pilar`.`id_pregunta` = `detalle_prospecto`.`id_pregunta`)
 WHERE detalle_prospecto.`idtipo_pilar`='".$data->pilar_inicial."' AND detalle_prospecto.id_nit='".$data->id_nit."'";
 
	$rs=mysql_query($SQL);
	while($row=mysql_fetch_assoc($rs)){
	  ?>
                <tr>
                  <td width="60%" align="right"><strong><?php echo $row['pregunta_det_prospec']?></strong></td>
                  <td width="40%"><?php echo $row['respuesta']?>&nbsp;</td>
                </tr>
                <?php } ?>
              </table></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" align="center"></td>
        </tr>
      </table>
      </form>
      </td>
      <td width="60%" valign="top" id="actividades">
      
      </td>
    </tr>
    <tr>
      <td colspan="2" align="center" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" align="center" valign="top"><button type="button" class="redButton" id="bt_pro_f_cancel">Cerrar</button>&nbsp;</td>
      </tr>
    <tr>
      <td colspan="2" align="center" valign="top">&nbsp;</td>
    </tr>
  </table>

</div>