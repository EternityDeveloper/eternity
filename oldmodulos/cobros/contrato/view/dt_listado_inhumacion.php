<?php 
if (!isset($protect)){
	exit;	
}

SystemHtml::getInstance()->includeClass("cobros","Inhumacion"); 
$inh= new Inhumacion($protect->getDBLINK()); 
 
$solicitud=$inh->getListadoInhumacion($cdata->serie_contrato,$cdata->no_contrato);
 
?><div class="fixed-table-container" style="width:900px">
    	<div style="background-color:#CCC;height:30px;"> </div>
        <div class="fixed-table-container-inner">
          <table id="_detalle_contrato"   border="1" style="border-spacing:1px;font-size:12px" class="tb_detalle fsDivPage">
            <thead>
              <tr   >
                <th width="157" ><div class="th-inner_hb"><strong>FECHA</strong></div></th>
                <th width="157" ><div class="th-inner_hb"><strong>ESTATUS</strong></div></th>
                <th width="229" ><div class="th-inner_hb"><strong>JARDIN</strong></div></th>
                <th width="229" ><div class="th-inner_hb"><strong>INHUMADO</strong></div></th>
                <th width="240" ><div class="th-inner_hb"><strong>CAVIDAD</strong></div></th>
                <th width="246" ><div class="th-inner_hb"><strong>CREADO POR</strong></div></th>
                <th width="24" ><div class="th-inner_hb"></div></th>
              </tr>
            </thead>
            <tbody>
<?php


if (count($solicitud)>0){
 
	foreach($solicitud as $key=>$row){ 
		$solicitud=System::getInstance()->Encrypt(json_encode($row)); 
		 
?>
              <tr style="height:30px;">
                <td align="center"><?php echo $row['fecha']?></td>
                <td align="center"><?php echo $row['estatus']?></td>
                <td align="center"><?php echo $row['id_jardin']."-".$row['id_fases']."-".$row['bloque']."-".$row['lote']." ".$row['osario']?></td>
                <td align="center"><?php echo $row['nombres']." ".$row['apellidos']?></td>
                <td align="center"><?php echo $row['cavidad']?></td>
                <td align="center"><?php echo $row['generado_por']?></td>
                <td align="center"><a href="./?mod_cobros/delegate&inhumado&orden_inhumacion_pdf&id=<?php echo $solicitud; ?>" target="new"><img src="images/document_preview.png" width="24" height="24"></a></td>
              </tr>

 <?php } 
 
 }?>
            </tbody>
         
          </table>

 
        </div>
     </div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
</table>