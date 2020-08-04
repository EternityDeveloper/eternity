<?php 
if (!isset($protect)){
	exit;	
}
if (!isset($_REQUEST['id'])){
	exit;	
}
$ct=trim(System::getInstance()->Decrypt($_REQUEST['id']));
 
SystemHtml::getInstance()->includeClass("client","PersonalData");

$person= new PersonalData($protect->getDBLink());	 
$detalle=$person->getClientData($ct);

$SQL="SELECT CONCAT(contratos.serie_contrato,' ',contratos.no_contrato) AS contrato,
		contratos.serie_contrato,contratos.no_contrato,id_nit_cliente
  FROM `contratos` WHERE id_nit_cliente='".$ct."'";
$rs=mysql_query($SQL);
$numero_c=mysql_num_rows($rs);
 
 
//print_r($detalle['numero_de_archivo']); 
 
$total_escanneado=true; 

 
?>
<div class="modal fade" id="view_modal_asignar_cliente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">ASIGNACION DE CORRELATIVO</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="120"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="detalle_contrato_asignacion"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <?php if (($detalle['numero_de_archivo']>0)){?> 
                    <tr>
                      <td><strong>NO. ARCHIVO:</strong></td>
                      <td  style="color:red;"><strong><?php echo $detalle['numero_de_archivo']?></strong></td>
                    </tr>
                   <?php } ?> 
                    <tr>
                      <td><strong>CEDULA:</strong></td>
                      <td width="458"><?php echo $detalle['id_nit'];?></td>
                    </tr>
                    <tr>
                      <td width="142"><strong>CLIENTE:</strong></td>
                      <td><?php echo $detalle['nombre_completo'];?></td>
                    </tr>
                    <tr class="table">
                      <td align="left"><strong>NO. CONTRATOS:</strong></td>
                      <td><?php echo $numero_c;?></td>
                    </tr>
                    <tr class="table">
                      <td align="left" valign="top"><strong>CONTRATOS</strong></td>
                      <td valign="top">
                       <table width="100%" border="0" cellspacing="0" cellpadding="0">
                       <?php 
					  	while($row=mysql_fetch_assoc($rs)){  
							$ct=System::getInstance()->Encrypt(json_encode(
								array(
									"serie_contrato"=>$row['serie_contrato'],
									"no_contrato"=>$row['no_contrato'],
									"id_nit"=>$row['id_nit_cliente']
								)
								));
				 	
						?>  
                          <tr>
                            <td width="100"><a href="./?mod_cobros/delegate&contrato_view&id=<?php echo $ct;?>" target="new"><?php echo $row['contrato'];?></a></td>
                            <td width="50"><?php 
							if (verificarEscanneo($row['serie_contrato'],$row['no_contrato'])<=0){
								$total_escanneado=false;
								echo  '<p  style="color:red">No tiene documentos escaneados!</p>';
							}else{
							?>
                             <img src="images/valid.png" width="16" height="16">
                            <?php	
							}	
							?>
                             </td>
                            <td><a href="#" id="<?php echo $ct;?>" class="print_label"><img src="images/preferences_desktop_printer.png" alt="" width="22" height="26"> Imprimir label</a></td>
                          </tr>
                          <?php } ?>
                        </table></td>
                    </tr>
                    <tr class="table">
                      <td align="left">&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                  </table></td>
                </tr>
                <?php if (($numero_c>0) && ($total_escanneado)&& ($detalle['numero_de_archivo']<=0)){?>
                <tr class="detalle_contrato_asignacion">
                  <td align="center"><button type="button" id="detalle_asignar_cliente" class="btn btn-warning">ASIGNAR NO. ARCHIVO</button></td>
                </tr> 
                <?php }?>
                               
                <tr style="display:none" class="detalle_transapcion" >
                  <td align="center"><h1><strong>NUMERO DE DE ARCHIVO</strong></h1></td>
                </tr>
                <tr style="display:none"  class="detalle_transapcion">
                  <td align="center" style="color:red;" id="_numero_correlativo"><h1>0</h1></td>
                </tr> 
                 <?php if (($detalle['numero_de_archivo']>0)){
					 $ct=System::getInstance()->Encrypt(json_encode(
								array(
									"serie_contrato"=>"",
									"no_contrato"=>"",
									"id_nit"=>$detalle['id_nit']
								)
								));
					 ?>        
                  <tr >
                  <td align="center">
                  	<button type="button" id="imprimir_no_archvio" item="<?php echo $ct; ?>" class="btn btn">IMPRIMIR NO. ARCHIVO</button></td>
                </tr> 
                <?php } ?>      
              </table></td>
            </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
           
          </table>
      </div>
   
    </div>
  </div>
</div>