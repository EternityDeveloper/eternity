<?php
if (!isset($protect)){exit;}

if ((validateField($_REQUEST,"descuento"))){
	$descuento=json_decode(base64_decode($_REQUEST['descuento']));
		 
	if (validateField($descuento,"descuento_id")){ 
 		$desc=$carrito->addDescuento($descuento); 
	 //	echo json_encode($rt); 
	}
}  
	
/*Remover un descuento aplicado*/
if ((validateField($_REQUEST,"process_descuento_remove"))){
	if ((validateField($_REQUEST,"index"))){ 
		$carrito->removeDescuento($_REQUEST['index']);  
	}	
}

$type=$_REQUEST['type'];
 
?><table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php 
	$obj=$carrito->getDescuento();  
 
	foreach($obj as $key=>$desc){
		 
		if ($desc->type==$type){
?>
  <tr>
    <td><?php echo $desc->descripcion?></td>
    <td><?php if ($desc->type=="MONTO"){echo number_format($desc->monto,2);}else{ echo $desc->porcentaje."%";}?><a href="#down" id="<?php echo $key?>" class="bt_remove_monto"><img src="images/cross.png" width="16" height="16"></a></td>
  </tr>
<?php 
		}	
	}
?>
</table>
