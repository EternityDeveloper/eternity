<?php
if (!isset($protect)){
	exit;
}

if (!isset($_REQUEST['list_reasing'])){
		echo "Error debes de selecionar un prospecto!";
	exit;
}

?>

<div class="modal fade" id="modal_reasig" tabindex="1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:730px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Estructura comercial</h4>
      </div>
      <div class="modal-body">
 

  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td valign="top">
        <form name="form_prospecto" id="form_prospecto" method="post" action="" class="fsForm  fsSingleColumn">
          <table width="100%" border="1" cellpadding="5"  style="border-spacing:10px;">
            <tr>
              <td colspan="2" align=""><h2>Seleccionar gerente:&nbsp;</h2></td>
            </tr>
            <tr>
              <td width="30%" align="right"><strong>Gerente:</strong></td>
              <td width="70%"><table border="1">
                <tr>
                  <td style="background-color:#CCCCCC;color:#C30;border-radius:2px;padding:2px;display:none" id="pros_new_rs_asesor">&nbsp;</td> 
                  <td  id="prosp_find_asesor"><button type="button" class="greenButton" id="bt_new_find_asesor">Elegir</button></td>
                </tr>
              </table></td>
            </tr>
            <tr >
              <td colspan="2"><h2>Listado de prospectos a reasignar</h2></td>
            </tr>
            <tr>
              <td colspan="2"><table border="0" class="display" id="prospecto_list_fracasado" style="font-size:13px;">
                <thead>
                  <tr>
                    <th>&nbsp;</th>
                    <th>Prospecto</th>
                    <th>Asesor</th>
                    <th>Estatus </th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($_REQUEST['list_reasing'] as $key =>$val){ 
		$prospecto=json_decode(System::getInstance()->Decrypt($val['val']));
?>
                  <tr>
                    <th>&nbsp;</th>
                    <th><?php echo ($prospecto->nombre_completo);?></th>
                    <th><?php echo ($prospecto->nombre_asesor);?></th>
                    <th><?php echo ($prospecto->estatus);?></th>
                  </tr>
                  <?php } ?>
                </tbody>
              </table></td>
            </tr>
            
          </table>
        </form>
      </td>
    </tr>
     
    <tr>
      <td align="center" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td align="center" valign="top"><button type="button" class="greenButton" id="bt_new_asignar">Asignar</button>
        &nbsp;&nbsp;
        <button type="button" class="redButton" id="bt_pro_f_cancel">Cancelar</button>&nbsp;</td>
      </tr>
    <tr>
      <td align="center" valign="top">&nbsp;</td>
    </tr>
  </table>


      </div>
       
    </div>
  </div>
</div>