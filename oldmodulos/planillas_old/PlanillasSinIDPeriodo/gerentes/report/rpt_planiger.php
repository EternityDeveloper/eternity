<?php 
 if (!isset($protect)){
	exit;
 }

 $mes         = isset($_REQUEST['periodo'])? System::getInstance()->Decrypt($_REQUEST['periodo']) : 0;
 $tipo_cierre = isset($_REQUEST['type'])   ? System::getInstance()->Decrypt($_REQUEST['type'])    : 0;
 $anio        = isset($_REQUEST['anio'])   ? System::getInstance()->Decrypt($_REQUEST['anio'])    : 0;
 
 if($tipo_cierre == "P"){$cierre = "PARCIAL";}else{$cierre = "TOTAL";}
 
  switch ((int)$mes) {
    case 1:
	   $nombreMes = "ENERO";
	   break;	 
    case 2:
	   $nombreMes = "FEBRERO";
	   break;	 
    case 3:
	   $nombreMes = "MARZO";
	   break;
    case 4:
	   $nombreMes = "ABRIL";
	   break;	 
    case 5:
	   $nombreMes = "MAYO";
	   break;	 
    case 6:
	   $nombreMes = "JUNIO";
	   break;
    case 7:
	   $nombreMes = "JULIO";
	   break;	 
    case 8:
	   $nombreMes = "AGOSTO";
	   break;	 
    case 9:
	   $nombreMes = "SEPTIEMBRE";
	   break;
    case 10:
	   $nombreMes = "OCTUBRE";
	   break;	 
    case 11:
	   $nombreMes = "NOVIEMBRE";
	   break;	 
    case 12:
	   $nombreMes = "DICIEMBRE";
	   break;	   	 
 }
 
 
 if( (in_array($mes, range(1,12))) && (($tipo_cierre=="P" || $tipo_cierre=="T")) ){
	 
$sql = "select a.codigo_gerente,
                CONCAT(c.primer_nombre, ' ', c.segundo_nombre) as nombre,
                CONCAT(c.primer_apellido, ' ', c.segundo_apellido, ' ', c.apellido_conyuge) as apellidos,
                SUM(IF(a.idconcepto = 1, a.monto,0)) as comision,
		        SUM(IF(a.idconcepto = 4, a.monto,0)) as precierre,
                SUM(IF(a.idconcepto = 5, a.monto,0)) as anulados
          from cm_detplanilla_gerente_tbl a,
                sys_gerentes_grupos b,
                sys_personas c
		 where a.codigo_gerente = b.codigo_gerente
		   and b.id_nit = c.id_nit
		   and a.anio = ".(int)$anio."
		   and a.mes = ".(int)$mes. "
		   and a.tipo_cierre = '".mysql_real_escape_string($tipo_cierre)."' group by a.codigo_gerente
		 order by CAST(a.codigo_gerente as unsigned)";	
		 
  $result =  mysql_query($sql);
  
}
?>

<page>
   <h1>Planilla Gerentes</h1>
   <table width="90%" class="fsPage">
       <tr>
         <td width="10%">Anio:</td>
         <td width="90%"><?=$anio?></td>
       </tr>
       <tr>
         <td width="10%">Mes:</td>
         <td width="90%"><?=$nombreMes?></td>
       </tr>
        <tr>
         <td width="10%">Tipo Cierre:</td>
         <td width="90%"><?=$cierre?></td>
       </tr>
   </table>
   
   <table width="90%" class="fsPage">
      <tr>
         <td width="59"  align="right">Gerente</td>
         <td width="416" align="left">Nombre</td>
         <td width="77"  align="right">Comision</td>
         <td width="77"  align="right">PreCierre</td>
         <td width="57"  align="right">Anulados</td>
         <td width="76"  align="right">Total</td>
      </tr>  
      <?php 
	    while($row = mysql_fetch_array($result)){
		 
		  $subtotal   = ((float)$row['comision'])-((float)$row['precierre']+(float)$row['anulados']);
	   ?>		
		  <tr>
             <td align="right"><?=$row['codigo_gerente']?></td>
             <td align="left"><?=$row['nombre']." ".$row['apellidos']?></td>
             <td align="right"><?=number_format($row['comision'],2,".",",")?></td>
             <td align="right"><?=number_format($row['precierre'],2,".",",")?></td>
             <td align="right"><?=number_format($row['anulados'],2,".",",")?></td>
             <td align="right"><?=number_format($subtotal,2,".",",")?></td>
          </tr>
	    
      <?php  	
		}
	  ?>
   </table>
</page>

<?php
   $content = ob_get_clean();
 
   require_once('class/lib/pdf/html2pdf.class.php');
   try
    {
        $html2pdf = new HTML2PDF('L', 'A4', 'fr', true, 'UTF-8',5,2,2,5);
		//DOTMATRI.TTF
        $html2pdf->pdf->SetDisplayMode('fullpage');
		//dotmatri.ttf
 
	    /*$x=$html2pdf->pdf->addTTFfont(dirname(__FILE__)."/dotmatri.ttf", 'TrueTypeUnicode', '', 32);*/

        $html2pdf->writeHTML($content);
        $html2pdf->Output('PlanillaGerente.pdf','');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>
