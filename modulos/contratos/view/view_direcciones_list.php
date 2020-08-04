<?php
if (!isset($protect)){
	exit;
}

if (!(isset($_REQUEST['serie_contrato'])) && (isset($_REQUEST['no_contrato']))){
	echo "Datos invalidos";
	exit;
}
SystemHtml::getInstance()->includeClass("client","PersonalData");
$person= new PersonalData($protect->getDBLink());

SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
$_contratos=new Contratos($protect->getDBLink()); 
$cc=$_contratos->getInfoContrato(System::getInstance()->Decrypt($_REQUEST['serie_contrato']),System::getInstance()->Decrypt($_REQUEST['no_contrato']));
 
$data_address=$person->getAddress($cc->id_nit_cliente);


$contrato=System::getInstance()->Encrypt(json_encode(array("serie_contrato"=>$cc->serie_contrato,"no_contrato"=>$cc->no_contrato)));

 
 
?><div class="modal fade" id="modal_address_contrato" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:800px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Direccion de cobro</h4>
      </div>
      <div class="modal-body"><div style="background-color:#FFF">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
 
    <tr>
      <td><h2 style="margin:0;color:#FFF;">Registrar nueva direccion: <button type="button" id="bt_create_direccion" data="<?php echo $contrato;?>">Agregar</button></h2>
         </td>
    </tr> 
    <tr>
      <td><table border="0" class="table table-bordered table-striped table-hover"  style="font-size:13px">
        <thead>
          <tr>
            <th><span >Provincia</span></th>
            <th><span >Ciudad</span></th>
            <th><span >Sector</span></th>
            <th><span >Avenida</span></th>
            <th><span >Calle</span></th>
            <th><span >Tipo</span></th>
          </tr>
        </thead>
        <tbody>
          <?php
 
foreach($data_address as $key => $row){
	
	$data=array(
				"cuidad_id"=>System::getInstance()->Encrypt($row['idciudad']),
				"provincia_id"=>System::getInstance()->Encrypt($row['idprovincia']),
				"municipio_id"=>System::getInstance()->Encrypt($row['idmunicipio']),
				"sector_id"=>System::getInstance()->Encrypt($row['idsector']),
				"direccion_numero"=>$row['numero'],
				"direccion_manzana"=>$row['manzana'],
				"direccion_recidencia"=>$row['residencia_colonia_condominio'],
				"direccion_referencia"=>$row['referencia'],
				"direccion_observacion"=>$row['observaciones'],
				"direccion_avenida"=>$row['avenida'],
				"direccion_calle"=>$row['calle'],
				"direccion_zona"=>$row['zona'],
				"direccion_departamento"=>$row['departamento'],
				"serie_contrato"=>$_REQUEST['serie_contrato'],
				"no_contrato"=>$_REQUEST['no_contrato'],
				"direccion_id"=>$_REQUEST['direccion_id']
				); 
	//print_r($row);
	$encriptID=base64_encode(json_encode($data));
?>
          <tr class="direccion_select" id="<?php echo $encriptID?>" style="cursor:pointer">
            <td><?php echo $row['provincia'];?></td>
            <td><?php echo $row['ciudad'];?></td>
            <td   ><?php echo $row['sector'];?></td>
            <td  ><?php echo $row['avenida']?></td>
            <td ><?php echo $row['calle']?></td>
            <td   ><?php echo $row['tipo']?></td>
          </tr>
          <?php 
}
 ?>
        </tbody>
      </table></td>
    </tr>
  </table>
</div>
 </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="close_view" data-dismiss="modal">Cerrar</button>
       </div>
    </div>
  </div>
</div>