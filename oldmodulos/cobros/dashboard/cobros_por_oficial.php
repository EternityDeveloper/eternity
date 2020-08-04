<?php
if (!isset($protect)){
	exit;
}
if (!$protect->getIfAccessPageById(173)){
	echo "No tiene permiso 173";
 	exit;
}

SystemHtml::getInstance()->includeClass("cobros","Cobros");    

$db = new Database(DB_SERVER, DB_USER, DB_PWD, DB_DATABASE); 
$db->connect(); 	  
	  
$cobros= new Cobros($protect->getDBLINK()); 

$filter=array();
$filter['action']='filter_range_date';
$filter['desde']=date('Y-m-d');
$filter['hasta']=date('Y-m-d');
if (validateField($_REQUEST,"fdesde") && validateField($_REQUEST,"fhasta")){ 
   $filter['desde']=$_REQUEST['fdesde'];
   $filter['hasta']=$_REQUEST['fhasta'];   
} 
$oficial=$cobros->getCobrosOficialXDia($filter);
$motorizado=$cobros->getCobrosMotorizadoXDia($filter);

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top"><table width="760" border="0" cellpadding="0" cellspacing="0" class="table table-hover">
      <thead>
        <tr style="background-color:#CCC;height:30px;">
          <td width="208" align="center"><strong>OFICIAL</strong></td>
          <td width="276" align="center"><strong>MONTO</strong></td>
          <td width="276" align="center"><strong>% Comisión</strong></td>
        </tr>
      </thead>
      <tbody>
        <?php
 
$monto=0;
$comision=0;
foreach($oficial as $key=>$row){ 
	//$encriptID=System::getInstance()->Encrypt(json_encode($contrato)); 	
 	$monto=$monto+$row['MONTO'];
 	$comision=$comision+($row['MONTO']*0.0050);	
?>
        <tr   style="height:30px;cursor:pointer" >
          <td align="center"><?php  echo strtoupper(utf8_encode($row['OFICIAL']));?></td>
          <td align="center"><?php  echo number_format($row['MONTO'],2);?></td>
          <td align="center"><?php  echo number_format(($row['MONTO']*0.0050),2);?></td>
        </tr>
        <?php } ?>
        <tr style="background-color:#CCC;height:30px;">
          <td align="center"><strong>TOTALES</strong></td>
          <td align="center"><strong><?php echo number_format($monto,2);?></strong></td>
          <td align="center"><strong><?php echo number_format($comision,2);;?></strong></td>
        </tr>
      </tbody>
    </table></td>
    <td width="40">&nbsp;</td>
    <td valign="top"><table width="760" border="0" cellpadding="0" cellspacing="0" class="table table-hover">
      <thead>
        <tr style="background-color:#CCC;height:30px;">
          <td width="208" align="center"><strong>MOTORIZADO</strong></td>
          <td width="276" align="center"><strong>MONTO</strong></td>
          <td width="276" align="center"><strong>% Comisión</strong></td>
        </tr>
      </thead>
      <tbody>
        <?php
 
$monto=0;
$comision=0;
foreach($motorizado as $key=>$row){ 
	//$encriptID=System::getInstance()->Encrypt(json_encode($contrato)); 	
 	$monto=$monto+$row['MONTO'];
 	$comision=$comision+($row['MONTO']*0.0050);	
?>
        <tr   style="height:30px;cursor:pointer" >
          <td align="center"><?php  echo strtoupper(utf8_encode($row['MOTORIZADO']));?></td>
          <td align="center"><?php  echo number_format($row['MONTO'],2);?></td>
          <td align="center"><?php  echo number_format(($row['MONTO']*0.0050),2);?></td>
        </tr>
        <?php } ?>
        <tr style="background-color:#CCC;height:30px;">
          <td align="center"><strong>TOTALES</strong></td>
          <td align="center"><strong><?php echo number_format($monto,2);?></strong></td>
          <td align="center"><strong><?php echo number_format($comision,2);;?></strong></td>
        </tr>
      </tbody>
    </table></td>
  </tr>
</table>
