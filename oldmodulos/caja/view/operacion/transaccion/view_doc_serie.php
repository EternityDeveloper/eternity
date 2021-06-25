<?php 
if (!isset($protect)){
	exit;	
}

$type=isset($_REQUEST['type'])?$_REQUEST['type']:"form";



if ($type=="form"){
  
SystemHtml::getInstance()->includeClass("contratos","Contratos");  
SystemHtml::getInstance()->includeClass("cobros","Cobros"); 

$cobros= new Cobros($protect->getDBLINK());  
$ct=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));

$info=$cobros->getAvisoCobroData($ct->serie_contrato,$ct->no_contrato); 

if ($info['monto_acobrar']>0){
	$monto=$info['monto_acobrar']; 
 
	$data=array(
			"monto"=>$monto,
			"no_cuota"=>$info['cuotas_acobrar'],
			'serie_doc'=>$info['serie'],
			'aviso_cobro'=>$info['aviso_cobro']
		);
	  
	$_contratos=new Contratos($this->db_link);
	$d_contrato=$_contratos->getInfoContrato($ct->serie_contrato,$ct->no_contrato);
	 
	$interes=($data['monto']*$d_contrato->porc_interes/100);
	$capital=$data['monto']-$interes;
 
// print_r($data);
//$monto=$interes+$capital; 
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle fsDivPage">
  <tr>
    <td colspan="8" align="left" style="font-size:12px;"><strong>AVISO DE COBROS POR FACTURAR</strong></td>
  </tr>
  <tr>
    <td align="center" style="font-size:12px;"><strong>DOCUMENTO</strong></td>
    <td align="center" style="font-size:12px;"><strong>SERIE DOC.</strong></td>
    <td align="center" style="font-size:12px;"><strong>NO. DOC.</strong></td>
    <td align="center" style="font-size:12px;"><strong>CANT. CUOTAS</strong></td>
    <td align="center" style="font-size:12px;"><strong>CAPITAL</strong></td>
    <td align="center" style="font-size:12px;"><strong>INTERESES</strong></td>
    <td align="center" style="font-size:12px;"><strong>MORA</strong></td>
    <td align="center" style="font-size:12px;"><strong>MONTO</strong></td>
  </tr>
  <tr>
    <td align="center" style="font-size:12px;">Aviso de cobro</td>
    <td align="center" style="font-size:12px;"><?php echo $info['serie'];?></td>
    <td align="center" style="font-size:12px;"><?php echo $info['aviso_cobro'];?></td>
    <td align="center" style="font-size:12px;"><?php echo $data['no_cuota'];?></td>
    <td align="center" style="font-size:12px;"><?php echo number_format($capital,2);?></td>
    <td align="center" style="font-size:12px;"><?php echo number_format($interes,2);?></td>
    <td align="center" style="font-size:12px;">0</td>
    <td align="center" style="font-size:12px;"><span class="badge alert-danger"><?php echo number_format($monto,2);?></span></td>
  </tr>
</table>
<?php  


echo json_encode(array("html"=>ob_get_clean(),"info"=>$data));

} else{ 

if (!isset($_REQUEST['token'])){
	echo "Falto enviar el Token";
	exit;	
}

SystemHtml::getInstance()->includeClass("caja","Caja"); 

?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><table width="150" border="0" cellspacing="5" cellpadding="1">
                <tr>
                  <td style="font-size:12px;"><strong>Tipo de documento</strong></td>
                </tr>
                <tr>
                  <td><select name="tipo_documento" id="tipo_documento" class="textfield_input required" style="width:200px;">
                    <option value="">Seleccione</option>
                    <?php 

		$SQL="SELECT * FROM `tipo_documento`";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			$encriptID=System::getInstance()->Encrypt(json_encode($row));
			$data=base64_encode(json_encode($row));
?>
                    <option value="<?php echo $encriptID?>" serialize="<?php echo $data;?>" ><?php echo ($row['DOCUMENTO'])?></option>
                    <?php } ?>
                  </select></td>
                </tr>
              </table></td>
              <td><table width="150" border="0" cellspacing="5" cellpadding="1">
                <tr>
                  <td style="font-size:12px;"><strong>Serie. Documento</strong></td>
                </tr>
                <tr>
                  <td id="serie_doc"><input type="text" name="serie_documento" id="serie_documento"  class="textfield_input required"/></td>
                </tr>
              </table></td>
              <td><table width="150" border="0" cellspacing="5" cellpadding="1">
                <tr>
                  <td style="font-size:12px;"><strong>No. Documento</strong></td>
                </tr>
                <tr>
                  <td><input type="text" name="no_documento" id="no_documento"  class="textfield_input required"/></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td width="25%"><table width="150" border="0" cellspacing="5" cellpadding="1" id="crt_reporte_venta" style="display:none">
                <tr>
                  <td><strong>Reporte de venta</strong></td>
                </tr>
                <tr>
                  <td><input type="text" name="reporte_venta" id="reporte_venta"  class="textfield_input"/></td>
                </tr>
              </table></td>
              <td width="25%">&nbsp;</td>
              <td width="25%">&nbsp;</td>
            </tr>
            </table>
<?php 

$caja= new Caja($protect->getDBLINK());  

$data=array(
	"monto"=>$caja->getItemMontoACobrar($_REQUEST['token'])
);


echo json_encode(array("html"=>ob_get_clean(),"info"=>$data));

} 

}else if ($type=="serie_doc_list"){
	$doc=$_REQUEST['doc'];
	?> 
	<select name="serie_documento" id="serie_documento" class="textfield_input required" style="width:200px;">
                    <option value="">Seleccione</option>
                    <?php 

		$SQL="SELECT * FROM `correlativo_doc`  WHERE `TIPO_DOC`='". mysql_real_escape_string($doc) ."'";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
                    <option value="<?php echo $encriptID?>" serialize="<?php echo base64_encode(json_encode($row));?>"><?php echo ($row['SERIE'])?></option>
                    <?php } ?>
                  </select>
<?php }else if ($type=="serie_doc"){?> 
	<input type="text" name="serie_documento" id="serie_documento"  class="textfield_input required"/>
<?php } ?> 