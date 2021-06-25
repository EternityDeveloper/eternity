<?php
 
if ((!validateField($_REQUEST,"moneda")) && (!validateField($_REQUEST,"type"))
 && (!validateField($_REQUEST,"situacion"))){
	echo "Debe de elegir la moneda";
	exit;
}


$moneda=$_REQUEST['moneda'];
$situacion=$_REQUEST['situacion'];

if (!($situacion=="PRE" || $situacion=="NSD")){
	echo "Debe de elegir la situación";
	exit;
}



$_SQL='';
if ($_REQUEST['type']=='MONTO'){
	$_SQL.=" AND (`monto`>0 OR `monto_ingresado`='S') ";	
}

if ($_REQUEST['type']=='PORCIENTO'){
	$_SQL.=" AND (`porcentaje`>0 OR `ingresado`='S') ";	
}

if ($situacion=="PRE"){
	$_SQL.=" and prenecidad='S' ";
}
if ($situacion=="NSD"){
	$_SQL.=" and necesidad='S' ";
}
?>
<style>
.fsPage{
 margin-bottom:10px;	
}
</style>
<div id="contrato_div" class="fsPage">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:8px;">
    <tr>
      <td><h2>Descuento</h2></td>
    </tr>
    <tr>
      <td><table width="100%" cellpadding="5">
        <tr >
          <td height="30" align="right"><strong>Porducto:&nbsp;</strong></td>
          <td><select name="desc_producto" id="desc_producto" class="form-control required" style="width:250px;">
            <option value="">Seleccione</option>
<?php 

$carrito = new Carrito($protect->getDBLink());
$items=$carrito->getListItem();  
foreach($items as $key => $val){  
	$carrito->setToken($key);
	$prod= $carrito->getProducto();
	$nombre_producto="";
	$producto=array();
	if (count($prod)>0){
 		if (isset($prod->serv_codigo)){
			$nombre_producto=$prod->serv_descripcion;
			$producto=$prod;	
		}else{
			foreach($prod as $rkey => $rval){  
				$producto=$rval;
				$nombre_producto=$rval->nombre_jardin;
			}
		}
		
	}
 ?>
            <option value="<?php echo System::getInstance()->Encrypt(json_encode($producto));?>" ><?php echo $nombre_producto;?></option>
            <?php } ?>
          </select></td>
        </tr>
        <tr>
          <td height="30" align="right"><strong>Tipo descuento:&nbsp;</strong></td>
          <td><select name="tipo_descuento" id="tipo_descuento" class="form-control required"  style="width:250px;">
            <option value="">Seleccione</option>
            <?php 

$SQL="SELECT * FROM descuentos WHERE moneda='".mysql_real_escape_string($moneda)."' ".$_SQL;
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encript=System::getInstance()->Encrypt(json_encode($row));
	unset($row['estatus']);	
?>
            <option value="<?php echo $encript;?>" alt='<?php  echo json_encode($row)?>' ><?php echo $row['descripcion']?></option>
            <?php } ?>
            </select></td>
        </tr>
<?php
if ($_REQUEST['type']=='MONTO'){ 
?> 
        <tr  class="cl_monto" style="display:none" >
          <td height="30" align="right"><strong>Monto:</strong></td>
          <td><span class="finder">
            <input name="monto" type="text" class="textfield textfieldsize" id="monto"  autocomplete="off" maxlength="10" style="width:100px;height:35px;" />
          </span></td>
        </tr>
<?php } ?>           
<?php
if ($_REQUEST['type']=='PORCIENTO'){ 
?>       
        <tr class="cl_porcentaje" style="display:none" >
          <td height="30" align="right"><strong>Porcentaje:</strong></td>
          <td><span class="finder">
            <input name="porcentaje" type="text" class="textfield textfieldsize" id="porcentaje"  autocomplete="off" maxlength="5" style="width:60px;height:25px;"  />
          %</span></td>
        </tr>
<?php } ?>        
        <tr>
          <td height="30">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" align="center"><button type="button" class="greenButton" id="desc_apply" disabled>Aplicar</button>&nbsp;&nbsp;<button type="button" class="redButton" id="cancel_decuento">Cerrar</button></td>
          </tr>
      </table></td>
    </tr>
  </table>

</div>