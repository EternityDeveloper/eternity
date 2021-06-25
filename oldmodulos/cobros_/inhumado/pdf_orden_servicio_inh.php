<?php 
if (!isset($protect)){
	exit;	
}

if (isset($_REQUESTP['id'])){
	exit;
}
$oinh=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
 
if (!isset($oinh->no_servicio)){
	exit;	
}

SystemHtml::getInstance()->includeClass("estructurac","Asesores");
SystemHtml::getInstance()->includeClass("client","PersonalData");  
SystemHtml::getInstance()->includeClass("cobros","Servicios");  
SystemHtml::getInstance()->includeClass("contratos","Contratos");  
$con=new Contratos($protect->getDBLink()); 
$cdata=$con->getInfoContrato($oinh->serie_contrato,$oinh->no_contrato);
 
$person= new PersonalData($protect->getDBLink());
$data_p=$person->getClientData($oinh->no_doc);
  

$addressData=$person->getAddress($cdata->id_nit_cliente);
$phoneData=$person->getPhone($cdata->id_nit_cliente);
	 
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

$ase=new Asesores($protect->getDBLink());
$asesor=$ase->getComercialParentData($cdata->codigo_asesor);
$nombre_asesor=$asesor[0]['nombre']." ".$asesor[0]['apellido'];
$nombre_gerente=$asesor[1]['nombre']." ".$asesor[1]['apellido'];

$fnc=Servicios::GI()->getFuneraria("",$oinh->idfunerarias);
$nombre_fun=$fnc['results'][0]['text'];
  
?>
<page>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
  </table>
  <table border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="175">&nbsp;</td>
      <td width="400" align="center" style="font-size:18px;font-weight: bold;"><b>Orden de Servicio</b><br>
      <b>Inhumación</b></td>
      <td width="180"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="58%"  style="background:#CCC"><strong>Contrato:</strong></td>
          <td width="42%" style="background:#CCC"><?php echo $oinh->serie_contrato?> <?php echo $oinh->no_contrato?></td>
        </tr>
        <tr>
          <td><strong>Fecha:</strong></td>
          <td><?php echo $oinh->fecha?></td>
        </tr>
        <tr>
          <td><strong>Numero.:</strong></td>
          <td><?php echo $oinh->no_servicio?></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td colspan="3" align="center">Dirección de Oficina y Parque: Avenida Jacobo Majluta, Sto. Dgo. Norte, Republica Dominicana</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <table  cellpadding="0" cellspacing="0" style="border:dotted 1px #000077;border-right:dotted 1px #000077;">
    <tr>
      <td width="700" height="20" align="center" bgcolor="#D1D1D1"><strong>INFORMACION GENERAL DEL SOLICITANTE</strong></td>
    </tr>
    <tr>
      <td><table width="740" border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td height="25" colspan="2"  style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;" ><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="250"><strong>Nombre de la Persona  de Contacto: </strong></td>
              <td height="20"><span style="border-bottom:dotted 1px #000077;"><?php echo $oinh->responsablexcliente;?></span></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="25" colspan="2"  style="border-right:dotted 1px #000077;border-bottom:dotted 1px #000077;" ><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="300" height="20"><strong>Parentesco:</strong></td>
                <td><strong>Teléfono:</strong><?php echo $oinh->telefono;?></td>
              </tr>
          </table></td>
          </tr>
        <tr>
          <td width="200" height="25" style="border-right:dotted 1px #000077;border-bottom:dotted 1px #000077;" ><strong>Titular del Contrato:</strong></td>
          <td width="550" height="25"  style="border-bottom:dotted 1px #000077;" ><?php echo $oinh->nombres." ".$oinh->apellidos;?></td>
        </tr>
        <tr>
          <td height="25" style="border-right:dotted 1px #000077;border-bottom:dotted 1px #000077;" ><strong>Cédula del Titular  del Contrato: </strong></td>
          <td height="25"  style="border-bottom:dotted 1px #000077;" ><?php echo $phone;?></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="20" align="center" bgcolor="#D1D1D1"><strong>INFORMACION DEL DIFUNTO</strong></td>
    </tr>
    <tr>
      <td align="left"><table width="740" border="0" align="left" cellpadding="0" cellspacing="0">
   
        <tr>
          <td width="200"  style="border-right:dotted 1px #000077;border-bottom:dotted 1px #000077;" ><table width="740" border="0" cellspacing="0" cellpadding="0">
            <tr >
              <td width="350" height="25"  style="border-bottom:dotted 1px #000077;"><strong>Nombre y Apellido:</strong><?php echo $data_p['nombre_completo'];?></td>
              <td width="400" align="left"  style="border-bottom:dotted 1px #000077;"><strong>Céd:</strong><?php echo $oinh->no_doc;?></td>
            </tr>
            <tr>
              <td height="25"  style="border-bottom:dotted 1px #000077;"><strong>Parentesco del  Titular:</strong></td>
              <td align="left"  style="border-bottom:dotted 1px #000077;"><strong>Edad</strong>:<?php echo $data_p['edad'];?></td>
            </tr>
            <tr>
              <td height="25"  style="border-bottom:dotted 1px #000077;"><strong>Causas del Fallec.: </strong><?php echo $oinh->motivo_fallecido;?></td>
              <td align="left"  style="border-bottom:dotted 1px #000077;"><strong>Fecha Nac.:</strong> <?php echo $data_p['fecha_nacimiento'];?></td>
            </tr>
            <tr>
              <td height="25" valign="top"  style="border-bottom:dotted 1px #000077;"><strong>Fecha del  Fallecimiento:</strong> <?php echo $oinh->fecha_fallecido;?></td>
              <td align="left"  style="border-bottom:dotted 1px #000077;"><strong>Médico que Firma el  Acta:</strong> <?php echo $oinh->medico;?></td>
            </tr>
            <tr>
              <td height="25" valign="top"><strong>Lugar de Defunción:</strong><?php echo $oinh->lugar_defuncion;?></td>
              <td align="left"><strong>Funeraria: </strong><?php echo $nombre_fun;?></td>
            </tr>
          </table></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="20" align="center" bgcolor="#D1D1D1"><strong>INFORMACION DE OPERACIONES</strong></td>
    </tr>
    <tr>
      <td align="center"><table border="0" cellpadding="0" cellspacing="0" class="tb_detalle fsDivPage">
        <tr>
          <td width="188" height="25" style="background: #E9E9E9; font-weight: bold;"><strong>Parcela</strong></td>
          <td width="188" style="background: #E9E9E9; font-weight: bold;"><strong>Plan</strong></td>
          <td width="188" style="background: #E9E9E9; font-weight: bold;"><strong>Bóveda</strong></td>
          <td width="188" style="background: #E9E9E9; font-weight: bold;">Hora del Servicio</td>
        </tr>
        <tr>
          <td height="25"><?php echo $oinh->id_jardin."-".$oinh->id_fases."-".$oinh->bloque."-".$oinh->lote?></td>
          <td id="dt_plan">P</td>
          <td id="dt_boveda"><?php echo $oinh->cavidad?></td>
          <td id="dt_hora_servicio">&nbsp;</td>
        </tr>
        <tr>
          <td height="25" style="background: #E9E9E9; font-weight: bold;"><strong>Tipo de Servicio</strong></td>
          <td style="background: #E9E9E9; font-weight: bold;"><strong>Asesor que Vende</strong></td>
          <td style="background: #E9E9E9; font-weight: bold;"><strong>Asesor que Atiende</strong></td>
          <td style="background: #E9E9E9; font-weight: bold;"><strong>Supervisor</strong></td>
        </tr>
        <tr>
          <td height="25">INHUMACION</td>
          <td><?php echo $nombre_asesor;?></td>
          <td id="dt_atendido_por">&nbsp;</td>
          <td width="188"><?php echo $nombre_gerente;?></td>
        </tr>
        <tr>
          <td height="25" style="background: #E9E9E9; font-weight: bold;"><strong>Nombre en Lápida</strong></td>
          <td colspan="3" align="left"><?php echo $oinh->nombre_lapida?></td>
        </tr>
        <tr>
          <td height="25" style="background: #E9E9E9; font-weight: bold;"><strong>Texto en Lápida</strong></td>
          <td colspan="3" align="left"><?php echo $oinh->esquela?></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="20" align="center" bgcolor="#D1D1D1"><strong>COMETARIOS</strong></td>
    </tr>
    <tr>
      <td height="80" align="left" valign="top"><?php echo $oinh->comentario;?></td>
    </tr>
    <tr>
      <td align="center"><table border="0" cellspacing="0" cellpadding="0" align="left">
        <tr >
          <td >&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <td >&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <td width="185" >_______________________</td>
          
          <td width="185">_______________________</td>
          <td width="185">_______________________</td>
          <td width="185">_______________________</td>
        </tr>
        <tr class="tb_detalle fsDivPage">
          <td height="25"><p><strong>Procesado  Por:</strong></p></td>
          <td><strong>Recibido Por:</strong></td>
          <td><strong>Revisado Por:</strong></td>
          <td><strong>Autorizado Por:</strong></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td align="center">&nbsp;</td>
    </tr>
    <tr>
      <td align="center">&nbsp;</td>
    </tr>
 
  </table> 
</page> 
<?php 
     $content = ob_get_clean();
 
    require_once('class/lib/pdf/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'letter', 'fr');
		//DOTMATRI.TTF
        $html2pdf->pdf->SetDisplayMode('fullpage'); 
	    $x=$html2pdf->pdf->addTTFfont(dirname(__FILE__)."/dotmatri.ttf", 'TrueTypeUnicode', '', 32); 
        $html2pdf->writeHTML($content,"");
        $html2pdf->Output("test"."-".date("d-m-Y").'.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>