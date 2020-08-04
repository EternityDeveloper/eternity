<?php 
/*MANEJA LOS SERVICIOS PRESTADOS*/
class ServiciosPrestados 
{
	private static $db_link;
	private $_data;
	private $token;
	private static $instance;
	
	public function __construct($db_link=""){
		if ($db_link!=""){
			self::$db_link=$db_link;
			ServiciosPrestados::$instance = $this;
		}
	} 
	
	public static function getInstance(){
		 if (!Actas::$instance instanceof self) {
             Actas::$instance = new Actas();
        }
        return Actas::$instance;
	}
	
	public function setActaDatos($tipo,$periodo){
		$_SESSION['ACTA_DATA']=array("tipo"=>$tipo,"periodo"=>$periodo);
	}
	
	public function getActaDatos(){
		if (!isset($_SESSION['ACTA_DATA'])){
			return array();
		}	
		return $_SESSION['ACTA_DATA'];	
	}
	
	public function getActasAnuladoOrDesistidos($tipo){ 
	
		$SQL="SELECT 
			ct.serie_contrato,
			ct.no_contrato,
			ct.`fecha_ingreso` as fecha_venta,
			ct.precio_neto,
			ct.cuotas,
			ct.valor_cuota,
			sys_personas.id_nit,
			CONCAT(sys_personas.primer_nombre,' ',
			sys_personas.segundo_nombre,' ',
			sys_personas.primer_apellido,' ',
			sys_personas.segundo_apellido) AS nombre_completo,
			(SELECT COUNT(*) AS total FROM `producto_contrato` WHERE producto_contrato.serie_contrato=ct.serie_contrato AND 
producto_contrato.no_contrato=ct.no_contrato) as producto_total,
			(SELECT  
				((ct.`interes`+ct.`capital_pagado`) -(SUM(movimiento_caja.monto)*movimiento_caja.tipo_cambio)) AS MONTO_NETO 
			FROM `movimiento_contrato` 
			INNER JOIN `caja` ON (caja.ID_CAJA=movimiento_contrato.ID_CAJA)
			INNER JOIN `movimiento_caja` ON  (movimiento_caja.`SERIE`=movimiento_contrato.`CAJA_SERIE` 
					AND movimiento_caja.`NO_DOCTO`=movimiento_contrato.`NO_DOCTO`) 
			WHERE 
				movimiento_caja.`NO_CONTRATO`=ct.no_contrato AND movimiento_caja.`SERIE_CONTRATO`=ct.serie_contrato
			AND movimiento_contrato.TIPO_MOV='CUOTA'
			ORDER BY movimiento_contrato.FECHA DESC ) as monto_no_cobrado, 
			(SELECT  
				SUM(movimiento_contrato.NO_CUOTA) AS CUOTAS_PAGAS 
			FROM `movimiento_contrato` 
			INNER JOIN `caja` ON (caja.ID_CAJA=movimiento_contrato.ID_CAJA)
			INNER JOIN `movimiento_caja` ON  (movimiento_caja.`SERIE`=movimiento_contrato.`CAJA_SERIE` 
					AND movimiento_caja.`NO_DOCTO`=movimiento_contrato.`NO_DOCTO`) 
			WHERE 
				movimiento_caja.`NO_CONTRATO`=ct.no_contrato AND movimiento_caja.`SERIE_CONTRATO`=ct.serie_contrato
			AND movimiento_contrato.TIPO_MOV='CUOTA'
			ORDER BY movimiento_contrato.FECHA DESC ) as cuotas_pagas,
			
			(SELECT 
				ROUND((DATEDIFF(CURDATE(),DATE_ADD(MAX(contratos.fecha_ingreso),
				INTERVAL SUM(movimiento_contrato.NO_CUOTA) MONTH))/30)) AS MES_ASTRASO
			FROM `movimiento_contrato` 
			INNER JOIN `caja` ON (caja.ID_CAJA=movimiento_contrato.ID_CAJA)
			INNER JOIN `movimiento_caja` ON  (movimiento_caja.`SERIE`=movimiento_contrato.`CAJA_SERIE` 
					AND movimiento_caja.`NO_DOCTO`=movimiento_contrato.`NO_DOCTO`) 
			INNER JOIN `contratos` ON (`contratos`.serie_contrato=movimiento_caja.`SERIE_CONTRATO` AND 
						  contratos.no_contrato=movimiento_caja.`NO_CONTRATO`)
			WHERE 
				movimiento_caja.`NO_CONTRATO`=ct.no_contrato AND movimiento_caja.`SERIE_CONTRATO`=ct.serie_contrato
			AND movimiento_contrato.TIPO_MOV='CUOTA'
			ORDER BY movimiento_contrato.FECHA DESC) as CUOTAS_EN_ATRASO,
			
			(SELECT 
				GROUP_CONCAT(CONCAT(pc.id_jardin,'-', pc.id_fases,'-', pc.bloque,'-',pc.lote)) AS PROPIEDAD
			 FROM `producto_contrato`  as pc
			WHERE pc.serie_contrato=ct.serie_contrato AND pc.no_contrato=ct.no_contrato) as PROPIEDAD,
			
			(SELECT  
			 CONCAT(OFICIAL.`primer_nombre`,' ',OFICIAL.`segundo_nombre`,' ',OFICIAL.`primer_apellido`,' ',OFICIAL.segundo_apellido) AS nombre_oficial
			FROM cobros_contratos  
			INNER JOIN sys_personas  AS OFICIAL ON (`OFICIAL`.`id_nit`=cobros_contratos.`nit_oficial`)  
			WHERE cobros_contratos.`no_contrato`=ct.no_contrato
			 AND cobros_contratos.`serie_contrato`=ct.serie_contrato) as OFICIAL,
			 
			(SELECT 
			CONCAT(p.`primer_nombre`,' ',p.`segundo_nombre`,' ',p.`primer_apellido`,' ',p.segundo_apellido) AS nombre
			 FROM `sys_asesor`  
			INNER JOIN sys_personas AS p ON (p.id_nit=sys_asesor.id_nit) 
			where sys_asesor.codigo_asesor=ct.codigo_asesor ) as ASESOR,
			
			(SELECT 
			CONCAT(p.`primer_nombre`,' ',p.`segundo_nombre`,' ',p.`primer_apellido`,' ',p.segundo_apellido) AS nombre
			FROM `sys_gerentes_grupos` AS gg
			INNER JOIN sys_personas AS p ON (p.id_nit=gg.id_nit) 
			WHERE gg.`codigo_gerente_grupo`=ct.codigo_gerente) as GERENTEV							 
			
					
		 FROM `contratos` as ct
			INNER JOIN `sys_personas` ON (sys_personas.id_nit=ct.id_nit_cliente)
			WHERE ct.estatus='".$tipo."' ";
		  
		$SQL.=$QUERY;
		$rs=mysql_query($SQL);
		$result=array();   
		while($row=mysql_fetch_assoc($rs)){	 
			$eID=System::getInstance()->Encrypt($row['id_nit']); 
			
			if ($row['CUOTAS_EN_ATRASO']<=0){
				$row['CUOTAS_EN_ATRASO']=0;
			}
			$row['MONTO_PENDIENTE_ATRASO']=$row['valor_cuota']*$row['CUOTAS_EN_ATRASO'];
			array_push($result,$row);
		} 
		
		return $result;
	}
	
	public function getActasCerradaOrPorCerrar($acta_id,$id_estado_actual){ 
		
		$sp=explode("-",$acta_id);
 
		$estatus=0;
		/*SI ES DEL TIPO ANULADO ENTONCES*/
		if ($sp[0]=='ANU'){
			$estatus=26;
			if (($id_estado_actual!=27)){
				$estatus=28;  //Posible a Anular
			} 
		}
		if ($sp[0]=='DES'){
			$estatus=25;
			if (($id_estado_actual!=27)){
				$estatus=24;  //Posibles a desistir en acta
			} 
		}		
		

		$SQL="SELECT 
			ct.serie_contrato,
			ct.no_contrato,
			ct.`fecha_ingreso` as fecha_venta,
			ct.precio_neto,
			ct.cuotas,
			ct.valor_cuota,
			sys_personas.id_nit,
			CONCAT(sys_personas.primer_nombre,' ',
			sys_personas.segundo_nombre,' ',
			sys_personas.primer_apellido,' ',
			sys_personas.segundo_apellido) AS nombre_completo,
			(SELECT COUNT(*) AS total FROM `producto_contrato` WHERE producto_contrato.serie_contrato=ct.serie_contrato AND 
producto_contrato.no_contrato=ct.no_contrato) as producto_total,
			(SELECT  
				((ct.`interes`+ct.`capital_pagado`) -(SUM(movimiento_caja.monto)*movimiento_caja.tipo_cambio)) AS MONTO_NETO 
			FROM `movimiento_contrato` 
			INNER JOIN `caja` ON (caja.ID_CAJA=movimiento_contrato.ID_CAJA)
			INNER JOIN `movimiento_caja` ON  (movimiento_caja.`SERIE`=movimiento_contrato.`CAJA_SERIE` 
					AND movimiento_caja.`NO_DOCTO`=movimiento_contrato.`NO_DOCTO`) 
			WHERE 
				movimiento_caja.`NO_CONTRATO`=ct.no_contrato AND movimiento_caja.`SERIE_CONTRATO`=ct.serie_contrato
			AND movimiento_contrato.TIPO_MOV='CUOTA'
			ORDER BY movimiento_contrato.FECHA DESC ) as monto_no_cobrado,
			
			
			(SELECT  
				SUM(movimiento_contrato.NO_CUOTA) AS CUOTAS_PAGAS 
			FROM `movimiento_contrato` 
			INNER JOIN `caja` ON (caja.ID_CAJA=movimiento_contrato.ID_CAJA)
			INNER JOIN `movimiento_caja` ON  (movimiento_caja.`SERIE`=movimiento_contrato.`CAJA_SERIE` 
					AND movimiento_caja.`NO_DOCTO`=movimiento_contrato.`NO_DOCTO`) 
			WHERE 
				movimiento_caja.`NO_CONTRATO`=ct.no_contrato AND movimiento_caja.`SERIE_CONTRATO`=ct.serie_contrato
			AND movimiento_contrato.TIPO_MOV='CUOTA'
			ORDER BY movimiento_contrato.FECHA DESC ) as cuotas_pagas,
			
			(SELECT 
				ROUND((DATEDIFF(CURDATE(),DATE_ADD(MAX(contratos.fecha_ingreso),
				INTERVAL SUM(movimiento_contrato.NO_CUOTA) MONTH))/30)) AS MES_ASTRASO
			FROM `movimiento_contrato` 
			INNER JOIN `caja` ON (caja.ID_CAJA=movimiento_contrato.ID_CAJA)
			INNER JOIN `movimiento_caja` ON  (movimiento_caja.`SERIE`=movimiento_contrato.`CAJA_SERIE` 
					AND movimiento_caja.`NO_DOCTO`=movimiento_contrato.`NO_DOCTO`) 
			INNER JOIN `contratos` ON (`contratos`.serie_contrato=movimiento_caja.`SERIE_CONTRATO` AND 
						  contratos.no_contrato=movimiento_caja.`NO_CONTRATO`)
			WHERE 
				movimiento_caja.`NO_CONTRATO`=ct.no_contrato AND movimiento_caja.`SERIE_CONTRATO`=ct.serie_contrato
			AND movimiento_contrato.TIPO_MOV='CUOTA'
			ORDER BY movimiento_contrato.FECHA DESC) as CUOTAS_EN_ATRASO,
			
			(SELECT 
				GROUP_CONCAT(CONCAT(pc.id_jardin,'-', pc.id_fases,'-', pc.bloque,'-',pc.lote,'\n')) AS PROPIEDAD
			 FROM `producto_contrato`  as pc
			WHERE pc.serie_contrato=ct.serie_contrato AND pc.no_contrato=ct.no_contrato) as PROPIEDAD,
			
			(SELECT  
			 CONCAT(OFICIAL.`primer_nombre`,' ',OFICIAL.`segundo_nombre`,' ',OFICIAL.`primer_apellido`,' ',OFICIAL.segundo_apellido) AS nombre_oficial
			FROM cobros_contratos  
			INNER JOIN sys_personas  AS OFICIAL ON (`OFICIAL`.`id_nit`=cobros_contratos.`nit_oficial`)  
			WHERE cobros_contratos.`no_contrato`=ct.no_contrato
			 AND cobros_contratos.`serie_contrato`=ct.serie_contrato) as OFICIAL,
			 
			(SELECT 
			CONCAT(p.`primer_nombre`,' ',p.`segundo_nombre`,' ',p.`primer_apellido`,' ',p.segundo_apellido) AS nombre
			 FROM `sys_asesor`  
			INNER JOIN sys_personas AS p ON (p.id_nit=sys_asesor.id_nit) 
			where sys_asesor.codigo_asesor=ct.codigo_asesor ) as ASESOR,
			
			(SELECT 
			CONCAT(p.`primer_nombre`,' ',p.`segundo_nombre`,' ',p.`primer_apellido`,' ',p.segundo_apellido) AS nombre
			FROM `sys_gerentes_grupos` AS gg
			INNER JOIN sys_personas AS p ON (p.id_nit=gg.id_nit) 
			WHERE gg.`codigo_gerente_grupo`=ct.codigo_gerente) as GERENTEV					 
		 FROM `contratos` as ct
			INNER JOIN `actas_desistidos_anulados` as acta ON (acta.serie_contrato=ct.serie_contrato AND
			acta.no_contrato=ct.no_contrato)		 
			INNER JOIN `sys_personas` ON (sys_personas.id_nit=ct.id_nit_cliente)
			WHERE ct.estatus='".$estatus."' and acta.idacta='".$acta_id."'";
		  
		$SQL.=$QUERY;
		$rs=mysql_query($SQL);
		$result=array();   
		while($row=mysql_fetch_assoc($rs)){	 
			$eID=System::getInstance()->Encrypt($row['id_nit']); 
			
			if ($row['CUOTAS_EN_ATRASO']<=0){
				$row['CUOTAS_EN_ATRASO']=0;
			}
			$row['MONTO_PENDIENTE_ATRASO']=$row['valor_cuota']*$row['CUOTAS_EN_ATRASO'];
			array_push($result,$row);
		} 
		
		return $result;
	}
	public function getListadoActasGeneradas($tipo){  
		$SQL="SELECT actas_desistidos_anulados.tipo,
				actas_desistidos_anulados.idacta,
				sys_status.`descripcion` AS estatus,
				sys_status.id_status,
				actas_desistidos_anulados.fecha_ingreso,
				actas_desistidos_anulados.fecha_operacion ,
				(SELECT Nombres FROM `Usuarios` WHERE Usuarios.id_usuario=actas_desistidos_anulados.`usuario_opero` ) AS operado_por
			FROM `actas_desistidos_anulados` 
			INNER JOIN `sys_status` ON (sys_status.id_status=actas_desistidos_anulados.id_status)
			GROUP BY actas_desistidos_anulados.idacta ";  
		$rs=mysql_query($SQL);
		$result=array();   
		while($row=mysql_fetch_assoc($rs)){	 
			$eID=System::getInstance()->Encrypt($row['id_nit']);  
			array_push($result,$row);
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
	
	public function createActa($listado_contrato){
		$inf=array(
				"valid"=>true,
				"mensaje"=>"No se puede completar la operacion debido a que no se han completados todos los cambios obligatorios");
		$tipo_acta=$this->getActaDatos();   
		$user_id=UserAccess::getInstance()->getID();
		
		foreach($listado_contrato as $key =>$row){	
			if ($this->validateIfContractoExitIntoActa($row->serie_contrato,$row->no_contrato,$estatus)>0){
				$inf['valid']=false;
				$inf['mensaje']="El contrato ".$row->serie_contrato.
								" ".$row->no_contrato." no se puede pasar al acta por favor verifique el estatus!";
								
								
				return $inf;
			}
		}
		
		foreach($listado_contrato as $key =>$row){	
			
			$tA="A";
			if ($tipo_acta['tipo']=="DES"){
				$tA="D";
			}
			$perido=explode("-",$tipo_acta['periodo']);
 			$obj= new ObjectSQL();	
			$obj->idacta=$tipo_acta['tipo']."-".$tipo_acta['periodo'];
			$obj->tipo=$tA;
			$obj->id_status=24;
			$obj->EM_ID=$data['oficial_nit'];			
			$obj->no_contrato=$row->no_contrato;
			$obj->serie_contrato=$row->serie_contrato;	
			$obj->mes=$perido[0];	
			$obj->anio=$perido[1];		
			//$obj->fecha_operacion="CONCAT(CURDATE(),' ',CURRENT_TIME())";		
			$obj->comentarios='';		
			$obj->fecha_ingreso="CONCAT(CURDATE(),' ',CURRENT_TIME())";
			$obj->usuario_opero=$user_id;		 
			$obj->setTable("actas_desistidos_anulados");
 			mysql_query($obj->toSQL("insert")); 
			
			/*PROCESO QUE ACTUALIZA LOS CONTRATOS PARA PONERLOS EN EL ACTA*/
			$obj= new ObjectSQL();	
			$obj->estatus=24; //POSIBLES A DESSISTIR EN EL ACTA		 
			$obj->setTable("contratos"); 
 			mysql_query($obj->toSQL("update"," where no_contrato='".$row->no_contrato."'
										 and serie_contrato='".$row->serie_contrato."' ")); 			 
 
		}
		$inf['mensaje']=true;
		$inf['mensaje']="Acta creada";
		 
		return $inf;
	}
	/*VALIDA SI UN CONTRATO ESTA EN EL ACTA DE POSIBLE DESISTIR*/
	public function validateIfContractoExitIntoActa($serie_contrato,$no_contrato,$estatus){
		$SQL="SELECT COUNT(*) AS TOTAL FROM contratos WHERE estatus='".$estatus."'
								 AND serie_contrato='".$serie_contrato."' AND no_contrato='".$no_contrato."'";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_array($rs);
		return $row['TOTAL'];
	}
	public function restart(){
		$_SESSION['ACTA_DATA_DOC']=array();
	}
	/*CARGA LA DOCUMENTACION DE UNA ACTA*/
	public function addDocument($id_acta,$document){
	
		$allowed = array('png', 'jpg', 'gif','zip','pdf','docx','doc');
		
		if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0){ 
			$extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION); 
			if(!in_array(strtolower($extension), $allowed)){
				echo '{"status":"error"}';
				exit;
			}  
			$upload_dir="temp_uploads/";
			if (!is_dir($upload_dir.$dir)) {
				mkdir($upload_dir.$dir);         
			}
			if(move_uploaded_file($_FILES['upl']['tmp_name'],$upload_dir.$dir."/".$_FILES['upl']['name'])){
				
				$data=array(
					"idacta"=>$id_acta, 
					"temp_path"=>$upload_dir.$dir."/".$_FILES['upl']['name'],
					"extension"=>$extension
				); 
				
				array_push($_SESSION['ACTA_DATA_DOC'],$data);
				echo '{"status":"success"}';
				exit;
			}else{
				echo '{"status":"fail"}';
				exit;
			}
		}		
	}
	/*VALIDA SI HAN SUBIDO EL ARCHIVO DEL ACTA*/
	public function validate_upload(){ 
		$err=array("mensaje"=>"Debe de cargar el acta firmada para poder continuar","valid"=>false);
		if (count($_SESSION['ACTA_DATA_DOC'])>0){
			if (isset($_SESSION['ACTA_DATA_DOC'][0]['temp_path'])){  
				if (file_exists($_SESSION['ACTA_DATA_DOC'][0]['temp_path'])){  
					$err['valid']=true;
					$err['mensaje']='';
				}
			}
		} 
		return $err;
	}
	/*PROCESO QUE GENERA EL ACTA */
	public function generarActa($id_acta,$descripcion=""){
		$return=array("mensaje"=>"Error datos incompletos","valid"=>false);
		  
		if (count($_SESSION['ACTA_DATA_DOC'])>0){
			//print_r($_SESSION['ACTA_DATA_DOC'][0]['idacta']);  
			if(isset($_SESSION['ACTA_DATA_DOC'][0]['idacta'])){
			
				if (file_exists($_SESSION['ACTA_DATA_DOC'][0]['temp_path'])){ 
					 
					$acta=$_SESSION['ACTA_DATA_DOC'][0];
				 
					$name=$acta['idacta']; 
					$upload_dir="up_loads_contratos/"; 
					$dir_acta="ACTAS/";
					if (!is_dir($upload_dir.$dir_acta)) {
						mkdir($upload_dir.$dir_acta); 
					}
					$upload_dir.=$dir_acta;
					
					if (!is_dir($upload_dir.$name)) {
						mkdir($upload_dir.$name);         
					}	  
					if (file_exists($acta['temp_path'])){
					 
 						$path=$upload_dir.$name."/".$name.".".$acta['extension']; 
						copy($acta['temp_path'],$path);
						unlink($acta['temp_path']); 
						$sp_ac=explode("-",$acta['idacta']);
						
						$obj= new ObjectSQL();
						$obj->idacta=$acta['idacta'];
						$obj->tipo=$sp_ac[0];
						$obj->mes=$sp_ac[1];
						$obj->anio=$sp_ac[2];
						$obj->fecha_creacion="CONCAT(CURDATE(),' ',CURRENT_TIME())";
						$obj->comentarios=$descripcion;
						$obj->usuario_opero=$descripcion;
						$obj->path=$path;
						$obj->setTable("actas_documentos");
						$SQL=$obj->toSQL("insert"); 
						mysql_query($SQL);  
						
						/*CAMBIO EL ESTATUS DE LA ACTA*/
						$obj= new ObjectSQL();
						$obj->id_status=27; //Acta generada
						$obj->fecha_operacion="CURDATE()"; //Acta generada
						$obj->setTable("actas_desistidos_anulados");
						$SQL=$obj->toSQL("update"," WHERE idacta='".$acta['idacta']."'"); 
						mysql_query($SQL);  
						
						
						/*ACTUALIZO LOS CONTRATOS QUE ESTAN EL ACTA*/
						$SQL="SELECT * FROM `actas_desistidos_anulados` WHERE `idacta`='".$acta['idacta']."' ";
						$rs=mysql_query($SQL);
						while($row=mysql_fetch_array($rs)){
							$obj= new ObjectSQL();
							if ($sp_ac[0]=="D"){
								$obj->estatus=25; //DESISTIDOS
							}else{
								$obj->estatus=26; //ANULADO
							}
							$obj->setTable("contratos");
							$SQL=$obj->toSQL("update"," WHERE `serie_contrato`='".$row['serie_contrato']."' AND 
									`no_contrato`='".$row['no_contrato']."'"); 
							mysql_query($SQL);  	
						} 
						
						$return['mensaje']="Acta cerrada";
						$return['valid']=true;
					}
				}
			}	
		}
		 
		return $return;
	}	
	public function saveComentaryActa($id_acta,$serie_contrato,$no_contrato,$comentario){
		$return=array("mensaje"=>"Datos guardados","valid"=>true);
		$obj= new ObjectSQL(); 
		$obj->comentarios=$comentario;
		$obj->setTable("actas_desistidos_anulados");
		$SQL=$obj->toSQL("update"," WHERE idacta='".$id_acta."' and 
									serie_contrato='".$serie_contrato."' and
									no_contrato='".$no_contrato."'"); 
									
		mysql_query($SQL);  
		return $return;		
	}
	/*DESCARGA EL ACTA QUE HA SIDO FIRMADA AL CERRARCE*/
	public function downloadActa($id_acta){
		$SQL="SELECT * FROM `actas_documentos` WHERE `idacta`='".$id_acta."' ";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){  
			$ext = pathinfo($row['path'], PATHINFO_EXTENSION); 
			$name=$row['idacta'].".".$ext; 
			header('Content-type: application/pdf'); 
			//header('Content-Disposition: attachment; filename='.$name);
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: '.filesize($row['path']));
			readfile($row['path']);  
		}
	}
}

?>