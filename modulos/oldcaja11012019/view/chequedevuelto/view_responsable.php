<?php 
if (!isset($protect)){
	exit;	
} 
if (!isset($_REQUEST['id'])){
	
	exit;	
} 

$cd=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
 
?>
<div class="modal fade" id="modal_view_responsable" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:830px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="title_template">SELECCIONAR RESPONSABLE</h4>
      </div>
      <div class="modal-body">
    
      <form method="post"  action="" id="caja_payment"  name="caja_payment" class="fsForm"> 
        <table width="700" border="0" cellspacing="0" cellpadding="0">
    
          <tr>
            <td ><table border="0" class="table table-bordered table-striped table-hover"  >
              <thead>
                <tr>
                  <th>NOMBRE CLIENTE</th>
                  <th>CONTRATO</th>
                  <th>&nbsp;</th>
                  </tr>
              </thead>
              <tbody>
<?php 

	$SQL="SELECT cd.*,
(SELECT CONCAT(reg.`primer_nombre`,' ',reg.`segundo_nombre`,
' ',reg.`primer_apellido`,' ',reg.segundo_apellido) FROM sys_personas AS reg
 WHERE reg.id_nit=cd.id_nit_cliente) AS nombre_cliente 
FROM `caja_cheque_devuelto_detalle` AS cd 	 	
 WHERE `id_caja_cheque_devuelto`='".$cd->cd_id."'
 GROUP BY `id_nit_cliente`,`serie_contrato`,`no_contrato`"; 
 
 
	$rs=mysql_query($SQL);
	while($row=mysql_fetch_assoc($rs)){  
		$nit=System::getInstance()->Encrypt(json_encode($row['id_nit_cliente'])); 
		$contrato=System::getInstance()->Encrypt(json_encode(array("serie_contrato"=>$row['serie_contrato'],"no_contrato"=>$row['no_contrato']))); 		
?>              
                <tr >
                  <th><?php echo $row['nombre_cliente']?></th>
                  <th><?php echo $row['serie_contrato']." ".$row['no_contrato']?></th>
                  <th><input type="button" id_nit="<?php  echo $nit;?>"  contrato="<?php  echo $contrato;?>" class="_repsonsable_sel btn btn-primary" value="Seleccionar"/></th>
                </tr>              
<?php  } ?>    
              </tbody>
            </table></td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
          </tr>
        </table>
        </form>
      </div>
       
    </div>
  </div>
</div>