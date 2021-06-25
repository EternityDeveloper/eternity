<?php
if (!isset($protect)){
	exit;
}	

 
if (isset($_REQUEST['contrato'])){ 
	$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));


		$SQL="SELECT contratos.* ,
	actividades_gestion.*,
	empresa.*,
	CONCAT(OFICIAL.`primer_nombre`,' ',OFICIAL.`segundo_nombre`,' ',OFICIAL.`primer_apellido`,' ',OFICIAL.segundo_apellido) AS nombre_oficial,
	CONCAT(CLIENTE.`primer_nombre`,' ',CLIENTE.`segundo_nombre`,' ',CLIENTE.`primer_apellido`,' ',CLIENTE.segundo_apellido) AS nombre_cliente,
	CONCAT(`contratos`.`serie_contrato`,' ',contratos.no_contrato) AS contrato,
	sys_status.`descripcion` AS estatus_name,
	tipo_actividades.`actividad`,
	 labor_cobro.*,
	 DATE_FORMAT(gestiones.fecha, '%d/%m/%Y')  AS fecha_aviso,
	 
	(SELECT CONCAT(MOTO.`primer_nombre`,' ',MOTO.`segundo_nombre`,' ',MOTO.`primer_apellido`,' ',MOTO.segundo_apellido)FROM `actividades_gestion`
INNER JOIN `sys_personas` AS MOTO ON (`MOTO`.id_nit=actividades_gestion.`responsable`)
WHERE actividades_gestion.`idtipoact`='MOTO' AND actividades_gestion.`id_status`=1 AND
actividades_gestion.`idgestion`=gestiones.`idgestion`) AS nombre_motorizado 
	FROM `gestiones`
	INNER JOIN actividades_gestion ON (`gestiones`.`idtipogestion`=gestiones.`idtipogestion`)
	INNER JOIN sys_personas  AS OFICIAL ON (`OFICIAL`.`id_nit`=gestiones.`responsable`) 
	INNER JOIN `empresa` ON (`empresa`.`EM_ID`=gestiones.`EM_ID`) 
	INNER JOIN `contratos` ON (`contratos`.`serie_contrato`=gestiones.serie_contrato 
			AND contratos.no_contrato=gestiones.no_contrato)
	INNER JOIN `labor_cobro` ON (`contratos`.`serie_contrato`=labor_cobro.serie_contrato 
			AND contratos.no_contrato=labor_cobro.no_contrato)			
	INNER JOIN sys_personas  AS CLIENTE ON (`CLIENTE`.`id_nit`=contratos.`id_nit_cliente`)		
	INNER JOIN `sys_status` ON (sys_status.`id_status`=gestiones.id_status)
	INNER JOIN `tipo_actividades` ON (tipo_actividades.`idtipoact`=actividades_gestion.idtipoact)
	WHERE tipo_actividades.idtipoact='AVICO' and contratos.serie_contrato='".$contrato->serie_contrato."' 
	and contratos.no_contrato='".$contrato->no_contrato."'  ";

 
	//	echo $SQL;
		$rs=mysql_query($SQL);
		$data=mysql_fetch_object($rs);
 
 
 	SystemHtml::getInstance()->includeClass("client","PersonalData");
	$person= new PersonalData($protect->getDBLink());	
  
	$addressData=$person->getAddress($data->id_nit_cliente);
	$phoneData=$person->getPhone($data->id_nit_cliente);
		

	$direccion="";
	foreach($addressData as $key=>$val){  
		$direccion=$val['provincia'].", ".$val['municipio'] .", ".$val['ciudad'] .", ".$val['sector'];
		$direccion.=trim($val['avenida'])!=""?",".$val['avenida']:'';
		$direccion.=trim($val['calle'])!=""?",".$val['calle']:'';
		$direccion.=trim($val['zona'])!=""?",".$val['zona']:'';
		$direccion.=trim($val['manzana'])!=""?",".$val['manzana']:'';
		$direccion.=trim($val['numero'])!=""?",".$val['numero']:'';
		$direccion.=trim($val['referencia'])!=""?",".$val['referencia']:'';
		$direccion.=trim($val['observaciones'])!=""?",".$val['observaciones']:''; 
		if ($val->tipo=="Cobro"){
			break;	
		}
	} 
	
	
	$phone="";
	foreach($phoneData as $key=>$val){   
		$phone.=$val['area'].$val['numero']; 
		if ($val->tipo==2){
			$phone.=" Ext.".$val['extencion'];
		}
		$phone.=", ";  
	}
	$phone=substr($phone,0, strlen($phone)-2); 
	
 
	//	$isApplyIncial=$_reserva->getIfReservaContainTipoMovimiento(1,$reserva);
 
}



//print_r($data);


 
?>
<page format="100x210" orientation="L" backcolor="#FFFFFF" style="font: arial;">

<table width="900" border="0" align="center" cellpadding="5" cellspacing="0" >
  <tr>
    <td><table width="900" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td colspan="2" style="font-size:22px;"><strong>SERVICIOS MEMORIALES DOMINICANOS, S.R.L.</strong></td>
      </tr>
      <tr>
        <td width="66%" valign="top"><span style="font-size:14px;">Av. 27 de Febrero No. 444 (Entre Av. Privada y Nuñez de Caceres)</span><br>
          Santo Domingo Norte, Santo Domingo<br>
          Telefono 809.683.2200<br>
          Republica Dominicana<br>
          RNC 130-81999-8</td>
        <td width="34%" valign="top"><table width="300" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="75"  style="font-size:12px"><strong>FECHA:</strong></td>
            <td width="117" style="font-size:12px"><?php echo $data->fecha_aviso?></td>
          </tr>
          <tr>
            <td style="font-size:12px"><strong>CONTRATO:</strong></td>
            <td style="font-size:12px"><?php echo strtoupper($data->serie_contrato." ".$data->no_contrato);?></td
          ></tr>
          <tr>
            <td  style="font-size:12px"><strong>Reqmto. de cobro</strong></td>
            <td style="font-size:12px"><?php echo $data->serie.$data->aviso_cobro?></td>
          </tr>
          <tr>
            <td  style="font-size:12px"><strong>Cobrador</strong>:</td>
            <td style="font-size:12px"><?php echo $data->nombre_motorizado?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td style="font-size:12px"><strong>NOMBRE DE CLIENTE:</strong>  <?php echo $data->id_nit_cliente?> - <?php echo $data->nombre_cliente?></td>
  </tr>
  
  <tr>
    <td><span style="font-size:12px;"><strong>COMENTARIO</strong></span></td>
  </tr>
  <tr>
    <td  style="font-size:10px;"><strong>TELEFONO:</strong>: <?php echo $phone;?></td>
  </tr>
<tr>
    <td ><span style="font-size:10px;width:325px;"><strong>DIRECCION: </strong> <?php echo $direccion;?></span></td>
  </tr>  
  <tr>
    <td height="60" valign="top"><table width="900" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td style="font-size:12px;border-bottom:#000 solid 1px;"><strong>DESCRIPCION</strong></td>
        <td width="100" style="font-size:12px;border-bottom:#000 solid 1px;"><strong>PRECIO</strong></td>
      </tr>
      <tr>
        <td width="670"  style="font-size:12px;" >JARDIN LAS TRINITARIAS2 CUOTA 3 DE 48</td>
        <td  style="font-size:12px;">RD$ 5,446.94</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="900" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="380"><table width="300" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="60"  style="font-size:12px;" >CHEQUE No:</td>
            <td  style="font-size:12px;" >&nbsp;</td>
          </tr>
          <tr>
            <td  style="font-size:12px;" >BANCO:</td>
            <td  style="font-size:12px;" >&nbsp;</td>
          </tr>
          <tr>
            <td  style="font-size:12px;" >CANCELADO EL:</td>
            <td  style="font-size:12px;" ><strong>__/__&nbsp;/__&nbsp;&nbsp;</strong></td>
          </tr>
          <tr>
            <td  style="font-size:12px;" >EFECTIVO:</td>
            <td  style="font-size:12px;" >()</td>
          </tr>
          <tr>
            <td  style="font-size:12px;" >FECHA DE RECEPCION:</td>
            <td  style="font-size:12px;" ><strong>__/__&nbsp;/__&nbsp;&nbsp;</strong></td>
          </tr>
        </table></td>
        <td width="380" align="right"><table width="250" border="0" align="right" cellpadding="0" cellspacing="0">
          <tr>
            <td width="80"  style="font-size:12px;" ><strong>Sub-Total:</strong></td>
            <td  style="font-size:12px;" >&nbsp;10,893.88</td>
          </tr>
          <tr>
            <td  style="font-size:12px;" ><strong>ITBIS:</strong></td>
            <td  style="font-size:12px;" >&nbsp;&nbsp;0.00</td>
          </tr>
          <tr>
            <td  style="font-size:12px;" ><strong>TOTAL GENERAL :</strong></td>
            <td  style="font-size:12px;" >&nbsp;10,893.88</td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td  style="font-size:12px;" ><strong>RECIBE NOMBRE Y FIRMA:</strong> _____________________________________________</td>
  </tr>
  <tr>
    <td align="center"  style="font-size:12px;" >&nbsp;</td>
  </tr>
  <tr>
    <td align="center"  style="font-size:12px;" ><input type="submit" name="dash_filtro_avanzado" id="dash_filtro_avanzado" value="Imprimir" /></td>
  </tr>
  <tr>
    <td align="center"  style="font-size:12px;" >&nbsp;</td>
  </tr>
</table>
</page>
<?php


exit;

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
        $html2pdf->Output('recibo_venta_'.$pago->reporte_venta.'.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>
