<?php 
/*MANEJA LAS ACTAS DESISTIDOS Y ANULADOS*/
class Actas{
	private static $db_link;
	private $_data;
	private $token;
	private static $instance;
	
	public function __construct($db_link=""){
		if ($db_link!=""){
			self::$db_link=$db_link;
			Actas::$instance = $this;
		}
	} 
	
	public static function getInstance(){
		 if (!Actas::$instance instanceof self) {
             Actas::$instance = new Actas();
        }
        return Actas::$instance;
	}
	/* AGREGAR AL CARRITO DE LAS ACTAS */
	public function agregar_pull_acta($acta,$contrato){ 
		$return=array("mensaje"=>"Error no agregado","valid"=>false);
		$SQL="SELECT COUNT(*) AS total FROM `actas` WHERE id='".$acta->id."'";   
		$rs=mysql_query($SQL);
		$result=array();   
		$row=mysql_fetch_assoc($rs); 
		$id=$acta->idacta.$acta->secuencia;
	 
		if ($row['total']>0){ 
			$data=STCSession::GI()->isSubmit($id);
			if (!is_array($data)){
				$data=array();
			} 
			$data[$contrato->serie_contrato.$contrato->no_contrato]=$contrato;
			STCSession::GI()->setSubmit($id,$data); 
			$return['valid']=true;
			$return['mensaje']="Agregado";
		}
		
		return $return;
	}
	/* AGREGAR AL CARRITO DE LAS ACTAS */
	public function remove_pull_acta($acta,$contrato){ 
		$return=array("mensaje"=>"Error no agregado","valid"=>false);
		$SQL="SELECT COUNT(*) AS total FROM `actas` WHERE id='".$acta->id."'";   
		$rs=mysql_query($SQL);
		$result=array();   
		$row=mysql_fetch_assoc($rs); 
		$id=$acta->idacta.$acta->secuencia;
		if ($row['total']>0){ 
			$data=STCSession::GI()->isSubmit($id);
			if (!is_array($data)){
				$data=array();
			} 
			unset($data[$contrato->serie_contrato.$contrato->no_contrato]);
			STCSession::GI()->setSubmit($id,$data); 
			$return['valid']=true;
			$return['mensaje']="Removido"; 
		}
		
		return $return;
	}	
	public function agregar_acta($periodo){
		$return=array("mensaje"=>"Datos guardados","valid"=>true);
		$SQL="SELECT COUNT(*)+1 AS total FROM `actas` WHERE `idacta`='".$periodo."'";   
		$rs=mysql_query($SQL);
		$result=array();   
		$row=mysql_fetch_assoc($rs); 
		$obj= new ObjectSQL(); 
		$obj->idacta=$periodo;
		$obj->secuencia=$row['total'];
		$obj->creado_por=UserAccess::getInstance()->getIDNIT();
		$obj->fecha_creacion="CONCAT(CURDATE(),' ',CURRENT_TIME())";
 		$obj->setTable("actas");
		$SQL=$obj->toSQL("insert");   
		mysql_query($SQL);  
		return $return;		
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
			ct.`fecha_venta` as fecha_venta,
			((ct.precio_lista-ct.monto_capitalizado) - ct.descuento) * ct.tipo_cambio AS precio_neto,
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
			
			(SELECT GROUP_CONCAT((CASE 
		 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
		 WHEN pc.id_jardin!='' THEN 
			CONCAT(pc.id_jardin,'-', pc.bloque,'-',pc.lote)
		  END )) 
		 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
		  pc.serie_contrato=ct.serie_contrato AND pc.no_contrato=ct.no_contrato) as PROPIEDAD,
			
			(SELECT  
			 CONCAT(OFICIAL.`primer_nombre`,' ',OFICIAL.`segundo_nombre`,' ',OFICIAL.`primer_apellido`,' ',OFICIAL.segundo_apellido) AS nombre_oficial
			FROM cobros_contratos  
			INNER JOIN sys_personas  AS OFICIAL ON (`OFICIAL`.`id_nit`=cobros_contratos.`nit_oficial`)  
			WHERE cobros_contratos.`no_contrato`=ct.no_contrato
			 AND cobros_contratos.`serie_contrato`=ct.serie_contrato LIMIT 1) as OFICIAL,
			 
			(SELECT 
			CONCAT(p.`primer_nombre`,' ',p.`segundo_nombre`,' ',p.`primer_apellido`,' ',p.segundo_apellido) AS nombre
			 FROM `sys_asesor`  
			INNER JOIN sys_personas AS p ON (p.id_nit=sys_asesor.id_nit) 
			where sys_asesor.codigo_asesor=ct.codigo_asesor LIMIT 1) as ASESOR,
			
			(SELECT 
			CONCAT(p.`primer_nombre`,' ',p.`segundo_nombre`,' ',p.`primer_apellido`,' ',p.segundo_apellido) AS nombre
			FROM `sys_gerentes_grupos` AS gg
			INNER JOIN sys_personas AS p ON (p.id_nit=gg.id_nit) 
			WHERE gg.`codigo_gerente_grupo`=ct.codigo_gerente) as GERENTEV							 
			
		 FROM `contratos` as ct
			INNER JOIN `sys_personas` ON (sys_personas.id_nit=ct.id_nit_cliente)
			WHERE ct.estatus='".$tipo."'  
				and concat(ct.serie_contrato,ct.no_contrato) not in (SELECT CONCAT(serie_contrato,no_contrato) FROM 
`actas_desistidos_anulados` WHERE id_status in (23,28,26,25) and id_status!=60  ) ";
		 
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

	public function getListadoDetalle($acta){ 
	
		$SQL="SELECT 
			ct.serie_contrato,
			ct.no_contrato,
			ct.`fecha_venta` as fecha_venta,
			((ct.precio_lista-ct.monto_capitalizado) - ct.descuento) * ct.tipo_cambio AS precio_neto,
			ct.cuotas,
			ct.valor_cuota,
			sys_personas.id_nit,
			ad.tipo,
			CONCAT(sys_personas.primer_nombre,' ',
			sys_personas.segundo_nombre,' ',
			sys_personas.primer_apellido,' ',
			sys_personas.segundo_apellido) AS nombre_completo,
			(SELECT COUNT(*) AS total FROM `producto_contrato` WHERE producto_contrato.serie_contrato=ct.serie_contrato AND 
producto_contrato.no_contrato=ct.no_contrato) as producto_total,
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
			
			(SELECT GROUP_CONCAT((CASE 
		 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
		 WHEN pc.id_jardin!='' THEN 
			CONCAT(pc.id_jardin,'-', pc.bloque,'-',pc.lote)
		  END )) 
		 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
		  pc.serie_contrato=ct.serie_contrato AND pc.no_contrato=ct.no_contrato) as PROPIEDAD,
			
			(SELECT  
			 CONCAT(OFICIAL.`primer_nombre`,' ',OFICIAL.`segundo_nombre`,' ',OFICIAL.`primer_apellido`,' ',OFICIAL.segundo_apellido) AS nombre_oficial
			FROM cobros_contratos  
			INNER JOIN sys_personas  AS OFICIAL ON (`OFICIAL`.`id_nit`=cobros_contratos.`nit_oficial`)  
			WHERE cobros_contratos.`no_contrato`=ct.no_contrato
			 AND cobros_contratos.`serie_contrato`=ct.serie_contrato LIMIT 1) as OFICIAL,
			 
			(SELECT 
			CONCAT(p.`primer_nombre`,' ',p.`segundo_nombre`,' ',p.`primer_apellido`,' ',p.segundo_apellido) AS nombre
			 FROM `sys_asesor`  
			INNER JOIN sys_personas AS p ON (p.id_nit=sys_asesor.id_nit) 
			where sys_asesor.codigo_asesor=ct.codigo_asesor LIMIT 1) as ASESOR,
			
			(SELECT 
			CONCAT(p.`primer_nombre`,' ',p.`segundo_nombre`,' ',p.`primer_apellido`,' ',p.segundo_apellido) AS nombre
			FROM `sys_gerentes_grupos` AS gg
			INNER JOIN sys_personas AS p ON (p.id_nit=gg.id_nit) 
			WHERE gg.`codigo_gerente_grupo`=ct.codigo_gerente) as GERENTEV							 
			
		 FROM `contratos` as ct
			INNER JOIN `sys_personas` ON (sys_personas.id_nit=ct.id_nit_cliente)
			INNER JOIN actas_desistidos_anulados as ad on (ad.serie_contrato=ct.serie_contrato and
				ad.no_contrato=ct.no_contrato)
			WHERE ad.acta_id='".$acta->id."'  
			and ad.id_status!=60 ";
		 
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
	
	public function getActasCerradaOrPorCerrar($acta_tipo,$acta_id,$id_estado_actual){ 
		  		
               /*  $registro = new ObjectSQL();
                $registro->texto="si entra";
                $registro->SetTable("prueba");
                $mSQL=$registro->toSQL("insert");
                mysql_query($mSQL);  */
           
		SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
	        $id_estado_actual=28;	
		$SQL="SELECT 
			ct.serie_contrato,
			ct.no_contrato,
			ct.`fecha_venta` as fecha_venta,
			ct.precio_neto,
			ct.cuotas,
			ct.valor_cuota,
			sys_personas.id_nit,
			CONCAT(sys_personas.primer_nombre,' ',
			sys_personas.segundo_nombre,' ',
			sys_personas.primer_apellido,' ',
			sys_personas.segundo_apellido) AS nombre_completo,
			acta.*
		 FROM `contratos` as ct
			INNER JOIN `actas_desistidos_anulados` as acta ON (acta.serie_contrato=ct.serie_contrato AND
		acta.no_contrato=ct.no_contrato)		 
		INNER JOIN `sys_personas` ON (sys_personas.id_nit=ct.id_nit_cliente)
		WHERE acta.id_status='".$id_estado_actual."' and acta.idacta='".$acta_id."'";
		 
		$SQL.=$QUERY;

                // print_r($SQL);
                //echo $SQL;

              /*  $registro = new ObjectSQL();
                $registro->texto="si entra";
                $registro->SetTable("prueba");
                $mSQL=$registro->toSQL("insert");
                mysql_query($mSQL); */

		$rs=mysql_query($SQL);
		$result=array();   
		
		$_ct= new Contratos($this->db_link,array());
		
		while($row=mysql_fetch_assoc($rs)){	 
			$eID=System::getInstance()->Encrypt($row['id_nit']); 
		 
			array_push($result,$row);
		} 
		
		return $result;
	}
	
	public function getListadoActasGeneradas(){  
		$SQL="SELECT actas.*,
				sys_status.descripcion AS estatus,
				(SELECT COUNT(*) AS total FROM 
				`actas_desistidos_anulados` WHERE 
				`idacta`=actas.id 
				 AND id_status!=60 ) AS total,
				 sys_status.id_status
				FROM `actas` 
				INNER JOIN `sys_status` ON (sys_status.id_status=actas.estatus)
				 ";   
		$rs=mysql_query($SQL);
		$result=array();   
		while($row=mysql_fetch_assoc($rs)){	  
			array_push($result,$row);
		}  
		return $result;
	} 
	
	/*Cerrar el acta */
	public function doCerrarActa($acta,$comentarios){
		SystemHtml::getInstance()->includeClass("planillas","DescuentoComision");
		 
		$inf=array(
				"valid"=>true,
				"mensaje"=>"No se puede completar la operacion debido a que no se han 
				completados todos los cambios obligatorios");
 
		$user_id=UserAccess::getInstance()->getID();

		$SQL="SELECT COUNT(*) AS total FROM `actas` WHERE id='".$acta->id."' ";   
		$rs=mysql_query($SQL);
		$result=array();   
		$row=mysql_fetch_assoc($rs);  
		if ($row['total']>0){   
 
			$obj= new ObjectSQL();	 
			$obj->estatus=27;
			$obj->fecha_cierre="CONCAT(CURDATE(),' ',CURRENT_TIME())";
			$obj->cerrado_por=UserAccess::getInstance()->getIDNIT();	
			$obj->comentarios=$comentarios;	 
			$obj->setTable("actas");
			$SQL=$obj->toSQL("update"," where id='".$acta->id."'");   
			mysql_query($SQL);			
 
			$SQL="SELECT aa.*,cc.id_nit_cliente
					 FROM `actas_desistidos_anulados` AS aa 
					INNER JOIN contratos AS cc ON (cc.serie_contrato=aa.serie_contrato AND cc.no_contrato = aa.no_contrato)
					WHERE aa.acta_id='".$acta->id."' ";   
			$rs=mysql_query($SQL);
			while($row=mysql_fetch_assoc($rs)){
				
				$obj= new ObjectSQL();	 
				$obj->id_status=$row['tipo']=="D"?25:26;
				$obj->fecha_operacion="CONCAT(CURDATE(),' ',CURRENT_TIME())";
				$obj->usuario_opero=UserAccess::getInstance()->getIDNIT();	
				$obj->comentarios=$comentarios;	 
				$obj->setTable("actas_desistidos_anulados");
				$SQL=$obj->toSQL("update"," where  serie_contrato='".$row['serie_contrato']."' and
						no_contrato='".$row['no_contrato']."' and acta_id='".$acta->id."' ");   
				mysql_query($SQL);
								
				$obj= new ObjectSQL();	 
				$obj->estatus=$row['tipo']=="D"?25:26;
				$obj->setTable("contratos");
				$SQL=$obj->toSQL("update"," where  serie_contrato='".$row['serie_contrato']."' and
						no_contrato='".$row['no_contrato']."' ");  
				mysql_query($SQL);
				
				$obj= new ObjectSQL(); 
				$obj->fecha="CONCAT(CURDATE(),' ',CURRENT_TIME())";
 				$obj->id_nit_cliente=$row['id_nit_cliente'];
				$obj->no_contrato=$row['no_contrato'];
				$obj->serie_contrato=$row['serie_contrato']; 	
				$obj->observaciones="";	
				$obj->comentario_cliente=$comentarios;
				$obj->idaccion='CMRIO'; 	
				$obj->estatus=18; 
				$obj->oficial_cobro=UserAccess::getInstance()->getIDNIT(); 
				$obj->setTable('labor_cobro');
				$SQL=$obj->toSQL("insert");  
				mysql_query($SQL);						
					
			} 
			
			/*AL CERRAR EL ACTA*/
			$comi= new DescuentoComision(self::$db_link);  
			$detalle=$comi->debitarActaAsesor($acta->id, 
									 UserAccess::getInstance()->getIDNIT()); 

			$inf=$detalle;
		}
		return $inf;
	}	
	public function remover_contratos_al_acta($acta,$ct,$comentarios){
		$inf=array(
				"valid"=>true,
				"mensaje"=>"No se puede completar la operacion debido a que no se han 
				completados todos los cambios obligatorios");
 
		$user_id=UserAccess::getInstance()->getID();
  
		$obj= new ObjectSQL();	
		$obj->id_status=60;
		$obj->fecha_operacion="CONCAT(CURDATE(),' ',CURRENT_TIME())";
		$obj->usuario_opero=UserAccess::getInstance()->getIDNIT();	
		$obj->comentarios=$comentarios;	 
		$obj->setTable("actas_desistidos_anulados");
		$SQL=$obj->toSQL("update"," where  serie_contrato='".$ct->serie_contrato."' and
				no_contrato='".$ct->no_contrato."' and idacta='".$acta->idacta."' and 
				secuencia='".$acta->secuencia."'"); 
		 
		mysql_query($SQL); 
 
		$inf['mensaje']=true;
		$inf['mensaje']="Contrato removido!";
		 
		return $inf;
	}	 
	public function agregar_contratos_al_acta($tipo,$acta){
		$inf=array(
				"valid"=>true,
				"mensaje"=>"No se puede completar la operacion debido a que no se han 
				completados todos los cambios obligatorios");
 
		$user_id=UserAccess::getInstance()->getID();
 
		
		$id=$acta->idacta.$acta->secuencia;
		
		$data=STCSession::GI()->isSubmit($id);	
		  
		if (count($data)<=0){ 
			return $inf;
		} 
		foreach($data as $key =>$row){	 
		
			$_tipo=28; //Posible a anular		
			$motivo="ANULACION";		
			if ($tipo=="D"){
				$_tipo=23; //Posible a desistir				 
				$motivo="DESESTIMIENTO";
			} 	
	 		 
			$perido=explode("-",$acta->idacta);
 			$obj= new ObjectSQL();	
			$obj->acta_id=$acta->id;	
			$obj->tipo=$tipo;
			$obj->id_status=$_tipo;
 			$obj->no_contrato=$row->no_contrato;
			$obj->serie_contrato=$row->serie_contrato;
                        $obj->EM_ID=$row->EM_ID;	
 			$obj->motivo=$motivo;
			$obj->mes=$perido[0];	
			$obj->anio=$perido[1];		
  			$obj->fecha_creacion="CONCAT(CURDATE(),' ',CURRENT_TIME())";
			$obj->creado_por=UserAccess::getInstance()->getIDNIT();		 
			$obj->setTable("actas_desistidos_anulados");
			$SQL=$obj->toSQL("insert");

                      /*   $registro = new ObjectSQL();
                        $registro->texto = $SQL;
                        $registro->SetTable("prueba");
                        $mSQL=$registro->toSQL("insert");
                        mysql_query($mSQL); */   
	 
 			mysql_query($SQL);  	 

		}
		$inf['mensaje']=true;
		$inf['mensaje']="Contrato agregado!";
		 
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
		$_SESSION['STC_CONTTROLER']=array(); 
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
			if(move_uploaded_file($_FILES['upl']['tmp_name'],$upload_dir.$dir.$_FILES['upl']['name'])){
				
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
						
										
						SystemHtml::getInstance()->includeClass("contratos","Contratos");  
						$con=new Contratos($this->db_link);

						$cn=$con->getDetalleGeneralFromContrato($row['serie_contrato'],$row['no_contrato']);						
						/*ACTUALIZO LOS CONTRATOS QUE ESTAN EL ACTA*/
						$SQL="SELECT * FROM `actas_desistidos_anulados` WHERE `idacta`='".$acta['idacta']."' ";
						$rs=mysql_query($SQL);
						while($row=mysql_fetch_assoc($rs)){
							$nestatus="";
							$lb_estatus='ANUL';
							$obj= new ObjectSQL();
							if ($sp_ac[0]=="DES"){
								$obj->estatus=25; //DESISTIDOS
								$nestatus="DESESTIMIENTO";		
								$lb_estatus='DESES';
							}else{
								$obj->estatus=26; //ANULADO
								$nestatus="ANULACION";	
								$lb_estatus='ANUL';																							
							}

							$obj->setTable("contratos");
							$SQL=$obj->toSQL("update"," WHERE `serie_contrato`='".$row['serie_contrato']."' AND 
									`no_contrato`='".$row['no_contrato']."'"); 
					 		mysql_query($SQL);
							
							

							/*INSERTO LA LABOR DE COBRO*/
							$obj= new ObjectSQL(); 
							$obj->fecha="CONCAT(CURDATE(),' ',CURRENT_TIME())";
							$obj->no_contrato=$row['no_contrato'];
							$obj->serie_contrato=$row['serie_contrato'];
							$obj->observaciones=$nestatus." DE CONTRATO ".$row['serie_contrato']." ".$row['no_contrato']." ACTA NO.".$acta['idacta'];
							$obj->comentario_cliente=$nestatus." DE CONTRATO ".$row['serie_contrato']." ".$row['no_contrato']." ACTA NO.".$acta['idacta'];
							$obj->idaccion=$lb_estatus; 
							$obj->estatus=18; 
							$obj->oficial_cobro=UserAccess::getInstance()->getIDNIT();
							$obj->id_nit_cliente=$cn['id_nit_cliente'];
							$obj->setTable('labor_cobro');
							$SQL=$obj->toSQL("insert");  
							mysql_query($SQL);							
							
							
							SysLog::getInstance()->Log($cn['id_nit_cliente'], 
													 $row['serie_contrato'],
													 $row['no_contrato'],
													  '',
													 '',
													 $nestatus." DE CONTRATO ".$row['serie_contrato']." ".
													 	$row['no_contrato'],
													 json_encode($cn),
													 'ANULACION');			
							/*ANULO O DESISTO LOS PRODUCTOS Y LOS LIBERO PARA SU UTILIZACION*/							
							$con->AnularOrDesistirProductos($row['serie_contrato'],$row['no_contrato'],$obj->estatus);											  						
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
