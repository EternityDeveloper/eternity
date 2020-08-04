<?php
if (!isset($protect)){
	exit;
}	
 
if (!isset($_REQUEST['id'])){
	echo "Error contrato no existe!";
	exit;
}	 

$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));

if (!isset($contrato->serie_contrato)){
	echo "Error contrato no existe!";
	exit;
}	

SystemHtml::getInstance()->includeClass("client","PersonalData");
SystemHtml::getInstance()->includeClass("contratos","Contratos");

$_asesor=new Asesores($protect->getDBLink());
$asesores=$_asesor->getComercialParentData($contrato->codigo_asesor);
 
$id_prospecto=System::getInstance()->Encrypt($contrato->prospecto);
$id_contrante=System::getInstance()->Encrypt($contrato->id_nit_cliente);
 
			
$person= new PersonalData($protect->getDBLink(),$_REQUEST);
$datos_p=$person->getClientData($contrato->id_nit_cliente);
$datos_p_telefono=$person->getPhone($contrato->id_nit_cliente);
$datos_p_direccion=$person->getAddress($contrato->id_nit_cliente);

$con= new Contratos($protect->getDBLink());
$representante=$con->getRepresentantes($contrato->serie_contrato,$contrato->no_contrato);

$product=$con->getDetalleProductsFromContrato($contrato->serie_contrato,$contrato->no_contrato);
$servicios=$con->getDetalleServicioFromContrato($contrato->serie_contrato,$contrato->no_contrato); 
 
//System::getInstance()->Decrypt($_REQUEST['serie_contrato']),System::getInstance()->Decrypt($_REQUEST['no_contrato'])

$ctelefono=array();
foreach($datos_p_telefono as $key =>$val){
	$ctelefono[$val['tipo']]=$val;
}

$plan_finan="";
foreach($servicios as $key =>$srv){
	$plan_finan=$srv['CODIGO_TP'];
	break;
} 
?>
<page backcolor="#FFFFFF" >
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>&nbsp;</td>
    </tr>
  </table>
  <table width="700" align="center"  cellpadding="0" cellspacing="0" style="border: dotted 1px #000077; border-right: dotted 1px #000077;font:Verdana, Geneva, sans-serif ">
    <tr>
      <td height="20" align="center" ><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="250"  style="border-right:dotted 1px #000077;border-bottom:dotted 1px #000077;" ><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="center"><img src="images/servicios_memorial.fw.png" width="222" height="75" /></td>
              </tr>
            <tr>
              <td style="font-size: 12px; text-align: center"><p><strong>Servicios Memoriales Dominicanos SMD SRL.<br />
                RNC 130-80999-8</strong></p></td>
              </tr>
            </table></td>
          <td width="250" align="center"  style="border-right:dotted 1px #000077;border-bottom:dotted 1px #000077;" ><strong><h1>Planilla de Solicitud</h1></strong></td>
          <td width="250"  style="border-bottom:dotted 1px #000077;" ><table width="100%" border="0" cellspacing="0" cellpadding="0" >
            <tr>
              <td width="120" height="35" align="left"  style="border-right:dotted 1px #000077;border-bottom:dotted 1px #000077;"  ><strong>Fecha:</strong></td>
              <td width="120" align="left"  style="border-bottom:dotted 1px #000077;" ><?php echo date("Y-m-d")?></td>
              </tr>
            <tr>
              <td height="35" align="left"  style="border-right:dotted 1px #000077;border-bottom:dotted 1px #000077;" ><strong>Recepcion</strong></td>
              <td align="left"  style="border-bottom:dotted 1px #000077;" ><?php echo $contrato->fecha_venta?></td>
              </tr>
            <tr>
              <td height="35" align="left"  style="border-right:dotted 1px #000077;"><strong>No. Solicitud</strong></td>
              <td align="left"    ><?php echo $contrato->no_contrato?></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td height="25" colspan="3" align="left"  style="border-bottom:dotted 1px #000077;" >Dirección de Oficina y Parque: Ave. Jacobo Majluta, Santo Domingo Norte, Republica Dominicana</td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td width="700" height="20" align="center" bgcolor="#D1D1D1"><strong>INFORMACION GENERAL DEL TITULAR</strong></td>
    </tr>
    <tr>
      <td><table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
        <tr>
          <td width="750" height="25"  style="border-bottom:dotted 1px #000077;" ><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="210"  style="border-right:dotted 1px #000077;"><strong>Nombre del Titular del contrato</strong></td>
              <td width="560" height="20"   > <?php echo $datos_p['nombre_completo'];?> </td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="25"  style="border-bottom:dotted 1px #000077;" ><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="450" height="20"  style="border-right:dotted 1px #000077;"><strong>Cédula de identidad del titular:</strong>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $contrato->id_nit_cliente;?></td>
              <td><strong>Tipo de contrato:&nbsp;&nbsp;</strong>Individual</td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="25" style="border-bottom:dotted 1px #000077;" ><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="80" ><strong>Tel&eacute;fono:</strong></td>
              <td width="150" style="border-right:dotted 1px #000077;"><?php echo $ctelefono['Residencia']['area']."-".$ctelefono['Residencia']['numero'];?></td>
              <td width="80"><strong>Celular:</strong></td>
              <td width="150"  style="border-right:dotted 1px #000077;"><?php echo $ctelefono['Celular']['area']."-".$ctelefono['Celular']['numero'];?></td>
              <td width="80"><strong>Oficina:</strong></td>
              <td width="150" height="20"><span style="border-right:dotted 1px #000077;"><?php echo $ctelefono['Laboral']['area']."-".$ctelefono['Laboral']['numero'];?></span></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td height="25" style="border-bottom:dotted 1px #000077;" ><strong>Direccion de cobro:<span style="font-size:14px;">
            <?php 		
		$SQL="SELECT
			 sys_direcciones.*,
		CONCAT(sys_provincia.`descripcion`,',',sys_ciudad.Descripcion,',',sys_sector.`descripcion`) AS direccion
FROM 
`sys_direcciones` 
INNER JOIN `sys_sector` ON (`sys_sector`.`idsector`=sys_direcciones.idsector)
INNER JOIN `sys_ciudad` ON (`sys_sector`.`idciudad`=sys_ciudad.idciudad)
INNER JOIN `sys_municipio` ON (`sys_municipio`.`idmunicipio`=sys_ciudad.idmunicipio) 
INNER JOIN `sys_provincia` ON (`sys_provincia`.`idprovincia`=sys_municipio.idprovincia)
WHERE sys_direcciones.serie_contrato='".$contrato->serie_contrato."' AND sys_direcciones.no_contrato='".$contrato->no_contrato."' ";
 
		$rs=mysql_query($SQL);
		$cant=mysql_num_rows($rs);
		$id_dir="";
		while($row=mysql_fetch_assoc($rs)){
			 echo $row['direccion'];
		}
 	 
		?>
          </span></strong></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="20" align="center" bgcolor="#D1D1D1"><strong>INFORMACION DE LOS AUTORIZADOS</strong></td>
    </tr>
    <tr>
      <td align="left"><?php 
 foreach($representante as $key =>$rep){
	 $datos_rep=$person->getClientData(System::getInstance()->Decrypt($rep['idnit'])); 
 ?> <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr >
          <td width="450" height="25"  style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;"><strong>Nombre y Apellido:</strong>&nbsp;<?php echo  $datos_rep['nombre_completo']?></td>
          <td width="315" align="left"  style="border-bottom:dotted 1px #000077;"><strong>Cédula:</strong>&nbsp;<?php echo  $datos_rep['id_nit']?></td>
        </tr>
        <tr>
          <td height="25"  style="border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;"><strong>Parentesco del  Titular:</strong>&nbsp;<?php echo  $rep['parentesco']?></td>
          <td align="left"  style="border-bottom:dotted 1px #000077;"><strong>Teléfono</strong>:&nbsp;<?php echo  $datos_rep['telefono']?></td>
        </tr>
        <tr>
          <td height="25" colspan="2"  style="border-bottom:dotted 1px #000077;"><strong>Direccion.: </strong>&nbsp;<?php echo  strip_tags($datos_rep['direccion']);?></td>
        </tr>
        </table>   <?php } ?> </td>
    </tr>
    <tr>
      <td height="20" align="center" bgcolor="#D1D1D1"><strong>INFORMACION DEL PLAN</strong></td>
    </tr>
    <tr>
      <td align="center"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tb_detalle fsDivPage">
        <tr>
          <td width="188" height="30" align="center" style="background: #E9E9E9; font-weight: bold;border-right:dotted 1px #000077;"><strong>Plan</strong></td>
          <td width="188" align="center" style="background: #E9E9E9; font-weight: bold;border-right:dotted 1px #000077;"><strong>Precio Base</strong></td>
          <td width="188" align="center" style="background: #E9E9E9; font-weight: bold;border-right:dotted 1px #000077;"><strong>Cant. de Parcelas</strong></td>
          <td width="188" align="center" style="background: #E9E9E9; font-weight: bold;">Descuento en <br>
            Precio base</td>
        </tr>
        <tr>
          <td height="30" align="center" style="border-right:dotted 1px #000077;"><?php echo $plan_finan;?></td>
          <td align="center" id="dt_plan" style="border-right:dotted 1px #000077;"><?php echo number_format($contrato->precio_lista,2);?></td>
          <td align="center" id="dt_boveda" style="border-right:dotted 1px #000077;"><?php echo $contrato->no_productos;?></td>
          <td align="center" id="dt_hora_servicio" ><?php echo number_format($contrato->descuento,2);?></td>
        </tr>
        <tr>
          <td height="30" align="center" style="background: #E9E9E9; font-weight: bold;border-right:dotted 1px #000077;"><strong>Precio Total</strong></td>
          <td align="center" style="background: #E9E9E9; font-weight: bold;border-right:dotted 1px #000077;"><strong>Monto Capitalizado</strong></td>
          <td align="center" style="background: #E9E9E9; font-weight: bold;border-right:dotted 1px #000077;"><strong>Monto de Inicial <br>
            o Abono</strong></td>
          <td align="center" style="background: #E9E9E9; font-weight: bold;"><strong>Descuento en <br>
            Inicial o Abono</strong></td>
        </tr>
        <tr>
          <td height="25" align="center" style="border-right:dotted 1px #000077;"><?php echo number_format($contrato->precio_neto,2);?></td>
          <td align="center" style="border-right:dotted 1px #000077;">0</td>
          <td align="center" style="border-right:dotted 1px #000077;"><?php echo number_format($contrato->enganche,2);?></td>
          <td width="188" align="center"  >0</td>
        </tr>
        <tr>
          <td height="30" align="center" style="background: #E9E9E9; font-weight: bold;border-right:dotted 1px #000077;"><strong>Saldo a Financiar</strong></td>
          <td align="center" style="background: #E9E9E9; font-weight: bold;border-right:dotted 1px #000077;"><strong>Interés de Financiamiento</strong></td>
          <td align="center" style="background: #E9E9E9; font-weight: bold;border-right:dotted 1px #000077;"><strong>Sub-Total</strong></td>
          <td align="center" style="background: #E9E9E9; font-weight: bold;"><strong>Pago Total</strong></td>
        </tr>
        <tr>
          <td height="30" align="center" style="border-right:dotted 1px #000077;"><?php echo number_format($contrato->interes,2);?></td>
          <td align="center" style="border-right:dotted 1px #000077;"><?php echo $contrato->porc_interes;?></td>
          <td align="center" style="border-right:dotted 1px #000077;"><?php echo number_format($contrato->interes+$contrato->precio_neto,2);?></td>
          <td align="center" ><?php echo number_format($contrato->interes+$contrato->precio_neto+$contrato->enganche,2);?></td>
        </tr>
        <tr>
          <td height="25" colspan="4" align="center"><table width="100%" border="0" cellpadding="0" cellspacing="0" class= style="border-bottom: dotted 1px #000077;">
            <tr>
              <td width="254" height="30" align="center" style="background: #E9E9E9; font-weight: bold;border-right:dotted 1px #000077;"><strong>Total de Cuotas</strong></td>
              <td width="254" align="center" style="background: #E9E9E9; font-weight: bold;border-right:dotted 1px #000077;"><strong>Periodicidad</strong></td>
              <td width="254" align="center" style="background: #E9E9E9; font-weight: bold;"><strong>Monto de la Cuota</strong></td>
              </tr>
            <tr>
              <td height="30" align="center" style="border-right:dotted 1px #000077;"><?php echo $contrato->cuotas;?></td>
              <td align="center" style="border-right:dotted 1px #000077;">MENSUAL</td>
              <td align="center"><?php echo number_format($contrato->valor_cuota,2);?></td>
              </tr>
          </table></td>
          </tr>
        <tr>
          <td height="25" colspan="4"><table width="100%" border="0" cellspacing="0" cellpadding="0" >
    <?php if (count($product )>0){?>          
            <tr>
              <td width="150" height="25"  style="border-top:dotted 1px #000077;border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;"><strong>Parcela Asignada</strong></td>
              <td width="620" align="left"  style="border-top:dotted 1px #000077;border-bottom:dotted 1px #000077;"><?php 		    
		foreach($product as $key =>$producto){ 
			echo $producto['id_jardin']."-".$producto['id_fases']."-".$producto['bloque']."-".$producto['lote']."   ";
		}
		?>                &nbsp;</td>
            </tr>
<?php } ?>            
            <?php if (count($servicios )>0){?>
            <tr>
              <td width="150" height="25"  style="border-top:dotted 1px #000077;border-bottom:dotted 1px #000077;border-right:dotted 1px #000077;"><strong>Servicios:</strong></td>
              <td width="620" align="left"  style="border-top:dotted 1px #000077;border-bottom:dotted 1px #000077;"><?php 		    
		foreach($servicios as $key =>$srv){
			echo  $srv['serv_descripcion']."  ";
		}
		?></td>
            </tr>
            <?php } ?>
          </table></td>
        </tr>
        <tr>
          <td height="25" colspan="4">&nbsp;</td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="20" align="center" bgcolor="#D1D1D1"><strong>COMETARIOS</strong></td>
    </tr>
    <tr>
      <td height="60" align="left" valign="top"><?php echo $oinh->comentario;?></td>
    </tr>
    <tr>
      <td align="center"><table border="0" cellspacing="0" cellpadding="0" align="left">
        <tr class="tb_detalle fsDivPage">
          <td width="185" height="25"><p><strong>Procesado  Por:</strong></p></td>
          <td width="185"><strong>Asesor:</strong></td>
          <td width="370"><strong>Autorizado por:</strong></td>
        </tr>
        <tr class="tb_detalle fsDivPage">
          <td height="25"><?php 
		   $proc=$person->getClientData($contrato->id_nit_reiterador);
		   echo $proc['nombre_completo']; ?></td>
          <td><?php echo $asesores[0]['nombre']." ".$asesores[0]['apellido'];?></td>
          <td>&nbsp;</td>
          </tr>
      </table></td>
    </tr>
  </table>
</page>
<?php 

     $content = ob_get_clean();
 
    require_once('class/lib/pdf/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', 0);
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->writeHTML($content,"");
        $html2pdf->Output('recibo_venta_'.$pago->reporte_venta.'.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>
