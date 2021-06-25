<?php
if (!isset($protect)){
	exit;
}


?><table id="list_cartera" width="100%" border="0" cellpadding="0" cellspacing="0"  style="font-size:9px;border-spacing:1px;">
  <thead>
    <tr style="background-color:#CCC;height:30px;">
      <td width="208" align="center">&nbsp;</td>
      <td align="center"><strong>CONTRATO</strong></td>
      <td width="276" align="center"><strong>ESTATUS</strong></td>
      <td align="center"><strong>ULTIMO COMENTARIO</strong></td>
      <td align="center"><strong>AGENDADO PARA</strong></td>
      <td align="center"><strong>NOMBRES</strong><strong>/APELLIDOS</strong></td>
      <td align="center"><strong>FECHA ULTIMO PAGO</strong></td>
      <td width="276" align="center"><strong>DIAS VENCIDOS</strong></td>
      <td width="276" align="center"><strong>FECHA CREACION</strong></td>
      <td width="276" align="center"><strong>SALDO DE 0 A 30</strong></td>
      <td width="276" align="center"><strong>SALDO DE 31 A 60</strong></td>
      <td width="276" align="center"><strong>SALDO DE 61 A 90</strong></td>
      <td width="276" align="center"><strong>SALDO DE 91 A 120</strong></td>
      <td width="276" align="center"><strong>SALDO MAS 120</strong></td>
      <td width="276" align="center"><strong>MONTO TOTAL POR COBRAR</strong></td>
      <td width="276" align="center"><strong>FORMA DE PAGO</strong></td>
      <td width="276" align="center"><strong>AREA DE COBRO</strong></td>
    </tr>
  </thead>
  <tbody>
    <?php
 

 //p_fecha_desde
 //p_fecha_hasta
$filter=array();
if (validateField($_REQUEST,"p_fecha_desde") && validateField($_REQUEST,"p_fecha_hasta")){
   $filter['desde']=$_REQUEST['p_fecha_desde'];
   $filter['hasta']=$_REQUEST['p_fecha_hasta'];   
}    
if (validateField($_REQUEST,"por_forma_pago")){
	$filter['por_forma_pago']=$_REQUEST['por_forma_pago'];   
}

if (validateField($_REQUEST,"por_compromiso") && validateField($_REQUEST,"monto_compromiso")){
   $filter['monto_compromiso']=$_REQUEST['monto_compromiso'];
   $filter['por_compromiso']=$_REQUEST['por_compromiso'];   
} 


if (validateField($_REQUEST,"contrato_condicion") && validateField($_REQUEST,"contrato_cuota")){
   $filter['contrato_condicion']=$_REQUEST['contrato_condicion'];
   $filter['contrato_cuota']=$_REQUEST['contrato_cuota'];   
} 

if (validateField($_REQUEST,"tipo_cuota") ){
   $filter['tipo_cuota']=$_REQUEST['tipo_cuota'];   
}   


if (validateField($_REQUEST,"pendiente_de_pago") ){
   $filter['pendiente_de_pago']=$_REQUEST['pendiente_de_pago'];   
}  

if (validateField($_REQUEST,"por_estatus") ){
   $filter['por_estatus']=json_decode(System::getInstance()->Decrypt($_REQUEST['por_estatus'])); 
   $filter['por_estatus']=$filter['por_estatus']->id_status;  
}   
 

SystemHtml::getInstance()->includeClass("cobros","Cobros"); 

$cobros= new Cobros($protect->getDBLINK()); 

$oficial=array();
if (isset($_REQUEST['oficial'])){
	if (is_array($_REQUEST['oficial'])>0){
		foreach($_REQUEST['oficial'] as $key =>$val){
			$valor=System::getInstance()->Decrypt($val);
			$oficial[$valor]=$valor;
		}	
		$filter['oficial']=$oficial;
	}
}

$motorizado=array(); 
if (isset($_REQUEST['motorizado'])){
	$sp=$_REQUEST['motorizado']; 
	if (is_array($sp)>0){ 
		foreach($sp as $key =>$val){ 
			if ($val!=''){
				$valor=System::getInstance()->Decrypt($val);
				$motorizado[$valor]=$valor;
			}
		}	
		$filter['motorizado']=$motorizado;
	}
}  
  

$gerente=array();
if (isset($_REQUEST['gerente'])){
	if (is_array($_REQUEST['gerente'])>0){
		foreach($_REQUEST['gerente'] as $key =>$val){
			$valor=System::getInstance()->Decrypt($val); 
			$gerente[$valor]=$valor;
		}	
		$filter['gerente']=$gerente;
	}
}
  
 
 
$filter['por_saldos']=array();
if (validateField($_REQUEST,"por_saldos")){
	if (is_array($_REQUEST['por_saldos'])>0){
		foreach($_REQUEST['por_saldos'] as $key =>$val){			
			array_push($filter['por_saldos'],$val); 
		}	
 
	}	   
} 
 
$my_cartera=$cobros->getCarteraAsignadaOficial($protect->getIDNIT(),$filter);
$monto_total=0;
//$rs=mysql_query($SQL);
foreach($my_cartera as $key=>$row){
	$contrato=array("serie_contrato"=>$row['serie_contrato'],"no_contrato"=>$row['no_contrato'],"id_nit"=>$row['id_nit_cliente']);
	$encriptID=System::getInstance()->Encrypt(json_encode($contrato)); 	
	//print_r($row);
	$monto_total=$monto_total+$row['saldo_0_30']+$row['saldo_31_60']+$row['saldo_61_90']+$row['saldo_91_120']+$row['saldo_mas_120'];
?>
    <tr style="height:30px;cursor:pointer" onclick="doViewContrato('<?php echo $encriptID;?>',this)">
      <td align="center"><?php if ($row['pagos_realiz']>0){?><span class="round_meta"></span><?php } ?></td>
      <td align="center"><?php  echo strtoupper($row['serie_contrato'])." ".$row['no_contrato'];?></td>
      <td align="center"><?php  echo $row['estatus_name'];?></td>
      <td align="center"><?php  echo $row['comentario_cliente'];?></td>
      <td align="center"><?php  echo $row['fecha_proximo_contacto'];?></td>
      <td align="center"><?php  echo $row['nombre_cliente'];?> </td>
      <td align="center"><?php  echo $row['fecha_ultimo_pago'];?></td>
      <td align="center"><?php  echo $row['dias_vencidos'];?></td>
      <td align="center"><?php  echo $row['fecha_venta'];?></td>
      <td align="center"><?php  echo number_format($row['saldo_0_30'],2);?></td>
      <td align="center"><?php  echo number_format($row['saldo_31_60'],2);?></td>
      <td align="center"><?php  echo number_format($row['saldo_61_90'],2);?></td>
      <td align="center"><?php  echo number_format($row['saldo_91_120'],2);?></td>
      <td align="center"><?php  echo number_format($row['saldo_mas_120'],2);?></td>
      <td align="center"><?php echo  number_format($row['saldo_0_30']+$row['saldo_31_60']+$row['saldo_61_90']+$row['saldo_91_120']+$row['saldo_mas_120'],2);?></td>
      <td align="center"><?php  echo $row['forpago_name'];?></td>
      <td align="center"><?php  echo $row['zona_id'];?></td>
    </tr>
    <?php } ?>
    <tfoot>
    <tr  style="height:30px;cursor:pointer">
      <td colspan="8" align="right">TOTALES CONTRATOS: <?php echo count($my_cartera);?> </td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center"><?php echo number_format($monto_total,2);?>&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
    </tr>
    </tfoot>    
  <tr>
    <td>    
  </tbody>
</table>
