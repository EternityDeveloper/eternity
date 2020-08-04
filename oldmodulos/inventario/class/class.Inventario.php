<?php

class Inventario{
	private $data;
	private $db_link;
	private $message=array("mensaje"=>"","error"=>true);
	private $token=0; //ES EL ID DE REFERENCIA DE LA TRANSAPCION
	public function __construct($db_link){
		$this->db_link=$db_link;
	}
	public function setToken($token){
		$this->token=$token;	
		if (!isset($_SESSION['INVT_DATA'][$this->token])){
			$_SESSION['INVT_DATA'][$this->token]=array(
												"producto"=>array(),
												"plan"=>array(),
												"descuento"=>array());
		}
	}
	/*DEBUELVE EL LISTADO DE INVENTARIO*/
	public function getList($_DATA=array()){
			/*CARGO LA CLASE DE MANEJAR LOS PRODUCTOS*/
		SystemHtml::getInstance()->includeClass("contratos","Carrito");
		SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
	
		$QUERY="";
		$HAVING="";
		if (isset($_DATA['sSearch'])){
		  if (trim($_DATA['sSearch'])!=""){
			$_DATA['sSearch']=mysql_escape_string($_DATA['sSearch']);
			$QUERY=" AND (concat(inventario_jardines.id_jardin,'-',inventario_jardines.id_fases)  LIKE '%".$_DATA['sSearch']."%'  OR 					concat(fases.id_fases,' ',fases.fase) LIKE '%".$_DATA['sSearch']."%' OR
			concat(inventario_jardines.id_jardin,' ',inventario_jardines.id_fases)  LIKE '%".$_DATA['sSearch']."%'  OR 					concat(fases.id_fases,' ',fases.fase) LIKE '%".$_DATA['sSearch']."%' OR
			reserva_inventario.no_reserva LIKE '%".$_DATA['sSearch']."%' 
			OR inventario_jardines.bloque LIKE '%".$_DATA['sSearch']."%' 
			OR  inventario_jardines.lote LIKE '%".$_DATA['sSearch']."%' 
			OR  inventario_jardines.osario LIKE '%".$_DATA['sSearch']."%' 
			OR  inventario_jardines.id_fases LIKE '%".$_DATA['sSearch']."%'  
			OR  inventario_jardines.id_jardin LIKE '%".$_DATA['sSearch']."%' 
			OR  sys_status.`descripcion` LIKE '%".$_DATA['sSearch']."%'
			OR  tipos_reservas.`reserva_descrip` LIKE '%".$_DATA['sSearch']."%'
			OR  reserva_inventario.`fecha_inicio` LIKE '%".$_DATA['sSearch']."%'
			OR  `reserva_inventario`.`fecha_fin` LIKE '%".$_DATA['sSearch']."%' 
			OR  CONCAT(inventario_jardines.`id_jardin`,' ',inventario_jardines.`bloque`,' ',inventario_jardines.`lote`) LIKE '%".$_DATA['sSearch']."%' 
			OR  CONCAT(inventario_jardines.`id_jardin`,'-',inventario_jardines.`bloque`,'-',inventario_jardines.`lote`) LIKE '%".$_DATA['sSearch']."%' 
			OR  CONCAT(inventario_jardines.`id_jardin`,' ',inventario_jardines.`id_fases`,' ',inventario_jardines.`bloque`,' ',inventario_jardines.`lote`) LIKE '%".$_DATA['sSearch']."%'  
			OR  CONCAT(inventario_jardines.`id_jardin`,'-',inventario_jardines.`id_fases`,'-',inventario_jardines.`bloque`,'-',inventario_jardines.`lote`) LIKE '%".$_DATA['sSearch']."%' OR  CONCAT(inventario_jardines.`id_jardin`,'-',inventario_jardines.`bloque`,'-',inventario_jardines.`lote`,inventario_jardines.osario) LIKE '%".$_DATA['sSearch']."%'			OR  CONCAT(inventario_jardines.`id_jardin`,'-',inventario_jardines.`id_fases`,inventario_jardines.`bloque`,'-',inventario_jardines.`lote`,inventario_jardines.osario) LIKE '%".$_DATA['sSearch']."%'
			
			
			 )";
		  }
		}
		if (isset($_DATA['status'])){
			$QUERY.=" AND sys_status.id_status=1 ";
		}
		 
		
		/*Filtro de los inventarios la parcela que este disponible*/
		$carrito = new Carrito($this->db_link);
		$items= $carrito->getListItem();
		$str="";
		if (count($items)>0){
			foreach($items as $key =>$value){
				$carrito->setToken($key);
				$obj=$carrito->getProducto(); 
				if (isset($obj->product_id)){
					$inv=json_decode(System::getInstance()->Decrypt($obj->product_id));
					$str.="'".$inv->bloque.''.$inv->lote.''.$inv->id_fases.''.$inv->id_jardin.''.$inv->osario."',"; 
				}
			}
		}

		if ($str!=""){
			$str=substr($str,0,strlen($str)-1);
			$QUERY.=" AND (CONCAT(inventario_jardines.bloque,inventario_jardines.lote,
			fases.id_fases,jardines.id_jardin,jardines.osario) NOT IN(".$str.")) ";
		}
	 
		 
		if (isset($_DATA['filter_estatus'])){
			$js=json_decode($_DATA['filter_estatus']);
			
			
			if (count($js)>0){
				$str="";
				$valid=false;
				foreach($js as $key => $val){
					if (is_numeric($val)){
						$valid=true;
						$str.="'".$val."',";
					}
				}
				if ($valid){
					$str=substr($str,0,strlen($str)-1);
					$QUERY.=" AND inventario_jardines.estatus IN (".$str.")";
				}
			}
		} 
		
		if (isset($_DATA['filter_by_nit'])){
			$id_nit=System::getInstance()->Decrypt($_DATA['filter_by_nit']);	
			$QUERY.=" AND reserva_inventario.id_nit ='". mysql_real_escape_string($id_nit)."'";
		}
		
	
		
		$SQL=" SELECT count(*) as total FROM `inventario_jardines` 
				LEFT JOIN jardines ON (jardines.`id_jardin`=inventario_jardines.`id_jardin`)
				LEFT JOIN `fases` ON (fases.`id_fases`=inventario_jardines.`id_fases`)
				INNER JOIN `sys_status` ON (sys_status.`id_status`=inventario_jardines.`estatus`)
				LEFT JOIN `reserva_inventario` ON (reserva_inventario.`no_reserva`=inventario_jardines.`no_reserva` AND 
				reserva_inventario.`id_reserva`=inventario_jardines.`id_reserva` )
				LEFT JOIN tipos_reservas ON (tipos_reservas.`id_reserva`=inventario_jardines.id_reserva)
			WHERE 1=1 	
				 ";
		
		$SQL.=$QUERY;
	  
	$rs=mysql_query($SQL);
	$row=mysql_fetch_assoc($rs);
	$total_row=$row['total'];
	
		$SQL="SELECT *,
				inventario_jardines.EM_ID as eEM_ID,
				inventario_jardines.id_fases AS fase_jardin,
				concat(jardines.id_jardin,' - ',jardines.jardin) as nombre_jardin,
				concat(fases.id_fases,' - ',fases.fase) as nombre_fase,
				reserva_inventario.no_reserva as serie_recibo_no,
				CONCAT(inventario_jardines.`lote`,inventario_jardines.osario) AS lotes,
				CONCAT(inventario_jardines.serie_contrato,' ',inventario_jardines.no_contrato) as contrato
			 FROM `inventario_jardines` 
			INNER JOIN jardines ON (jardines.`id_jardin`=inventario_jardines.`id_jardin`)
			LEFT JOIN `fases` ON (fases.`id_fases`=inventario_jardines.`id_fases`)
			INNER JOIN `sys_status` ON (sys_status.`id_status`=inventario_jardines.`estatus`)
			LEFT JOIN `reserva_inventario` ON (reserva_inventario.`no_reserva`=inventario_jardines.`no_reserva` AND 
			reserva_inventario.`id_reserva`=inventario_jardines.`id_reserva` )
			LEFT JOIN tipos_reservas ON (tipos_reservas.`id_reserva`=inventario_jardines.id_reserva) 
		WHERE 1=1  ";
		$SQL.=$QUERY;
		
		$SQL." ORDER BY inventario_jardines.id_jardin,
						inventario_jardines.id_fases,
						inventario_jardines.id_bloque,
						inventario_jardines.lote ";

		if (!isset($_DATA['iDisplayStart'])){
			$_DATA['iDisplayStart']=0;
		}
		if (!isset($_DATA['iDisplayLength'])){
			$_DATA['iDisplayLength']=10;
		}		
		$SQL.=" limit ". mysql_real_escape_string($_DATA['iDisplayStart']).",".mysql_real_escape_string($_DATA['iDisplayLength'])."";
		 
	 
		$data=array(
			'sEcho'=>$_DATA['sEcho'],
			'iTotalRecords'=>10,
			'iTotalDisplayRecords'=>$total_row,
			'aaData' =>array()
		);
	 
		if ((!validateField($_DATA,"iDisplayStart")) && (!validateField($_DATA,"iDisplayLength"))){

			echo json_encode($data);
			exit;
		} 
		$rs=mysql_query($SQL);
		$result=array();
		 
		$_contratos=new Contratos($this->db_link); 
		while($row=mysql_fetch_assoc($rs)){	
			$row['descripcion']=utf8_encode($row['descripcion']);
			$encriptID=System::getInstance()->Encrypt(json_encode($row));
			$row['bt_reserva']='';
			$row['bt_editar']='<a href="#" class="inventario_edit" id="'.$encriptID.'" onclick="openDialogEditLote(\''.$encriptID.'\')"><img src="images/clipboard_edit.png"  /></a>';
			
			$row['bt_select']='<a href="#" class="inventario_edit" id="'.$encriptID.'"><img src="images/plus.png"  /></a>';
			$row['_seleccionar']='';
			if (($row['id_status']!=3) && ($row['id_status']!=17)){
				$row['bt_reserva']='<a href="#" class="blueButton" onclick="_reservar(\''.$encriptID.'\')">Reservar</a>';
				$row['_seleccionar']='<button type="button" class="orangeButton" onclick="_seleccionar(\''.$encriptID.'\')" style="float:right;margin-right:10%;">&nbsp;Seleccionar&nbsp;</button>';
			}
			
			
			if (trim($row['contrato'])!=""){
				$dx=$_contratos->getInfoContrato($row['serie_contrato'],$row['no_contrato']);	
				$r=System::getInstance()->Encrypt(json_encode(array("serie_contrato"=>$row['serie_contrato'],"no_contrato"=>$row['no_contrato'],"id_nit"=>$dx->id_nit_cliente)));
				$row['contrato']='<a href="?mod_cobros/delegate&contrato_view&id='.$r.'" target="new">'.$row['contrato'].'</a>';
				 
			}
			array_push($data['aaData'],$row);
			 
		}
		return $data;		
	}	
	
	/*DEBUELVE EL LISTADO DE INVENTARIO AGRUPADO POR RESERVA*/
		public function getListWithReserva($_DATA=array()){
		/*CARGO LA CLASE DE MANEJAR LOS PRODUCTOS*/
		SystemHtml::getInstance()->includeClass("contratos","Carrito"); 

		$QUERY="";
		$HAVING="";
		  
		if (isset($_DATA['status'])){
			$QUERY.=" AND sys_status.id_status=1 ";
		} 
		/*Filtro de los inventarios la parcela que este disponible*/
		$carrito = new Carrito($this->db_link);
		$items= $carrito->getListItem(); 
		//print_r($items);
		$str="";
		$filter="";
		$iCodigo=array();
		foreach($items as $key =>$value){
			$carrito->setToken($key);  
			$obj=$carrito->getProducto();   
			if (count($obj)>0){   
				
				foreach($obj as $keys =>$inv){   
					if (isset($inv->id_jardin)){
						if ($_DATA['token']!=$key){
							$str.="'".$inv->bloque.''.$inv->lote.''.$inv->osario.''.$inv->id_fases.''.$inv->id_jardin."',"; 
						}
						if ($_DATA['token']==$key){
							//$row['id_jardin'].$row['id_fases'].$row['bloque'].$row['lote'].$row['osario'];
//							$filter="'".$inv->bloque.$inv->id_fases.$inv->id_jardin."',"; 
							$filter="'".$inv->id_fases.$inv->id_jardin."',"; 
							//$filter="'".$inv->bloque.$inv->lote.$inv->osario.''.$inv->id_fases.''.$inv->id_jardin."'";
							$iCodigo[$inv->id_jardin.$inv->id_fases.$inv->bloque.$inv->lote.$inv->osario]=$inv->bloque.$inv->lote.$inv->osario.$inv->id_fases.$inv->id_jardin; 
						}  
						if (count($obj)==1){ 
						//	$str.="'".$inv->bloque.''.$inv->lote.$inv->osario.''.$inv->id_fases.''.$inv->id_jardin."',";  
						}  
					
					} 
				}

			} 
		}   
	 
		if ($filter!=""){ 
			$filter=substr($filter,0,strlen($filter)-1);
			$QUERY.=" AND (CONCAT(inventario_jardines.id_fases,inventario_jardines.id_jardin) IN (".$filter.")) ";
/*
			$QUERY.=" AND (CONCAT(inventario_jardines.bloque,inventario_jardines.id_fases,inventario_jardines.id_jardin) IN (".$filter.")) ";

*/
		}    
		if ($str!=""){
			$str=substr($str,0,strlen($str)-1);
			$QUERY.=" AND (CONCAT(inventario_jardines.bloque,inventario_jardines.lote,inventario_jardines.osario,fases.id_fases,jardines.id_jardin) NOT IN(".$str.")) ";
		}
		
		if (isset($_DATA['filter_estatus'])){
			$js=json_decode($_DATA['filter_estatus']); 
			if (count($js)>0){
				$str="";
				$valid=false;
				foreach($js as $key => $val){
					if (is_numeric($val)){
						$valid=true;
						$str.="'".$val."',";
					}
				}
				if ($valid){
					$str=substr($str,0,strlen($str)-1);
					$QUERY.=" AND inventario_jardines.estatus IN (".$str.")";
				}
			}
		} 
		
		
		if (isset($_DATA['filter_by_nit'])){
			$id_nit=System::getInstance()->Decrypt($_DATA['filter_by_nit']);	
			$QUERY.=" AND reserva_inventario.id_nit ='". mysql_real_escape_string($id_nit)."'";
		} 
		  
		$SQL=" SELECT count(*) as total FROM `inventario_jardines` 
				INNER JOIN jardines ON (jardines.`id_jardin`=inventario_jardines.`id_jardin`)
				LEFT JOIN `fases` ON (fases.`id_fases`=inventario_jardines.`id_fases`)
				INNER JOIN `sys_status` ON (sys_status.`id_status`=inventario_jardines.`estatus`)
				LEFT JOIN `reserva_inventario` ON (reserva_inventario.`no_reserva`=inventario_jardines.`no_reserva` AND 
				reserva_inventario.`id_reserva`=inventario_jardines.`id_reserva` )
				LEFT JOIN tipos_reservas ON (tipos_reservas.`id_reserva`=inventario_jardines.id_reserva)
			WHERE 1=1 ";
		
		$SQL.=$QUERY; 
	$rs=mysql_query($SQL);
        if (!$rs) {
               die('consutla no valida'.mysql_error());
}
	$row=mysql_fetch_assoc($rs);
	$total_row=$row['total'];
	
		$SQL="SELECT inventario_jardines.bloque,
					inventario_jardines.osario,
					inventario_jardines.lote,
					CONCAT(inventario_jardines.lote,inventario_jardines.osario) as lote_codigo,
					inventario_jardines.id_fases,
					inventario_jardines.id_jardin,
					inventario_jardines.EM_ID AS eEM_ID,
					reserva_inventario.`no_reserva`,
					reserva_inventario.`id_reserva`,
					CONCAT(jardines.id_jardin,' - ',jardines.jardin) AS nombre_jardin,
					CONCAT(fases.id_fases,' - ',fases.fase) AS nombre_fase,
					reserva_inventario.no_reserva AS serie_recibo_no, 	
					sys_status.descripcion
			 FROM `inventario_jardines` 
			INNER JOIN jardines ON (jardines.`id_jardin`=inventario_jardines.`id_jardin`)
			LEFT JOIN `fases` ON (fases.`id_fases`=inventario_jardines.`id_fases`)
			INNER JOIN `sys_status` ON (sys_status.`id_status`=inventario_jardines.`estatus`)
			LEFT JOIN `reserva_inventario` ON (reserva_inventario.`no_reserva`=inventario_jardines.`no_reserva` AND 
			reserva_inventario.`id_reserva`=inventario_jardines.`id_reserva` )
			LEFT JOIN tipos_reservas ON (tipos_reservas.`id_reserva`=inventario_jardines.id_reserva) 
		WHERE 1=1 and inventario_jardines.estatus=3 ";
		
		$SQL.=$QUERY;
		
		$SQL." ORDER BY inventario_jardines.id_jardin,
						inventario_jardines.id_fases,
						inventario_jardines.id_bloque,
						inventario_jardines.lote,
						inventario_jardines.osario ";

		$SQL.="  LIMIT ". mysql_real_escape_string($_DATA['iDisplayStart']).",".mysql_real_escape_string($_DATA['iDisplayLength']).""; 
		$data=array(
			'sEcho'=>$_DATA['sEcho'],
			'iTotalRecords'=>10,
			'iTotalDisplayRecords'=>$total_row,
			'aaData' =>array()
		);
		
		if ((!validateField($_DATA,"iDisplayStart")) && (!validateField($_DATA,"iDisplayLength"))){ 
			echo json_encode($data);
			exit;
		} 
		$rs=mysql_query($SQL);
                if (!$rs) {
                       die('consulta no valida: '.mysql_error());
                }
		$result=array();  
		while($row=mysql_fetch_assoc($rs)){	
			$encriptID=System::getInstance()->Encrypt(json_encode($row));
			$row['product_id']=$encriptID;
			$row['bt_reserva']=''; 
			$item = base64_encode(json_encode($row)); 
			//$inv->bloque.$inv->lote.$inv->id_fases.$inv->id_jardin
			$idCode=$row['id_jardin'].$row['id_fases'].$row['bloque'].$row['lote'].$row['osario'];
			$check=""; 
			
			if (array_key_exists($idCode,$iCodigo)){   
				$check=' checked="checked" ';
			}
			 
			$row['total']='<input type="checkbox" class="abnp_check" '.$check.' name="abp_check[]" id="abp_check[]" value="'.$encriptID.'">';
		 
			$row['bt_editar']='<a href="#" class="inventario_edit" id="'.$encriptID.'" item="'.$item .'" onclick="openDialogEditLote(\''.$encriptID.'\')"><img src="images/clipboard_edit.png"  /></a>';
			
			$row['bt_select']='<a href="#" class="inventario_edit" id="'.$encriptID.'"  item="'.$item.'"><img src="images/plus.png"  /></a>';
			 
			array_push($data['aaData'],$row);
		}  
		return $data;		
	}	
	/*
		id_nit => codigo identificacion del cliente
		filter => es el filtro del jardin el cual es la combinacion de: bloque+id_fases+jardines.id_jardin
	*/
	public function getTotalReservaEqualParcela($id_nit,$filter){
   
   /*	backup
   		$SQL=" SELECT count(*) as total FROM `inventario_jardines` 
				INNER JOIN jardines ON (jardines.`id_jardin`=inventario_jardines.`id_jardin`)
				INNER JOIN `fases` ON (fases.`id_fases`=inventario_jardines.`id_fases`)
				INNER JOIN `sys_status` ON (sys_status.`id_status`=inventario_jardines.`estatus`)
				LEFT JOIN `reserva_inventario` ON (reserva_inventario.`no_reserva`=inventario_jardines.`no_reserva` AND 
				reserva_inventario.`id_reserva`=inventario_jardines.`id_reserva` )
				LEFT JOIN tipos_reservas ON (tipos_reservas.`id_reserva`=inventario_jardines.id_reserva)
			WHERE 1=1 AND reserva_inventario.id_nit ='". mysql_real_escape_string($id_nit)."' AND (CONCAT(inventario_jardines.bloque,fases.id_fases,jardines.id_jardin) IN('".$filter."'))";
   */
		$SQL=" SELECT count(*) as total FROM `inventario_jardines` 
				INNER JOIN jardines ON (jardines.`id_jardin`=inventario_jardines.`id_jardin`)
				left JOIN `fases` ON (fases.`id_fases`=inventario_jardines.`id_fases`)
				INNER JOIN `sys_status` ON (sys_status.`id_status`=inventario_jardines.`estatus`)
				LEFT JOIN `reserva_inventario` ON (reserva_inventario.`no_reserva`=inventario_jardines.`no_reserva` AND 
				reserva_inventario.`id_reserva`=inventario_jardines.`id_reserva` )
				LEFT JOIN tipos_reservas ON (tipos_reservas.`id_reserva`=inventario_jardines.id_reserva)
			WHERE 1=1 AND reserva_inventario.id_nit ='". mysql_real_escape_string($id_nit)."' AND (CONCAT(fases.id_fases,jardines.id_jardin) IN('".$filter."'))";
 
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs); 
		return $row['total'];		
	}
	
	/*LIBERA UNA PARECELA QUE TIENE ASIGNADO UN CONTRATO*/
	public function liberar_parecela_contrato($producto,$comentario=""){

if ($producto[osario] == NULL) {
     $producto[osario]= " ";
}

 		$SQL="SELECT * FROM inventario_jardines 
				WHERE id_jardin='".$producto[id_jardin]."' AND 
						bloque='".$producto[bloque]."' AND 
						lote='".$producto[lote]."' AND 
						id_fases='".$producto[id_fases]."' AND 
						osario='".$producto[osario]."' ";
     
if ($producto[osario] == NULL) {
           $SQL="SELECT * FROM inventario_jardines
                          WHERE id_jardin ='".$producto[id_jardin]."' AND
                                bloque='".$producto[bloque]."' AND
                                lote = '".$producto[lote]."' AND
                                id_fases='".$producto[id_fases]."' ";

}    
	 // AND estatus IN ('1','3')
               
		$rs=mysql_query($SQL);
                
		while($row=mysql_fetch_assoc($rs)){ 
			/*VALIDO QUE LA PARCELA NO SE ESTE UTLIZANDO*/
			$SQL="SELECT * FROM producto_contrato 
					WHERE id_estatus=1  and
						id_jardin='".$producto[id_jardin]."' AND 
							bloque='".$producto[bloque]."' AND 
							lote='".$producto[lote]."' AND 
							id_fases='".$producto[id_fases]."' AND 
							osario='".$producto[osario]."' ";

			$rsx=mysql_query($SQL); 
			$rowx=mysql_fetch_assoc($rsx);
			if (mysql_num_rows($rsx)>0){
				return array("valid"=>false,"mensaje"=>"Error de liberación, la parcela tiene un contrato asignado (".$rowx['serie_contrato']." ".$rowx['no_contrato'].")");	
			} 
		 
			/*LIBERO LA PARCELA*/
			$ob= new ObjectSQL();
			$ob->estatus=1;
			$ob->serie_contrato="";
			$ob->no_contrato="";
			$ob->id_reserva="";
			$ob->no_reserva=0;
			$ob->setTable("inventario_jardines");
			$SQL=$ob->toSQL("update"," WHERE id_jardin='".$producto['id_jardin']."' AND 
						bloque='".$producto['bloque']."' AND 
						lote='".$producto['lote']."' AND 
						id_fases='".$producto['id_fases']."' AND 
						osario='".$producto['osario']."' ");
                        $resultado = mysql_query($SQL);
                        if (!$resultado) {
                           die('consulta no valida: '.mysql_error());
                         }
			mysql_query($SQL);	


			/* LIBERO LA RESERVA*/
			$ob= new ObjectSQL();
			$ob->estatus=$producto['_estatus'];
			$ob->setTable("reserva_inventario");
			$SQL=$ob->toSQL("update"," WHERE no_reserva='".$row['no_reserva']."' ");
                        
			mysql_query($SQL); 					
			
			$SQL="SELECT * FROM `reserva_ubicaciones` 
			WHERE id_jardin='".$producto['id_jardin']."' AND 
					bloque='".$producto['bloque']."' AND 
					lote='".$producto['lote']."' AND 
					id_fases='".$producto['id_fases']."' AND 
					osario='".$producto['osario']."' AND estatus IN ('1') ";
			
			$rsx=mysql_query($SQL); 
			while($rowx=mysql_fetch_assoc($rsx)){ 
				/* LIBERO LA RESERVA*/
				$ob= new ObjectSQL();
				$ob->estatus=$producto['_estatus'];
				$ob->setTable("reserva_inventario");
				$SQL=$ob->toSQL("update"," WHERE no_reserva='".$rowx['no_reserva']."' ");
                                
				mysql_query($SQL); 					
			}

			/* LIBERO LA RESERVA UBICACION*/
			$ob= new ObjectSQL();
			$ob->estatus=$producto['_estatus'];
			$ob->setTable("reserva_ubicaciones");
			$SQL=$ob->toSQL("update"," WHERE id_jardin='".$producto['id_jardin']."' 
							AND bloque='".$producto['bloque']."'
							AND lote='".$producto['lote']."'
							AND id_fases='".$producto['id_fases']."'
							AND osario='".$producto['osario']."' ");
			
                        mysql_query($SQL);										 		

			SysLog::getInstance()->Log($producto['id_nit_cliente'], 
									 $producto['serie_contrato'],
									 $producto['no_contrato'],
									 $row['no_reserva'],
									 $row['id_reserva'],
									 $comentario." LIBERACION DE PRODUCTO CEMENTERIO ".$producto['id_jardin'].$producto['id_fases'].$producto['bloque'].$producto['lote'].$producto['osario'],
									 json_encode($row),
									 'ANULACION',
									 '',
									 '',
									 $producto['id']);		
									 
			return true;
		}
		return false;				
						
	}
	  
}
	
?>
