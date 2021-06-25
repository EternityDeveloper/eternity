<?php 

if ($_REQUEST['template_producto']=="1"){

?><style type="text/css">
.fsPage {	margin-bottom:10px;	
}
</style>
<table width="300" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td style="color:#FFF;background:#009900;padding-left:10px;"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><strong>PRODUCTO</strong></td>
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
<?php } else if ($_REQUEST['template_producto']=="2") { 
	/*VALIDO QUE EL OBJETO CARRITO ESTE CREADO */
	if (!isset($carrito)){
		echo "Error debe crear un objecto carrito!";
		exit;
	} 
	$carrito->setToken($_REQUEST['token']);
	$productos=$carrito->getProducto(); 
	$total=0;
 	$obj= new ObjectSQL();
	foreach($productos as $key =>$val){
		$obj->jardin=$val->id_jardin;
		$obj->fase=$val->id_fases;
		$obj->bloque=$val->bloque;
		$obj->lote=$val->lote; 
		$obj->nombre_jardin=$val->nombre_jardin;
		$total++;  
	} 
	$obj->total=$total; 
	
	//print_r($productos);
 
?>
<table width="300" border="0" align="center" cellpadding="0" cellspacing="0" class="detalle_costo" style="background:#FFF;">
 
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:8px;">
      <tr >
        <td><strong>JARDIN</strong></td>
        <td><?php echo $obj->nombre_jardin?></td>
      </tr>
      <tr >
        <td><strong>FASE</strong></td>
        <td ><?php echo $obj->fase?></td>
      </tr>
      <tr style="">
        <td width="38%" style=""><strong>BLOQUE</strong></td>
        <td width="62%" ><?php echo $obj->bloque?></td>
      </tr>
      <tr >
        <td ><strong>CANTIDAD</strong></td>
        <td><?php echo $obj->total?></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php } ?>