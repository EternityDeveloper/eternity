<?php
if (!isset($protect)){
	exit;	
}
$doc=STCSession::GI()->getSubmit("DOC_TO_PRINT");
 
?>
<div style="padding-right:65px; padding-left:50px;font-size:10.5px;padding-top:50px; text-align: justify;width:690px;">
 <?php 
 
 	$html= str_replace("</o:p>","",str_replace("<o:p>","",$doc->TEXTO));
	 
 	echo $html;
	
 exit;
 ?>
</div>
<?php
 
    $content = ob_get_clean();
    require_once('class/lib/pdf/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', 0);
      //  $html2pdf->pdf->SetDisplayMode('fullpage');
	//    $x=$html2pdf->pdf->addTTFfont("/fonts/dotmatri.ttf", '', '', 32);
		$html2pdf->pdf->setFont('dotmatri');
        $html2pdf->writeHTML($content,"");
        $html2pdf->Output('recibo_venta_'.$pago->reporte_venta.'.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>

