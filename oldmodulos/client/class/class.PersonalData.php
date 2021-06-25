<?php

/*
	Esta clase maneja la insercion de datos
	de un cliente 
*/
class PersonalData{
	private $data;
	private static $db_link;
	private static $instance;	
	private $message=array("mensaje"=>"","error"=>true,"typeError"=>0);
	
	private $errorList=array(
		100 => "Registro creado correctamente!",
		101 => "Este identficador de cliente existe, el sistema no acepta duplicados!",
		102 => "No se pudo completar la operacion",
		103 => "Debe de llenar todos los campos obligatorios.",
		104 => "Registro actualizado!",
		105 => "Cliente no existe"
	);
	var $required_personal_data_fields=array(
			"numero_documento"=>true,
			"primer_nombre"=>true,
			"primer_apellido"=>true, 
			"id_documento"=>true
		);
	
	public function __construct($db_link=null,$data=null){
		if ($db_link!=''){
			$this->data=$data;
			$this->db_link=$db_link;
		}
	}
	public static function getInstance(){
		 if (!PersonalData::$instance instanceof self) {
             PersonalData::$instance = new PersonalData();
        }
        return PersonalData::$instance;
	} 	
	public static function GI(){ 
        return PersonalData::getInstance();
	} 		
	public function filterNit($nit){
		return str_replace("-","",$nit);
	}
	public function create(){
		
		
		if (!$this->validateFields($this->required_personal_data_fields)){
			$this->message['mensaje']=$this->errorList[103];
			$this->message['error']=true;
			$this->message['typeError']="103";
			return false;
		} 
		
		if (!$this->existClient($this->filterNit($this->data['numero_documento']))){
			$obj= new ObjectSQL();
			/*ORGANIZANDO LOS DATOS DEL CLIENTE*/
			$obj->id_nit=$this->filterNit(trim($this->data['numero_documento']));
			$obj->primer_nombre=trim(strtoupper($this->data['primer_nombre']));
			$obj->segundo_nombre=trim(strtoupper($this->data['segundo_nombre']));
			$obj->tercer_nombre=trim(strtoupper($this->data['tercer_nombre']));
			$obj->primer_apellido=trim(strtoupper($this->data['primer_apellido']));
			$obj->segundo_apellido=trim(strtoupper($this->data['segundo_apellido']));
			$obj->apellido_conyuge=trim(strtoupper($this->data['apellido_conyuge']));
			$obj->numero_documento=trim(strtoupper($this->data['numero_documento']));
			//$sp=explode("-",$this->data['fecha_nacimiento']);
			$obj->fecha_nacimiento="STR_TO_DATE('".$this->data['fecha_nacimiento']."','%d-%m-%Y')";//$sp[2]."-".$sp[1]."-".$sp[0];
			$obj->lugar_nacimiento=$this->data['lugar_nacimiento'];
			$obj->nacionalidad=$this->data['lugar_nacimiento'];
			$obj->numero_hijos=$this->data['numero_hijos'];
			$obj->id_status=1;
			$obj->fecha_creacion="CONCAT(CURDATE(),' ',CURRENT_TIME())";
			$obj->id_nacionalidad=System::getInstance()->getEncrypt()->decrypt($this->data['id_nacionalidad'],UserAccess::getSessionID());
			$obj->id_clasificacion=System::getInstance()->getEncrypt()->decrypt($this->data['sys_clasificacion_persona'],UserAccess::getSessionID());
			$obj->id_clase=System::getInstance()->getEncrypt()->decrypt($this->data['idtipo_cliente'],UserAccess::getSessionID());
			$obj->id_genero=System::getInstance()->getEncrypt()->decrypt($this->data['id_genero'],UserAccess::getSessionID());
			$obj->id_clase=System::getInstance()->getEncrypt()->decrypt($this->data['idtipo_cliente'],UserAccess::getSessionID());
			$obj->id_religion=System::getInstance()->getEncrypt()->decrypt($this->data['id_religion'],UserAccess::getSessionID());
			$obj->id_profecion=System::getInstance()->getEncrypt()->decrypt($this->data['id_profecion'],UserAccess::getSessionID());
			$obj->id_estado_civil=System::getInstance()->getEncrypt()->decrypt($this->data['id_estado_civil'],UserAccess::getSessionID());
			$obj->id_documento=System::getInstance()->getEncrypt()->decrypt($this->data['id_documento'],UserAccess::getSessionID());
			
			$obj->creado_por=UserAccess::getInstance()->getIDNIT();
			
		 
			$SQL=$obj->getSQL("insert","sys_personas");
		 
			$rs=@mysql_query($SQL);
			
			if ($this->existClient($obj->id_nit)){
			/*	$this->addAddress($obj->id_nit);
				$this->addPhone($obj->id_nit);
				$this->addEmail($obj->id_nit);
				$this->addReference($obj->id_nit);*/
			
				$this->message['mensaje']=$this->errorList[100];
				$this->message['error']=false;
				$this->message['typeError']="100";
				$this->message['nit']=System::getInstance()->Encrypt($obj->id_nit);
				$this->message['idnit']=$this->message['nit'];
				$this->message['id_documento']=$this->data['id_documento'];
				 
				return true;
			}else{
				$this->message['mensaje']=$this->errorList[102];
				$this->message['error']=true;
				$this->message['typeError']="102";
			
			
				return false;
			}
		}else{
			$this->message['mensaje']=$this->errorList[101];
			$this->message['error']=true;
			$this->message['typeError']="101";
			
			return false;
		}
	}
	
	/*
		Actualiza el tipo de clasificacion de un prospecto 
			3 => A
			4 => B
			5 => C
	*/
	public function updateTipoClasificacion($tipo,$id_nit){
		if ($this->existClient($id_nit)){
			$obj = new ObjectSQL();
			$obj->id_clasificacion=$tipo;
			$SQL=$obj->getSQL("update","sys_personas"," where id_nit='". mysql_escape_string($id_nit) ."' ");
			$rs=@mysql_query($SQL);
			$this->message['mensaje']=$this->errorList[104];
			$this->message['error']=false;
			$this->message['typeError']="104";
		}
	}
	
	public function checkTipoClasificacion($id_nit){
		$SQL="SELECT 
			sys_personas.id_nit,
			sys_genero.descripcion AS genero,
			sys_clase_local_extrangero.Descripcion AS clase,
			sys_clasificacion_persona.descripcion AS clasificacion,
			DATE_FORMAT(sys_personas.fecha_nacimiento, '%d-%m-%Y') AS fecha_nacimiento, 
			
			(
			SELECT
		 COUNT(sys_sector.`descripcion`) AS direccion
		  FROM 
		`sys_direcciones` 
		INNER JOIN `sys_ciudad` ON (sys_ciudad.`idciudad`=sys_direcciones.idciudad)
		INNER JOIN `sys_provincia` ON (sys_provincia.`idprovincia`=sys_direcciones.idprovincia)
		INNER JOIN `sys_sector` ON (sys_sector.`idsector`=sys_direcciones.idsector)
		WHERE sys_direcciones.id_nit=sys_personas.id_nit  ) AS direccion,
		(SELECT COUNT(sys_telefonos.`id_telefonos`) AS PHONE FROM `sys_telefonos` WHERE sys_telefonos.id_nit=sys_personas.id_nit ) AS telefono,
		(SELECT COUNT(direccion) FROM `sys_cuentas_emails` WHERE `status`=1 AND sys_cuentas_emails.id_nit=sys_personas.id_nit AND direccion!='' ) AS email	
		 FROM sys_personas
		LEFT JOIN `sys_clase_local_extrangero` ON (sys_personas.`id_clase`=sys_clase_local_extrangero.`id_clase`)
		LEFT JOIN sys_clasificacion_persona ON (sys_clasificacion_persona.id_clasificacion=sys_personas.`id_clasificacion`)
		LEFT JOIN sys_genero ON (sys_genero.`id_genero`=sys_personas.`id_genero`)
		LEFT JOIN `sys_documentos_identidad` ON (`sys_documentos_identidad`.`id_documento`=sys_personas.`id_documento`)
		WHERE 	sys_personas.id_nit='". mysql_real_escape_string($id_nit) ."' ";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			/*Tipo de clasificacion B = 4*/
			if (($row['direccion']>0) && ($row['telefono']>1)){
				$this->updateTipoClasificacion(4,$row['id_nit']);
			}else{
				$this->updateTipoClasificacion(5,$row['id_nit']);
			} 
		}		
	}
	
	public function doFacturarFiscal($do_Fiscal){ 
		$id_nit=System::getInstance()->Decrypt($this->data['id']); 
		if ($this->existClient($id_nit)){ 
			$obj= new ObjectSQL(); 
			$obj->factura_fiscal=$do_Fiscal;
			$SQL=$obj->getSQL("update","sys_personas"," where id_nit='". mysql_escape_string($id_nit) ."' ");
			$rs=@mysql_query($SQL);
			$this->message['mensaje']=$this->errorList[104];
			$this->message['error']=false;
			$this->message['typeError']="104";
		}else{
			$this->message['mensaje']=$this->errorList[102];
			$this->message['error']=true;
			$this->message['typeError']="102";	
		}
	}
	
	public function updatePersonalData(){
		unset($this->required_personal_data_fields['numero_documento']);
		unset($this->required_personal_data_fields['id_documento']);
		if (!$this->validateFields($this->required_personal_data_fields)){
			$this->message['mensaje']=$this->errorList[103];
			$this->message['error']=true;
			$this->message['typeError']="103";
			return false;
		}
		$id_nit=System::getInstance()->getEncrypt()->decrypt($this->data['id'],UserAccess::getSessionID());
		
		if ($this->existClient($id_nit)){
			$obj= new ObjectSQL();
			
			/*ORGANIZANDO LOS DATOS DEL CLIENTE*/
			$obj->primer_nombre=trim(strtoupper($this->data['primer_nombre']));
			$obj->segundo_nombre=trim(strtoupper($this->data['segundo_nombre']));
			$obj->tercer_nombre=trim(strtoupper($this->data['tercer_nombre']));
			$obj->primer_apellido=trim(strtoupper($this->data['primer_apellido']));
			$obj->segundo_apellido=trim(strtoupper($this->data['segundo_apellido']));
			$obj->apellido_conyuge=trim(strtoupper($this->data['apellido_conyuge']));
			$obj->fecha_nacimiento="STR_TO_DATE('".$this->data['fecha_nacimiento']."','%d-%m-%Y')";
			$obj->lugar_nacimiento=$this->data['lugar_nacimiento'];
			$obj->nacionalidad=$this->data['lugar_nacimiento'];
			$obj->numero_hijos=trim($this->data['numero_hijos']);
			$obj->cuenta_bancaria=trim($this->data['cuenta_bancaria']);
/*			if (isset($this->data['factura_fiscal'])){
				$obj->factura_fiscal=$this->data['factura_fiscal'];
			}else{
					
			}*/
			 
			$obj->id_nacionalidad=System::getInstance()->getEncrypt()->decrypt($this->data['id_nacionalidad'],UserAccess::getSessionID());
			$obj->id_clasificacion=System::getInstance()->getEncrypt()->decrypt($this->data['sys_clasificacion_persona'],UserAccess::getSessionID());
			$obj->id_clase=System::getInstance()->getEncrypt()->decrypt($this->data['idtipo_cliente'],UserAccess::getSessionID());
			$obj->id_genero=System::getInstance()->getEncrypt()->decrypt($this->data['id_genero'],UserAccess::getSessionID());
			$obj->id_clase=System::getInstance()->getEncrypt()->decrypt($this->data['idtipo_cliente'],UserAccess::getSessionID());
			$obj->id_religion=System::getInstance()->getEncrypt()->decrypt($this->data['id_religion'],UserAccess::getSessionID());
			$obj->id_profecion=System::getInstance()->getEncrypt()->decrypt($this->data['id_profecion'],UserAccess::getSessionID());
			$obj->id_estado_civil=System::getInstance()->getEncrypt()->decrypt($this->data['id_estado_civil'],UserAccess::getSessionID());
			
			$SQL=$obj->getSQL("update","sys_personas"," where id_nit='". mysql_escape_string($id_nit) ."' ");
	 
			
			$rs=@mysql_query($SQL);
			$this->message['mensaje']=$this->errorList[104];
			$this->message['error']=false;
			$this->message['typeError']="104";
				
		}else{
			$this->message['mensaje']=$this->errorList[102];
			$this->message['error']=true;
			$this->message['typeError']="102";
		}
	}
	
	public function getMessages(){
		return $this->message;
	}
	
	public function validateFields($fields){
		foreach($fields as $key =>$val){
			if (isset($this->data[$key])){
				if ($this->data[$key]==""){
					return false;	
				}
			}else{
				return false;
			}
		}
		return true;
	}
	
	/* Verifica si un idenficiador de cliente existe*/
	public function existClient($client_id){
		$SQL="SELECT COUNT(*) AS EXIST FROM sys_personas WHERE  id_nit='". mysql_escape_string($client_id) ."'";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		return $row['EXIST'];
	}
	
	/* Verifica si un idenficiador de cliente existe*/
	public function existClientByDocument($client_id,$id_documento){
		$SQL="SELECT COUNT(*) AS EXIST FROM sys_personas 
			WHERE  id_nit='". mysql_escape_string($client_id) ."' AND
			id_documento='". mysql_escape_string($id_documento) ."'";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		return $row['EXIST'];
	}
	
	public function getClientData($client_id){
		$data=array();
		$SQL="SELECT *,
				DATEDIFF(CURDATE(),sys_personas.fecha_nacimiento) AS edad, 
				CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`segundo_nombre`,' ',sys_personas.`primer_apellido`,' ',sys_personas.segundo_apellido) AS nombre_completo,
			DATE_FORMAT(fecha_nacimiento,'%d-%m-%Y') as fecha_nac,
			sys_nacionalidad.`Descripcion` as nacionalidad,
(SELECT
			 CONCAT(sys_ciudad.`Descripcion`,' \\ <br>',sys_provincia.`descripcion`,' \\ <br>',sys_sector.`descripcion`) AS direccion
			  FROM 
			`sys_direcciones` 
			INNER JOIN `sys_ciudad` ON (sys_ciudad.`idciudad`=sys_direcciones.idciudad)
			INNER JOIN `sys_provincia` ON (sys_provincia.`idprovincia`=sys_direcciones.idprovincia)
			INNER JOIN `sys_sector` ON (sys_sector.`idsector`=sys_direcciones.idsector)
			WHERE id_nit=sys_personas.id_nit LIMIT 1) as direccion,
			(SELECT CONCAT('(',IFNULL(sys_telefonos.`area`,''),') ',IFNULL(sys_telefonos.numero,''),' Ext:',IFNULL(sys_telefonos.extencion,'')) AS PHONE FROM `sys_telefonos` WHERE id_nit=sys_personas.id_nit LIMIT 1) as telefono,
			(SELECT direccion FROM `sys_cuentas_emails` WHERE `status`=1 AND id_nit=sys_personas.id_nit and direccion!='' LIMIT 1) as email				
		FROM sys_personas
		LEFT JOIN `sys_documentos_identidad` ON (sys_documentos_identidad.`id_documento`=sys_personas.id_documento)
		LEFT JOIN `sys_nacionalidad` ON (`sys_nacionalidad`.`id_nacionalidad`=sys_personas.id_nacionalidad)		
		 WHERE  id_nit='". mysql_escape_string($client_id) ."'";
 		
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			//echo $row['fecha_nac']."_AQUI";
			$data=$row;
			$data['fecha_nacimiento']=$row['fecha_nac'];
		}
		return $data;
	}
	
	public function getTotalTelefonos($id_nit){
		$SQL="SELECT COUNT(*) AS total FROM `sys_personas` 
			INNER JOIN `sys_telefonos` ON (sys_telefonos.`id_nit`=sys_personas.id_nit)
		WHERE sys_personas.id_nit='".$id_nit."' and sys_telefonos.status_telefono=1 ";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		return $row['total'];		
	}
	
	public function getContactList($client_id,$contact_id=0){
		$data=array();
 
		$SQL="SELECT 
					*,
					sys_tipos_clasificacion.`descripcion` AS tipo
				FROM `sys_contactos` 
				INNER JOIN `sys_tipos_clasificacion` ON (`sys_tipos_clasificacion`.`id_tipos_clasifica`=sys_contactos.idtipo_contacto)
			WHERE sys_contactos.id_nit='". mysql_escape_string($client_id) ."' AND sys_contactos.estatus=1  ";	 
			
		
		if ($contact_id>0){
			$SQL.=" and sys_contactos.id_contactos='".$contact_id."' ";	
		}
			
		
		$SQL.=" GROUP BY sys_contactos.id_contactos ";
			 
	 	//echo $SQL;
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}
		return $data;
	}
		
	public function getPhone($client_id,$contact=0,$address_id=0){
		$data=array();
		$SQL="SELECT 
				sys_telefonos.*,
				sys_tipos_clasificacion.`descripcion` AS tipo
			FROM 
			sys_telefonos
			INNER JOIN `sys_tipos_clasificacion` ON (`sys_tipos_clasificacion`.`id_tipos_clasifica`=sys_telefonos.tipo_telefono)
	 WHERE   sys_telefonos.status_telefono='1' ";
	 	
		if ($address_id>0){
			$SQL.=" and  sys_telefonos.id_telefonos='".$address_id."'";	
		} 
		
		if ($contact==0){
			$SQL.=" and sys_telefonos.id_nit='". mysql_escape_string($client_id) ."' and (id_contactos=0 or id_contactos is null)";	
		}else{
			$SQL.=" and id_contactos='". mysql_escape_string($contact) ."' ";
		}
		
 
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}
		return $data;
	}
	
	public function getEmails($client_id,$contact=0,$email_id=0){
		$data=array();
		$SQL="SELECT 
				sys_cuentas_emails.* ,
				sys_tipos_clasificacion.`descripcion` AS tipo
			FROM sys_cuentas_emails
			INNER JOIN `sys_tipos_clasificacion` ON (`sys_tipos_clasificacion`.`id_tipos_clasifica`=sys_cuentas_emails.tipos_email) 
			WHERE  sys_cuentas_emails.status='1' ";
			
		if ($contact==0){
			$SQL.=" and sys_cuentas_emails.id_nit='". mysql_escape_string($client_id) ."' and (sys_cuentas_emails.id_contactos=0 or sys_cuentas_emails.id_contactos is null)";	
		}else{
			$SQL.=" and sys_cuentas_emails.id_contactos='". mysql_escape_string($contact) ."' ";
			
		}
		if ($email_id>0){
			$SQL.=" and id_emails='". mysql_escape_string($email_id)."'";
		}  
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}
		return $data;
	}
		
	public function getPersonalRef($client_id,$refere_id=0){
		$data=array();
		$SQL="SELECT 
				sys_referencia_personales.*,
				sys_tipos_clasificacion.`descripcion` AS tipo
			FROM sys_referencia_personales
			INNER JOIN `sys_tipos_clasificacion` ON (`sys_tipos_clasificacion`.`id_tipos_clasifica`=sys_referencia_personales.tipo_refencia) 
			WHERE  sys_referencia_personales.id_nit='". mysql_escape_string($client_id) ."' and sys_referencia_personales.status='1'";
			
		if ($refere_id>0){
			$SQL.=" AND sys_referencia_personales.id_referencias='". mysql_escape_string($refere_id) ."'";	
		}
	 

		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}
		return $data;
	}		
	
	public function getPersonalRefefencia($client_id){
		$data=array();
		$SQL="SELECT 
					referidos.*,
					sys_tipos_clasificacion.`descripcion` AS tipo
				FROM 
					`referidos`
				INNER JOIN `sys_tipos_clasificacion` ON 
				(`sys_tipos_clasificacion`.`id_tipos_clasifica`=referidos.tipo_refencia) 
			WHERE  referidos.id_nit='". mysql_escape_string($client_id) ."' and referidos.status='1'";
  
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}
		return $data;
	}	
		
	public function updateEstatusReferidos($client_id,$id_tipo,$estatus){
		if ($this->existClient($client_id)){
			$obj= new ObjectSQL();
			$obj->status=$estatus;		
			$SQL=$obj->getSQL("update","referidos"," WHERE id_nit='". mysql_escape_string($client_id) ."' and tipo_refencia='".mysql_escape_string($id_tipo)."'");	
			  
			mysql_query($SQL);	
			$this->message['mensaje']=$this->errorList[104];
			$this->message['error']=false;
			$this->message['typeError']="104";		
		}else{
			$this->message['mensaje']=$this->errorList[101];
			$this->message['error']=true;
			$this->message['typeError']="101";
		}	
	}

	/*ACTUALIA LOS DATOS DE LOS REFERIDOS*/
	public function updateReferidos($client_id,$id_tipo,$id_tipo_old,$estatus,$nombre1,$nombre2,$apellido1,$apellido2,$telefono,$celular,$descripcion){
		if ($this->existClient($client_id)){
			$obj= new ObjectSQL();
			$obj->status=$estatus;	
			$obj->nombre1=$nombre1;
			$obj->nombre2=$nombre2;
			$obj->apellido1=$apellido1;
			$obj->apellido2=$apellido2;
			$obj->telefono=$telefono;
			$obj->movil=$celular;
			$obj->tipo_refencia=$id_tipo;
			$obj->descripcion=$descripcion;
				
			$SQL=$obj->getSQL("update","referidos"," WHERE id_nit='". mysql_escape_string($client_id) ."' and tipo_refencia='".mysql_escape_string($id_tipo_old)."'");	
			 
			mysql_query($SQL);	
			$this->message['mensaje']=$this->errorList[104];
			$this->message['error']=false;
			$this->message['typeError']="104";		
		}else{
			$this->message['mensaje']=$this->errorList[101];
			$this->message['error']=true;
			$this->message['typeError']="101";
		}	
	}	

	public function updateEstatusReferencia($client_id,$id_tipo,$estatus,$refere_id){
		if ($this->existClient($client_id)){
			$obj= new ObjectSQL();
			$obj->status=$estatus;		
			$SQL=$obj->getSQL("update","sys_referencia_personales"," WHERE id_nit='". mysql_escape_string($client_id) ."' and tipo_refencia='".mysql_escape_string($id_tipo)."'");	
			
			if ($refere_id>0){
				$SQL.=" AND sys_referencia_personales.id_referencias='". mysql_escape_string($refere_id) ."'";	
			}
				

			mysql_query($SQL);	
			$this->message['mensaje']=$this->errorList[104];
			$this->message['error']=false;
			$this->message['typeError']="104";		
		}else{
			$this->message['mensaje']=$this->errorList[101];
			$this->message['error']=true;
			$this->message['typeError']="101";
		}	
	}

	public function updateReferencia($client_id,$id_tipo,$id_tipo_old,$nombre,$telefono,$telefono2,$observacion,$estatus,$refere_id){
		if ($this->existClient($client_id)){
			$obj= new ObjectSQL();
			$obj->status=$estatus;	
			$obj->tipo_refencia=$id_tipo;		
			$obj->telefono=$telefono;	
			$obj->nombre_completo=$nombre;	
			$obj->telefono_2=$telefono2;	
			$obj->observaciones=$observacion;	
			
			$SQL=$obj->getSQL("update","sys_referencia_personales"," WHERE id_nit='". mysql_escape_string($client_id) ."' and tipo_refencia='".mysql_escape_string($id_tipo_old)."'");	
			
			if ($refere_id>0){
				$SQL.=" AND sys_referencia_personales.id_referencias='". mysql_escape_string($refere_id) ."'";	
			}
				

			mysql_query($SQL);	
			$this->message['mensaje']=$this->errorList[104];
			$this->message['error']=false;
			$this->message['typeError']="104";		
		}else{
			$this->message['mensaje']=$this->errorList[101];
			$this->message['error']=true;
			$this->message['typeError']="101";
		}	
	}
		
	public function updateEstatusEmail($client_id,$id_tipo,$estatus,$email_id=0){
		if ($this->existClient($client_id)){
			$obj= new ObjectSQL();
			$obj->status=$estatus;		
			$SQL=$obj->getSQL("update","sys_cuentas_emails"," WHERE id_nit='". mysql_escape_string($client_id) ."' and tipos_email='".mysql_escape_string($id_tipo)."'");	
			
			if ($email_id>0){
				$SQL.=" and id_emails='". mysql_escape_string($email_id) ."'";	
			}
		
			mysql_query($SQL);	
			$this->message['mensaje']=$this->errorList[104];
			$this->message['error']=false;
			$this->message['typeError']="104";		
		}else{
			$this->message['mensaje']=$this->errorList[101];
			$this->message['error']=true;
			$this->message['typeError']="101";
		}	
	}

	/*ACTUALIZA LOS DATOS DEL CORREO ELECTRONICO*/
	public function updateEmail($client_id,$id_tipo,$tipo_old,$estatus,$email,$observacion,$email_id=0){
		if ($this->existClient($client_id)){
			$obj= new ObjectSQL();
			$obj->status=$estatus;	
			$obj->direccion=$email;	
			$obj->observaciones=$observacion;	
			$obj->tipos_email=	$id_tipo;
			
			$SQL=$obj->getSQL("update","sys_cuentas_emails"," WHERE id_nit='". mysql_escape_string($client_id) ."' and tipos_email='".mysql_escape_string($tipo_old)."'");	
			 
			if ($email_id>0){
				$SQL.=" and id_emails='". mysql_escape_string($email_id) ."'";	
			}
		
			mysql_query($SQL);	
			$this->message['mensaje']=$this->errorList[104];
			$this->message['error']=false;
			$this->message['typeError']="104";		
		}else{
			$this->message['mensaje']=$this->errorList[101];
			$this->message['error']=true;
			$this->message['typeError']="101";
		}	
	}
	
	public function updateEstatusPhone($client_id,$id_tipo,$estatus,$phone_id=0){
		if ($this->existClient($client_id)){
			$obj= new ObjectSQL();
			$obj->status_telefono=$estatus;		
			
			$SQL=$obj->getSQL("update","sys_telefonos"," WHERE id_nit='". mysql_escape_string($client_id) ."' and tipo_telefono='".mysql_escape_string($id_tipo)."' ");	
			
			if ($phone_id>0){
				$SQL.=" and id_telefonos='". mysql_escape_string($phone_id)."'";
			}
			 
			mysql_query($SQL);	
			$this->message['mensaje']=$this->errorList[104];
			$this->message['error']=false;
			$this->message['typeError']="104";		
		}else{
			$this->message['mensaje']=$this->errorList[101];
			$this->message['error']=true;
			$this->message['typeError']="101";
		}	
	}
	
	public function updatePhone($client_id,$id_tipo,$id_tipo_old,$estatus,$area_code,$number,$extension=0,$phone_id=0){
		if ($this->existClient($client_id)){
			$obj= new ObjectSQL();
			$obj->status_telefono=$estatus;	
			$obj->tipo_telefono=$id_tipo;		
			$obj->area=$area_code;		
			$obj->numero=$number;	
			$obj->extencion=$extension;	 		
			
			$SQL=$obj->getSQL("update","sys_telefonos"," WHERE id_nit='". mysql_escape_string($client_id) ."' and tipo_telefono='".mysql_escape_string($id_tipo_old)."' ");	
		//	echo $SQL;
			if ($phone_id>0){
				$SQL.=" and id_telefonos='". mysql_escape_string($phone_id)."'";
			}
			 
			mysql_query($SQL);	
			$this->message['mensaje']=$this->errorList[104];
			$this->message['error']=false;
			$this->message['typeError']="104";		
		}else{
			$this->message['mensaje']=$this->errorList[101];
			$this->message['error']=true;
			$this->message['typeError']="101";
		}	
	}
	
	public function getAddress($client_id,$contact=0,$address_id=0){
		$data=array();
		$SQL="SELECT 
				sys_direcciones.*,
				sys_ciudad.Descripcion AS ciudad,
				sys_provincia.`descripcion` AS provincia,
				sys_municipio.`descripcion` AS municipio,
				sys_sector.`descripcion` AS sector,
				sys_tipos_clasificacion.`descripcion` AS tipo,
				sys_sector.`longitud`,
				sys_sector.`latitud`
			FROM sys_direcciones 
			INNER JOIN `sys_ciudad` ON (`sys_ciudad`.`idciudad`=sys_direcciones.idciudad)
			INNER JOIN `sys_provincia` ON (`sys_provincia`.`idprovincia`=sys_direcciones.idprovincia)
			LEFT JOIN `sys_municipio` ON (`sys_municipio`.`idmunicipio`=sys_direcciones.idmunicipio)
			INNER JOIN `sys_sector` ON (`sys_sector`.`idsector`=sys_direcciones.idsector)
			INNER JOIN `sys_tipos_clasificacion` ON (`sys_tipos_clasificacion`.`id_tipos_clasifica`=sys_direcciones.tipo_direccion)
			 WHERE sys_direcciones.status=1  ";

		if ($address_id>0){
			$SQL.=" and  sys_direcciones.id_direcciones='".$address_id."'";	
		} 			 
			 
		if ($contact==0){
			$SQL.=" and id_nit='". mysql_escape_string($client_id) ."' and id_contactos=0";	
		}else{
			$SQL.=" and id_contactos='". mysql_escape_string($contact) ."' ";
			
		}
		 
	 
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}
		return $data;
	}
	
	public function updateEstatusAddress($client_id,$id_tipo,$estatus,$contact_id=0,$direccion_id=0){
		if ($this->existClient($client_id)){
			$obj= new ObjectSQL();
			$obj->status=$estatus;		
			
			$SQLdata=" WHERE id_nit='". mysql_escape_string($client_id) ."' and tipo_direccion='".mysql_escape_string($id_tipo)."' ";
			if ($contact_id>0){
				$SQLdata.=" and id_contactos='". mysql_escape_string($contact_id)."'";
			}
			if ($direccion_id>0){
				$SQLdata.=" and id_direcciones='". mysql_escape_string($direccion_id)."'";
			}
			
			$SQL=$obj->getSQL("update","sys_direcciones",$SQLdata);	
			mysql_query($SQL);	
			$this->message['mensaje']=$this->errorList[104];
			$this->message['error']=false;
			$this->message['typeError']="104";		
		}else{
			$this->message['mensaje']=$this->errorList[101];
			$this->message['error']=true;
			$this->message['typeError']="101";
		}	
	}
	
	public function updateEstatusContacto($client_id,$id_tipo,$estatus,$contacto_id){
		if ($this->existClient($client_id)){
			$obj= new ObjectSQL();
			$obj->estatus=$estatus;		
			$SQL=$obj->getSQL("update","sys_contactos"," WHERE id_nit='". mysql_escape_string($client_id) ."' and idtipo_contacto='".mysql_escape_string($id_tipo)."'");	
			
			if ($contacto_id>0){
				$SQL.=" and id_contactos='". mysql_escape_string($contacto_id)."' ";
			}			
			
			mysql_query($SQL);	
			
			$this->message['mensaje']=$this->errorList[104];
			$this->message['error']=false;
			$this->message['typeError']="104";		
		}else{
			$this->message['mensaje']=$this->errorList[101];
			$this->message['error']=true;
			$this->message['typeError']="101";
		}	
	}
	
	/*AGREGA DIRECCION A UNA CLIENTE*/
	public function addAddress($client_id,$contactid=0){
		
		if (count($this->data['provincia_id'])>0){
			
			for($i=0;$i<count($this->data['provincia_id']);$i++){	
				$obj= new ObjectSQL();
				$obj->id_nit=$client_id;
				$obj->idciudad=System::getInstance()->getEncrypt()->decrypt($this->data['cuidad_id'][$i],UserAccess::getSessionID());
				$obj->idprovincia=System::getInstance()->getEncrypt()->decrypt($this->data['provincia_id'][$i],UserAccess::getSessionID());
				$obj->idmunicipio=System::getInstance()->getEncrypt()->decrypt($this->data['municipio_id'][$i],UserAccess::getSessionID());
				$obj->idsector=System::getInstance()->getEncrypt()->decrypt($this->data['sector_id'][$i],UserAccess::getSessionID());
				$obj->tipo_direccion=System::getInstance()->getEncrypt()->decrypt($this->data['direccion_tipo'][$i],UserAccess::getSessionID());
				$obj->status="1";
				$obj->numero=$this->data['direccion_numero'][$i];
				$obj->manzana=$this->data['direccion_manzana'][$i];
				$obj->residencia_colonia_condominio=$this->data['direccion_recidencial'][$i];
				$obj->referencia=$this->data['direccion_referencia'][$i];
				$obj->observaciones=$this->data['direccion_observacion'][$i];
				$obj->avenida=$this->data['direccion_avenida'][$i];
				$obj->calle=$this->data['direccion_calle'][$i];
				$obj->zona=$this->data['direccion_zona'][$i];
				$obj->departamento=$this->data['direccion_departamento'][$i];
				$obj->id_contactos=0;
			 
				$SQL=$obj->getSQL("insert","sys_direcciones");	
				mysql_query($SQL);		
				
				$this->updateStatusProspecto($client_id);
				 
				$this->message['mensaje']=$this->errorList[100];
				$this->message['error']=false;
				$this->message['typeError']="100";		
			}	
		}else{
			$this->message['mensaje']=$this->errorList[103];
			$this->message['error']=true;
			$this->message['typeError']="103";
		}
	}
	
	public function updateStatusProspecto($id_nit){
		SystemHtml::getInstance()->includeClass("prospectos","Prospectos"); 
	
		$prospecto= new Prospectos($this->db_link,$this->data);
		$info=$prospecto->getProspectoDataActivo($id_nit);
		$prospecto->doCreateReportCache($info['pilar_inicial'],
									$info['id_comercial'],
									$info['id_nit'],
									$info['correlativo'],
									$info['pilar_final'],
									$info['ultima_actividad']);  
									
	}
	/* Agrega un contacto al cliente*/
	public function addContacto($client_id){
		if ($this->existClient($client_id)){
			if (count($this->data['contacto_tipo'])>0){
				for($i=0;$i<count($this->data['contacto_tipo']);$i++){	
		
					$obj= new ObjectSQL();
					$obj->idtipo_contacto=System::getInstance()->getEncrypt()->decrypt($this->data['contacto_tipo'][$i],UserAccess::getSessionID());
					$obj->id_nit=$client_id;
					$obj->Nombres=$this->data['contactos_nombre'][$i];
					$obj->Apellidos=$this->data['contactos_apellido'][$i];
					$obj->estatus='1';
					$SQL=$obj->getSQL("insert","sys_contactos");	
					mysql_query($SQL);	
					$id=mysql_insert_id($this->db_link->link_id);

					if ($id>0){
						//$this->addAddress($client_id,$id);
						$this->message['mensaje']=$this->errorList[100];
						$this->message['error']=false;
						$this->message['typeError']="100";	
						$this->message['contact_id']=System::getInstance()->Encrypt($id);
					}else{
						//Error de creacion de contacto
						$this->message['mensaje']=$this->errorList[102];
						$this->message['error']=true;
						$this->message['typeError']="102";	
					}
			
				}
			}
		}else{
			//CLIENTE NO EXISTE	
			$this->message['mensaje']=$this->errorList[105];
			$this->message['error']=true;
			$this->message['typeError']="105";	
		}

	}
	
	public function addPhone($client_id,$contact=0){
 
		if (count($this->data['telefonos_tipo'])>0){
			for($i=0;$i<count($this->data['telefonos_tipo']);$i++){	
				$obj= new ObjectSQL();
				$obj->id_nit=$client_id;
				$obj->tipo_telefono=System::getInstance()->getEncrypt()->decrypt($this->data['telefonos_tipo'][$i],UserAccess::getSessionID());
				$obj->status_telefono="1";
				$obj->area=$this->data['telefono_area'][$i];
				$obj->numero=$this->data['telefonos'][$i];
				$obj->extencion=$this->data['telefono_extension'][$i];
				$obj->id_contactos=0;
				
				$SQL=$obj->getSQL("insert","sys_telefonos");	
				mysql_query($SQL);	
				$this->message['mensaje']=$this->errorList[100];
				$this->message['error']=false;
				$this->message['typeError']="100";				
			}	
		}else{
			$this->message['mensaje']=$this->errorList[103];
			$this->message['error']=true;
			$this->message['typeError']="103";
		}
	}
	
	public function addEmail($client_id,$contact=0){
		if (count($this->data['email_tipo'])>0){
			for($i=0;$i<count($this->data['email_tipo']);$i++){	
				$obj= new ObjectSQL();
				$obj->id_nit=$client_id;
				$obj->tipos_email=System::getInstance()->getEncrypt()->decrypt($this->data['email_tipo'][$i],UserAccess::getSessionID());
				$obj->status="1";
				$obj->direccion=$this->data['email_direccion'][$i];
				$obj->observaciones=$this->data['email_descripcion'][$i];
				$obj->id_contactos=0;
				$SQL=$obj->getSQL("insert","sys_cuentas_emails");	
				mysql_query($SQL);	
				
				$this->message['mensaje']=$this->errorList[100];
				$this->message['error']=false;
				$this->message['typeError']="100";				
			}	
		}else{
			$this->message['mensaje']=$this->errorList[103];
			$this->message['error']=true;
			$this->message['typeError']="103";
		}
	}
	
	public function addReference($client_id){

		if (count($this->data['referencia_tipo'])>0){
			for($i=0;$i<count($this->data['referencia_tipo']);$i++){	
				$obj= new ObjectSQL();
				$obj->id_nit=$client_id;
				$obj->tipo_refencia=System::getInstance()->getEncrypt()->decrypt($this->data['referencia_tipo'][$i],UserAccess::getSessionID());
				$obj->status=1;
				$obj->telefono=$this->data['referencia_telefono_one'][$i];
				$obj->nombre_completo=$this->data['referencia_nombre'][$i];
				$obj->telefono_2=$this->data['referencia_telefono_two'][$i];
				$obj->observaciones=$this->data['referencia_descripcion'][$i];
			
				$SQL=$obj->getSQL("insert","sys_referencia_personales");	
				mysql_query($SQL);	
				
				$this->message['mensaje']=$this->errorList[100];
				$this->message['error']=false;
				$this->message['typeError']="100";					
			}	
		}else{
			$this->message['mensaje']=$this->errorList[103];
			$this->message['error']=true;
			$this->message['typeError']="103";
		}
	}		
	
	public function addReferido($client_id){
 
		$obj= new ObjectSQL();
		$obj->id_nit=$client_id;
		$obj->tipo_refencia=System::getInstance()->Decrypt($this->data['referencia_tipo']);
		$obj->nombre1=$this->data['nombre1'];
		$obj->nombre2=$this->data['nombre2'];
		$obj->apellido1=$this->data['apellido1'];
		$obj->apellido2=$this->data['apellido2'];
		$obj->telefono=$this->data['telefono'];
		$obj->movil=$this->data['movil'];
		$obj->descripcion=$this->data['descripcion'];
		$obj->fecha_ingreso="curdate()";
 
		
		$SQL=$obj->getSQL("insert","referidos");	
		mysql_query($SQL);	
	
		$this->message['mensaje']=$this->errorList[100];
		$this->message['error']=false;
		$this->message['typeError']="100";		
 
	}	

	public function addEmpresa($client_id){
		 
		if ($this->existClient($client_id)){
			 
			
			$obj= new ObjectSQL();
			
			unset($_POST['submit_empresa']);
			unset($_POST['id']);
			$obj->push($_POST);
 			 
			$SQL=$obj->getSQL("update","sys_personas"," where id_nit='".$client_id."'");	
			 
		//	print_r($SQL);
			mysql_query($SQL);	
			
			$this->message['mensaje']=$this->errorList[104];
			$this->message['error']=false;
			$this->message['typeError']="104";					
 
		}else{
			$this->message['mensaje']=$this->errorList[105];
			$this->message['error']=true;
			$this->message['typeError']="105";
		}
	}	
	
	public function addDatosFacturacion($client_id,$FACTURAR__FAC_CLI,$NIT_FAC_CLI,$DIRECCION_FAC_CLI,$EM_ID="",$serie_contrato="",$no_contrato=""){
		 
		if ($this->existClient($client_id)){
			  
			$obj= new ObjectSQL();
			$obj->id_nit=$client_id;
			$obj->EM_ID=$EM_ID;
			$obj->no_contrato=$no_contrato;
			$obj->serie_contrato=$serie_contrato;
			$obj->FACTURAR__FAC_CLI=$FACTURAR__FAC_CLI;
			$obj->NIT_FAC_CLI=$NIT_FAC_CLI;
			$obj->DIRECCION_FAC_CLI=$DIRECCION_FAC_CLI;
 			$obj->setTable('facturacion_cliente');
			$SQL=$obj->toSQL("insert");	
	
			mysql_query($SQL);	 
			 
			$this->message['mensaje']=$this->errorList[100];
			$this->message['error']=false;
			$this->message['typeError']="100";					
 
		}else{
			$this->message['mensaje']=$this->errorList[105];
			$this->message['error']=true;
			$this->message['typeError']="105";
		}
	}	
	
}


?>
