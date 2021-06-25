<?php



class ManagerRoles {
	private static $instance;
	private $fields = array(
			"id_pantalla"=>0,
			"Id_role"=>0,
			"alta"=>0,
			"baja"=>0,
			"Cambios"=>0,
			"Consulta"=>0,
			"acceso"=>0	
			);
			
	public static function getInstance(){
		 if (!ManagerRoles::$instance instanceof self) {
             ManagerRoles::$instance = new ManagerRoles();
        }
        return ManagerRoles::$instance;
	}
	
	public function updatePermiso($roleID,$pantallaID,$campo,$value){
		 $data=$this->checkRoleExist($roleID,$pantallaID);
		 $retur=array("mensaje"=>"No se pudo completar la operacion","error"=>true);

		 if ($data['exist']){
		 	$obj= new ObjectSQL();
			$info=array($campo=>$value);
			$obj->phush_obj($info);
			$SQL=$obj->getSQL("update","Seguridad"," where id_pantalla='".$pantallaID."' and Id_role='".$roleID."'");
			//echo $SQL;
			mysql_query($SQL);
			$retur['mensaje']="Registro actualizado correctamente!";
			$retur['error']=false;
		 }else{
		 	$this->createNewPermiso($roleID,$pantallaID,$campo,$value);
			$data=$this->checkRoleExist($roleID,$pantallaID);
			if ($data['exist']){
				$retur['mensaje']="Registro actualizado correctamente!";
				$retur['error']=false;
			}
		 }
		 
		 
		 return $retur;
	}
	public function createNewPermiso($roleID,$pantallaID,$campo,$value){
		$obj= new ObjectSQL();
		$this->fields[$campo]=$value;
		$obj->phush_obj($this->fields);
		$obj->id_pantalla=$pantallaID;
		$obj->Id_role=$roleID;
		$SQL=$obj->getSQL("insert","Seguridad");
		mysql_query($SQL);
		$this->fields[$campo]=0;
	}
	public function checkRoleExist($roleID,$pantallaID){
		$ret=array("exist"=>false,"data"=>null);
		$SQL="SELECT * FROM Seguridad where id_pantalla='".$pantallaID."' and Id_role='".$roleID."'";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			$ret['data']=$row;
			$ret['exist']=true;
		}
		
		return $ret;
	}


}


?>