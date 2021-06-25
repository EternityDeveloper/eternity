<?php
 

class Servicios{
	private static $db_link;
	private $_data;
	private $token;
	private static $instance;
	
	public function __construct($db_link=""){
		if ($db_link!=""){
			self::$db_link=$db_link;
			Servicios::$instance = $this;
		}
	}  
	public static function getInstance(){
		 if (!Servicios::$instance instanceof self) {
             Servicios::$instance = new Servicios();
        }
        return Servicios::$instance;
	} 
	public static function GI(){
		return Servicios::getInstance();	
	}	
	public function getCementerio($search){
		$search=mysql_real_escape_string($search);
		$QUERY="";
		$HAVING="";
		if (isset($search)){
		  if (trim($search)!=""){ 
			$QUERY=" WHERE  (sp_cementerios.cementerio LIKE '%".$search."%'   ) "; 
		  }
		}

	
		$SQL="SELECT * FROM `sp_cementerios` ";
		  
		$SQL.=$QUERY;
		$rs=mysql_query($SQL);
		$result=array("results"=>array());  
		
		while($row=mysql_fetch_assoc($rs)){	
			$idcementerios=System::getInstance()->Encrypt($row['idcementerios']);
			$data=array("id"=>$idcementerios,"text"=>$row['cementerio']);
			array_push($result['results'],$data);
		}
 
		
		return $result;
	}
	
	public function getFuneraria($search,$id=0){
		$search=mysql_real_escape_string($search);
		$QUERY="";
		$HAVING="";
		if (isset($search)){
		  if (trim($search)!=""){ 
			$QUERY=" WHERE  (sp_funerarias.funeraria LIKE '%".$search."%'   ) "; 
		  }
		} 
		if ($id>0){
			$QUERY=" WHERE  (sp_funerarias.idfunerarias='".$id."') "; 
		}
		$SQL="SELECT * FROM `sp_funerarias` "; 
		$SQL.=$QUERY;
		$rs=mysql_query($SQL);
		$result=array("results"=>array());  
		
		while($row=mysql_fetch_assoc($rs)){	
			$idfunerarias=System::getInstance()->Encrypt($row['idfunerarias']);
			$data=array("id"=>$idfunerarias,"text"=>$row['funeraria']);
			array_push($result['results'],$data);
		}
 
		
		return $result;
	}	
	
	public function clearServicioSession(){
		$_SESSION['FUNC_SERVICIO']=array(); 
	}
	public function saveServicioToSession($_data){ 
		$return=array("valid"=>false,"mensaje"=>"Datos invalido");
		$id_init=System::getInstance()->Decrypt($_data['id_nit']);
		$ciudad=System::getInstance()->Decrypt($_data['ciudad']); 
		$preparado_por=System::getInstance()->Decrypt($_data['preparado_por']);
		$atendido_por=(System::getInstance()->Decrypt($_data['atendido_por']));
		$contrato=json_decode(System::getInstance()->Decrypt($_data['contrato']));
		$cementerio=(System::getInstance()->Decrypt($_data['cementerio']));
		$funeraria=(System::getInstance()->Decrypt($_data['funeraria']));
		$causa_fallecimiento=$_data['causa_fallecimiento']; 
		$lugar_defuncion=$_data['lugar_defuncion'];
		$fecha_defuncion=$_data['fecha_defuncion'];
		$medico=$_data['medico'];
		$no_acta_defuncion=$_data['no_acta_defuncion'];
		$serv_fecha_inicio=$_data['serv_fecha_inicio'];
		$serv_fecha_fin=$_data['serv_fecha_fin'];
		$inicio_servicio=$_data['inicio_servicio'];						
		$fin_servicio=$_data['fin_servicio'];								
		$inhu_tipo_cofre=$_data['inhu_tipo_cofre'];
		$inhu_religion=$_data['inhu_religion'];
		
		if ((!isset($contrato->serie_contrato)) || (!isset($contrato->no_contrato))){
			 $return['mensaje']="Debe de seleccionar un contrato!";
			 return $return;
		}
		if (trim($id_init)==""){
			$return['mensaje']="Debe de seleccionar un Inhumado!";
			return $return;
		}
		SystemHtml::getInstance()->includeClass("client","PersonalData");
		$person= new PersonalData($this->db_link,$_REQUEST);
		if (!$person->existClient($id_init)){
			$return['mensaje']="Debe de seleccionar un Inhumado que exista!";
			return $return;
		} 
		$data=$person->getClientData($id_init);		
		
		$nombre_funeraria="";
		if (is_numeric($funeraria)){
			$fun=$this->getFuneraria("",$funeraria);	
			if (count($fun['results'])==1){
				$nombre_funeraria= $fun['results'][0]['text'];
			}
		}
		
		SystemHtml::getInstance()->includeClass("estructurac","Asesores"); 
		
		//$ase=new Asesores($this->db_link);
		//$asesor=$ase->getDataAsesor($atendido_por); 
		
 		$info=array(
			"id_init"=>$id_init,
			"ciudad"=>$ciudad,
			"preparado_por"=>$preparado_por,
  			"contrato"=>$_REQUEST['contrato'],
			"cementerio"=>$cementerio,
			"funeraria"=>$funeraria,
			"nombre_funeraria"=>$nombre_funeraria,
			"causa_fallecimiento"=>$causa_fallecimiento,
			"lugar_defuncion"=>$lugar_defuncion,
			"fecha_defuncion"=>$fecha_defuncion,
			"no_acta_defuncion"=>$no_acta_defuncion,
			"serv_fecha_inicio"=>$serv_fecha_inicio,
			"serv_fecha_fin"=>$serv_fecha_fin,
			"hora_inicio"=>$inicio_servicio,	
			"hora_fin"=>$fin_servicio,								
			"medico"=>$medico, 
			"inhu_tipo_cofre"=>$inhu_tipo_cofre,
			"inhu_religion"=>$inhu_religion,
			"edad"=>$data['edad'],
			"nombre_completo"=>$data['nombre_completo'],
			"fecha_nac"=>$data['fecha_nac'],
			"valid"=>true
		); 
		if (!isset($_SESSION['FUNC_SERVICIO'])){
			$_SESSION['FUNC_SERVICIO']=array();
		}
		$_SESSION['FUNC_SERVICIO']=$info;		  
		return $info;		
	}
	/*optiene el siguiente id de servicios prestados*/
	public function getNextNumberServicioPrestado(){
		$SQL="SELECT (COUNT(*)+1) AS TOTAL FROM sp_servicios_prestados ";
		$rs=mysql_query($SQL); 
		$row=mysql_fetch_assoc($rs);
		return $row['TOTAL'];		
	}
	public function generateSolicitud($data){
		SystemHtml::getInstance()->includeClass("client","PersonalData"); 
		$return=array("valid"=>false,"mensaje"=>"Datos invalido"); 
		/*VALIDO SI PUEDO PROCESAR EL FORMULARIO*/
		if (!STCSession::GI()->isSubmit("prc_inhumacion")){
			return $return;
		}
		$rt=$this->validteSolicitud($data);
		if ($rt['valid']){ 
			$servicio=$_SESSION['FUNC_SERVICIO'];
			$dContrato=json_decode(System::getInstance()->Decrypt($servicio['contrato']));
			$post_contrato=json_decode(System::getInstance()->Decrypt($data['contrato']));
			$parcela=json_decode(System::getInstance()->Decrypt($data['servicio_parcela']));
			$nit_difunto=System::getInstance()->Decrypt($data['id_nit']);
			$parentesco=System::getInstance()->Decrypt($data['solicitante_parentesco']);
			$religion=System::getInstance()->Decrypt($servicio['inhu_religion']);
			$boveda=$this->getBoveda($dContrato,$parcela);
 
			//print_r($parcela);
			SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
			SystemHtml::getInstance()->includeClass("estructurac","Asesores"); 
			SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
			
			$con=new Contratos($this->db_link); 
			$pcontr=$con->getInfoContrato($dContrato->serie_contrato,$dContrato->no_contrato);
			
			$next_id=$this->getNextNumberServicioPrestado();
			

			/*GRABO EL SERVICIO PRESTADO*/
			$sp= new ObjectSQL();
			$sp->idtipo_servicio="INH";
			$sp->no_servicio="CONCAT('".$sp->idtipo_servicio."','-',DATE_FORMAT(CURDATE(), '%d-%m-%Y'),'-',".$next_id.")";
			$sp->EM_ID="";
			$sp->no_contrato=$dContrato->no_contrato;
			$sp->serie_contrato=$dContrato->serie_contrato;												
			$sp->id_status=35;// Estatus Solicitud Orden de servicio
			$sp->osario=$parcela->osario;
			$sp->id_jardin=$parcela->id_jardin;
			$sp->id_fases=$parcela->id_fases;
			$sp->lote=$parcela->lote;
			$sp->bloque=$parcela->bloque;
			$sp->idfunerarias=$servicio['funeraria'];
			$sp->idcementerios=$servicio['cementerio'];
			$sp->id_capilla=0;
			$sp->id_parentesco=$parentesco;
			$sp->id_documento=""; //TIPO DOCUMENTO DEL RESPONSABLE
			$sp->identificacion="";	//NO IDENTIFICACION RESPONABLE			
			$sp->responsablexcliente=$data['solicitante_nombre_contacto'];	
			$sp->telefono=$data['solicitante_telefono'];		
			$sp->movil="";	
			$sp->parentezco="";	
			$sp->encargado_servicio=System::getInstance()->Decrypt($data['atendido_por']);	
			$sp->fecha_inicio="STR_TO_DATE('".$servicio['serv_fecha_inicio']."','%d-%m-%Y')";	
			$sp->hora_inicio=$servicio['hora_inicio'];	
			$sp->fecha_fin="STR_TO_DATE('".$servicio['serv_fecha_fin']."','%d-%m-%Y')";	 	
			$sp->hora_fin=$servicio['hora_fin'];		
			$sp->nombre_lapida=$data['nombre_lapida'];	
			$sp->fecha="curdate()";	
			$sp->esquela=$data['esquela'];	
			$sp->religion=$religion;	
			$sp->generado_por=UserAccess::getInstance()->getIDNIT();
			$sp->setTable("sp_servicios_prestados");
			$SQL=$sp->toSQL("insert"); 
			mysql_query($SQL);
			
			$sp_inh= new ObjectSQL();
			$sp_inh->serie_contrato=$sp->serie_contrato;
			$sp_inh->no_contrato=$sp->no_contrato;
			$sp_inh->EM_ID="";
			$sp_inh->no_servicio=$sp->no_servicio;
			$sp_inh->osario=$sp->osario;
			$sp_inh->id_jardin=$sp->id_jardin;
			$sp_inh->id_fases=$sp->id_fases;
			$sp_inh->lote=$sp->lote;
			$sp_inh->bloque=$sp->bloque;
			$sp_inh->cavidad=$boveda;	
			$sp_inh->idtipo_servicio=$sp->idtipo_servicio;
			$sp_inh->fecha_inhuma="STR_TO_DATE('".$data['serv_fecha_fin']."','%d-%m-%Y')";	
			$sp_inh->setTable("sp_servicios_cementerio");
			$SQL=$sp_inh->toSQL("insert");	
			mysql_query($SQL);		
			
			$cl_data=PersonalData::GI()->getClientData($nit_difunto);
		 
			$fa= new ObjectSQL();
			$fa->no_acta_defuncion=$data['no_acta_defuncion'];
			$fa->serie_contrato=$sp->serie_contrato;
			$fa->no_contrato=$sp->no_contrato;
			$fa->EM_ID="";	
			$fa->idtipo_servicio=$sp->idtipo_servicio;
			$fa->no_servicio=$sp->no_servicio;
			$fa->id_documento=$cl_data['id_documento'];
			$fa->id_parentesco="";	
			$fa->nombres=$cl_data['primer_nombre'].trim(" ".$cl_data['segundo_nombre']);			
			$fa->apellidos=$cl_data['primer_apellido'].trim(" ".$cl_data['segundo_apellido']);		
			$fa->fecha_nac=$cl_data['fecha_nacimiento'];			
			$fa->fecha_fallecido="STR_TO_DATE('".$data['fecha_defuncion']."','%d-%m-%Y')";															
			$fa->no_doc=$nit_difunto;	
			$fa->motivo_fallecido=$data['causa_fallecimiento'];	
			$fa->medico=$data['medico'];
			$fa->lugar_defuncion=$data['lugar_defuncion'];
			$fa->sexo=$cl_data['id_genero']==1?'M':'F'; 
			$fa->setTable("sp_fallecidos");
			$SQL=$fa->toSQL("insert");	
			mysql_query($SQL); 
	
			$cb= new Cobros($this->db_link);
			
		/*	$gestion=$cb->createGestion("INH",
										$sp_inh->EM_ID,
										$sp->no_contrato,
										$sp->serie_contrato,
										$data['servicio_descripcion']); 	*/
			
			
			$productos=FacturarPS::getInstance()->getCarProductoList();				 
			
			foreach($productos as $key=>$row){

				$docto=MVFactura::getInstance()->doCreateDocument($pcontr->id_nit_cliente,
																'',
																$sp->no_contrato,
																$sp->serie_contrato,
																$row['producto']->tipo_mov,
																RECIBO_VIRTUAL,
																'',//RESERVA
																'', //RESERVA
																$row['neto'],
																1,
																0, 
																"RECIBO PRODUCTO INHUMACION",
																"RECIBO PRODUCTO INHUMACION",
																'', //MOTORIZADO
																0, //NO_CUOTA
																0, //PLANILLA_GESTION
																$sp->no_servicio, //NO_SERVICIO_PRESTADO
																$row['producto']->id_producto, //PRODUCTO
																$row['cantidad'],
																$row['producto']->costo
																);
																
																
			}
			
			STCSession::GI()->setSubmit("prc_inhumacion",false);
			$return=array("valid"=>true,"mensaje"=>"Solicitud generada!"); 					
		}else{
			$return=$rt;
		}
		return $return;
	}
	
	public function validteSolicitud($data){ 
		$return=array("valid"=>false,"mensaje"=>"Datos invalido"); 
		$servicio=$_SESSION['FUNC_SERVICIO'];
		$dContrato=json_decode(System::getInstance()->Decrypt($servicio['contrato']));
		$post_contrato=json_decode(System::getInstance()->Decrypt($data['contrato']));		
		if (!isset($servicio['id_init'])){
			return $return;
			exit;
		} 
		if ((!isset($post_contrato->serie_contrato)) &&(!isset($dContrato->serie_contrato)) ){
			return $return;
			exit;
		}		 
		if (!(trim($post_contrato->no_contrato)==trim($dContrato->no_contrato))){
			return $return;
			exit;
		}		 
		
		$return['valid']=true;
		
		return $return;	
	}
	
	public function getPreparadoPor($search){
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
	CONCAT(sys_personas.primer_nombre,' ',sys_personas.segundo_nombre,' ',sys_personas.primer_apellido,' ',sys_personas.segundo_apellido) AS nombre_completo 
		 FROM sys_personas ";
		  
		$SQL.=$QUERY;
		$rs=mysql_query($SQL);
		$result=array("results"=>array());  
		
		while($row=mysql_fetch_assoc($rs)){	
			$eID=System::getInstance()->Encrypt($row['id_nit']);
			$data=array("id"=>$eID,"text"=>$row['nombre_completo']);
			array_push($result['results'],$data);
		}
 
		
		return $result;
	}
	
	public function getAtendidoPor($search){
		SystemHtml::getInstance()->includeClass("estructurac","Asesores");
		$asesores= new Asesores($this->db_link); 
		
		$search=trim(mysql_real_escape_string($search));
		
		$QUERY="";
		$HAVING="";
		if (isset($search)){
		  if (trim($search)!=""){ 
			$QUERY=" AND 
(sys_personas.id_nit LIKE '%".$search."%' 
OR CONCAT(sys_personas.primer_nombre,' ' ,sys_personas.segundo_nombre) LIKE '%".$search."%' 
OR CONCAT(sys_personas.primer_apellido,' ',sys_personas.segundo_apellido) LIKE '%".$search."%' 
OR CONCAT(sys_personas.primer_nombre,' ',sys_personas.primer_apellido) LIKE '%".$search."%' 
OR CONCAT(sys_personas.segundo_nombre,' ',sys_personas.primer_apellido) LIKE '%".$search."%' ) ";
	  
		  }else{
			$QUERY=" AND sys_asesor.id_nit=0 ";
		  }
		}else{
			$QUERY=" AND sys_asesor.id_nit=0 ";
		}
 
		$SQL="SELECT 
			sys_asesor.codigo_asesor,
			CONCAT(sys_personas.primer_nombre,' ',
			sys_personas.segundo_nombre,' ',sys_personas.primer_apellido,' ',
			sys_personas.segundo_apellido) AS nombre_completo,
			sys_asesor.codigo_gerente_grupo
		 FROM sys_personas 
		 INNER JOIN sys_asesor ON  (sys_asesor.`id_nit`=sys_personas.id_nit)
		 WHERE sys_asesor.status=1 ";
		  
		$SQL.=$QUERY; 
		$rs=mysql_query($SQL);
		$result=array("results"=>array());  
		
		while($row=mysql_fetch_assoc($rs)){	
			$eID=System::getInstance()->Encrypt($row['codigo_asesor']);
			$gerente=$asesores->getGerenteData($row['codigo_gerente_grupo']);
			$gerente=$gerente[0];
			$gID=System::getInstance()->Encrypt($row['codigo_gerente_grupo']);
		 
			$data=array("id"=>$eID,"text"=>utf8_encode($row['nombre_completo']),
				"idGerente"=>$gID,
				"nombre_gerente"=>utf8_encode($gerente['nombre'])." ".utf8_encode($gerente['apellido']));
 			array_push($result['results'],$data);
		}
 
		
		return $result;
	}
	
	public function getInfoParcela($data){
		$return=array("valid"=>false,"mensaje"=>"Datos invalido"); 
		$parcela=json_decode(System::getInstance()->Decrypt($data['parcela']));
		$contrato=json_decode(System::getInstance()->Decrypt($data['contrato']));

		if ($contrato->no_contrato==$parcela->no_contrato){ 
			$return['plan']=$parcela->CODIGO_TP;
			$boveda=$this->getBoveda($contrato,$parcela);
			$return['boveda']=$boveda; 
			$return['valid']=true;
		}
		
		return $return;
	}
	public function getBoveda($contrato,$parcela){
		
		$SQL="SELECT * FROM `sp_servicios_cementerio` as sc
			INNER JOIN `sp_servicios_prestados` ON (`sp_servicios_prestados`.`no_servicio`=sc.`no_servicio`)
			WHERE 
			sc.no_contrato='".$contrato->no_contrato."' AND sc.serie_contrato='".$contrato->serie_contrato."' 
			AND sc.osario='".$parcela->osario."' AND sc.id_jardin='".$parcela->id_jardin."'
			 AND sc.id_fases='".$parcela->id_fases."' 
			AND sc.lote='".$parcela->lote."' AND sc.bloque='".$parcela->bloque."'";	 
 
		$boveda="I";
		$rs=mysql_query($SQL); 
		while($row=mysql_fetch_assoc($rs)){	 
			if ($row['cavidad']=="I"){
				$boveda="S";	
			}
		}
		return $boveda;
	}

 		  
}

?>