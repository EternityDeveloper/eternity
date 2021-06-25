<?php
if (!isset($protect)){
	exit;
}	
$rand=$_REQUEST['rand'];

 
if (isset($_REQUEST['template_producto'])){ 
	include("template_producto.php"); 
	exit;	
}
if (isset($_REQUEST['template_financiamiento'])){ 
	include("template_fnto.php"); 
	exit;	
}
if (isset($_REQUEST['process_descuento_add'])){ 
	include("template_descuento.php"); 
	exit;	
}
if (isset($_REQUEST['process_descuento_remove'])){ 
	include("template_descuento.php"); 
	exit;	
}

if (isset($_REQUEST['template_servicio'])){ 
	include("template_servicio.php"); 
	exit;	
}

/*ESTE METODO LA UTILIZO PARA LIMPIAR AQUELLAS VARIABLES QUE NO ESTAN EN USO*/
SystemHtml::getInstance()->includeClass("contratos","Carrito"); 
$car= new Carrito($protect->getDBLink());
$car->removeItem();

?>
<style>
.fsPage{
	margin-bottom:10px;	
}
table.display2 {}
</style>
<div id="contrato_div"  >
 
  <table width="100%" border="0" cellpadding="0" cellspacing="0"  >
    <tr>
      <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td colspan="2" valign="top">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" align="center" valign="top" id="mensaje_producto" style="display:none" ></td>
          </tr>
        <tr>
          <td colspan="2" align="center" valign="top"><table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td valign="top" id="template_producto">&nbsp;</td>
              <td width="300" rowspan="2" valign="top" id="template_detalle"><?php include("template_detalle.php");?></td>
            </tr>
            <tr>
              <td width="300" valign="top" id="template_plan">&nbsp;</td>
              </tr>
            </table></td>
          </tr>
        <tr>
          <td width="50%" valign="top">&nbsp;</td>
          <td width="50%" valign="top" >&nbsp;</td>
          </tr>
      </table></td>
    </tr>
 
    <tr>
      <td align="center" valign="top"><button type="button" class="greenButton"  style="display:none" id="bt_c_add_product">&nbsp;Agregar&nbsp;</button>
        <button type="button" class="redButton" id="bt_produc_cancel">Cerrar</button>&nbsp;</td>
      </tr>
    <tr>
      <td align="center" valign="top" id="cuadro_producto" style="display:none"><table width="200" border="0" cellspacing="0" cellpadding="0" class="fsPage detalle_costo" id="bloque_producto_<?php echo $rand;?>" style="width:210px;">
        <tr>
          <td class="titlest">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="80%" id="title_<?php echo $rand;?>">x</td>
                <td><img class="edit_product" id="producto_<?php echo $rand;?>" token="<?php echo $rand;?>" style="cursor:pointer;float:right" src="images/subtract_from_cart.png" /></td>
                <td> &nbsp;</td>
                <td><img class="prt_remove_product" id="producto_remove_<?php echo $rand;?>" token="<?php echo $rand;?>" style="cursor:pointer;float:right" src="images/cross.png" /></td>
                </tr>
            </table></td>
          </tr>
        <tr>
          <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td ><table width="100%" border="0" cellspacing="0" cellpadding="0" >
                <tr>
                  <td width="50%" align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>PLAN:</strong></td>
                  <td width="50%"  id="detalle_plan_<?php echo $rand;?>"  style="padding-bottom:3px; padding-top:10px;padding-left:5px;border-bottom-style:double;">&nbsp;</td>
 
                <tr>
                  <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>PLAZO:</strong></td>
                  <td style="padding-top:10px;padding-left:5px;border-bottom-style:double;" id="detalle_plazo_<?php echo $rand;?>">0</td>
                  </tr>
                <tr style="display:none">
                  <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>CANTIDAD:</strong></td>
                  <td style="padding-top:10px;padding-left:5px;border-bottom-style:double;" id="detalle_cantidad_<?php echo $rand;?>">0</td>
                </tr>
                
                </table></td>
              </tr>
            <tr>
              <td id="detalle_plan_<?php echo $rand;?>2">&nbsp;</td>
              </tr>
            </table></td>
          </tr>
      </table></td>
    </tr>
  </table>
 
</div>