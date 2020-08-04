<?php

/*
	Asesores
*/
class Asesores{
	private $data;
	private $db_link;
	private $message=array("mensaje"=>"","error"=>true,"typeError"=>0);
	private $errorList=array(
		100 => ""
	); 
	public function __construct($db_link,$data=null){
		$this->data=$data;
		$this->db_link=$db_link;
	}
	
	public function getComercialParentData($code_asesor){
  
		$SQL="SELECT  
				'Asesor de Familia' as tabla,
				codigo_asesor AS id_comercial,
				CONCAT(sys_personas.primer_nombre,' ' ,
				sys_personas.segundo_nombre) AS nombre,
				CONCAT(sys_personas.primer_apellido,' ',
				sys_personas.segundo_apellido) AS apellido,
				sys_personas.id_nit,
				asesores_g_d_gg_view.codigo_gerente_grupo
			FROM sys_asesor AS asesores_g_d_gg_view 
			INNER JOIN sys_personas ON (sys_personas.id_nit=asesores_g_d_gg_view.id_nit)
		WHERE 
			asesores_g_d_gg_view.codigo_asesor='".$code_asesor."' " ;	
		  
		$rs=mysql_query($SQL); 
		$asesor=array();
		while($row=mysql_fetch_assoc($rs)){
			$row['tabla']=utf8_encode($row['tabla']); 
			array_push($asesor,$row); 
			$gerente=$this->getGerenteData($row['codigo_gerente_grupo']);		
			array_push($asesor,$gerente[0]); 
		}
	 		
		return $asesor;
	}
	public function getComercialParentDataByIDNit($id_nit){
  
		$SQL="SELECT  
				'Asesor de Familia' as tabla,
				codigo_asesor AS id_comercial,
				CONCAT(sys_personas.primer_nombre,' ' ,
				sys_personas.segundo_nombre) AS nombre,
				CONCAT(sys_personas.primer_apellido,' ',
				sys_personas.segundo_apellido) AS apellido,
				sys_personas.id_nit,
				asesores_g_d_gg_view.codigo_gerente_grupo
			FROM sys_asesor AS asesores_g_d_gg_view 
			INNER JOIN sys_personas ON (sys_personas.id_nit=asesores_g_d_gg_view.id_nit)
		WHERE 
			sys_personas.id_nit='".$id_nit."' " ;
			 
		$rs=mysql_query($SQL); 
		$asesor=array();
		while($row=mysql_fetch_assoc($rs)){
			$row['tabla']=utf8_encode($row['tabla']); 
			array_push($asesor,$row); 
			$gerente=$this->getGerenteData($row['codigo_gerente_grupo']);		
			array_push($asesor,$gerente[0]); 
		}
	 		
		return $asesor;
	}	
	public function getGerenteData($code_gerente){		
		$SQL="SELECT  
					'Gerente de ventas' as tabla,
					`codigo_gerente_grupo` AS id_comercial,
					CONCAT(sys_personas.primer_nombre,' ' ,
					sys_personas.segundo_nombre) AS nombre,
					CONCAT(sys_personas.primer_apellido,' ',
					sys_personas.segundo_apellido) AS apellido,
					sys_personas.id_nit
				FROM
					sys_gerentes_grupos AS asesores_g_d_gg_view 
				INNER JOIN sys_personas ON (sys_personas.id_nit=asesores_g_d_gg_view.id_nit)
				WHERE  
					asesores_g_d_gg_view.codigo_gerente_grupo='".$code_gerente."' " ;		 
		$rs=mysql_query($SQL); 
		$asesor=array();
		while($row=mysql_fetch_assoc($rs)){
			$row['tabla']=utf8_encode($row['tabla']);
			array_push($asesor,$row); 
		}			
		return $asesor;
	}	
	/*FUNCION QUE FORMATEA EL ID COMERCIAL Y LO DIVIDE EN ASESOR 
	DIRECTOR Y GERENTE*/
	public function formatAsesor($id_comercial){
		$asesor=array(
			"asesor"=>"",
			"director"=>"",
			"gerente"=>"",
			"gerente_general"=>""
		);
		$sp=explode("_",$id_comercial);
		
		$asesor['asesor']=$id_comercial;
		if (isset($sp[1]) && isset($sp[2])){
			$asesor['director']=$sp[1]."_".$sp[2];
		}
		if (isset($sp[3]) && isset($sp[4]) && isset($sp[5]) && isset($sp[6])){
			$asesor['gerente']=$sp[3]."_".$sp[4]."_".$sp[5]."_".$sp[6];
		}
		if (isset($sp[0])){
			$asesor['gerente_general']=$sp[0];
		}
		return $asesor;
	}
	
	public function getIDComercialFromProspecto($id_nit){
		$prosp=array();
		$SQL="SELECT 
				prospecto_comercial.*  
			  FROM prospecto_comercial 
				WHERE 
			  prospecto_comercial.id_nit='".$id_nit."' AND prospecto_comercial.estatus IN (6,17,4) " ;
 
		$rs=mysql_query($SQL); 
		while($row=mysql_fetch_assoc($rs)){
			$prosp=$row; 
		}	
		return $prosp;
	}	

	public function getDataAsesor($code_asesor){
		$asesor=array();
  
		$SQL="SELECT  
				'Asesor de Familia' as tabla,
				codigo_asesor AS id_comercial,
				CONCAT(sys_personas.primer_nombre,' ',sys_personas.segundo_nombre) AS nombre,
				CONCAT(sys_personas.primer_apellido,' ',sys_personas.segundo_apellido) AS apellido,
				sys_personas.id_nit,
				asesores_g_d_gg_view.codigo_gerente_grupo
			FROM sys_asesor AS asesores_g_d_gg_view 
			INNER JOIN sys_personas ON (sys_personas.id_nit=asesores_g_d_gg_view.id_nit)
		WHERE 
			asesores_g_d_gg_view.codigo_asesor='".$code_asesor."' " ;	
	 
			
		$rs=mysql_query($SQL); 
		$asesor=array();
		while($row=mysql_fetch_assoc($rs)){
			$asesor=$row; 
		}	
		return $asesor;
	}		
	public function getAsesorList($search){
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
 
		$SQL="SELECT 
				'Asesor de Familia' as tabla,
				codigo_asesor AS id_comercial,
				sys_personas.id_nit,
				CONCAT(sys_personas.primer_nombre,' ',
					sys_personas.segundo_nombre,' ',
					sys_personas.primer_apellido,' ',
					sys_personas.segundo_apellido) AS nombre_completo,
					asesores_g_d_gg_view.codigo_gerente_grupo
			FROM sys_asesor AS asesores_g_d_gg_view 
			INNER JOIN sys_personas ON (sys_personas.id_nit=asesores_g_d_gg_view.id_nit) ";
		  
		$SQL.=$QUERY;
		$rs=mysql_query($SQL);
		$result=array("results"=>array());  
		
		while($row=mysql_fetch_assoc($rs)){	
			$gerente=$this->getGerenteData($row['codigo_gerente_grupo']); 
			$row['nombre_completo']=$row['nombre_completo']." (".$gerente[0]['nombre']." ".$gerente[0]['apellido'].")"; 
			
			$eID=System::getInstance()->Encrypt(json_encode($row));
			$data=array("id"=>$eID,"text"=>$row['nombre_completo']);
			array_push($result['results'],$data);
		}
 
		
		return $result;
	}	
	public function getMessages(){
		return $this->message;
	}
	public function getListEmpleados(){
		$searchTerm = $this->data['searchTerm'];
		if(!$sidx) $sidx =1;
		if ($searchTerm=="") {
			$searchTerm="%";
		} else {
			$searchTerm = "%" . mysql_real_escape_string($searchTerm) . "%";
		}
		$SQL="SELECT id_nit,
			CONCAT(primer_nombre, ' ',segundo_nombre,' ',primer_apellido,' ',segundo_apellido) AS nombre 
			FROM sys_personas
		 WHERE (
		 CONCAT(primer_nombre, ' ',segundo_nombre,' ',primer_apellido,' ',segundo_apellido) like '".$searchTerm."' OR
		 CONCAT(primer_nombre,' ',primer_apellido) like '".$searchTerm."' OR
		 CONCAT(primer_apellido) like '".$searchTerm."') limit 20 ";
 
		$rs=mysql_query($SQL); 		
		$total=mysql_num_rows($rs);
		$i=0;
		$response->page = $total;
		$response->total = $total;
		$response->records = $total;

		$response->rows[$i]['value']="";
		$response->rows[$i]['nombre']="NO ASIGNADO"; 		
		$i++;
		
		while($row = mysql_fetch_array($rs)) {
 
			$response->rows[$i]['value']=System::getInstance()->Encrypt(json_encode($row));
			$response->rows[$i]['nombre']=$row['nombre']; 
			//$response->rows[$i]=array($row[id],$row[invdate],$row[name],$row[amount],$row[tax],$row[total],$row[note]);
			$i++;
		} 
		
		return $response;
	}
	
	/*OPTENER LOS EMPLEADOS QUE NO ESTAN REGISTRADOS CON USUARIO Y PASSWORD*/
	public function getSinUusarioEmpleados(){
		$searchTerm = $this->data['searchTerm'];
		if(!$sidx) $sidx =1;
		if ($searchTerm=="") {
			$searchTerm="%";
		} else {
			$searchTerm =mysql_real_escape_string($searchTerm) ;
		}
		$SQL="SELECT id_nit,
			CONCAT(primer_nombre, ' ',segundo_nombre,' ',primer_apellido,' ',segundo_apellido) AS nombre
		 FROM sys_personas
		 WHERE id_nit NOT IN (SELECT id_nit FROM usuarios WHERE id_nit IS NULL ) and (
		 CONCAT(primer_nombre, ' ',segundo_nombre,' ',primer_apellido) like '%".$searchTerm."%' OR
		 CONCAT(primer_nombre,' ',primer_apellido) like '%".$searchTerm."%' OR
		 CONCAT(primer_apellido) like '%".$searchTerm."%' OR id_nit like '%".$searchTerm."%') limit 20 ";
		$rs=mysql_query($SQL); 		
		$total=mysql_num_rows($rs);
		$i=0;
		$response->page = $total;
		$response->total = $total;
		$response->records = $total;

		$response->rows[$i]['value']="";
		$response->rows[$i]['nombre']="NO ASIGNADO"; 		
		$i++;
		
		while($row = mysql_fetch_array($rs)) {

		 
			$response->rows[$i]['value']=System::getInstance()->Encrypt(json_encode($row));
			$response->rows[$i]['nombre']=$row['nombre']; 
			//$response->rows[$i]=array($row[id],$row[invdate],$row[name],$row[amount],$row[tax],$row[total],$row[note]);
			$i++;
		} 
		
		return $response;
	}
	
	public function validateMetas($id_comercial){
		$CODIGOS=$this->formatAsesor($id_comercial);
		$SQL="SELECT count(*) as total FROM metas_ventas WHERE ID_COMERCIAL='".$id_comercial."'";
	 
		$rs=mysql_query($SQL); 		 
		while($row = mysql_fetch_array($rs)) {
			 
			if ($row['total']==0){
				for($i=1;$i<=12;$i++){
					$ob= new ObjectSQL();
					$ob->MES_META=$i;
					$ob->ANO_META=date("Y");
					$ob->ID_COMERCIAL=$id_comercial;
					$ob->NEGOCIOS_META=0;
					$ob->MONTO_META=0;
					$ob->ID_GERENTE_META=$CODIGOS['gerente'];
					$ob->ID_DIRECTOR_META=$CODIGOS['director'];
					$ob->ID_GERENTE_GENERAL_META=$CODIGOS['gerente_general'];
					$ob->ID_ASESOR_META=$CODIGOS['asesor'];
					$ob->setTable("metas_ventas");
					$SQL=$ob->toSQL("insert");
					mysql_query($SQL); 
				}
			}
		
		}
	}
	
	public function updateMetas($mes,$id_comercial,$negocios,$monto){
		 
		$ob= new ObjectSQL();
		$ob->MES_META=$mes;
		$ob->ANO_META=date("Y");
	//	$ob->ID_GERENTE_META=$id_comercial;
		$ob->NEGOCIOS_META=$negocios;
		$ob->MONTO_META=$monto;
		$ob->setTable("metas_ventas");
		$SQL=$ob->toSQL("update"," where ID_COMERCIAL='".$id_comercial."' and MES_META='".$mes."' AND ANO_META='".date("Y")."' ");
		mysql_query($SQL); 	
	}
	
	public function getMetas($id_comercial){
		$SQL="SELECT * FROM metas_ventas WHERE ID_COMERCIAL='".$id_comercial."' AND ANO_META='".date("Y")."'";
		$rs=mysql_query($SQL); 	
		$arr=array();	 
		while($row = mysql_fetch_array($rs)) {
			 array_push($arr,$row);
		}
		return $arr;
	}	
	
	public function getMyAsesores($id_comercial_gerente){
		$QUERY="";
		$HAVING="";
		if (isset($_REQUEST['sSearch'])){
		  if (trim($_REQUEST['sSearch'])!=""){
			$_REQUEST['sSearch']=mysql_escape_string($_REQUEST['sSearch']);
				$QUERY=" AND CONCAT(sys_personas.primer_nombre,' ' ,sys_personas.primer_apellido) LIKE '%".$_REQUEST['sSearch']."%'";
		  }
		}
		
		$SQL="SELECT count(*) as total
			FROM view_estructura_comercial as asesores_g_d_gg_view
			INNER JOIN sys_personas ON (sys_personas.id_nit=asesores_g_d_gg_view.id_nit)
			INNER JOIN sys_asesor ON (sys_asesor.codigo_asesor=asesores_g_d_gg_view.id_comercial)
			WHERE  
				(sys_asesor.codigo_gerente_grupo='".$id_comercial_gerente."' AND sys_asesor.status=1 AND asesores_g_d_gg_view.tabla='Asesor de Familia')";		
		$SQL.=$QUERY;
		 
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs);
		$total_row=$row['total'];
		$SQL=" SELECT * FROM asesores_g_d_gg_view";
		$SQL="SELECT asesores_g_d_gg_view.*,
				sys_personas.primer_nombre,
				sys_personas.segundo_nombre,
				sys_personas.primer_apellido,
				sys_personas.segundo_apellido
 			FROM view_estructura_comercial as asesores_g_d_gg_view
			INNER JOIN sys_personas ON (sys_personas.id_nit=asesores_g_d_gg_view.id_nit)
			INNER JOIN sys_asesor ON (sys_asesor.codigo_asesor=asesores_g_d_gg_view.id_comercial)
			WHERE  (sys_asesor.codigo_gerente_grupo='".$id_comercial_gerente."' AND sys_asesor.status=1 AND asesores_g_d_gg_view.tabla='Asesor de Familia')";
		 
			if (!isset($_REQUEST['iDisplayStart'])){
				$_REQUEST['iDisplayStart']=0;	
			}
			if (!isset($_REQUEST['iDisplayLength'])){
				$_REQUEST['iDisplayLength']=1;	
			}			
			
			$SQL.=" limit ".$_REQUEST['iDisplayStart'].",".$_REQUEST['iDisplayLength']."";			 
			 
		$SQL.=$QUERY;	
		$rs=mysql_query($SQL);

                $prueba= new ObjectSQL();
                $prueba->texto=$SQL;
                $kk=$prueba->getSQL("insert","prueba");
                mysql_query($kk);

		$info=array();
		$data=array(
			'sEcho'=>$_REQUEST['sEcho'],
			'iTotalRecords'=>10,
			'iTotalDisplayRecords'=>$total_row,
			'aaData' =>array()
		);
		while($row=mysql_fetch_assoc($rs)){
			$row['nombre_completo']=utf8_encode($row['primer_nombre']) ." ".utf8_encode($row['segundo_nombre'])." ".utf8_encode($row['primer_apellido'])." ".utf8_encode($row['segundo_apellido']);
 			$id=System::getInstance()->Encrypt(json_encode($row));
			
			unset($row['id_comercial']);
			unset($row['id_nit']);
			unset($row['tabla']);
			unset($row['primer_nombre']);
			unset($row['primer_apellido']);
			$row['checklist']='<input type="checkbox" class="remove_asesor" value="'.$id.'">'; 			
			array_push($data['aaData'],$row);
		}
		
		
		return $data;
	}
	/*Remover Asesor */
	public function remove($id,$desc){
		$asesor=json_decode(System::getInstance()->Decrypt($id));
	
		if (isset($asesor->id_nit)){
			$obj= new ObjectSQL();
			$obj->status=2;
			$obj->descripcion=$desc;
			$SQL=$obj->getSQL("update","sys_asesor"," where id_nit='". mysql_escape_string($asesor->id_nit) ."' ");	 
			@mysql_query($SQL);
			$address=array();
			array_push($address,array("email"=>"jose.ramos@gpmemorial.com","name"=>"Eudalisa Rodriguez"));
			array_push($address,array("email"=>"eudalisa.rodriguez@gpmemorial.com","name"=>"Jose Gregorio Ramos"));
			
			//print_r($asesor);
			$data=" ASESOR : ".$asesor->nombre_completo."\n";
			$data.=" CEDULA : ".$asesor->id_nit."\n";
			$data.=" COMENTARIO : ".$desc."\n";
					
			sendMailer("noreplay@memorial.com.do","ASESOR REMOVIDO (".$asesor->nombre_completo.")",$address,"ASESOR REMOVIDO (".$asesor->nombre_completo.")",$data);
			
			$retur=array("mensaje"=>"Asesor removido","error"=>false);
			return $retur;
		}else{
			$retur=array("mensaje"=>"Fallo al tratar de remover el prospecto","error"=>true);
			return $retur;	
		}
	}	

}

?>
