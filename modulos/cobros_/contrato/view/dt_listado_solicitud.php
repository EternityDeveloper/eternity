<?php 
if (!isset($protect)){
	exit;	
}

SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
$cobros= new Cobros($protect->getDBLINK()); 
 
$solicitud=$cobros->getSolicitudesAbonoCapital($cdata->serie_contrato,$cdata->no_contrato);
 
?><div class="fixed-table-container" style="width:900px">
    	<div style="background-color:#CCC;height:30px;"> </div>
        <div class="fixed-table-container-inner">
          <table id="_detalle_contrato"   border="1" style="border-spacing:1px;font-size:12px" class="tb_detalle fsDivPage">
            <thead>
              <tr   >
                <th width="157" ><div class="th-inner_hb"><strong>FECHA</strong></div></th>
                <th width="157" ><div class="th-inner_hb"><strong>ESTATUS</strong></div></th>
                <th width="229" ><div class="th-inner_hb"><strong>GESTION</strong></div></th>
                <th width="240" ><div class="th-inner_hb"><strong>DESCRIPCION</strong></div></th>
                <th width="246" ><div class="th-inner_hb"><strong>RESPONSABLE</strong></div></th>
                <th width="24" ><div class="th-inner_hb"></div></th>
              </tr>
            </thead>
            <tbody>
<?php


if (count($gestion)>0){
$i=0;
	foreach($solicitud as $key=>$val){
		$solicitud=System::getInstance()->Encrypt(json_encode($val)); 
		$i++; 
?>
              <tr style="height:30px;">
                <td align="center"><?php echo $val['fecha']?></td>
                <td align="center"><?php echo $val['estatus']?></td>
                <td align="center"><?php echo $val['gestion']?></td>
                <td align="center"><?php echo $val['descrip_general']?></td>
                <td align="center"><?php echo $val['responsable']?></td>
                <td align="center"><a href="./?mod_cobros/delegate&solicitud_gestion_abono&id=<?php echo $solicitud; ?>" target="new"><img src="images/document_preview.png" width="24" height="24"></a></td>
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