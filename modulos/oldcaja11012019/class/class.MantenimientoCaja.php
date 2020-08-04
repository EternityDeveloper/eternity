<?php 
/*
Clase que maneja el mantenimiento de una caja

*/
class MantenimientoCaja{
	private $db_link;
	private $_data;
	
	public function __construct($db_link){
		$this->db_link=$db_link;
	}
	
	public function getListCaja($search=null){
		$search=mysql_real_escape_string($search); 

		$SQL="SELECT 
			caja.`ID_CAJA` AS id_caja,
			caja.`DESCRIPCION_CAJA` AS descripcion,
			caja.`IP_CAJA` AS ip,
			caja.`INICIAL_CAJA` AS monto_inicial,
			CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`segundo_nombre`,' ',sys_personas.`primer_apellido`,' ',sys_personas.`segundo_apellido`) AS cajero,
			Usuarios.id_usuario
			 FROM caja
			INNER JOIN `Usuarios` ON (Usuarios.id_usuario=caja.`id_usuario`)
			INNER JOIN `sys_personas` ON (sys_personas.id_nit=Usuarios.`id_nit`) ";
		$rs=mysql_query($SQL);
		 
		$data=array(
				'sEcho'=>$_REQUEST['sEcho'],
				'iTotalRecords'=>10,
				'iTotalDisplayRecords'=>mysql_num_rows($rs),
				'aaData' =>array()
			);
		while($row=mysql_fetch_assoc($rs)){
			$row['enc_id_caja']=System::getInstance()->Encrypt(json_encode($row));
			$row['settings']='<a href="#" id="'.$row['enc_id_caja'].'" class="edit_mante_caja"><img src="images/subtract_from_cart.png"  /></a>'; 
			array_push($data['aaData'],$row); 
		}
		
		return $data;
	}
		
	public function validateCajaExist($caja){
		$SQL="SELECT COUNT(*) AS TOTAL FROM caja where ID_CAJA='". mysql_real_escape_string($caja) ."'";
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		return $row['TOTAL'];
	} 
	
	public function getListUsuarios(){
		$searchTerm = $_REQUEST['searchTerm'];
		if(!$sidx) $sidx =1;
		if ($searchTerm=="") {
			$searchTerm="%";
		} else {
			$searchTerm = "%" . mysql_real_escape_string($searchTerm) . "%";
		}
		$SQL="SELECT Usuarios.id_usuario,sys_personas.id_nit,CONCAT(primer_nombre, ' ',segundo_nombre,' ',primer_apellido,' ',segundo_apellido) AS nombre 
			FROM `sys_personas`
			INNER JOIN Usuarios ON (Usuarios.id_nit=sys_personas.id_nit)
		 WHERE (
		 CONCAT(primer_nombre, ' ',segundo_nombre,' ',primer_apellido,' ',segundo_apellido) like '".$searchTerm."' OR
		 CONCAT(primer_nombre,' ',primer_apellido) like '".$searchTerm."' OR
		 CONCAT(primer_apellido) like '".$searchTerm."' or CONCAT(Usuarios.id_usuario) like '".$searchTerm."') limit 20 ";
  
 
		$rs=mysql_query($SQL); 		
		$total=mysql_num_rows($rs);
		$i=0;
		$response->page = $total;
		$response->total = $total;
		$response->records = $total; 
		
		while($row = mysql_fetch_array($rs)) { 
			$response->rows[$i]['value']=System::getInstance()->Encrypt(json_encode($row));
			$response->rows[$i]['nombre']=$row['nombre']; 
			//$response->rows[$i]=array($row[id],$row[invdate],$row[name],$row[amount],$row[tax],$row[total],$row[note]);
			$i++;
		} 
		
		return $response;
	}
	
	public function createCaja($_DATA){
		$data=array("error"=>true,"mensaje"=>'La información proporcionada no esta completa!');
		$usuario=json_decode(System::getInstance()->Decrypt($_DATA["id_cajero"]));
		
		$ob=new ObjectSQL();	
		$ob->ID_CAJA=$_DATA["id_caja"];
		$ob->DESCRIPCION_CAJA=$_DATA["descripcion"];
		$ob->id_usuario=$usuario->id_usuario;
		$ob->IP_CAJA=$_DATA["ip_caja"];
		$ob->INICIAL_CAJA=$_DATA["monto_inicial"]; 
		$ob->setTable("caja");
		$SQL=$ob->toSQL('insert');
		mysql_query($SQL); 
		$data['error']=false;
		$data['mensaje']='Registro agregado!';
		return $data;
	}	
	
	public function editCaja($_DATA){
		$data=array("error"=>true,"mensaje"=>'La información proporcionada no esta completa!');
		$usuario=json_decode(System::getInstance()->Decrypt($_DATA["id_cajero"]));
		$id_caja=System::getInstance()->Decrypt($_DATA["id_caja"]);
 
		$ob=new ObjectSQL();	
		//$ob->ID_CAJA=$_DATA["id_caja"];
		$ob->DESCRIPCION_CAJA=$_DATA["descripcion"];
		$ob->id_usuario=$usuario->id_usuario;
		$ob->IP_CAJA=$_DATA["ip_caja"];
		$ob->INICIAL_CAJA=$_DATA["monto_inicial"]; 
		$ob->setTable("caja");
		$SQL=$ob->toSQL('update',"where ID_CAJA='".$id_caja."'");
		mysql_query($SQL); 
		
		/*CARGO TODOS LOS MOVIMIENTOS DE UNA CAJA X*/
		$mov=$this->getTipoMovimiento($id_caja);
		/*EVALUO QUE TENGA REGISTRO*/
		if (count($mov)>0){
			$mov_list=array();
			/*Deshabilito todos los tipos de movimiento de la caja x*/
			$this->disableTipoMovimiento($id_caja);

			foreach($mov as $key =>$val){
				$tmov=$val['TIPO_MOV'];//trim(System::getInstance()->Decrypt($val)); 
				$mov_list[$tmov]=$tmov;
			} 
			/*RECORRO LOS MOVIMIENTOS QUE YA HAN SIDO AGREGADOS*/
			foreach($_DATA['tipo_movimiento'] as $key =>$val){
				$tmov=trim(System::getInstance()->Decrypt($val)); 
				/*VALIDO SI EL TIPO DE MOVIMIENTO EXISTE*/
				if (isset($mov_list[$tmov])){ 
					/*SI EXISTE LO HABILITO*/ 
					$this->changeEstatusTipoMovimiento($id_caja,$tmov);
				}else{
					/*SI NO EXISTE LO AGREGO*/ 
					$dtmov= new ObjectSQL();
					$dtmov->CAJA_TIPO_MOV_CAJA=$tmov;
					$dtmov->TIPO_MOV=$tmov;
					$dtmov->CAJA_ID_CAJA=$id_caja;
					$dtmov->setTable("tipo_mov_caja");
					$SQL=$dtmov->toSQL('insert');
					mysql_query($SQL);	
				}
				 
			}	
		 
		}else{
			/*EN CASO QUE NO TENGA REGISTROS INSERTO*/
			foreach($_DATA['tipo_movimiento'] as $key =>$val){
				$tmov=trim(System::getInstance()->Decrypt($val)); 
				$dtmov= new ObjectSQL();
				$dtmov->CAJA_TIPO_MOV_CAJA=$tmov;
				$dtmov->TIPO_MOV=$tmov;
				$dtmov->CAJA_ID_CAJA=$id_caja;
				$dtmov->setTable("tipo_mov_caja");
				$SQL=$dtmov->toSQL('insert');
				mysql_query($SQL);	
			} 
		} 
		
		
		$T_DOC=$this->getTipoDocumento($id_caja);

		/*EVALUO QUE TENGA REGISTRO*/
		if (count($T_DOC)>0){
			$mov_list=array();
			/*Deshabilito todos los tipos de movimiento de la caja x*/
			$this->disableTipoDocumentos($id_caja);

			foreach($T_DOC as $key =>$val){
				$tmov=$val['CAJA_TIPO_DOC'];//trim(System::getInstance()->Decrypt($val)); 
				$mov_list[$tmov]=$tmov;
			} 
			 
			/*RECORRO LOS MOVIMIENTOS QUE YA HAN SIDO AGREGADOS*/
			foreach($_DATA['tipo_documentos'] as $key =>$val){
				$tmov=trim(System::getInstance()->Decrypt($val));  	 
				/*VALIDO SI EL TIPO DE MOVIMIENTO EXISTE*/
				if (isset($mov_list[$tmov])){ 
					/*SI EXISTE LO HABILITO*/ 
					$this->changeEstatusTipoDocumento($id_caja,$tmov);
				}else{
					/*SI NO EXISTE LO AGREGO*/ 
					$dtmov= new ObjectSQL(); 
					$dtmov->CAJA_TIPO_DOC=$tmov;
					$dtmov->CAJA_ID_CAJA=$id_caja;
					$dtmov->setTable("tipo_documentos_caja");
					$SQL=$dtmov->toSQL('insert');
			 		mysql_query($SQL);	
				}
				 
			}	
		 
		}else{ 
			/*EN CASO QUE NO TENGA REGISTROS INSERTO*/
			foreach($_DATA['tipo_documentos'] as $key =>$val){
				$tmov=trim(System::getInstance()->Decrypt($val)); 
				$dtmov= new ObjectSQL(); 
				$dtmov->CAJA_TIPO_DOC=$tmov;
				$dtmov->CAJA_ID_CAJA=$id_caja;
				$dtmov->setTable("tipo_documentos_caja");
				$SQL=$dtmov->toSQL('insert'); 
			 	mysql_query($SQL);	
			} 
		} 
		
				
		
		$data['error']=false;
		$data['mensaje']='Registro actualizado!';
		return $data;
	}
	 
	function getTipoMovimiento($id_caja){
		$SQL="SELECT * FROM 
				`tipo_mov_caja` 
			WHERE `CAJA_ID_CAJA`='". mysql_real_escape_string($id_caja) ."'";
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}
		return $data;
	}
	function getTipoDocumento($id_caja){
		$SQL="SELECT * FROM 
				`tipo_documentos_caja` 
			WHERE `CAJA_ID_CAJA`='". mysql_real_escape_string($id_caja) ."'";
		$rs=mysql_query($SQL);
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}
		return $data;
	}	
	/*deshabilito todos lo tipos de movimientos de una caja*/
	function disableTipoMovimiento($id_caja){
		$ob=new ObjectSQL();	
		$ob->estatus=2;
		$ob->setTable("tipo_mov_caja");
		$SQL=$ob->toSQL('update',"where CAJA_ID_CAJA='".$id_caja."'");
		mysql_query($SQL);	
	}
	/*deshabilito todos lo tipos de documentos de una caja*/
	function disableTipoDocumentos($id_caja){
		$ob=new ObjectSQL();	
		$ob->estatus=2;
		$ob->setTable("tipo_documentos_caja");
		$SQL=$ob->toSQL('update',"where CAJA_ID_CAJA='".$id_caja."'");
		mysql_query($SQL);	
	}	
		/*deshabilito todos lo tipos de documentos de una caja*/
	function changeEstatusTipoDocumento($id_caja,$tipo_documento,$estatus=1){
		$ob=new ObjectSQL();	
		$ob->estatus=$estatus;
		$ob->setTable("tipo_documentos_caja");
		$SQL=$ob->toSQL('update',"where CAJA_ID_CAJA='".$id_caja."' and CAJA_TIPO_DOC='".$tipo_documento."'");
		mysql_query($SQL);	
	}	
		/*deshabilito todos lo tipos de movimientos de una caja*/
	function changeEstatusTipoMovimiento($id_caja,$tipo_movimiento,$estatus=1){
		$ob=new ObjectSQL();	
		$ob->estatus=$estatus;
		$ob->setTable("tipo_mov_caja");
		$SQL=$ob->toSQL('update',"where CAJA_ID_CAJA='".$id_caja."' and CAJA_TIPO_MOV_CAJA='".$tipo_movimiento."'");
		mysql_query($SQL);	
	}
}

?>