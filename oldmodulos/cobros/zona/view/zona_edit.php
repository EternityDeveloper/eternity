<?php
if (!isset($protect)){
	exit;
}
if (!isset($_REQUEST['zona_id'])){
	echo "Debe de seleccionar una zona!";
	exit;	
}
$zona=json_decode(System::getInstance()->Decrypt($_REQUEST['zona_id']));

if (!isset($zona->zona_id)){
	echo "Debe de seleccionar una zona!";
	exit;	
}
 
?><table width="100%" border="0" cellspacing="0" cellpadding="0" class="fsPage fsDivPage">
  <tr>
        <td align="left">&nbsp;</td>
  </tr>
  <tr>
    <td align="center"><table width="500" border="1" cellpadding="5"  style="border-spacing:8px;">
      <tr >
        <td align="right"><strong>CODIGO ZONA:</strong></td>
        <td><input  name="txt_cod_zona" type="text" class="required" id="txt_cod_zona" disabled value="<?php echo $zona->zona_id?>" /></td>
      </tr>
      <tr >
        <td align="right"><strong>ZONA:</strong></td>
        <td><input  name="txt_zona" type="text" class="required" id="txt_zona" value="<?php echo $zona->zdescripcion?>" /></td>
      </tr>
      <tr >
        <td align="right" ><strong>OFICIAL:</strong></td>
        <td><div id="dt_oficial" style="cursor:pointer">&nbsp;<?php echo $zona->nombre_oficial; ?></div>
        <div id="item_oficial" style="display:none">
          <input  name="txt_oficial" type="hidden" class="required" id="txt_oficial" style="width:300px;" />
          </div></td>
      </tr>
      <tr >
        <td align="right" ><strong>MOTORIZADO:</strong></td>
        <td><div id="dt_motorizado" style="cursor:pointer"><?php echo $zona->nombre_motorizado; ?></div>
        <div id="item_motorizado" style="display:none">
          <input  name="txt_motorizado" type="hidden" class="required" id="txt_motorizado" style="width:300px;" />
          </div></td>
      </tr>
      <tr >
        <td colspan="2" align="center" >&nbsp;</td>
      </tr>
      <tr >
        <td colspan="2" align="center" ><input type="submit" name="btz_create" id="btz_create" class="btn btn-primary bt-sm" value="GUARDAR" /></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="68%"><strong>IDENTIFICAR ZONA EN EL MAPA</strong></td>
        <td width="8%"><strong>CLIENTES:</strong></td>
        <td width="8%" ><span id="z_clientes" class="badge alert-danger">0</span></td>
        <td width="10%"><strong>CONTRATOS:</strong></td>
        <td width="6%" ><span  id="z_contratos"class="badge alert-danger">0</span></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="left"><div id="map_zona" style="height:400px;"></div></td>
  </tr>
</table
><?php

$html=ob_get_contents();
ob_end_clean();
			 
			
			$zo= new Zonas($protect->getDBLink());   
			
			$return =array("html"=>$html,
			  "motorizado"=>System::getInstance()->Encrypt($zona->motorizado),
			  "oficial_nit"=>System::getInstance()->Encrypt($zona->oficial_nit),
			  "polygon"=>base64_encode($zona->polygon),
			  "zona_id"=>$zona->zona_id,
			  "zdescripcion"=>$zona->zdescripcion,
			  "nombre_motorizado"=>$zona->nombre_motorizado,
			  "nombre_oficial"=>$zona->nombre_oficial,
			  "total_contratos"=>$zo->getTotalContratosFromPolygon($zona->polygon),
			  "total_clientes"=>$zo->getTotalClientesFromPolygon($zona->polygon)
		   );
echo json_encode($return);


?> 
