<?php
/*
	Maneja la parte de descuentos
*/

class Descuentos{
	private $data="";
	private $db_link;
	public function __construct($db_link,$data=null){
		$this->data=$data;
		$this->db_link=$db_link;
	}
	
	public function addDescuento($_data){
		$form=array();
		$error=true;
		foreach($_data['form_data'] as $key =>$val){
			$form[$val['name']]=$val['value'];
		}
		if (!$this->existCodigo($form['codigo'])){
			$obj=new ObjectSQL();
			/*
			if (trim($form['tipo_monto'])=="1"){
				$form['monto']=$form['money'];
				$error=false;
				$obj->monto_ingresado="S";
				unset($form['money']);
			}else{
				$obj->monto_ingresado="N";
			}
			
			if (trim($form['tipo_monto'])=="2"){
				$form['porcentaje']=$form['money'];
				$error=false;
				$obj->ingresado="S";
				unset($form['money']);
			}else{
				$obj->ingresado="N";
			}*/
			$negocios="";
			if ((trim($form['desde'])!="")&& (trim($form['hasta'])!="")){
			   $negocios=$form['desde']."-".$form['hasta']; 
			} 
			
			unset($form['desde']);
			unset($form['hasta']);
			
			unset($form['tipo_monto']);
			
			$obj->negocios=$negocios;
			$obj->necesidad=$form['necesidad']=="S"?"S":'N';
			$obj->prenecidad=$form['prenecidad']=="S"?"S":'N';
						
			$obj->push($form);
			$SQL=$obj->getSQL("insert","descuentos");
			mysql_query($SQL);
			
			
			return array("mensaje"=> "Registro agregado",
									"error"=>false );	
		}else{
			return array("mensaje"=> "Error el sistema no admite registros duplicados",
									"error"=>true );		
		}
								
	}
	
	public function editDescuento($_data){
		$form=array();
		$return=array("mensaje"=> "Registro actualizado",
									"error"=>false );
		
		foreach($_data['form_data'] as $key =>$val){
			$form[$val['name']]=$val['value'];
		}
		$descuento=json_decode(System::getInstance()->Decrypt($form['id']));
		$estatus=System::getInstance()->Decrypt($form['estado']);
		if (isset($descuento->codigo)){
			
			
			$obj=new ObjectSQL();
			
			$negocios="";
			if ((trim($form['desde'])!="")&& (trim($form['hasta'])!="")){
			   $negocios=$form['desde']."-".$form['hasta']; 
			} 
	 		
			
			$obj->monto=$form['monto'];
			$obj->porcentaje=$form['porcentaje'];
			$obj->negocios=$negocios;
 		
			$obj->descripcion=$form['descripcion'];
			$obj->ingresado=$form['ingresado'];
			$obj->monto_ingresado=$form['monto_ingresado'];
		//	$obj->moneda=$form['moneda'];
			$obj->prioridad=$form['prioridad'];
			$obj->autorizacion=$form['autorizacion'];
			$obj->necesidad=$form['necesidad']=="S"?"S":'N';
			$obj->prenecidad=$form['prenecidad']=="S"?"S":'N';
			$obj->estatus=$estatus;
			$SQL=$obj->getSQL("update","descuentos","where codigo='".mysql_escape_string($descuento->codigo)."'");
	//		echo $SQL;
			mysql_query($SQL);
			 
									
		}else{
			$return['error']=true;
			$return['mensaje']="Error al tratar de actualizar los datos";
		}
			
			
		return $return;					
								
	}
	/*Retorna el listado de precios */
	public function getListadoDescuento($search=""){
		$QUERY="";
		
		if (trim($search)!=""){
			$QUERY=" AND (
				codigo like '%".$search."%' OR
				descripcion like '%".$search."%' OR
				monto like '%".$search."%' OR
				porcentaje like '%".$search."%' OR
				ingresado like '%".$search."%' OR
				monto_ingresado like '%".$search."%' OR
				autorizacion like '%".$search."%' OR
				moneda like '%".$search."%' OR
				negocios like '%".$search."%' OR
				prioridad like '%".$search."%'
			) ";	
		}
 
 
		$SQL="SELECT count(*) as total  FROM `descuentos` WHERE estatus=1  ";
 		$SQL.=$QUERY;
		
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		$total_row=$row['total'];
		
		$SQL="SELECT * FROM  `descuentos` WHERE estatus=1 ";
		
		$SQL.=$QUERY;
	  
		$SQL.=" limit ".$_REQUEST['iDisplayStart'].",".$_REQUEST['iDisplayLength']."";

		
		$rs=mysql_query($SQL);
		$result=array();
		$data=array(
			'sEcho'=>$_REQUEST['sEcho'],
			'iTotalRecords'=>10,
			'iTotalDisplayRecords'=>$total_row,
			'aaData' =>array()
		);
		
		while($row=mysql_fetch_assoc($rs)){	
			$encriptID=System::getInstance()->Encrypt(json_encode($row));

			$row['bt_editar']='<a href="#" class="desc_edit_plan" id="'.$encriptID.'"><img src="images/subtract_from_cart.png"  /></a>';
			array_push($data['aaData'],$row);
		}	
	
		
		return $data;	
	}
	
	/*Valida el codigo de descueto si existe */
	public function existCodigo($codigo){
		$SQL="SELECT count(*) as total  FROM `descuentos` WHERE codigo ='".$codigo."'  ";		
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		if ($row['total']>0){
			return true;
		}else{
			return false;	
		}
	}
	
}

?>