<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}

 
 
if (isset($_REQUEST['getListfase'])){
	if (isset($_REQUEST['jardin'])){ 
		$dencryt=System::getInstance()->getEncrypt()->decrypt($_REQUEST['jardin'],$protect->getSessionID());
		$data=json_decode($dencryt);	
	//	print_r($data);
		?>
        <select name="fase" id="fase">
          <option value="">Seleccione</option>
          <?php 
			$SQL="SELECT * FROM `fases`
			where id_fases not in (SELECT id_fases FROM `jardines_activos` WHERE estatus=1 AND id_jardin='".$data->id_jardin."') ";
	 
			$rs=mysql_query($SQL);
			while($row=mysql_fetch_assoc($rs)){
				$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_fases'],$protect->getSessionID());
        ?>
          <option value="<?php echo $encriptID?>"><?php echo $row['fase']?></option>
        <?php } ?>
        </select>       
        
        <?php
		
		exit;
	}
}


/*
if (isset($_REQUEST['submit'])){
	$dencryt=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_empleado'],$protect->getSessionID());
	$data=json_decode($dencryt);
	
	$group=json_decode(System::getInstance()->getEncrypt()->decrypt($_REQUEST['group_id'],$protect->getSessionID()));

	$tipo_usuario=System::getInstance()->getEncrypt()->decrypt($_REQUEST['tipo_usuario'],$protect->getSessionID());
//	print_r($group->Id_role);
//	print_r($data);
	
	$retur=array("mensaje"=>"No se pudo completar la operacion","error"=>true);
	
	if (($_REQUEST['usuario']!="") && ($_REQUEST['password']!="")){
		
		if ($_REQUEST['password']==$_REQUEST['new_password']){
			$obj= new ObjectSQL();
			$obj->email=$_REQUEST['usuario'];
			$obj->Contrasena=md5($_REQUEST['password']);
			$obj->id_nit=$data->id_nit;
			$obj->Nombres=$data->nombre;
			$obj->id_usuario=$_REQUEST['usuario'];
			$obj->status="1";
			$obj->idtipo_usuario=$tipo_usuario;
			$SQL=$obj->getSQL("insert","Usuarios");
			mysql_query($SQL);
			$id=mysql_insert_id($protect->getDBLink()->link_id);
			
			//if ($id>0){
		 
			$obj= new ObjectSQL();
			$obj->id_role=$group->Id_role;
			$obj->id_usuario=$_REQUEST['usuario'];
			$SQL=$obj->getSQL("insert","usu_role");
			mysql_query($SQL);
		//	}
			
			$retur['mensaje']="Registro actualizado correctamente! ";
			$retur['error']=false;
			echo json_encode($retur);
			exit;
		}else{
			$retur['mensaje']="La contraseña anterior no coincide ";
			$retur['error']=true;	
		}
 
	}else{
		$retur['mensaje']="Usuario o Contraseña son obligatorias";
		$retur['error']=true;	
	}
	

	
	echo json_encode($retur);
	exit;
}
*/


?>
<style>
	.fsPage2{
		width:90%
	}
</style>
<form name="form_user_edit" id="form_user_edit" method="post" action="" class="fsForm  fsSingleColumn">
<div class="fsPage fsPage2" style="padding:10px 10px 10px 10px;margin:10px 10px 10px 10px;">
<table width="100%" border="1">
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td align="right"><strong>Jardin:</strong></td>
    <td><select name="jardin" id="jardin">
      <option value="<?php 
	//$row=array("nombre"=>"","id_nit"=>0);
   // echo System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
			
		?>">Seleccione</option>
      <?php 

$SQL="SELECT * FROM `jardines` ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
?>
      <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['id_nit']==$data['sys_personas_id_nit']){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['jardin']?></option>
      <?php } ?>
      </select></td>
  </tr>
  <tr>
    <td align="right"><strong>Fase:</strong></td>
    <td id="m_fase"><select name="fase" id="fase">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT * FROM `fases` ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_fases'],$protect->getSessionID());
?>
      <option value="<?php echo $encriptID?>"><?php echo $row['fase']?></option>
<?php } ?>
    </select></td>
  </tr>
  <tr>
    <td align="right"><strong>Costo:</strong></td>
    <td>
    <div id="bloques_1" style="display:none">
    <input name="bloque_from" type="text" id="bloque_from" style="width:30px" maxlength="3" /> - 
        <input name="bloque_to" type="text" id="bloque_to" style="width:30px" maxlength="3"  />
   </div>
    <input type="text" name="costo" id="costo"></td>
  </tr>
  <tr>
    <td align="right"><strong>Cta contable:</strong></td>
    <td><input type="text" name="cta_contable" id="cta_contable" /></td>
  </tr>
  <tr>
    <td><strong>Minimo abono a capital:</strong></td>
    <td><select name="minimo_abono_capital" id="minimo_abono_capital" class="valid">
      <option value="NINGUNO">NINGUNO</option>
      <option value="CUOTA">CUOTA</option>
      <option value="MONTO">MONTO</option>
    </select></td>
  </tr>
  <tr id="monto_m_capital" style="display:none">
    <td><strong>Monto minimo abono a capital</strong></td>
    <td><input type="text" name="monto_minimo" id="monto_minimo" /></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><h2>Jardines</h2></td>
    </tr>
  <tr>
    <td align="right"><strong>Lista Precio  Local Necesidad</strong></td>
    <td><select name="precio_venta_local_nec" id="precio_venta_local_nec">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='LOCAL' ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['CODIGO_TP']);
?>
      <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['CODIGO_TP']==$data->precio_venta_local_nec){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['CODIGO_TP']?></option>
      <?php } ?>
      </select></td>
  </tr>
  <tr>
    <td align="right"><strong> Lista Precio Dolares Necesidad</strong></td>
    <td><select name="precio_venta_dolares_nec" id="precio_venta_dolares_nec">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='DOLARES' ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['CODIGO_TP']);
?>
      <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['CODIGO_TP']==$data->precio_venta_local_nec){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['CODIGO_TP']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td align="right"><strong>Lista Precio Local Pre-necesidad</strong></td>
    <td><select name="precio_venta_local_pre" id="precio_venta_local_pre">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='LOCAL' ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['CODIGO_TP']);
?>
      <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['CODIGO_TP']==$data->precio_venta_dolares_nec){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['CODIGO_TP']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td align="right"><strong>Lista Precio  Dolares Pre-necesidad</strong></td>
    <td><select name="precio_venta_dolares_pre" id="precio_venta_dolares_pre">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='DOLARES' ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['CODIGO_TP']);
?>
      <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['CODIGO_TP']==$data->precio_venta_local_pre){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['CODIGO_TP']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><h2>Osario</h2></td>
  </tr>
  <tr>
    <td align="right"><strong>Osario Lista Precio Local Necesidad</strong></td>
    <td><select name="osario_precio_venta_local_nec" id="osario_precio_venta_local_nec">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='LOCAL' ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['CODIGO_TP']);
?>
      <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['CODIGO_TP']==$data->precio_osario_local_nec){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['CODIGO_TP']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td align="right"><strong>Osario Lista Precio Dolar Necesidad</strong></td>
    <td><select name="osario_precio_venta_dolares_nec" id="osario_precio_venta_dolares_nec">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='DOLARES' ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['CODIGO_TP']);
?>
      <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['CODIGO_TP']==$data->precio_venta_dolares_nec){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['CODIGO_TP']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td align="right"><strong>Osario Lista Precio  Local Pre-necesidad</strong></td>
    <td><select name="osario_precio_venta_local_pre" id="osario_precio_venta_local_pre">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='LOCAL' ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['CODIGO_TP']);
?>
      <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['CODIGO_TP']==$data->precio_venta_local_pre){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['CODIGO_TP']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td align="right"><strong>Osario Lista Precio Dolar Pre-necesidad</strong></td>
    <td><select name="osario_precio_venta_dolares_pre" id="osario_precio_venta_dolares_pre">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT `CODIGO_TP` FROM `tabla_precios` where MONEDA_TP='DOLARES' ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['CODIGO_TP']);
?>
      <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['CODIGO_TP']==$data->precio_venta_dolares_pre){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['CODIGO_TP']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><input name="submit_jardin_activo" type="hidden" id="submit_jardin_activo" value="1" /></td>
  </tr>
  <tr>
    <td colspan="2" align="center">   
                      <button type="button" class="positive" id="bt_save">
                        <img src="images/apply2.png" alt=""/> 
                        Guardar</button>
                       <button type="button" class="positive" id="bt_cancel">
                        <img src="images/cross.png" alt=""/> 
                        Cancel</button>  
                  </td>
    </tr>
</table>
</div>
</form>