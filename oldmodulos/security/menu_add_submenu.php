<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}

if (isset($_REQUEST['submit'])){
	$retur=array("mensaje"=>"No se pudo completar la operacion","error"=>true);
	/* desencripto los datos de la patalla que seleccione en el formulario*/
	$dencryt=System::getInstance()->getEncrypt()->decrypt($_REQUEST['Pantalla'],$protect->getSessionID());
	$data=json_decode($dencryt);
	/* desencripto los datos  del Main menu que seleccione*/
	$dencryt=System::getInstance()->getEncrypt()->decrypt($_REQUEST['main_menu'],$protect->getSessionID());
	$main_menu=json_decode($dencryt);
	if (isset($data->id_pantalla) && isset($main_menu->Id_clas_pantallas)){
	//	print_r($data);
	//	print_r($main_menu);
		$obj= new ObjectSQL();
		$obj->Pantallas_id_pantalla=$data->id_pantalla;
		$obj->tipo_menu="Submenu";
		$obj->hijo_de_id_clas_pantallas=$main_menu->Id_clas_pantallas;
		$obj->order_=$_REQUEST['orden'];
		$obj->nombre=$_REQUEST['nombre'];
		$SQL=$obj->getSQL("insert","clasificacion_pantallas");
	//	print_r($SQL);	
		mysql_query($SQL);
		$id=mysql_insert_id($protect->getDBLink()->link_id);
		if ($id>0){
			$retur['mensaje']="Registro insertado correctamente!";
			$retur['error']=false;
			
			$obj= new ObjectSQL();
			$obj->posicion_jeralquia=$main_menu->posicion_jeralquia.$id."_";
			$SQL=$obj->getSQL("update","clasificacion_pantallas"," where Id_clas_pantallas='".$id."'");
	//		print_r($SQL);	
			mysql_query($SQL);
		
			echo json_encode($retur);
		}else{
			echo json_encode($retur);
		} 
	}else{
		echo json_encode($retur);
	}
	exit;
} 





?>
<style>
	.fsPage2{
		width:90%
	}
</style>
<form name="form_pantallas" method="post" action="" class="fsForm  fsSingleColumn">
<div class="fsPage fsPage2" style="padding:10px 10px 10px 10px;margin:10px 10px 10px 10px;">
<table width="100%" border="1">
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td>Nombre:</td>
    <td><label>
      <input name="nombre" type="text" id="nombre" />
      </label>    </td>
  </tr>
  <tr>
    <td>Posicion:</td>
    <td><input name="orden" type="text" id="orden" /></td>
  </tr>
  <tr>
    <td>Pantalla:</td>
    <td><label>
    <select name="Pantalla" id="Pantalla">
	  <option value="-1">Seleccione</option>
<?php 

$SQL="SELECT * FROM Pantallas ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
?>	  
      <option value="<?php echo $encriptID?>"><?php echo $row['Pantalla']?></option>
<?php } ?>	  
    </select>
    </label>    
    <input name="submit" type="hidden" id="submit" value="submit" /></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><div class="buttons">   
                      <button type="button" class="positive" id="bt_save">
                        <img src="images/apply2.png" alt=""/> 
                        Guardar</button>
                      <a href="#" onClick="_reload()" class="negative"><img src="images/cross.png" alt=""/> Cancel</a>
                  </div></td>
    </tr>
</table>
</div>
</form>