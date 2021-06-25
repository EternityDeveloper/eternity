<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
if (!isset($_REQUEST['id'])){
	echo "No hay registros!";
	exit;
}
$data=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
if (!isset($data->codigo_asesor)){
	echo "No hay registros!";
	exit;	
}

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-hover" style="font-size:12px">
      <thead>
        <tr>
          <td><strong>ID ACTA</strong></td>
          <td><strong> DOCUMENTO</strong></td>
          <td><strong>DESCRIPCION</strong></td>
          <td><strong>FECHA REGISTRO</strong></td>
          <td align="center"><strong>SALDO</strong></td>
        </tr>
      </thead>
      <tbody>
<?php  

$SQL="SELECT 	
	cxc.no_docto,
	cxc.descripcion,
	cxc.fecha_registro,
	cxc.monto,
	CONCAT(a.idacta,'-',a.secuencia) AS idacta 
FROM cxc_balance_asesor AS cxc
	LEFT JOIN `actas` AS a ON (a.id=cxc.idacta)
 WHERE cxc.estatus=1 AND cxc.codigo_asesor='". mysql_escape_string($data->codigo_asesor) ."' ";
$rs=mysql_query($SQL);
$monto=0;
while($row=mysql_fetch_assoc($rs)){
	$monto=$monto+$row['monto'];
	$id=System::getInstance()->Encrypt(json_encode($row));  
?>
        <tr >
          <td  ><?php echo utf8_encode($row['idacta']);?></td>
          <td  ><?php echo utf8_encode($row['no_docto']);?></td>
          <td  ><?php echo utf8_encode($row['descripcion']);?></td>
          <td  ><?php echo utf8_encode($row['fecha_registro']);?></td>
          <td align="center" ><?php echo number_format($row['monto'],2); ?></td>
        </tr> 
        <?php } ?>
        
      </tbody>
      <tfoot>
        <tr   >
          <td  >&nbsp;</td>
          <td  >&nbsp;</td>
          <td  >&nbsp;</td>
          <td  >&nbsp;</td>
          <td align="center" ><strong><?php echo number_format($monto,2);?></strong></td>
        </tr>
      </tfoot>
    </table>