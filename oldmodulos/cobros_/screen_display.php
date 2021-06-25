<?php
 	
SystemHtml::getInstance()->includeClass("cobros","Cobros");  
SystemHtml::getInstance()->addTagStyle("css/demo_table.css");

SystemHtml::getInstance()->addModule("header");	  

$cobros= new Cobros($protect->getDBLINK()); 
$detalle_c=$cobros->detalleCartera($protect->getIDNIT());
$detall_cc=$cobros->getDetalleCarteraCobrada($protect->getIDNIT());
	 
//$db = new Database(DB_SERVER, DB_USER, DB_PWD, DB_DATABASE); 
//$db->connect(); 	  
	  
$cobros= new Cobros($protect->getDBLINK()); 


$filter=array();
$filter['action']='filter_range_date';
$filter['desde']=date('Y-m-01');
$filter['hasta']=date('Y-m-30');

$detalle=$cobros->getDetalleCobroOficialMotorizado($filter);   

$detalle_des_anul=$cobros->detalle_desestimiento_anulacion($protect->getIDNIT());
	 
?>
<script>
setTimeout(function(){
	window.location.reload();	
},25000);

memorial.LoadComplete("ON_TIME_OUT");
</script>
<style>
.AlertColorDanger td
{
 color:#FFF;
 background-color: #D90000 !important;
}
.AlertColor5 td
{
 color:#000;
 background-color: #FFD24D !important;
}
._document{}

</style>
<link type="text/css" href="css/style.css?nocache=1081204003" rel="stylesheet"/>
<link type="text/css" href="css/bar_menu.css?nocache=1081204003" rel="stylesheet"/>
<link type="text/css" href="css/select2-bootstrap.css?nocache=1081204003" rel="stylesheet"/>
<link type="text/css" href="css/select2.css?nocache=1081204003" rel="stylesheet"/>
<link type="text/css" href="css/tinyeditor.css?nocache=1081204003" rel="stylesheet"/>
<link type="text/css" href="css/south-street/jquery-ui-1.10.3.custom.css?nocache=1081204003" rel="stylesheet"/>
<link type="text/css" href="css/bootstrap/css/bootstrap.min.css?nocache=1081204003" rel="stylesheet"/>
<link type="text/css" href="css/jquery.dataTables.css?nocache=1081204003" rel="stylesheet"/>
<link type="text/css" href="css/smoothness/jquery.ui.combogrid.css?nocache=1081204003" rel="stylesheet"/>
<link type="text/css" href="css/demo_table.css?nocache=1081204003" rel="stylesheet"/>
<style>
.border_r{ 
	border-radius: 4px 4px 4px 4px;
	-moz-border-radius: 4px 4px 4px 4px;
	-webkit-border-radius: 4px 4px 4px 4px;
	border: 0px solid #000000;
}
</style>
<table width="900" border="0" cellspacing="0" cellpadding="0" style="font-size:9px;">
  <tr>
    <td height="10" colspan="2" align="center" valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="top">
    <table width="100" border="0" cellspacing="0" cellpadding="0" >
      <tr>
        <td width="0" style="padding:0;margin:0"> 
            <table width="145px" cellspacing="3" cellpadding="3" class="border_r" style="font-size:17px;background-color:#428bca;color:#FFF;">
              <tr>
                <td><?php echo $detalle_c['total_clientes'];?>&nbsp;</td>
                <td><?php echo $detall_cc['NO_CLIENTES']?>&nbsp;</td>
              </tr>
              <tr>
                <td colspan="2" style="font-size:12px;">TOTAL CLIENTES</td>
              </tr>
            </table>
           </td>
        <td>&nbsp;</td>
        <td width="150" style="padding:0;margin:0">
         <table width="160"   class="border_r" style="font-size:17px;background-color:#428bca;color:#FFF;"> 
          <tr>
            <td > <?php echo $detalle_c['total_contratos'];?>&nbsp;</td>
            <td  ><?php echo $detall_cc['NO_CONTRATO']?>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2" style="font-size:12px;">TOTAL CONTRATOS</td>
          </tr>
        </table>
                 
                 
        </td>
        <td width="400"> <table width="300"   class="border_r" style="font-size:17px;background-color:#428bca;color:#FFF;margin:5px;">
          <tr>
            <td width="50"> <?php echo number_format($detalle_c['monto_meta'],2);?> </td>
            <td width="50"><?php echo number_format($detall_cc['MONTO'],2);?></td>
          </tr>
          <tr>
            <td colspan="2" style="font-size:12px;">META</td>
          </tr>
        </table></td>
        <td><table width="200" border="0"  class="border_r" style="font-size:17px;background-color:#FF2222;color:#FFF;">
          <tr>
            <td width="50px"><?php echo $detalle_des_anul['POSIBLE_DES'];?></td>
            <td width="50px"><?php echo $detalle_des_anul['POSIBLE_ANUL'];?></td>
          </tr>
          <tr>
            <td style="font-size:12px;">POR DESISTIR </td>
            <td style="font-size:12px;">POR ANULAR</td>
          </tr>
        </table></td>
      </tr>
    </table>
       </td>
  </tr>
  <tr>
    <td align="center" valign="top" style="font-size:16px;"><strong>ACUMULADO EN EL MES</strong></td>
    <td align="center" valign="top" style="font-size:16px;"><strong>HOY</strong></td>
  </tr>
  <tr>
    <td width="583" align="center" valign="top"><table width="100" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" cellpadding="0" cellspacing="0"  style="width:400px;">
          <thead>
            <tr style="background-color:#CCC;height:10px;">
              <td height="20" align="left"><strong>OFICIAL</strong></td>
              <td align="center"><strong>MONTO</strong></td>
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
 
	foreach($oficial as $kym=>$motorizado){ 
		if (trim($kym)==""){$kym="NO IDENTIFICADO";}
	
		$comision=0;
		foreach($motorizado as $key=>$row){ 
			$monto_total=$monto_total+$row['MONTO'];
		}	
	} 
	$monto_general=$monto_general+$monto_total;	
?>
            <tr  class="oficial_cod_detalle"  style="height:20px;cursor:pointer;background:#F7F7F7;border-bottom:#999 solid 1px;"  id="<?php echo str_replace(" ","",$keys."_".$kym);?>">
              <td align="left" style="padding-left:20px;"><strong>
                <?php  echo strtoupper(utf8_encode($keys));?>
              </strong></td>
              <td align="center"><strong>
                <?php echo number_format($monto_total,2);?>
              </strong></td>
            </tr>
<?php 

} ?>
            <tr style="background-color:#CCC;height:20px;">
              <td align="right"><strong>TOTALES</strong></td>
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
        <td><table border="0" cellpadding="0" cellspacing="0" class="" style="width:400px;">
          <thead>
            <tr style="background-color:#CCC;height:20px;">
              <td height="20" align="left"><strong>MOTORIZADO</strong></td>
              <td width="100" align="center"><strong>MONTO</strong></td>
            </tr>
          </thead>
          <tbody>
            <?php
$monto_general=0;
foreach($detalle['motorizado'] as $kym=>$motorizado){ 
	if (trim($kym)==""){$kym="NO IDENTIFICADO";}
	
?>
            <tr  class="motorizado_detalle"  style="height:20px;cursor:pointer;background:#F7F7F7;border-bottom:#999 solid 1px;"  id="_<?php echo str_replace(" ","",$kym);?>">
              <td align="left" style="padding-left:20px;"><strong>
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
            <?php } ?>
            <?php } 

	?>
            <tr style="background-color:#CCC;height:20px;">
              <td align="right"><strong>TOTALES</strong></td>
              <td align="center"><strong><?php echo number_format($monto_general,2);?></strong></td>
            </tr>
          </tbody>
        </table></td>
      </tr>
    </table></td>
    <td width="582" align="center" valign="top">
<?php

  

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
        <td><table border="0" cellpadding="0" cellspacing="0" class="" style="width:400px;">
          <thead>
            <tr style="background-color:#CCC;height:20px;">
              <td height="20" align="left"><strong>OFICIAL</strong></td>
              <td align="center"><strong>MONTO</strong></td>
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
 
	foreach($oficial as $kym=>$motorizado){ 
		if (trim($kym)==""){$kym="NO IDENTIFICADO";}
	
		$comision=0;
		foreach($motorizado as $key=>$row){ 
			$monto_total=$monto_total+$row['MONTO'];
		}	
		
	}	
	$monto_general=$monto_general+$monto_total;
?>
            <tr  class="oficial_cod_detalle"  style="height:20px;cursor:pointer;background:#F7F7F7;border-bottom:#999 solid 1px;"  id="<?php echo str_replace(" ","",$keys."_".$kym);?>2">
              <td align="left" style="padding-left:20px;"><strong>
                <?php  echo strtoupper(utf8_encode($keys));?>
              </strong></td>
              <td align="center"><strong> <?php echo number_format($monto_total,2);?> </strong></td>
            </tr>
            <?php 
} 
 ?>
            <tr style="background-color:#CCC;height:20px;">
              <td align="right"><strong>TOTALES</strong></td>
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
        <td><table border="0" cellpadding="0" cellspacing="0" class="" style="width:400px;">
          <thead>
            <tr style="background-color:#CCC;height:20px;">
              <td height="20" align="left"><strong>MOTORIZADO</strong></td>
              <td width="100" align="center"><strong>MONTO</strong></td>
            </tr>
          </thead>
          <tbody>
            <?php
$monto_general=0;
foreach($detalle['motorizado'] as $kym=>$motorizado){ 
	if (trim($kym)==""){$kym="NO IDENTIFICADO";}
	
?>
            <tr  class="motorizado_detalle"  style="height:20px;cursor:pointer;background:#F7F7F7;border-bottom:#999 solid 1px;"  id="_<?php echo str_replace(" ","",$kym);?>2">
              <td align="left" style="padding-left:20px;"><strong>
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
            <?php } ?>
            <?php } 

	?>
            <tr style="background-color:#CCC;height:20px;">
              <td align="right"><strong>TOTALES</strong></td>
              <td align="center"><strong><?php echo number_format($monto_general,2);?></strong></td>
            </tr>
          </tbody>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
