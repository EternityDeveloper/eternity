<?php 
if (!isset($protect)){
	exit;	
}

if (!validateField($_REQUEST,"lote")){
	exit;
}
$lote=json_decode(System::getInstance()->Decrypt($_REQUEST['lote']));
if (!validateField($lote,"pap_codigo_lote")){
	exit;
} 
 
SystemHtml::getInstance()->includeClass("papeleria","Recibos"); 
$pap= new Recibos($protect->getDBLINK());   
 
?>
<div class="modal fade" id="view_modal_lote_detalle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:1000px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">DETALLE LOTE</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="120"><table border="0" class="table table-bordered table-striped table-hover"  style="font-size:13px;">
                <thead>
                  <tr>
                    <th>ASIGNADO A</th>
                    <th>DESDE</th>
                    <th>HASTA</th>
                    <th><span >LOTE</span></th>
                    <th>TOTAL</th>
                    <th>CANT. USADA</th>
                    <th>DISPONIBLE</th>
                    <th>CREADO POR</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
 
$listado= $pap->getListadoDetalle($lote->pap_codigo_lote);
foreach($listado as $key=>$row){ 
	$id=System::getInstance()->Encrypt(json_encode($row)); 
?>
                  <tr  >
                    <td><?php echo $row['ASIGNADO_A'];?></td>
                    <td><?php echo $row['pap_desde'];?></td>
                    <td><?php echo $row['pap_hasta'];?></td>
                    <td><?php echo $row['pap_codigo_lote'];?></td>
                    <td><?php echo $row['TOTAL'];?></td>
                    <td><?php echo round($row['USADA']);?></td>
                    <td><?php echo $row['DISPONIBLE'];?></td>
                    <td><?php echo $row['CREADO_POR'];?></td>
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
  </div>
</div>