<?php

$listado=FacturarPS::getInstance()->getCarProductoList();

?><table id="_detalle_abonos" width="100%" border="0" style="font-size:12px;border-spacing:1px;" class="tb_detalle table-hover">
  <thead>
    <tr  style="background-color:#CCC;height:30px;" >
      <td width="208" align="center"><strong>CODIGO</strong></td>
      <td width="276" align="center"><strong>DESCRIPCION</strong></td>
      <td width="276" align="center"><strong>CANTIDAD</strong></td>
      <td width="276" align="center"><strong>BASE</strong></td>
      <td align="center"><strong>NETO</strong></td>
      <td align="center"></td>
    </tr>
  </thead>
  <tbody>
<?php
foreach($listado as $key =>$row){
	$id=System::getInstance()->Encrypt(json_encode($row));
?>
    <tr style="height:30px;" class="abono_persona">
      <td align="center"><?php echo $row['producto']->id_producto;?></td>
      <td align="center"><?php echo $row['producto']->producto;?></td>
      <td align="center"><?php echo $row['cantidad'];?></td>
      <td align="center"><?php echo number_format($row['base'],2);?></td>
      <td align="center"><?php echo number_format($row['neto'],2);?></td>
      <td align="center"><button type="button" class="orangeButton prt_remove_inh" id="<?php echo $id;?>" >Eliminar</button></td>
    </tr>
<?php } ?>    
    <tr style="height: 30px; font-weight: bold;">
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
    </tr>
  </tbody>
</table>