<?php 
if (!isset($protect)){
	exit;	
}  

 
?>
<div class="modal fade" id="modal_listado_cheque" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:930px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="title_template">LISTADO DE CHEQUE</h4>
      </div>
      <div class="modal-body">
 <?php
 /*
 if (!$protect->getIfAccessPageById(180)){ 
	echo "No tiene permiso para realizar esta operacion!";
	exit;
  } */
 ?>    
 
<table width="100%" border="0" cellspacing="0" cellpadding="0">   

  <tr>
    <td  >&nbsp;</td>
  </tr>                   
  <tr>
    <td >
    <table border="0" class="table table-bordered table-striped table-hover" id="tb_cheques_devueltos" >
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Contrato</th>
          <th>Documento </th>
          <th>Autorizacion</th>
          <th>Monto</th>
          <th>Banco </th> 
          <th></th> 
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table></td>
  </tr>
  <tr>
    <td align="center">&nbsp;</td>
  </tr>
</table>
  
      </div>
       
    </div>
  </div>
</div>