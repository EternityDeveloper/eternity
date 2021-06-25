<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}

if (isset($_REQUEST['submit'])){
	$retur=array("mensaje"=>"No se pudo completar la operacion","error"=>true);
	
	
	$edit=0;
	if (isset($_REQUEST['edit'])){
		$edit=$_REQUEST['edit'];
	}
	
	
	$id_gerente=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_gerente'],$protect->getSessionID());
	$id_empleado=json_decode(System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_empleado'],$protect->getSessionID()));
	$id_division=json_decode(System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_division'],$protect->getSessionID()));
	
	if (isset($id_empleado->id_nit) && $id_gerente!=""){
		
		$id_director=$id_gerente."_".$id_division->iddivision;
		
		$SQL="select count(*) total from  sys_directores_division where (sys_personas_id_nit ='". mysql_escape_string($id_empleado->id_nit)."' or iddirectores ='". mysql_escape_string($id_director)."') and status='1' ";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		
		if ($edit=="0"){
			if ($row['total']<=0){
				
				$obj= new ObjectSQL();
				$obj->idgerentes=$id_gerente;
				$obj->sys_personas_id_nit=$id_empleado->id_nit;
				$obj->status=1;
				$obj->iddivision=$id_division->iddivision;
				$obj->iddirectores=$id_director;//$_REQUEST['id_directores'];
				
				$SQL=$obj->getSQL("insert","sys_directores_division");
				mysql_query($SQL);
				
				$retur['mensaje']="Registro insertado correctamente!";
				$retur['error']=false;
				echo json_encode($retur);
			}else{
				$retur['mensaje']="El ID del Director o el empleado ya esta registrado no se puede volver a asignar!";
				$retur['error']=true;
				echo json_encode($retur);	
			}
		}else if ($edit=="1"){ //Actualizando los datos
		
				$id_director=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_director'],$protect->getSessionID());

		
				$obj= new ObjectSQL();
			//	$obj->idgerentes=$id_gerente;
				$obj->sys_personas_id_nit=$id_empleado->id_nit;
				$obj->status=1;
				$obj->iddivision=$id_division->iddivision;
				
				$SQL=$obj->getSQL("update","sys_directores_division"," where iddirectores='". mysql_escape_string($id_director)."'");
				
				mysql_query($SQL);
	 
				$retur['mensaje']="Registro actualizado correctamente! ";
				$retur['error']=false;
				echo json_encode($retur);
		}
	}else{
		echo json_encode($retur);
	}
	exit;
} 

/* en caso de elimniar a alguien */
/*Certifico que no tenga a alguien agregado debajo de el */
if (isset($_REQUEST['remove'])){
	if ($_REQUEST['remove']=="true"){	
		
		$retur=array("mensaje"=>"No se pudo completar la operacion","error"=>true);
		$id_director=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_director'],$protect->getSessionID());
	

		$SQL="SELECT COUNT(iddirectores) AS total FROM `sys_gerentes_grupos` WHERE `iddirectores`='".$id_director."' AND status=1";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		if ($row['total']<=0){
			
			$SQL="UPDATE `sys_directores_division` SET `status`=0 WHERE `iddirectores`='".$id_director."'";
			//$SQL="SELECT COUNT(iddirectores) AS TOTAL FROM `sys_gerentes_grupos` WHERE `iddirectores`='".$id_director."'";
			mysql_query($SQL);
	
			$retur['mensaje']="Registro removido correctamente!";
			$retur['error']=false;
			
		}else{
			$retur['mensaje']="El director division tiene a cargo un gerente grupo de ventas, por tal razon no se puede eliminar!";
			$retur['error']=true;
		}
		
		echo json_encode($retur);
		exit;
	}
}




$edit=0;
$data=array();



if (isset($_REQUEST['edit'])){
	if ($_REQUEST['edit']>0){
		$edit=$_REQUEST['edit'];
	
		$id_gerente=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_gerente'],$protect->getSessionID());
		$id_director=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_director'],$protect->getSessionID());

		if ($id_gerente!="" && $id_director!=''){	
			$SQL="SELECT *,
				sys_personas.id_nit,
				CONCAT(primer_nombre, ' ',segundo_nombre,' ',primer_apellido) AS nombre 
			 FROM `sys_directores_division` 
			LEFT JOIN sys_personas ON (sys_personas.id_nit=sys_directores_division.sys_personas_id_nit)
			WHERE status='1' and idgerentes='". mysql_escape_string($id_gerente) ."' and iddirectores='". mysql_escape_string($id_director) ."'";
			$rs=mysql_query($SQL);
			$data=mysql_fetch_assoc($rs);
 
		}else{
			echo "Error cliente ID fail";
			exit;	
		}
	}
}




?>
<style>
	.fsPage2{
		width:90%
	}
</style>
<form name="form_pantallas" id="form_pantallas" method="post" action="" class="fsForm  fsSingleColumn">
<div class="fsPage fsPage2" style="padding:10px 10px 10px 10px;margin:10px 10px 10px 10px;">
<table width="100%" border="1">

  <tr <?php echo count($data)<=0?' style="display:none"':''?>>
    <td>ID Director:</td>
    <td><?php echo $data['iddirectores'];?></td>
  </tr>
  <tr>
    <td>Divicion:</td>
    <td><label>
      <select name="id_division" id="id_division"  class="required" >
        <option value="">Seleccione</option>
        <?php 

$SQL="SELECT * FROM `sys_divisiones` where status='1'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt(json_encode($row),$protect->getSessionID());
?>
        <option value="<?php echo $encriptID?>" <?php if (count($data)>0){ 
						if ($row['iddivision']==$data['iddivision']){ 
							echo 'selected="selected"';
						}
					}?>><?php echo $row['division']?></option>
        <?php } ?>
        </select>
      </label>
      <input name="submit" type="hidden" id="submit" value="submit" /></td>
  </tr>
  <tr>
    <td>Empleado:</td>
    <td><input name="nombre_empleado" id="nombre_empleado" type="text" class="textfield" style="width:200px;height:18px;" value="<?php 
	if ($edit=="1"){
		echo $data['nombre'];
	}
	
	?>"/>
    <input name="id_empleado" id="id_empleado" type="hidden"  value="<?php echo System::getInstance()->Encrypt(json_encode($data));?>" />
      <input name="id_gerente" type="hidden" id="id_gerente" value="<?php echo $_REQUEST['id_gerente'] ?>" />
      <input name="edit" type="hidden" id="edit" value="<?php echo $edit?>" />
      <input name="id_director" type="hidden" id="id_director" value="<?php echo $_REQUEST['id_director'] ?>" /></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><div class="buttons">   
                      <button type="button" class="positive" id="bt_save">
                        <img src="images/apply2.png" alt=""/> 
                        Guardar</button>
                      <a href="#" id="bt_cancel" class="negative"><img src="images/cross.png" alt=""/> Cancel</a>
                  </div></td>
    </tr>
</table>
</div>
</form>