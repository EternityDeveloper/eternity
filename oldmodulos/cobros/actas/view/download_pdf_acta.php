<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
  

?>
<page format="216x280" orientation="L" backcolor="#FFFFFF" style="font: arial;font-size:9px">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td style="font-size:12px;">&nbsp;</td>
  </tr>
  <tr>
    <td style="font-size:12px;">&nbsp;<strong>FECHA IMPRESA: <?php echo date("d-m-Y")?></strong></td>
  </tr>
  <tr>
    <td style="font-size:14px;"><strong>ACTA  <?php echo $acta->idacta ." ".$acta->secuencia;?></strong></td>
    </tr>
</table> 
<table width="700" border="0" cellpadding="0" cellspacing="0" style="font-size: 9px; font-weight: bold;">
  <thead>
    <tr style=" border-top: 1px #000 solid;">
      <td width="6%" bgcolor="#CCCCCC"><strong>CONTRATO</strong></td>
      <td width="14%" align="center" bgcolor="#CCCCCC"><strong>CLIENTE</strong></td>
      <td width="8%" align="center" bgcolor="#CCCCCC"><strong>FECHA VENTA</strong></td>
      <td width="7%" align="center" bgcolor="#CCCCCC"><strong>PRODUCTO</strong></td>
      <td width="5%" align="center" bgcolor="#CCCCCC"><strong>PRECIO <br>
        NETO</strong></td>
      <td width="12%" align="center" bgcolor="#CCCCCC"><strong>PRECIO N.COBRADO</strong></td>
      <td width="7%" align="center" bgcolor="#CCCCCC"><strong>UBICACION</strong></td>
      <td width="5%" align="center" bgcolor="#CCCCCC"><strong>CUOTAS</strong></td>
      <td width="3%" align="center" bgcolor="#CCCCCC"><strong>PAG.</strong></td>
      <td width="3%" align="center" bgcolor="#CCCCCC"><strong>CUOTAS ATRASO</strong></td>
      <td width="5%" align="center" bgcolor="#CCCCCC">OFICIAL</td>
      <td width="5%" align="center" bgcolor="#CCCCCC"><strong>ASESOR</strong></td>
      <td width="8%" align="center" bgcolor="#CCCCCC"><strong>GTE. VENTAS</strong></td>
      </tr>
  </thead>
  <tbody>
<?php  
 
SystemHtml::getInstance()->includeClass("cobros","Actas");    
$actas=new Actas($protect->getDBLink());
//$acta->id_status
$desistidos=$actas->getActasCerradaOrPorCerrar($acta->tipo,$acta->idacta,23); 

foreach($desistidos as $key =>$row){
  $total_clientes++;
  $monto_atraso=$monto_atraso + $row['MONTO_PENDIENTE_ATRASO'];
 
?>
    <tr>
      <td  ><?php echo $row['serie_contrato']." ".$row['no_contrato'];?></td>
      <td width="100" align="center"  ><?php echo $row['nombre_completo'];?></td>
      <td width="50" align="center"  ><?php echo $row['fecha_venta']; ?></td>
      <td width="20" align="center" ><?php echo $row['no_productos'];?></td>
      <td align="center"  ><?php echo number_format($row['precio_neto'],2);?></td>
      <td align="center"  ><?php echo number_format($row['capital_pendiente'],2);?></td>
      <td align="center" ><?php echo $row['productos'];?></td>
      <td align="center" ><?php echo $row['cuotas'];?></td>
      <td align="center"  ><?php echo $row['cuotas_pagas'];?></td>
      <td align="center"  ><?php echo $row['cuotas_en_atrasao'];?></td>
      <td width="100" align="center" ><?php echo $row['nombre_oficial'];?></td>
      <td width="100" align="center" ><?php echo $row['nombre_asesor'];?></td>
      <td width="100" align="center" ><?php echo $row['nombre_gerente'];?></td>
      </tr>
     <?php } ?>     
    <tr>
      <td  >&nbsp;</td>
      <td align="center"  >&nbsp;</td>
      <td align="center"  >&nbsp;</td>
      <td align="center" >&nbsp;</td>
      <td align="center"  >&nbsp;</td>
      <td align="center"  >&nbsp;</td>
      <td align="center" >&nbsp;</td>
      <td align="center" >&nbsp;</td>
      <td align="center"  >&nbsp;</td>
      <td align="center"  >&nbsp;</td>
      <td align="center" >&nbsp;</td>
      <td align="center" >&nbsp;</td>
      <td align="center" >&nbsp;</td>
    </tr>
    <tr>
      <td  >&nbsp;</td>
      <td align="left"  >CONTRATOS </td>
      <td align="left" valign="top"  ><?php echo $total_clientes ;?></td>
      <td align="center" >&nbsp;</td>
      <td align="center"  >&nbsp;</td>
      <td align="center"  >&nbsp;</td>
      <td align="center" >&nbsp;</td>
      <td align="center" >&nbsp;</td>
      <td align="center"  >&nbsp;</td>
      <td align="center"  >&nbsp;</td>
      <td align="center" >&nbsp;</td>
      <td align="center" >&nbsp;</td>
      <td align="center" >&nbsp;</td>
    </tr>
    <tr>
      <td  >&nbsp;</td>
      <td align="left"  >MONTO TOTAL CONTRATOS</td>
      <td align="left"  ><?php echo number_format($monto_atraso,2);?></td>
      <td align="center" >&nbsp;</td>
      <td align="center"  >&nbsp;</td>
      <td align="center"  >&nbsp;</td>
      <td align="center" >&nbsp;</td>
      <td align="center" >&nbsp;</td>
      <td align="center"  >&nbsp;</td>
      <td align="center"  >&nbsp;</td>
      <td align="center" >&nbsp;</td>
      <td align="center" >&nbsp;</td>
      <td align="center" >&nbsp;</td>
    </tr>
    <tr>
      <td  >&nbsp;</td>
      <td align="left"  >&nbsp;</td>
      <td align="left"  >&nbsp;</td>
      <td align="center" >&nbsp;</td>
      <td align="center"  >&nbsp;</td>
      <td align="center"  >&nbsp;</td>
      <td align="center" >&nbsp;</td>
      <td align="center" >&nbsp;</td>
      <td align="center"  >&nbsp;</td>
      <td align="center"  >&nbsp;</td>
      <td align="center" >&nbsp;</td>
      <td align="center" >&nbsp;</td>
      <td align="center" >&nbsp;</td>
    </tr>

  </tbody>
  <tfoot>
  </tfoot>
</table> 
<table   align="center" cellpadding="0" cellspacing="0" style="font-size:12px" >
  <tr>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td align="center">_____________________________________________</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">_____________________________________________</td>
  </tr>
  <tr>
    <td align="center">Generado Por</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">Aprovado por</td>
  </tr>
</table>
</page> 
<?php
  
    $content = ob_get_clean();
 
    require_once('class/lib/pdf/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', 0);
		//DOTMATRI.TTF
        $html2pdf->pdf->SetDisplayMode('fullpage');
		//dotmatri.ttf
 
	    $x=$html2pdf->pdf->addTTFfont(dirname(__FILE__)."/dotmatri.ttf", 'TrueTypeUnicode', '', 32);

        $html2pdf->writeHTML($content,"");
        $html2pdf->Output($contrato->id_acta->idacta."-".date("d-m-Y").'.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>