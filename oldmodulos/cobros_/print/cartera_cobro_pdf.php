<?php
if (!isset($protect)){
	exit;
}	

$page=array();
$page[0]=array();
$counter=0;
$i=0;
foreach($my_cartera as $key =>$row){
 	if ($counter==20){
		$counter=0;	
		$i=$i+1;
		$page[$i]=array();
		array_push($page[$i],$row);	
	}else{
		array_push($page[$i],$row);	
	}
	$counter=$counter+1;
}
 
 $monto_total=0;
 $total=0;
foreach($page as $key =>$pages){
?> 
<page  orientation="L" backcolor="#FFFFFF" style="font: arial;">
 <page_header>
  <table   class="page_header" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="100"  height="30" style="font-size:12px;border-bottom:#000 solid 1px;background:#CCC"><strong>CONTRATO</strong></td>
      <td   width="250" style="font-size:12px;border-bottom:#000 solid 1px;background:#CCC"><strong>NOMBRE_CLIENTE</strong></td>
      <td  width="150"  style="font-size:12px;border-bottom:#000 solid 1px;background:#CCC"><strong>NOMBRE_OFICIAL</strong></td>
      <td  width="100"  style="font-size:12px;border-bottom:#000 solid 1px;background:#CCC"><strong>FECHA_VENTA</strong></td>
      <td   width="100"  style="font-size:12px;border-bottom:#000 solid 1px;background:#CCC"><strong>MONTO_TOTAL</strong></td>
      <td  width="220"  style="font-size:12px;border-bottom:#000 solid 1px;background:#CCC"><strong>DIRECCION</strong></td>
      <td  width="150"   style="font-size:12px;border-bottom:#000 solid 1px;background:#CCC"><strong>TELEFONO</strong></td>
    </tr>
        <tr>
      <td width="100"  style="font-size:12px;border-bottom:#000 solid 1px;" > </td>
      <td width="250"  style="font-size:12px;border-bottom:#000 solid 1px;" > </td>
      <td width="150"  style="font-size:12px;border-bottom:#000 solid 1px;"> </td>
      <td width="100"  style="font-size:12px;border-bottom:#000 solid 1px;"> </td>
      <td width="100"  style="font-size:12px;border-bottom:#000 solid 1px;"> </td>
      <td width="220"  style="font-size:12px;border-bottom:#000 solid 1px;"> </td>
      <td width="150"  style="font-size:12px;border-bottom:#000 solid 1px;"> </td>
    </tr>
    </table>
    </page_header>
  <table border="0" style="margin-top:40px;" cellspacing="0" cellpadding="0"> 
    <?php 

 	foreach($pages as $key =>$row){
		if (trim($row['categoria'])==''){
			$row['categoria']='CUOTA';	
		}
	 	$my_cartera[$key]['MONTO_TOTAL']=$row['saldo_0_30']+$row['saldo_31_60']+$row['saldo_61_90']+$row['saldo_91_120']+$row['saldo_mas_120']; 
		$monto_total= $monto_total+$my_cartera[$key]['MONTO_TOTAL'];
		 $total= $total+1;
		?>
    <tr>
      <td width="100"  style="font-size:12px;border-bottom:#000 solid 1px;" ><?php echo $row['serie_contrato']." ".$row['no_contrato'];?></td>
      <td width="250"  style="font-size:12px;border-bottom:#000 solid 1px;" ><?php echo utf8_encode($row['nombre_cliente']);?></td>
      <td width="150"  style="font-size:12px;border-bottom:#000 solid 1px;"><?php echo $row['nombre_oficial'];?></td>
      <td width="100"  style="font-size:12px;border-bottom:#000 solid 1px;"><?php echo $row['fecha_venta'];?></td>
      <td width="100"  style="font-size:12px;border-bottom:#000 solid 1px;"><?php echo number_format($my_cartera[$key]['MONTO_TOTAL'],2);?></td>
      <td width="220"  style="font-size:12px;border-bottom:#000 solid 1px;"><?php echo utf8_encode($row['direccion_cobro']);?></td>
      <td width="150"  style="font-size:12px;border-bottom:#000 solid 1px;"><?php echo $row['TELEFONO'];?></td>
    </tr>

    <?php }  ?>
    <tr>
      <td colspan="2"  style="font-size:12px;border-bottom:#000 solid 1px;" >TOTAL CONTRATOS <?php echo $total;?></td>
      <td  style="font-size:12px;border-bottom:#000 solid 1px;">&nbsp;</td>
      <td  style="font-size:12px;border-bottom:#000 solid 1px;">&nbsp;</td>
      <td  style="font-size:12px;border-bottom:#000 solid 1px;"><?php echo number_format($monto_total,2);?></td>
      <td  style="font-size:12px;border-bottom:#000 solid 1px;">&nbsp;</td>
      <td  style="font-size:12px;border-bottom:#000 solid 1px;">&nbsp;</td>
    </tr>    
  </table>
</page>
<?php }  ?>
<?php  
    $content = ob_get_clean();
    require_once('class/lib/pdf/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('L', 'A4', 'fr', true, 'UTF-8', 0);
        $html2pdf->pdf->SetDisplayMode('fullpage');
	   // $html2pdf->pdf->addTTFfont("fonts/dotmatri.ttf", '', '', 32);
	   //$html2pdf->pdf->addTTFfont("fonts/dotmatri.ttf", '', '', 32);
		//$html2pdf->pdf->setFont('DotShort');
	//	$html2pdf->pdf->AddTTFFont('fonts/dotmatri.ttf');
	//	$html2pdf->addFont('DotShort','', 'domatri.php');
//		$html2pdf->pdf->setDefaultFont('DotMatrix');
        $html2pdf->writeHTML($content,"");
        $html2pdf->Output('recibo_venta_'.$pago->reporte_venta.'.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>
