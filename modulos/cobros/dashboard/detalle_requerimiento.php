<?php
if (!isset($protect)){
	exit;
}
if (!$protect->getIfAccessPageById(173)){
	echo "No tiene permiso 173";
 	exit;
}

SystemHtml::getInstance()->includeClass("cobros","Cobros");    
	  
$cobros= new Cobros($protect->getDBLINK()); 
?><table width="1312" border="0" cellpadding="0" cellspacing="0" class="table table-hover">
  <thead>
    <tr style="background-color:#CCC;height:30px;">
      <td width="208" align="center"><strong>OFICIAL</strong></td>
      <td width="276" align="center"><strong>CANT. REQUERIMIENTO</strong></td>
      <td width="276" align="center"><strong>MONTO</strong></td>
      <td width="276" align="center"><strong>CANT. LLAMADAS</strong></td>
      <td width="276" align="center"><strong>CANT. MINUTOS</strong></td>
    </tr>
  </thead>
  <tbody>
    <?php
 

 //p_fecha_desde
 //p_fecha_hasta
$filter=array();
$filter['action']='filter_range_date';
$filter['desde']=date('Y-m-d');
$filter['hasta']=date('Y-m-d');
if (validateField($_REQUEST,"fdesde") && validateField($_REQUEST,"fhasta")){ 
   $filter['desde']=$_REQUEST['fdesde'];
   $filter['hasta']=$_REQUEST['fhasta'];   
} 
$my_cartera=$cobros->getDetalleLBC($protect->getIDNIT(),$filter);
$monto=0;
$cl=0;
$minutos=0;
$cant_req=0;
$llamadas=0;
$tiempo=0;

foreach($my_cartera as $key=>$row){ 
	//$encriptID=System::getInstance()->Encrypt(json_encode($contrato)); 
	$monto=$monto+$row['MONTO']; 
	$call=$cobros->getTotalOficialCall($row['extension'],$filter['desde'],$filter['hasta']);
	$cant_req=$cant_req+$row['TOTAL_REQUERIMIENTO'];
	if (count($call)>0){
		$cl=$cl+$call[0]['llamadas'];
		$minutos=$minutos+$call[0]['tiempo'];	
		$cant_req=$cant_req+ $row['cantidad'];
		$llamadas=$call[0]['llamadas'];
		$tiempo=$call[0]['tiempo'];
	}
 
?>
    <tr   style="height:30px;cursor:pointer" >
      <td align="center"><?php  echo strtoupper(utf8_encode($row['NOMBRE_OFICIAL']));?></td>
      <td align="center"><?php  echo $row['TOTAL_REQUERIMIENTO'];?></td>
      <td align="center"><?php  echo number_format($row['MONTO'],2);?></td>
      <td align="center"><?php  echo $llamadas;?></td>
      <td align="center"><?php  echo $tiempo;?></td>
    </tr>
    <?php } ?>
    <tr style="background-color:#CCC;height:30px;">
      <td align="center"><strong>TOTALES</strong></td>
      <td align="center"><strong><?php echo $cant_req;?></strong></td>
      <td align="center"><strong><?php echo number_format($monto,2);?></strong></td>
      <td align="center"><strong><?php echo $cl;?></strong></td>
      <td align="center"><strong><?php echo number_format($minutos/60,2);?> Horas</strong></td>
    </tr>    
  </tbody>
</table>
