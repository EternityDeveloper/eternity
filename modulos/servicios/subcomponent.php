<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
 
	/* AGREGAR UN COMPONENTE NUEVO */
	if (isset($_REQUEST['submit_subcomponent_add'])){
		if ($_REQUEST['submit_subcomponent_add']=="1"){
			
			$obj= new ObjectSQL();
			$obj->push($_POST);
			
			$retur=array("mensaje"=>"No se pudo completar la operacion!","error"=>true,"id"=>$obj->sub_subcomponente);
			
			$SQL="SELECT count(*) as total FROM `subcomponentes` WHERE `id_componente`='".$obj->id_componente."' and sub_subcomponente='".$obj->sub_subcomponente."'";
			$rs=mysql_query($SQL);
			$row=mysql_fetch_assoc($rs);
			
			if ($row['total']==0){
				/*ELIMINO LOS OBJECTOS QUE NO NECESITO*/
				unset($obj->submit_subcomponent_add);
				unset($obj->PHPSESSID); 
				$SQL=$obj->getSQL("insert","subcomponentes");
			//	print_r($obj);
				mysql_query($SQL);
			
				$retur['mensaje']="Registro agregado correctamente!";
				$retur['error']=false;
				$retur['id']=$obj->sub_subcomponente;	
				//$retur['id_component']=System::getInstance()->Encript($obj->sub_subcomponente);	
				
			}else{
				$retur['mensaje']="Error el componente existe!";
				$retur['error']=true;
				$retur['id']=0;	
			}
			
			
			//array("mensaje"=>,"error"=>false,"id"=>$obj->id_componente);
			echo json_encode($retur);
			exit;
		}
	}
 
 
 
	/* AGREGAR UNA IMAGEN A UN COMPONENTE */
	if (isset($_REQUEST['submit_subcomponent_upload_image'])){
		if ($_REQUEST['submit_subcomponent_upload_image']=="1"){
			SystemHtml::getInstance()->includeClass("servicios","subComponent");
	 
			$comp= new subComponent($protect->getDBLink());
			$retur=$comp->uploadImage("imagen_upload_component",$_REQUEST['id']);
		//	$retur=array("mensaje"=>"Registro agregado correctamente!","error"=>false);
			echo json_encode($retur);
			exit;
		}
	} 

	/* AGREGAR UN COMPONENTE NUEVO */
	if (isset($_REQUEST['submit_subcomponent_edit'])){
		if ($_REQUEST['submit_subcomponent_edit']=="1"){
			
			$obj= new ObjectSQL();
			$obj->push($_POST);
			
			$retur=array("mensaje"=>"No se pudo completar la operacion!","error"=>true,"id"=>$obj->sub_subcomponente);
			
			$SQL="SELECT count(*) as total FROM `subcomponentes` WHERE `sub_subcomponente`='".$obj->sub_subcomponente."'";
			$rs=mysql_query($SQL);
			$row=mysql_fetch_assoc($rs);
			
			if ($row['total']==1){
				$id_component=$obj->id_component;
				/*ELIMINO LOS OBJECTOS QUE NO NECESITO*/
				unset($obj->submit_subcomponent_edit);
				unset($obj->PHPSESSID); 
				unset($obj->id_component);
				$SQL=$obj->getSQL("update","subcomponentes"," where sub_subcomponente='". mysql_escape_string($obj->sub_subcomponente) ."'");
			//	print_r($obj);
				mysql_query($SQL);
			
				$retur['mensaje']="Registro actualizado correctamente!";
				$retur['error']=false;
				$retur['id']=$obj->sub_subcomponente;	
				$retur['id_component']=$id_component;		
				
			}else{
				$retur['mensaje']="Error el componente existe!";
				$retur['error']=true;
				$retur['id']=0;	
			}
			 
			//array("mensaje"=>,"error"=>false,"id"=>$obj->id_componente);
			echo json_encode($retur);
			exit;
		}
	}
?>