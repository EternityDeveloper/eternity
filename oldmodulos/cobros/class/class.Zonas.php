<?php
 

class Zonas{
	private static $db_link;
	private $_data;
	private $token;
	private static $instance;
	
	public function __construct($db_link=""){
		if ($db_link!=""){
			self::$db_link=$db_link;
			Zonas::$instance = $this;
		}
	} 
	
	public static function getInstance(){
		 if (!Zonas::$instance instanceof self) {
             Zonas::$instance = new Zonas();
        }
        return Zonas::$instance;
	}
	
	public function getMotorizadosList($search){
		$search=mysql_real_escape_string($search);
		$QUERY="";
		$HAVING="";
		if (isset($search)){
		  if (trim($search)!=""){ 
			$QUERY=" WHERE 
(sys_personas.id_nit LIKE '%".$search."%' 
OR CONCAT(sys_personas.primer_nombre,' ' ,sys_personas.segundo_nombre) LIKE '%".$search."%' 
OR CONCAT(sys_personas.primer_apellido,' ',sys_personas.segundo_apellido) LIKE '%".$search."%' 
OR CONCAT(sys_personas.primer_nombre,' ',sys_personas.primer_apellido) LIKE '%".$search."%' 
OR CONCAT(sys_personas.segundo_nombre,' ',sys_personas.primer_apellido) LIKE '%".$search."%' ) ";
	  
		  }
		}

	
		$SQL="SELECT sys_personas.id_nit,
				CONCAT(sys_personas.primer_nombre,' ',
					sys_personas.segundo_nombre,' ',
					sys_personas.primer_apellido,' ',
					sys_personas.segundo_apellido) AS nombre_completo 
		 FROM sys_personas ";
		  
		$SQL.=$QUERY;
		$rs=mysql_query($SQL);
		$result=array("results"=>array());  
		
		while($row=mysql_fetch_assoc($rs)){	
			$eID=System::getInstance()->Encrypt($row['id_nit']);
			$data=array("id"=>$eID,"text"=>utf8_encode($row['nombre_completo']));
			array_push($result['results'],$data);
		}
 
		
		return $result;
	}
	
	public function add($data){
		$inf=array("valid"=>true,"mensaje"=>"No se puede completar la operacion debido a que no se han completados todos los cambios obligatorios"); 
		$data['polygon']=base64_decode($data['polygon']);
		$data['motorizado']=System::getInstance()->Decrypt($data['motorizado']);
 
		if (!isset($data['polygon'])){
			$inf['mensaje']="Falta definir la zona de cobros (Poligono)!";
			$inf['valid']=false;
		}
		if (!isset($data['oficial_nit'])){
			$inf['mensaje']="Falta definir el oficial!";
			$inf['valid']=false;
		}
		if (!isset($data['motorizado'])){
			$inf['mensaje']="Falta definir el motorizado!";
			$inf['valid']=false;
		}
		if (!isset($data['nombre_zona'])){
			$inf['mensaje']="Falta definir el nombre de la zona!";
			$inf['valid']=false;
		}
		if (!isset($data['codigo_zona'])){
			$inf['mensaje']="Falta definir el codigo de la zona!";
			$inf['valid']=false;
		}	
		if ($this->validateZonaExist($data['codigo_zona'])){
			$inf['mensaje']="La zona existe no se aceptan duplicados!";
			$inf['valid']=false;
		}	
		if ($inf['valid']){	
			
			$obj= new ObjectSQL();	
			$obj->zona_id=$data['codigo_zona'];
			$obj->zdescripcion=$data['nombre_zona'];
			$obj->motorizado=$data['motorizado'];
			$obj->oficial_nit=$data['oficial_nit'];			
			$obj->polygon=$data['polygon']." ";
			$obj->estatus="1";
			$obj->setTable("cobros_zona");
			mysql_query($obj->toSQL("insert")); 
			$inf['mensaje']="Zona agregada!";
		}
		return $inf;
	} 
	
	public function edit($data){
		$inf=array("valid"=>true,"mensaje"=>"No se puede completar la operacion debido a que no se han completados todos los cambios obligatorios"); 
		$data['polygon']=base64_decode($data['polygon']);
		$data['motorizado']=System::getInstance()->Decrypt($data['motorizado']);
		$data['oficial_nit']=System::getInstance()->Decrypt($data['oficial_nit']);
 
		if (!isset($data['polygon'])){
			$inf['mensaje']="Falta definir la zona de cobros (Poligono)!";
			$inf['valid']=false;
		}
		if (!isset($data['oficial_nit'])){
			$inf['mensaje']="Falta definir el oficial!";
			$inf['valid']=false;
		}
		if (!isset($data['motorizado'])){
			$inf['mensaje']="Falta definir el motorizado!";
			$inf['valid']=false;
		}
		if (!isset($data['nombre_zona'])){
			$inf['mensaje']="Falta definir el nombre de la zona!";
			$inf['valid']=false;
		}
		if (!isset($data['codigo_zona'])){
			$inf['mensaje']="Falta definir el codigo de la zona!";
			$inf['valid']=false;
		}	
		if (!$this->validateZonaExist($data['codigo_zona'])){
			$inf['mensaje']="La zona existe no se aceptan duplicados!";
			$inf['valid']=false;
		}	
		if ($inf['valid']){	
			
			$obj= new ObjectSQL();	
			//$obj->zona_id=$data['codigo_zona'];
			$obj->zdescripcion=$data['nombre_zona'];
			$obj->motorizado=$data['motorizado'];
			$obj->oficial_nit=$data['oficial_nit'];			
			$obj->polygon=$data['polygon']." ";
			$obj->estatus="1"; 
			$obj->setTable("cobros_zona");
			mysql_query($obj->toSQL("update"," where zona_id='". mysql_real_escape_string($data['codigo_zona'])."'")); 
			
			
			$inf['mensaje']="Zona editada!";
		}
		return $inf;
	} 	
	
	public function validateZonaExist($codigo_zona){
		$SQL="SELECT COUNT(*) AS tt FROM `cobros_zona` WHERE `zona_id`='".$codigo_zona."' ";
		$rs=mysql_query($SQL); 
		$row=mysql_fetch_assoc($rs);
		return $row['tt'];
	}
	/*OPTIENE LA CANTIDAD DE CONTRATOS EN UN POLIGONO DADO*/
	public function getTotalContratosFromPolygon($polygon){
		$SQL="SELECT count(*) as total FROM `sys_direcciones`
		INNER JOIN `sys_personas` ON (sys_personas.id_nit=sys_direcciones.id_nit)
		INNER JOIN `sys_ciudad` ON (sys_ciudad.`idciudad`=sys_direcciones.idciudad)
		INNER JOIN `sys_provincia` ON (sys_provincia.`idprovincia`=sys_direcciones.idprovincia)
		INNER JOIN `sys_sector` ON (sys_sector.`idsector`=sys_direcciones.idsector)
		WHERE CONTAINS(GEOMETRYFROMTEXT('".$polygon."'),POINT(sys_sector.`longitud`, sys_sector.`latitud`))";
		$rs=mysql_query($SQL); 
		$row=mysql_fetch_assoc($rs);
		return $row['total'];
	}	
	/*OPTIENE LA CANTIDAD DE CONTRATOS EN UN POLIGONO DADO*/
	public function getTotalClientesFromPolygon($polygon){
		$SQL="SELECT count(*) as total FROM `sys_direcciones`
		INNER JOIN `sys_personas` ON (sys_personas.id_nit=sys_direcciones.id_nit)
		INNER JOIN `sys_ciudad` ON (sys_ciudad.`idciudad`=sys_direcciones.idciudad)
		INNER JOIN `sys_provincia` ON (sys_provincia.`idprovincia`=sys_direcciones.idprovincia)
		INNER JOIN `sys_sector` ON (sys_sector.`idsector`=sys_direcciones.idsector)
		WHERE CONTAINS(GEOMETRYFROMTEXT('".$polygon."'),POINT(sys_sector.`longitud`, sys_sector.`latitud`))";
		$rs=mysql_query($SQL); 
		$row=mysql_fetch_assoc($rs); 
		return $row['total'];
	}		
 
	public function getZona(){
		$SQL="SELECT cobros_zona.*,
	CONCAT(sys_personas.primer_nombre,' ',sys_personas.segundo_nombre,' ',sys_personas.primer_apellido,' ',sys_personas.segundo_apellido) AS nombre_motorizado,
	CONCAT(oficial.primer_nombre,' ',oficial.segundo_nombre,' ',oficial.primer_apellido,' ',oficial.segundo_apellido) AS nombre_oficial
	 FROM `cobros_zona` 
LEFT JOIN `sys_personas` ON (`sys_personas`.id_nit=cobros_zona.`motorizado`)
LEFT JOIN `sys_personas` AS  oficial ON (`oficial`.id_nit=cobros_zona.`oficial_nit`)
WHERE cobros_zona.estatus=1 ";
		$rs=mysql_query($SQL); 
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			$id=System::getInstance()->Encrypt(json_encode($row)); 
			$row['encID']=$id;  
			array_push($data,$row);
		}
		return $data;
	}	 	

}

?>