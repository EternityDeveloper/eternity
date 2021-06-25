<?php 

if ($_REQUEST['template_servicio']=="1"){

?><style type="text/css">
.fsPage {	margin-bottom:10px;	
}
</style>
<table width="300" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td style="color:#FFF;background:#009900;padding-left:10px;"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><strong>SERVICIO</strong></td>
          <td align="right"><button type="button" class="orangeButton" id="bt_c_add_producto" style="float:right;margin-right:15%;">&nbsp;Agregar&nbsp;</button></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0" class="fsPage"  style="margin-left:0px;"	>
     
      <tr>
        <td id="display_product" style="display:none"></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php } else if ($_REQUEST['template_servicio']=="2") { 
	/*VALIDO QUE EL OBJETO CARRITO ESTE CREADO */
	if (!isset($carrito)){
		echo "Error debe crear un objecto carrito!";
		exit;
	}
	

	$carrito->setToken($_REQUEST['token']);
	if (isset($_REQUEST['product_add'])){
		if ((!validateField($_REQUEST,"producto")) && (!validateField($_REQUEST,"token"))){
			echo "Debe de seleccionar un producto";
			exit;
		} 
		
		$obj= json_decode(base64_decode($_REQUEST['producto']));
		
		if (!isset($obj->servicio_id)){
			echo "Debe de seleccionar un producto valido";
			exit;
		}
	 
		$producto=json_decode(System::getInstance()->Decrypt($obj->servicio_id)); 
		
		
 
		if (!isset($obj->cantidad)){
			$producto->cantidad=1;
		}else{
			$producto->cantidad=$obj->cantidad;	
		}  
		$carrito->addProducto($producto);
	 
	} 
	
	$obj=$carrito->getProducto(); 
 
 	 $data=array(
		"codigo"=>$obj->serv_codigo,
		"descripcion"=>$obj->serv_descripcion,
		"servicio_id"=>System::getInstance()->Encrypt(json_encode($obj)),
		"cantidad"=>$producto->cantidad
	  );
?>
<table width="300" border="0" align="center" cellpadding="0" cellspacing="0" class="detalle_costo" style="background:#FFF;">
 
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:8px;">
      <tr >
        <td width="38%"><strong>PLAN</strong></td>
        <td width="62%"><?php echo $obj->serv_descripcion?></td>
      </tr>
      <tr >
        <td><strong>CODIGO</strong></td>
        <td ><?php echo $obj->serv_codigo?></td>
      </tr>
      <tr >
        <td><strong>CANTIDAD</strong></td>
        <td ><input name="servicio_field_cantidad" type="text" id="servicio_field_cantidad" item_data="<?php echo base64_encode(json_encode($data));   ?>" value="<?php echo $obj->cantidad?>" /></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php } ?>