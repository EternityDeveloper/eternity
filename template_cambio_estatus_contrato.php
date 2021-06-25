<?php ob_start();
include("includes/config.inc.php"); 
include("includes/function.php"); 
$db_link = mysql_connect(DB_SERVER,DB_USER,DB_PWD);
mysql_select_db(DB_DATABASE,$db_link);

if (isset($_REQUEST['filter'])){
$SQL="SELECT 
 CONCAT(contratos.serie_contrato,' ',contratos.no_contrato) AS CONTRATO,
 CONCAT(cliente.primer_nombre, ' ', cliente.segundo_nombre, ' ', cliente.primer_apellido) AS NOMBRE_CLIENTE,
 CONCAT(asesor.primer_nombre, ' ', asesor.segundo_nombre, ' ', asesor.primer_apellido) AS ASESOR,
CONCAT(gerente.primer_nombre, ' ', gerente.segundo_nombre, ' ', gerente.primer_apellido) AS GERENTE,
CONCAT(oficial.primer_nombre, ' ', oficial.segundo_nombre, ' ', oficial.primer_apellido) AS OFICIAL_COBROS,
fecha_venta,
 cuotas AS PLAZO,
 no_productos AS CANTIDAD_PROCUCTO,
 tipo_moneda AS MONEDA,
 tipo_cambio AS TASA,
 (precio_lista - descuento) * tipo_cambio AS PRECIO_NETO,
 (precio_neto * tipo_cambio)  AS CAPITAL_FINACIAR,
 descuento,
 (enganche* tipo_cambio) AS INICIAL,
 estatus.descripcion AS ESTATUS,
(SELECT (CASE 
 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
 WHEN pc.id_jardin!='' THEN (SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin) END ) AS producto 
 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
  pc.serie_contrato=contratos.serie_contrato AND pc.no_contrato=contratos.no_contrato LIMIT 1) AS producto ,
 DATEDIFF(CURDATE(),fecha_ultimo_pago) AS DIAS_DE_ATRASO,
 cobros_contratos.nit_oficial,
 gerente.id_nit AS gerente_nit,
  cobros_contratos.fecha_ultimo_pago,
 cobros_contratos.comentario_cliente AS ultima_gestion
 FROM contratos
 INNER JOIN `cobros_contratos` ON (`contratos`.serie_contrato=cobros_contratos.serie_contrato AND
contratos.no_contrato=cobros_contratos.no_contrato)
 INNER JOIN sys_personas AS cliente ON (cliente.id_nit = contratos.id_nit_cliente)
 INNER JOIN sys_asesor ON (sys_asesor.codigo_asesor = contratos.codigo_asesor)
 INNER JOIN sys_personas AS asesor ON (asesor.id_nit = sys_asesor.id_nit)
 INNER JOIN sys_status AS estatus ON (estatus.id_status = contratos.estatus) 
 INNER JOIN sys_gerentes_grupos ON (sys_gerentes_grupos.codigo_gerente_grupo = contratos.codigo_gerente)
 INNER JOIN sys_personas AS gerente ON (gerente.id_nit = sys_gerentes_grupos.id_nit) 
 INNER JOIN sys_personas AS oficial ON (oficial.id_nit = cobros_contratos.nit_oficial) 
 WHERE 1=1  ";	
				 	
   	if ($_REQUEST['filter']=="gerente"){
		$SQL.=" AND contratos.codigo_gerente='". mysql_real_escape_string($_REQUEST['id'])."'";
				 
	}else if ($_REQUEST['filter']=="oficial"){
		$SQL.=" AND cobros_contratos.nit_oficial='". mysql_real_escape_string($_REQUEST['id'])."'";	 
	}else if ($_REQUEST['filter']=="gerencia"){ 
		 			 
	}else{
		exit;	
	}
}else{
	exit;	
} 
 
?><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" ><p>Estimado  <?php echo base64_decode($_REQUEST['nombre']);?>,</p>
      <p>Luego de  saludarle, nos dirigimos a usted con el fin de informarle que adjuto encontrara un listado de contratos que se encuentra en estado POR DESISTIR, para su posible gestion.</p>
    <p><strong>Saludos,</strong></p></td>
  </tr>
  <tr>
    <td align="left" style="font-style: italic;">&nbsp;</td>
  </tr>
  <tr>
    <td align="left" style="font-style: italic;"><strong>POSIBLE A DESISTIR</strong></td>
  </tr>
  <tr>
    <td align="left" style="font-style: italic;"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size:10px;border:solid 1px; border-collapse: collapse;"  >
      <tr>
        <td height="30" align="center" bgcolor="#CCCCCC" style="border:solid 1px;border-right: solid 1px;"><strong>CONTRATO</strong></td>
        <td align="center" bgcolor="#CCCCCC" style="border:solid 1px;border-right: solid 1px;"><strong>PRODUCTO</strong></td>
        <td align="center" bgcolor="#CCCCCC" style="border:solid 1px;border-right: solid 1px;"><strong>NOMBRE CLIENTE</strong></td>
        <td align="center" bgcolor="#CCCCCC" style="border:solid 1px;border-right: solid 1px;"><strong>ASESOR</strong></td>
        <td align="center" bgcolor="#CCCCCC" style="border:solid 1px;border-right: solid 1px;"><strong>GERENTE</strong></td>
        <td align="center" bgcolor="#CCCCCC" style="border:solid 1px;border-right: solid 1px;"><strong>OFICIAL</strong></td>
        <td align="center" bgcolor="#CCCCCC" style="border:solid 1px;border-right: solid 1px;"><strong>DIAS DE ATRASO</strong></td>
        <td align="center" bgcolor="#CCCCCC" style="border:solid 1px;border-right: solid 1px;"><strong>FECHA VENTA</strong></td>
        <td align="center" bgcolor="#CCCCCC" style="border:solid 1px;border-right: solid 1px;"><strong>FECHA ULTIMO PAGO</strong></td>
        <td align="center" bgcolor="#CCCCCC" style="border:solid 1px;border-right: solid 1px;"><strong>ULTIMA GESTION</strong></td>
      </tr>
      <?php
	$i=0;
	$SQL1=$SQL."  and contratos.estatus IN (23)  ";
	$rs=mysql_query($SQL1);
	while($ct=mysql_fetch_object($rs)){	 
		$i++;
	//	print_r($ct);
?>
      <tr >
        <td align="center" style="border:solid 1px;border-right: solid 1px;"><?php echo $ct->CONTRATO;?></td>
        <td align="center" style="border:solid 1px;border-right: solid 1px;"><?php echo $ct->producto;?></td>
        <td height="20" align="center" style="border:solid 1px;border-right: solid 1px;"><?php echo $ct->NOMBRE_CLIENTE;?></td>
        <td align="center" style="border:solid 1px;border-right: solid 1px;"><?php echo $ct->ASESOR;?></td>
        <td align="center" style="border:solid 1px;border-right: solid 1px;"><?php echo $ct->GERENTE;?></td>
        <td align="center" style="border:solid 1px;border-right: solid 1px;"><?php echo $ct->OFICIAL_COBROS;?></td>
        <td align="center" style="border:solid 1px;border-right: solid 1px;"><?php echo $ct->DIAS_DE_ATRASO;?></td>
        <td align="center" style="border:solid 1px;border-right: solid 1px;"><?php echo $ct->fecha_venta;?></td>
        <td align="center" style="border:solid 1px;border-right: solid 1px;"><?php echo $ct->fecha_ultimo_pago;?></td>
        <td width="160" align="center" style="font-size:9px;border:solid 1px;border-right: solid 1px;" ><?php echo $ct->ultima_gestion;?></td>
      </tr>
      <?php } ?>
      <tr>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td height="20" align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">TOTALES</td>
        <td align="center"><?php echo $i;?>&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="left" style="font-style: italic;">&nbsp;</td>
  </tr>
  <tr>
    <td align="left" style="font-style: italic;">&nbsp;</td>
  </tr>
  <tr>
    <td align="left" style="font-style: italic;">&nbsp;</td>
  </tr>
  <tr>
    <td align="left" style="font-style: italic;"><strong>CONTRATOS QUE ESTAN POSIBLES ANULAR DEL MES <?php echo date("m")?></strong></td>
  </tr>
  <tr>
    <td align="left" style="font-style: italic;"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size:10px;border:solid 1px; border-collapse: collapse;"  >
      <tr>
        <td height="30" align="center" bgcolor="#CCCCCC" style="border:solid 1px;border-right: solid 1px;"><strong>CONTRATO</strong></td>
        <td align="center" bgcolor="#CCCCCC" style="border:solid 1px;border-right: solid 1px;"><strong>PRODUCTO</strong></td>
        <td align="center" bgcolor="#CCCCCC" style="border:solid 1px;border-right: solid 1px;"><strong>NOMBRE CLIENTE</strong></td>
        <td align="center" bgcolor="#CCCCCC" style="border:solid 1px;border-right: solid 1px;"><strong>ASESOR</strong></td>
        <td align="center" bgcolor="#CCCCCC" style="border:solid 1px;border-right: solid 1px;"><strong>GERENTE</strong></td>
        <td align="center" bgcolor="#CCCCCC" style="border:solid 1px;border-right: solid 1px;"><strong>OFICIAL</strong></td>
        <td align="center" bgcolor="#CCCCCC" style="border:solid 1px;border-right: solid 1px;"><strong>DIAS DE ATRASO</strong></td>
        <td align="center" bgcolor="#CCCCCC" style="border:solid 1px;border-right: solid 1px;"><strong>FECHA VENTA</strong></td>
        <td align="center" bgcolor="#CCCCCC" style="border:solid 1px;border-right: solid 1px;"><strong>FECHA ULTIMO PAGO</strong></td>
        <td align="center" bgcolor="#CCCCCC" style="border:solid 1px;border-right: solid 1px;"><strong>ULTIMA GESTION</strong></td>
      </tr>
      <?php
	$i=0;
	$SQL2=$SQL."  and contratos.estatus IN (28)  ";
	$rs=mysql_query($SQL2);
	while($ct=mysql_fetch_object($rs)){	 
		$i++;
	//	print_r($ct);
?>
      <tr >
        <td align="center" style="border:solid 1px;border-right: solid 1px;"><?php echo $ct->CONTRATO;?></td>
        <td align="center" style="border:solid 1px;border-right: solid 1px;"><?php echo $ct->producto;?></td>
        <td height="20" align="center" style="border:solid 1px;border-right: solid 1px;"><?php echo $ct->NOMBRE_CLIENTE;?></td>
        <td align="center" style="border:solid 1px;border-right: solid 1px;"><?php echo $ct->ASESOR;?></td>
        <td align="center" style="border:solid 1px;border-right: solid 1px;"><?php echo $ct->GERENTE;?></td>
        <td align="center" style="border:solid 1px;border-right: solid 1px;"><?php echo $ct->OFICIAL_COBROS;?></td>
        <td align="center" style="border:solid 1px;border-right: solid 1px;"><?php echo $ct->DIAS_DE_ATRASO;?></td>
        <td align="center" style="border:solid 1px;border-right: solid 1px;"><?php echo $ct->fecha_venta;?></td>
        <td align="center" style="border:solid 1px;border-right: solid 1px;"><?php echo $ct->fecha_ultimo_pago;?></td>
        <td width="160" align="center" style="font-size:9px;border:solid 1px;border-right: solid 1px;" ><?php echo $ct->ultima_gestion;?></td>
      </tr>
      <?php } ?>
      <tr>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td height="20" align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">TOTALES</td>
        <td align="center"><?php echo $i;?>&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="left" style="font-style: italic;">
    <img src="http://memorial.com.do/image/johanna_cruz.png"  /> 
    </td>
  </tr>
</table>
 