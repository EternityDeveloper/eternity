<?php 
	if (isset($_REQUEST['p_fecha_desde'])){
		$_SESSION['p_fecha_desde']=$_REQUEST['p_fecha_desde'];
		$_SESSION['p_fecha_hasta']=$_REQUEST['p_fecha_hasta'];
	}else{
		$_REQUEST['p_fecha_desde']=$_SESSION['p_fecha_desde'];
		$_REQUEST['p_fecha_hasta']=$_SESSION['p_fecha_hasta'];
	}
	
	$pendiente=false;
	if (isset($_REQUEST['pendiente_x_cobrar'])){
		if ($_REQUEST['pendiente_x_cobrar']=="true"){
			$pendiente=true;
		}
	}

$numero_contrato=0; 

  
$SQL="SELECT  labor_cobro.*,
	mf.*,
	mc.*,
	mf.fecha AS creacion,
	(mf.MONTO*mf.TIPO_CAMBIO) AS MONTO,
	CONCAT(mf.serie_contrato,' ',mc.no_contrato) AS contrato,
	tipo_movimiento.`DESCRIPCION` as TMOV,
	( 
	SELECT CONCAT(motorizado.`primer_nombre`,' ',
		motorizado.`segundo_nombre`,' ',
		motorizado.`primer_apellido`,' ',
		motorizado.segundo_apellido) FROM sys_personas AS motorizado WHERE id_nit=mf.ID_NIT_MOTORIZADO) AS responsable, 
	CONCAT(cliente.`primer_nombre`,' ',cliente.`segundo_nombre`,' ',cliente.`primer_apellido`,' ',cliente.segundo_apellido) AS cliente ,
	contratos.tipo_moneda as moneda	
FROM contratos 
INNER JOIN `movimiento_caja` AS mc ON (contratos.serie_contrato=mc.serie_contrato AND 
	mc.no_contrato=contratos.no_contrato)
INNER JOIN `labor_cobro` ON (labor_cobro.aviso_cobro=mc.NO_DOCTO AND labor_cobro.serie=mc.SERIE)	 
INNER JOIN `movimiento_factura` AS mf ON (mf.SERIE=mc.SERIE AND mf.NO_DOCTO=mc.NO_DOCTO)
INNER JOIN `sys_personas` AS cliente ON (cliente.id_nit=`contratos`.`id_nit_cliente`)
INNER JOIN `tipo_movimiento` ON (`tipo_movimiento`.TIPO_MOV=mf.TIPO_MOV)
WHERE  1=1 ";
 if (!$protect->getIfAccessPageById(184)){
	 $SQL.=" AND mf.ID_NIT_OFICIAL='".UserAccess::getInstance()->getIDNIT()."'";
 }
//	
if (validateField($_REQUEST,"p_fecha_desde") && validateField($_REQUEST,"p_fecha_desde")){
  	 $SQL.=" AND mc.FECHA_DOC BETWEEN STR_TO_DATE('".$_REQUEST['p_fecha_desde']."','%d-%m-%Y') and STR_TO_DATE('".$_REQUEST['p_fecha_hasta']."','%d-%m-%Y')"; 
}else{
	$SQL.=" AND mc.FECHA_DOC=CURDATE() ";
} 

if ($pendiente){
	$SQL.=" AND mf.CAJA_NO_DOCTO is null ";
}

 
 $valor=""; 
if (isset($_REQUEST['oficial'])){
	if (is_array($_REQUEST['oficial'])>0){
		foreach($_REQUEST['oficial'] as $key =>$val){
			$valor.="'".System::getInstance()->Decrypt($val)."',";
		}	
		$valor=substr($valor,0,strlen($valor)-1);
		 
	}
} 
if ($valor!=""){
	$SQL.=" AND mf.ID_NIT_OFICIAL IN (".$valor.")";
}

$SQL.=" ORDER BY  mc.FECHA_DOC desc ";	 
 
  
  
$rs=mysql_query($SQL); 
$data=array(); 
$numero_contrato=mysql_num_rows($rs);
while($row=mysql_fetch_assoc($rs)){
	array_push($data,$row);	
	$monto_total=$monto_total+$row['MONTO'];
}

 

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
    <td align="left">&nbsp;</td>
  </tr>
  <tr>
    <td width="17%"><strong>NO. CONTRATOS:&nbsp;<?php echo $numero_contrato;?></strong></td>
    <td width="83%" align="left"><strong>MONTO TOTAL:&nbsp;<?php echo number_format($monto_total,2);?></strong></td>
  </tr>
</table>
<table id="list_cartera" width="100%" border="0" cellpadding="0" cellspacing="0" class="fsDivPage" style="font-size:12px;border-spacing:1px;">
        <thead>
          <tr style="background-color:#CCC;height:30px;">
            <td align="center">
              <input type="checkbox" class="selectAll"  name="checkbox2" id="checkbox2" /></td>
            <td width="208" align="center"><strong>CONTRATO</strong></td>
            <td width="276" align="center"><strong>ACCION</strong></td>
            <td width="276" align="center"><strong>NOMBRES/APELLIDOS</strong></td>
            <td width="276" align="center"><strong>FECHA REQ.</strong></td>
            <td width="276" align="center"><strong>FECHA PAGO</strong></td>
            <td width="276" align="center"><strong>MONTO</strong></td>
            <td width="276" align="center"><strong>MOTORIZADO</strong></td>
            <td width="276" align="center"><strong>MONEDA</strong></td>
          </tr>
  </thead>
        <tbody>
<?php
$monto_total=0;
foreach($data as $key =>$row){
	//$numero_contrato=$numero_contrato+1;
	//$contrato=array("serie_contrato"=>$row['serie_contrato'],"no_contrato"=>$row['no_contrato']);
	$encriptID=System::getInstance()->Encrypt(json_encode($row)); 	
	$monto_total=$monto_total+$row['MONTO'];
?>
          <tr style="height:30px;cursor:pointer" >
            <td align="center"><input type="checkbox" class="individual_c" name="checkbox" id="checkbox"  value="<?php echo $encriptID;?>"/></td>
            <td align="center"><?php  echo strtoupper($row['contrato']);?></td>
            <td align="center"><?php  echo $row['TMOV'];?></td>
            <td align="center"><?php  echo $row['cliente'];?></td>
            <td align="center"><?php  echo $row['creacion'];?></td>
            <td align="center"><?php  echo $row['fecha_cobro'];?></td>
            <td align="center"><?php  echo number_format($row['MONTO'],2);?></td>
            <td align="center"><?php  echo $row['responsable'];?></td>
            <td align="center"><?php  echo $row['moneda'];?></td>
          </tr>
 <?php } ?>

        </tbody>
        <tfoot>
          <tr >
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">NO. CONTRATOS: <?php echo $numero_contrato;?></td>
            <td height="30" align="center"><strong><?php echo number_format($monto_total,2);?></strong></td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
          </tr>         
        </tfoot>
</table>