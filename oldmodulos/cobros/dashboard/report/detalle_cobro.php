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
if (validateField($_REQUEST,"fdesde") && validateField($_REQUEST,"fhasta") ){ 
   $filter['desde']=$_REQUEST['fdesde'];
   $filter['hasta']=$_REQUEST['fhasta'];   
} 
$detalle=$cobros->getDetalleCobroOficialMotorizado($filter); 
 
 
?>
<table width="100" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table border="0" cellpadding="0" cellspacing="0" class="table table-hover" style="font-size:12px;width:750px;">
      <thead>
        <tr style="background-color:#CCC;height:30px;">
          <td width="150" height="25" align="center"><strong>OFICIAL</strong></td>
          <td width="100" align="center"><strong>DOCUMENTO</strong></td>
          <td width="100" align="center"><strong>CONTRATO</strong></td>
          <td width="400" align="center"><strong>NOMBRE CLIENTE</strong></td>
          <td width="100" align="center"><strong>MOVIMIENTO</strong></td>
          <td width="100" align="center"><strong>MONTO</strong></td>
        </tr>
      </thead>
      <tbody>
        <?php
$monto_general=0; 
 
foreach($detalle['data'] as $keys=>$oficial){ 
 
if (trim($keys)==""){
	$keys=" NO IDENTIFICADO"; 
}
$monto_total=0;
?>
        <tr   style="height:30px;cursor:pointer;background:#E5E5E5" >
          <td colspan="6" align="left"><strong>
            <?php  echo strtoupper(utf8_encode($keys));?>
          </strong></td>
        </tr>
        <?php
foreach($oficial as $kym=>$motorizado){ 
	if (trim($kym)==""){$kym="NO IDENTIFICADO";}
?>
        <tr  class="oficial_cod_detalle"  style="height:30px;cursor:pointer;background:#F7F7F7 "  id="<?php echo str_replace(" ","",$keys."_".$kym);?>">
          <td colspan="5" align="left" style="padding-left:20px;"><strong>
            <?php  echo strtoupper(utf8_encode($kym));?>
          </strong></td>
          <td align="center"><strong>
            <?php 
	   $monto=0;
	   foreach($motorizado as $key=>$row){ $monto=$monto+$row['MONTO']; }
	   		echo number_format($monto,2);?>
          </strong></td>
        </tr>
        <?php
 

$comision=0;
foreach($motorizado as $key=>$row){ 
 
	$id=System::getInstance()->Encrypt(json_encode(array("serie_contrato"=>$row['serie_contrato'],"no_contrato"=>$row['no_contrato'],"id_nit"=>$row['ID_NIT'],"NO_DOCTO"=>$row['NO_DOCTO'],"SERIE"=>$row['SERIE']))); 
 	$monto_total=$monto_total+$row['MONTO'];
 
 //	$comision=$comision+($row['MONTO']*0.0050);	
?>
        <tr style="height:30px;display:none" class="<?php echo str_replace(" ","",$keys."_".$kym);?>"  
        <?php if (trim($row['TMOV'])!="CAPITAL"){?> onclick="doEditView('<?php echo $id;?>')"
         <?php } ?> >
          <td align="center">&nbsp;</td>
          <td align="center" style="cursor:pointer"  class="_document"  id="<?php echo $id;?>"><?php echo $row['DOCUMENTO'];?></td>
          <td align="center"><a href="?mod_cobros/delegate&amp;contrato_view&amp;id=<?php echo $id;?>" target="new"><?php echo $row['contrato'];?></a></td>
          <td align="left"><?php echo $row['NOMBRE_CLIENTE'];?></td>
          <td align="center"><?php echo $row['TIPO_MOV'];?></td>
          <td align="center"><?php echo number_format($row['MONTO'],2);?></td>
        </tr>
        <?php } ?>
        <?php } 
	$monto_general=$monto_general+$monto_total;
	?>
        <tr   style="height:30px;cursor:pointer" >
          <td colspan="5" align="right"><strong>TOTALES:</strong></td>
          <td align="center"><strong><?php echo number_format($monto_total,2);?></strong></td>
        </tr>
        <?php } ?>
        <tr style="background-color:#CCC;height:30px;">
          <td align="center"><strong>TOTALES</strong></td>
          <td align="center">&nbsp;</td>
          <td align="center">&nbsp;</td>
          <td align="center">&nbsp;</td>
          <td align="center">&nbsp;</td>
          <td align="center"><strong><?php echo number_format($monto_general,2);?></strong></td>
        </tr>
      </tbody>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><strong>POR MOTORIZADO</strong></td>
  </tr>
  <tr>
    <td>
    
    <table border="0" cellpadding="0" cellspacing="0" class="table table-hover" style="font-size:12px;width:750px;">
      <thead>
        <tr style="background-color:#CCC;height:30px;">
          <td width="150" height="25" align="center"><strong>MOTORIZADO</strong></td>
          <td width="100" align="center"><strong>DOCUMENTO</strong></td>
          <td width="100" align="center"><strong>CONTRATO</strong></td>
          <td width="400" align="center"><strong>NOMBRE CLIENTE</strong></td>
          <td width="100" align="center"><strong>MOVIMIENTO</strong></td>
          <td width="100" align="center"><strong>MONTO</strong></td>
        </tr>
      </thead>
      <tbody>
<?php
$monto_general=0;
foreach($detalle['motorizado'] as $kym=>$motorizado){ 
	if (trim($kym)==""){$kym="NO IDENTIFICADO";}
	
?>
        <tr  class="motorizado_detalle"  style="height:30px;cursor:pointer;background:#F7F7F7 "  id="_<?php echo str_replace(" ","",$kym);?>">
          <td colspan="5" align="left" style="padding-left:20px;"><strong>
            <?php  echo strtoupper(utf8_encode($kym));?>
          </strong></td>
          <td align="center"><strong>
            <?php 
	   $monto=0;
	   foreach($motorizado as $key=>$row){ $monto=$monto+$row['MONTO']; }
	   		echo number_format($monto,2);?>
          </strong></td>
        </tr>
        <?php
$monto_total=0;
foreach($motorizado as $key=>$row){ 
	$id=System::getInstance()->Encrypt(json_encode(array("serie_contrato"=>$row['serie_contrato'],"no_contrato"=>$row['no_contrato'],"id_nit"=>$row['ID_NIT'],"NO_DOCTO"=>$row['NO_DOCTO'],"SERIE"=>$row['SERIE']))); 
	
 	$monto_total=$monto_total+$row['MONTO'];
	$monto_general=$monto_general+$row['MONTO'];	
 //	$comision=$comision+($row['MONTO']*0.0050);	
?>
        <tr style="height:30px;cursor:pointer;display:none;" class="_<?php echo str_replace(" ","",$kym);?>"  >
          <td align="center">&nbsp;</td>
          <td align="center"   <?php if (trim($row['TMOV'])!="CAPITAL"){?>  onclick="doEditView('<?php echo $id;?>')" <?php } ?>><?php echo $row['DOCUMENTO'];?></td>
          <td align="center"><a href="?mod_cobros/delegate&amp;contrato_view&amp;id=<?php echo $id;?>" target="new"><?php echo $row['contrato'];?></a></td>
          <td align="left"><?php echo $row['NOMBRE_CLIENTE'];?></td>
          <td align="center"><?php echo $row['TIPO_MOV'];?></td>
          <td align="center"><?php echo number_format($row['MONTO'],2);?></td>
        </tr>
        <?php } ?>
 <?php } 

	?>
 
        <tr style="background-color:#CCC;height:30px;">
          <td align="center"><strong>TOTALES</strong></td>
          <td align="center">&nbsp;</td>
          <td align="center">&nbsp;</td>
          <td align="center">&nbsp;</td>
          <td align="center">&nbsp;</td>
          <td align="center"><strong><?php echo number_format($monto_general,2);?></strong></td>
        </tr>
      </tbody>
    </table></td>
  </tr>
</table>
