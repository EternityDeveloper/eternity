<?php
if (!isset($protect)){
	exit;
}
if (!isset($_REQUEST['id_direccion'])){
	exit;
}

$direccion=json_decode(System::getInstance()->Decrypt($_REQUEST['id_direccion']));
 
?>
<div class="modal fade" id="maps_view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:1000px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">DIRECCION</h4>
      </div>
      <div class="modal-body">
     <table width="100%" border="0" cellspacing="0" cellpadding="0"  > 
      <tr>
        <td align="left"><div id="map_zona" style="height:400px;"></div></td>
      </tr>
    </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button> 
      </div>
    </div>
  </div>
</div>
<?php

$html=ob_get_contents();
ob_end_clean();
			  
$return =array("html"=>$html,"longitud"=>$direccion->longitud,"latitud"=>$direccion->latitud);
echo json_encode($return);


?> 
 
