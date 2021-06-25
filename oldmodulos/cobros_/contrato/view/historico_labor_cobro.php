<?php 
if (!isset($protect)){
	exit;	
}

SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
$cobros= new Cobros($protect->getDBLINK()); 
 
$labor_cobro=$cobros->getListLaborCobro($cdata->id_nit_cliente);
  
?><div class="fixed-table-container" style="width:90%;height:600px;">
    	<div style="background-color:#CCC;height:30px;"> </div>
        <div class="fixed-table-container-inner">
          <table id="_detalle_contrato"   border="1" style="border-spacing:1px;font-size:12px" class="tb_detalle fsDivPage table-hover">
            <thead>
              <tr   >
                <th width="208" ><div class="th-inner_hb"><strong>FECHA</strong></div></th>
                <th width="276" ><div class="th-inner_hb"><strong>ACCION</strong></div></th>
                <th width="276" ><div class="th-inner_hb"><strong>CONTACTO</strong></div></th>
                <th width="276" ><div class="th-inner_hb"><strong>DESCRIPCION CLIENTE</strong></div></th>
                <th width="276" ><div class="th-inner_hb"><strong>OBSERVACION</strong></div></th>
                <th width="208" ><div class="th-inner_hb"><strong>OFICIAL</strong></div></th>
                <th width="276" ><div class="th-inner_hb"><strong>PROXIMO CONTACTO</strong></div></th>
              </tr>
            </thead>
            <tbody>
<?php


if (count($labor_cobro)>0){
$i=0;
	foreach($labor_cobro as $key=>$val){ 
		$i++; 
?>
              <tr style="height:30px;">
                <td align="center"><?php echo $val['fecha_']?></td>
                <td align="center"><?php echo utf8_encode($val['accion'])?></td>
                <td align="center"><?php echo $val['contacto']?></td>
                <td align="center"><?php echo utf8_encode($val['comentario_cliente'])?></td>
                <td align="center"><?php echo utf8_encode($val['observaciones']); ?></td>
                <td align="center"><?php echo utf8_encode($val['nombre_motorizado']);?></td>
                <td align="center"><?php echo $val['proximo_contacto_']?></td>
              </tr>

 <?php } 
 
 }?>
            </tbody>
         
          </table>

 
        </div>
        <blockquote>&nbsp;</blockquote>
     </div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
</table>