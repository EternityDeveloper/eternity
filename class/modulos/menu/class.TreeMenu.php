<?php



class TreeMenu {
	private $_menu;

	public function __construct(){
		$this->generate();
	}
	public function print_menu(){
		echo $this->generateUL($this->_menu);
	}
	private function generateUL($menu){
		$data="";
		
		
		$data.='<ul>'."\n";
		foreach($menu as $key =>$val){
			$encriptID=System::getInstance()->getEncrypt()->encrypt(json_encode($val),UserAccess::getSessionID());
			$id=$encriptID;
		//	$url=$val['url']==""?'':$val['url'];
			$data.=' <li> '."\n".'<a href="#"  id="'.$id.'" type="'.$val['tipo_menu'].'">'.$val['nombre'].'</a>'."\n";
			$data.=$this->generateUL($val['hijos'])."\n".'</li>';
		}		
		$data.='</ul>';
		
		return $data;
	}
	public function generate(){
		$menu=array();
		$SQL="SELECT 
					clasificacion_pantallas.`Id_clas_pantallas`,
					clasificacion_pantallas.posicion_jeralquia,
					Pantallas.id_pantalla AS permiso,
					clasificacion_pantallas.tipo_menu,
					clasificacion_pantallas.nombre,
					Pantallas.URL,
					Pantallas.Pantalla
			FROM clasificacion_pantallas
				INNER JOIN Pantallas ON (Pantallas.id_pantalla=clasificacion_pantallas.Pantallas_id_pantalla)
			ORDER BY tipo_menu,order_,posicion_jeralquia ";
		$rs=SystemHtml::getInstance()->getSystemProtect()->getDBLink()->query($SQL);
		while($row=mysql_fetch_assoc($rs)){
		//	print_r($row);
			if ($row['tipo_menu']=="Menu"){
				$sp=explode("_",$row['posicion_jeralquia']);
				$menu[$sp[0]]=array(
									'nombre'=>$row['nombre'],
									"url"=>$row['URL']==""?'':$row['URL'],
									"Id_clas_pantallas"=>$row['Id_clas_pantallas'],
									"tipo_menu"=>$row['tipo_menu'],
									"posicion_jeralquia"=>$row['posicion_jeralquia'],
									'name_pantalla'=>$row['Pantalla'],
									"hijos"=>array()
								);
								
			}
			if ($row['tipo_menu']=="Submenu"){
				$sp=explode("_",$row['posicion_jeralquia']);
				$submenu=array(
								'nombre'=>$row['nombre'],
								"url"=>$row['URL']==""?'':$row['URL'],
								"Id_clas_pantallas"=>$row['Id_clas_pantallas'],
								"tipo_menu"=>$row['tipo_menu'],
								"posicion_jeralquia"=>$row['posicion_jeralquia'],	
								'name_pantalla'=>$row['Pantalla'],							
								"hijos"=>array()
								);
				array_push($menu[$sp[0]]['hijos'],$submenu);
			}
		}
		$this->_menu=$menu;
	}
				





}


?>