<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}

 
if (isset($_REQUEST['submit'])){
	$retur=array("mensaje"=>"No se pudo completar la operacion","error"=>true); 
 	$id_empleado=json_decode(System::getInstance()->Decrypt($_REQUEST['id_empleado']));
	$id_director=System::getInstance()->Decrypt($_REQUEST['director_id']);
	$id_gerente_grupo=System::getInstance()->Decrypt($_REQUEST['id_gerente_grupo']); 
  
	$edit=0;
	if (isset($_REQUEST['edit'])){
		$edit=$_REQUEST['edit'];
	}	


// and isset($id_division->iddivision)
	if ((isset($id_empleado->id_nit) and $id_gerente_grupo!="")){
		
		$SQL="select count(*) total from  sys_asesor where 
			(id_nit ='". mysql_escape_string($id_empleado->id_nit)."' ) and status='1' ";
	 //or codigo_gerente_grupo ='". mysql_escape_string($id_gerente_grupo)."'

		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs); 
		if ($edit=="0"){
			if ($row['total']<=0){
				
				$obj= new ObjectSQL(); 
				$obj->status=1;
				$obj->codigo_gerente_grupo=$id_gerente_grupo;
				$obj->codigo_director=$id_director;
				$obj->id_nit=$id_empleado->id_nit;
                                $obj->sys_gerentes_grupos_idgrupos=" ";
                                $obj->iddirectores=" ";
                                $obj->idgerentes=" ";
                                $obj->idgrupos=" ";
                                $obj->idgerente_grupo=" ";  
				$SQL=$obj->getSQL("insert","sys_asesor");  
			 	$prueba = mysql_query($SQL);
                                if (!$prueba) {
                                   $retur["mensaje"]="error asesor".mysql_error()."sql".$SQL;
                                   $retur['error']=true;
                                   echo json_encode($retur);
                                }
				
				$retur['mensaje']="Registro insertado correctamente!";
				$retur['error']=false;
				echo json_encode($retur);
			}else{
				$retur['mensaje']="El ID del Asesor o el empleado ya esta registrado no se puede volver a asignar!".$row['total'];
				$retur['error']=true;
				echo json_encode($retur);	
			}
		}else if ($edit=="1"){
				$obj= new ObjectSQL();	
				$obj->id_nit=$id_empleado->id_nit;
				
				$SQL=$obj->getSQL("update","sys_asesor"," where sys_gerentes_grupos_idgrupos='". mysql_escape_string($_REQUEST['id_asesor']) ."'");	
				mysql_query($SQL);
				
				$retur['mensaje']="Registro actualizado correctamente!";
				$retur['error']=false;
				echo json_encode($retur);
		}else if ($edit=="3"){
				$obj= new ObjectSQL();	
				$obj->status=2;
				
				$SQL=$obj->getSQL("update","sys_asesor"," where sys_gerentes_grupos_idgrupos='". mysql_escape_string($_REQUEST['id_asesor']) ."'");	
				mysql_query($SQL);
				
				$retur['mensaje']="Registro actualizado correctamente!";
				$retur['error']=false;
				echo json_encode($retur);
		}
	}else{
		echo json_encode($retur);
	}
	exit;
} 
 
/* en caso de elimniar a alguien */
if (isset($_REQUEST['remove'])){
	if ($_REQUEST['remove']=="true"){	
		
		$retur=array("mensaje"=>"No se pudo completar la operacion","error"=>true);
		$id_asesor=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_asesor'],$protect->getSessionID());

		/*Certifico que no tenga a alguien agregado debajo de el */
		$SQL="SELECT COUNT(idgerente_grupo) AS total FROM `sys_asesor` WHERE `sys_gerentes_grupos_idgrupos`='".$id_asesor."' AND status=1";

		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		if ($row['total']>0){
			
			$SQL="UPDATE `sys_asesor` SET `status`=0 WHERE `sys_gerentes_grupos_idgrupos`='".$id_asesor."'";
			mysql_query($SQL);
		
			$retur['mensaje']="Registro removido correctamente!";
			$retur['error']=true;
			
		}else{
			$retur['mensaje']="Este asesor no existe!";
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
		$id_asesor=System::getInstance()->Decrypt($_REQUEST['id']);
		if ($id_gerente!="" && $id_director!=''){	
			$SQL=" SELECT *,
				sys_personas.id_nit,
				CONCAT(primer_nombre, ' ',segundo_nombre,' ',primer_apellido) AS nombre 
			 FROM `sys_asesor` 
			LEFT JOIN sys_personas ON (sys_personas.id_nit=sys_asesor.id_nit)
			WHERE status='1' and sys_gerentes_grupos_idgrupos='". mysql_real_escape_string($id_asesor) ."'";
			 
			$rs=mysql_query($SQL);
			$data=mysql_fetch_assoc($rs);
 		 
		}else{
			echo "Error cliente ID fail";
			exit;	
		}
	}
}




?>
<div class="modal fade" id="modal_agregar_asesor" tabindex="1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:430px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">AGREGAR ASESOR</h4>
      </div>
      <div class="modal-body">
<form name="form_pantallas" id="form_pantallas" method="post" action="" class="fsForm  fsSingleColumn">
 <table width="100%" border="1">
  <tr>
    <td>Empleado:</td>
    <td><input name="nombre_empleado" id="nombre_empleado" type="text" class="form-control" value="<?php 
	if ($edit=="1"){
		echo $data['nombre'];
	}
	
	?>"/>
      <input name="id_empleado" id="id_empleado" type="hidden"  value="<?php echo System::getInstance()->Encrypt(json_encode($data));?>" />
      <input name="id_gerente" type="hidden" id="id_gerente" value="<?php echo $_REQUEST['id_gerente'] ?>" />
      <input name="director_id" type="hidden" id="director_id" value="<?php echo $_REQUEST['director_id'] ?>" />
      <input name="id_gerente_grupo" type="hidden" id="id_gerente_grupo" value="<?php echo $_REQUEST['gerente_grupo'] ?>" />
      <input name="edit" type="hidden" id="edit" value="<?php echo $edit?>" />
      <input name="id_asesor" type="hidden" id="id_asesor" value="<?php echo $data['sys_gerentes_grupos_idgrupos']?>" /></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><div class="buttons">   
                      <button type="button" class="positive" id="bt_save">
                        <img src="images/apply2.png" alt=""/> 
                        Guardar</button>
                      <a href="#" id="bt_remove" class="negative" <?php echo $edit=="0"?' style="display:none"':''?>><img src="images/draft.png" alt=""/>Elminar puesto</a>
                      <a href="#" id="bt_cancel" class="negative"><img src="images/cross.png" alt=""/> Cancel</a>
                  </div></td>
    </tr>
</table>
 
</form>
      </div>
       
    </div>
  </div>
</div>
