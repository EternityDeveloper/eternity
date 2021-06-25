<?php 
if (!isset($protect)){
	exit;	
} 
SystemHtml::getInstance()->includeClass("caja","Caja"); 

$ct=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));
 
if (isset($ct->serie_contrato)){
	$cn=array(
		"contrato"=>1,
		"id_nit"=>$id_nit,
		"serie_contrato"=>$ct->serie_contrato,
		"no_contrato"=>$ct->no_contrato,			
	);	
}
$cj= new Caja($protect->getDBLINK());    
$recibos=$cj->getListadoNotaCredito($cn);
  		 	
?> <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><h2 style="color:#FFF;font-size:14px;">NOTAS DE CREDITO DISPONIBLES</h2></td>
  </tr>
  <tr>
    <td> 
       <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td ><table border="0" class="table table-bordered table-striped table-hover"  style="font-size:13px">
            <thead>
              <tr>
                <th><input class="select_all_nc" type="checkbox" name="checkbox2" id="checkbox2" /></th>
                <th>No. Recibo</th>
                <th>Contrato</th>
                <th>Descripcion</th>
                <th>Monto</th>
                <th>Fecha </th>
                <th width="170">Oficial</th>
                <th>Motorizado</th>
                </tr>
            </thead>
            <tbody>
<?php
 
 
foreach($recibos as $key =>$row){  
	$solicitud=System::getInstance()->Encrypt(json_encode($row));
 
?>
              <tr style="cursor:pointer" id="<?php echo $solicitud;?>" class="no_recibo_fact" ref="<?php echo $row['SERIE'].$row['NO_DOCTO'];?>">
                <td><input class="listado_nc" type="checkbox" name="checkbox" id="checkbox" value="<?php echo $solicitud;?>">
                  <label for="checkbox"></label></td>
                <td><?php echo $row['SERIE']." ".$row['NO_DOCTO'];?></td>
                <td><?php echo $row['SERIE_CONTRATO']." ".$row['NO_CONTRATO'];?></td>
                <td><?php echo $row['tmovimiento'];?></td>
                <td><?php echo number_format($row['MONTO_TOTAL'],2);?></td>
                <td><?php echo $row['FECHA_REQUERIMIENTO'];?></td>
                <td><?php echo utf8_encode($row['oficial']);?></td>
                <td><?php echo utf8_encode($row['motorizado']);?></td>
                </tr>
 
              <?php 
}
 ?>
            </tbody>
          </table></td>
        </tr>
      </table></td>
  </tr>
 
 
</table>
