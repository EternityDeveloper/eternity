<?php 
if (!isset($protect)){
	exit;	
}

SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
$cobros= new Cobros($protect->getDBLINK()); 
 
if (!isset($cache->listado_cuotas)){
	$avico=$cobros->getAvisoCobroData($cdata->serie_contrato,$cdata->no_contrato);
	$listado_cuotas=$cobros->getListCuotasContratos($cdata);
	SystemCache::GI()->doPutCache("listado_cuotas",$listado_cuotas);
	SystemCache::GI()->doPutCache("avico",$avico);
}else{
	$listado_cuotas=(array)$cache->listado_cuotas;	 
	$avico=(array)$cache->avico;	 
}
?><div class="fixed-table-container">
    	<div style="background-color:#CCC;height:30px;"> </div>
        <div class="fixed-table-container-inner">
          <table id="_detalle_contrato"   border="1" style="border-spacing:1px;font-size:12px" class="tb_detalle fsDivPage table-hover" >
            <thead>
              <tr>
                <th width="208" align="center"><div class="th-inner" style="width:70px;padding-top:5px;"><input name="check_cuota_0" type="checkbox" id="check_cuota_0" value="1"  /></div></th>
                <th width="208" ><div class="th-inner"><strong>FECHA</strong></div></th>
                <th width="276" ><div class="th-inner"><strong>FECHA VENCE</strong></div></th>
                <th width="276" ><div class="th-inner"><strong>NO. CUOTA</strong></div></th>
                <th width="276" ><div class="th-inner"><strong>MONTO NETO.</strong></div></th>
                <th width="276" ><div class="th-inner"><strong>CAPITAL</strong></div></th>
                <th width="276" ><div class="th-inner"><strong>INT. FINCMT.</strong></div></th>
                <th width="276" ><div class="th-inner"><strong>INT. MORA</strong></div></th>
                <th width="276" ><div class="th-inner"><strong>REF.</strong></div></th>
                <th width="276" ><div class="th-inner"><strong>MONTO</strong></div></th>
              </tr>
            </thead>
            <tbody>
<?php

 
if (count($listado_cuotas)>0){
$i=0;
	foreach($listado_cuotas as $key=>$val){ 
		$val=(array)$val;
		$i++;  
	 
?>
              <tr style="height:30px;">
                <td width="1" align="center"><?php if (!isset($avico[$i-1]['cuotas_acobrar'])){?><input type="checkbox" class="c_cuotas" disabled name="check_cuota_<?php echo $i?>" id="check_cuota_<?php echo $i?>" value="<?php echo System::getInstance()->Encrypt(json_encode($val));?>" data-monto="<?php echo $val['monto_neto']; ?>" /><?php }else{?><img src="images/current_work_1.png" width="24" height="24" /><?php } ?></td>
                <td align="center"><?php echo $val['fecha']?></td>
                <td align="center"><?php echo $val['fecha_vence']?></td>
                <td align="center"><?php echo $val['no_cuota']?></td>
                <td align="center"><?php echo number_format($val['monto_neto']+$val['monto_ref'],2); ?></td>
                <td align="center"><?php echo number_format($val['capital'],2);?></td>
                <td align="center"><?php echo number_format($val['intfincmt'],2); ?></td>
                <td align="center"><?php echo number_format($val['intcuota'],2); ?></td>
                <td align="center"><?php echo number_format($val['monto_ref'],2); ?></td>
                <td align="center"><?php echo number_format($val['intmora'],2); ?></td>
              </tr>

 <?php } 
 
 }?>
            </tbody>
         
          </table>
 
        </div>
     </div>