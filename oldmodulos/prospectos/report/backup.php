<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	




$data=array();
$SQL="SELECT  * FROM `asesores_g_d_gg_view`  
INNER JOIN sys_personas ON (sys_personas.id_nit=asesores_g_d_gg_view.id_nit)
WHERE (id_comercial  LIKE '%".$protect->getComercialID()."%' AND id_comercial!='".$protect->getComercialID()."')  ";
$rs=mysql_query($SQL);
while($row= mysql_fetch_assoc($rs)){
	array_push($data,$row);
}


//print_r($data);

$size=0;
$tipo_p=array();
$SQL="SELECT idtipo_prospecto as tipo,`Descrip_tipoprospecto` FROM `tipos_prospectos`  ";
$rs=mysql_query($SQL);
while($row= mysql_fetch_assoc($rs)){
	array_push($tipo_p,$row);
	$size+=145;
}



function getReport($protect,$pilar,$asesor_id){
	$SQL="SELECT 
		prospecto_comercial.id_comercial,
		sys_clasificacion_persona.descripcion,
		sys_clasificacion_persona.id_clasificacion,
		tracking_prospecto.id_actividad  AS actividad,
		prospecto_comercial.pilar_inicial AS `idtipo_prospecto`,
		prospectos.`pilar_final`
	FROM `prospectos`
	INNER JOIN `prospecto_comercial` ON(`prospecto_comercial`.`id_nit`=prospectos.id_nit) 
INNER JOIN `tipos_prospectos` ON(`tipos_prospectos`.idtipo_prospecto=prospecto_comercial.pilar_inicial) 
	INNER JOIN `sys_personas` ON(`sys_personas`.`id_nit`=prospectos.id_nit)
	LEFT JOIN `sys_clasificacion_persona` ON(`sys_clasificacion_persona`.`id_clasificacion`=sys_personas.id_clasificacion)
	INNER JOIN `sys_status` ON(`sys_status`.`id_status`=prospectos.`Estatus`)
	LEFT JOIN `tracking_prospecto` ON(`tracking_prospecto`.`id_nit`=sys_personas.`id_nit`)
	
	WHERE   prospecto_comercial.estatus NOT IN (12,16)  AND prospecto_comercial.id_comercial  LIKE '%".$protect->getComercialID()."%' AND  (tracking_prospecto.actividad_proxima IS NULL OR tracking_prospecto.actividad_proxima ='')
	AND prospecto_comercial.pilar_inicial='".$pilar."' 
	AND prospecto_comercial.id_comercial='".$asesor_id."' "; 
 
 
 
	$rs=mysql_query($SQL);
	$data=array(
		"B"=>0,
		'A'=>0,
		'C'=>0,
		'CITA'=>0,
		'PRE'=>0,
		'CIE'=>0,
		'RES'=>0
	);
	while($row= mysql_fetch_assoc($rs)){
		//$data[$pilar]=
		if (trim($row['idtipo_prospecto'])!=""){
		//array_push($tipo_p,$row);
		 
			if (!isset($data[$pilar])){
				$data[$pilar]=0;
			}
			$data[$pilar]+=1;
			
			$data[$row['descripcion']]+=1;
			
			if (trim($row['actividad'])!=""){
				$data[$row['actividad']]+=1;
			}
			
			
		}
		//$tipo_p
	}
	 
	//print_r($data);
	return $data;
}
 
?>
<style>
.AML{
	background:#92D050;
	border:#000 solid 1px;
}
.AML td{
	border:#000 solid 1px;
}
.AMT{
	background:#948B54;
	border:#000 solid 1px;
}
.AMT td{
	border:#000 solid 1px;
}
.REF{
	background:#B2A1C7;
	border:#000 solid 1px;
}
.REF td{
	border:#000 solid 1px;
}

.ETC{
	background:#B2A1C7;
	border:#000 solid 1px;
}
.ETC td{
	border:#000 solid 1px;
}
.EVENT{
	background:#B8CCE4;
	border:#000 solid 1px;
}
.EVENT td{
	border:#000 solid 1px;
}

.TURNO{
	background:#B8CCE4;
	border:#000 solid 1px;
}
.TURNO td{
	border:#000 solid 1px;
}
.INHUM{
	background:#FF8000;
	color:#FFF;
	border:#000 solid 1px;
}
.INHUM td{
	border:#000 solid 1px;
}
.META{
	background:#E6B9B8;
	color:#FFF;
	border:#000 solid 1px;
}
.META td{
	border:#000 solid 1px;
}
.STAND{
	background:#D99795;
	color:#FFF;
	border:#000 solid 1px;
}
.STAND td{
	border:#000 solid 1px;
}

.grid{
	width:2600px;
}
.grid td{
	border:#000 solid 1px;
}
 
.person td{
	border:none;
	border-bottom:#000 solid 2px;	
	border-left:#333 solid 2px;
}
.paddings{
	width:1000px;
	overflow-x: scroll;
	overflow-y: hidden;
}
.order{
	list-style-type:none;	
	padding:0;
	margin:0;
	float:left;	
	width:<?php echo $size?>px;
}
.order li {
	float:left;
	padding:0;
	margin:0;	
	width:600px; 
}

.tbtest{
	border:#F00 solid 1px;
	width:100%;
}
</style>
<page orientation="L"  format="150x<?php echo $size?>" >
 
  <table cellspacing="0" style="width:100%;border:#000 solid 1px;margin-left:10px;margin-top:10px;">
  <tr>
    <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="person" >
<tr class="tbtest">
        <td height="33" align="center" valign="middle" bgcolor="#FFFF00"><strong>NO.</strong></td>
        <td height="50" align="center" valign="middle" bgcolor="#7F7F7F"><strong><font color="#FFFFFF">ASESOR DE VENTAS</font></strong></td>
        </tr>
      <?php 
	 $i=1;
	foreach($data as $key=>$val){
	?>
      
      <tr>
        <td width="58" align="center" valign="middle"><?php echo $i;?></td>
        <td><?php echo $val['primer_nombre']." ".$val['segundo_nombre'] ?></td>
        </tr>
      <?php $i++;} ?>
    </table></td>

      <?php

foreach($tipo_p as $key =>$row){
?>  
 <td valign="top"  >  
 
      <table width="600" border="0" cellspacing="0" cellpadding="0"  >
        <tr>
          <td colspan="8" align="center" class="<?php echo $row['tipo'];?>"><?php echo $row['tipo'];?></td>
        </tr>
        <tr class="<?php echo $row['tipo'];?>">
          <td width="50" rowspan="2" align="center" valign="middle">CONTAC<br />
            TO </td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td width="60" rowspan="2" align="center" valign="middle">CITA</td>
          <td width="80" rowspan="2" align="center" valign="middle">PRESEN<br />
            TACION </td>
          <td width="70" rowspan="2" align="center" valign="middle">CIERRE </td>
          <td width="60" rowspan="2" align="center" valign="middle">RESERV<br />
            AS </td>
        </tr>
        <tr class="<?php echo $row['tipo'];?>">
          <td width="40" align="center">A</td>
          <td width="40" align="center">B</td>
          <td width="40" align="center">C</td>
        </tr>
        <?php 
	 $sum_contacto=0;
	 $sum_A=0;
	 $sum_B=0;
	 $sum_C=0;
	 $sum_Cita=0;
	 $sum_PRE=0;
	 $sum_CIE=0;
	 $sum_RES=0;
	 if (count($data)>0){
		foreach($data as $key=>$val){
			//break;
			
			$detalle = getReport($protect,$row['tipo'],$val['id_comercial']);
			$sum_contacto+=$detalle['A']+$detalle['B']+$detalle['C'];
			$sum_A+=$detalle['A'];
			$sum_B+=$detalle['B'];
			$sum_C+=$detalle['C'];
			$sum_Cita+=$detalle['CITA'];
			$sum_PRE+=$detalle['PRE'];
			$sum_CIE+=$detalle['CIE'];
			$sum_RES+=$detalle['RES'];			
	?>
        <tr class="grid">
          <td align="center"><?php echo $detalle['A']+$detalle['B']+$detalle['C']?></td>
          <td align="center"><?php echo $detalle['A']?></td>
          <td align="center"><?php echo $detalle['B']?></td>
          <td align="center"><?php echo $detalle['C']?></td>
          <td align="center"><?php echo $detalle['CITA']?></td>
          <td align="center"><?php echo $detalle['PRE']?></td>
          <td align="center"><?php echo $detalle['CIE']?></td>
          <td align="center"><?php echo $detalle['RES']?></td>
        </tr>
        <?php 
		}
	}else{?>
        <tr class="grid">
          <td align="center">0</td>
          <td align="center">0</td>
          <td align="center">0</td>
          <td align="center">0</td>
          <td align="center">0</td>
          <td align="center">0</td>
          <td align="center">0</td>
          <td align="center">0</td>
        </tr>
        <?php } ?>    
        <tr class="grid">
          <td align="center" bgcolor="#FFFF00"><?php echo $sum_contacto;?></td>
          <td align="center" bgcolor="#FFFF00"><?php echo $sum_A;?></td>
          <td align="center" bgcolor="#FFFF00"><?php echo $sum_B;?></td>
          <td align="center" bgcolor="#FFFF00"><?php echo $sum_C;?></td>
          <td align="center" bgcolor="#FFFF00"><?php echo $sum_Cita;?></td>
          <td align="center" bgcolor="#FFFF00"><?php echo $sum_PRE;?></td>
          <td align="center" bgcolor="#FFFF00"><?php echo $sum_CIE;?></td>
          <td align="center" bgcolor="#FFFF00"><?php echo $sum_RES;?></td>
        </tr>
      </table>
    </td>  
  <?php  

  
} ?>        


  </tr>
</table>
  </page>
<?php

    $content = ob_get_clean();

    require_once('class/lib/pdf/html2pdf.class.php');
    try
    {
		// format="632x540" 
		$format =array('200','540');
        $html2pdf = new HTML2PDF('L',$format, 'en', true, 'UTF-8', 0);
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->writeHTML($content,"");
        $html2pdf->Output('reporte.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>
