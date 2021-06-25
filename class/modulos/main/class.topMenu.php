<?php

class topMenu {
	private $_menu;

	public function __construct(){
		$this->generate();
	}
	public function print_menu($id){
		//print_r($this->_menu);
		echo $this->generateUL($this->_menu,$id);
	}
	private function generateUL($menu,$id=""){
		$data="";
		
		$id=$id!=""?'id="'.$id.'"':'';
		$data.='<ul '.$id.'>';
		foreach($menu as $key =>$val){
			$url=$val['url']==""?'':$val['url'];
			$data.=' <li> <a href="./?'.$url.'">'.$val['nombre'].'</a>';
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
					Pantallas.URL,Seguridad.*
			FROM clasificacion_pantallas
				INNER JOIN Pantallas ON (Pantallas.id_pantalla=clasificacion_pantallas.Pantallas_id_pantalla)
				INNER JOIN Seguridad ON (Seguridad.id_pantalla=Pantallas.`id_pantalla`)
			WHERE 
				Seguridad.Id_role='". SystemHtml::getInstance()->getSystemProtect()->getRoleId()."' 
				and Seguridad.acceso=1
			ORDER BY tipo_menu,order_,posicion_jeralquia ";
		
		
		$rs=SystemHtml::getInstance()->getSystemProtect()->getDBLink()->query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			//print_r($row);
			if ($row['tipo_menu']=="Menu"){
				$sp=explode("_",$row['posicion_jeralquia']);
				$menu[$sp[0]]=array('nombre'=>$row['nombre'],"url"=>$row['URL'],"hijos"=>array());
			}
			if ($row['tipo_menu']=="Submenu"){
				$sp=explode("_",$row['posicion_jeralquia']);
				$submenu=array('nombre'=>$row['nombre'],"url"=>$row['URL'],"hijos"=>array());
				if (isset($menu[$sp[0]])){
					array_push($menu[$sp[0]]['hijos'],$submenu);
				}
			}
		}
		$this->_menu=$menu;
	}
				





}


?>